<?php

namespace Module\Admin\Auth;

use Exception\InvalidLogin;
use Exception\LoginRequired;
use Npf\Core\Common;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Facebook
 * @package Module\Admin\OAuth
 */
class Facebook extends Base
{
    /**
     * @param $code
     * @return mixed
     * @throws DBQueryError
     * @throws InternalError
     * @throws LoginRequired
     * @throws ReflectionException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function connect($code)
    {
        $token = $this->module->OAuth->Facebook->getAccessToken($this->app->request->getFullRequestUri(), $code);
        if (isset($token['error']))
            $this->app->redirect($this->module->OAuth->Facebook->getOAuthUrl($this->app->request->getFullRequestUri(), 'state=facebook'));
        $this->app->profiler->debug($token);
        $oauthUser = $this->module->OAuth->Facebook->getUserInfo($token['access_token']);
        if (!$this->module->Model->OAuthConnect->getByParty('facebook', $oauthUser['id']))
            $this->module->Model->OAuthConnect->create($this->admin->getAdminId(), 'facebook', $oauthUser['id']);
        return $this->auth->prepare($this->admin->getAdmin());
    }

    /**
     * @param $code
     * @return mixed
     * @throws DBQueryError
     * @throws InternalError
     * @throws InvalidLogin
     */
    final public function login($code)
    {
        $token = $this->module->OAuth->Facebook->getAccessToken($this->app->request->getFullRequestUri(), $code);
        $oauthUser = $this->module->OAuth->Facebook->getUserInfo($token['access_token']);
        $adminId = $this->module->Model->OAuthConnect->getByParty('facebook', $oauthUser['id']);
        if (!$admin = $this->model->AdminManager->get($adminId))
            throw new InvalidLogin('Username/Password is wrong');
        return $this->auth->prepare($admin);
    }
}