<?php

namespace Module\Admin\Auth;

use Exception\InvalidLogin;
use Exception\LoginRequired;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Microsoft
 * @package Module\Admin\OAuth
 */
class Microsoft extends Base
{
    /**
     * @param $code
     * @return mixed
     * @throws DBQueryError
     * @throws InternalError
     * @throws LoginRequired
     */
    final public function connect($code)
    {
        $token = $this->module->OAuth->Microsoft->getAccessToken($this->app->request->getFullRequestUri(), $code);
        $oauthUser = $this->module->OAuth->Microsoft->getUserInfo($token['id_token']);
        if (!$this->module->Model->OAuthConnect->getByParty('microsoft', $oauthUser['id']))
            $this->module->Model->OAuthConnect->create($this->admin->getAdminId(), 'microsoft', $oauthUser['id']);
        return $this->auth->prepare($this->admin->getAdmin());
    }

    /**
     * @param $code
     * @return array|false
     * @throws DBQueryError
     * @throws InternalError
     * @throws ReflectionException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function login($code)
    {
        $token = $this->module->OAuth->Microsoft->getAccessToken($this->app->request->getFullRequestUri(), $code);
        $oauthUser = $this->module->OAuth->Microsoft->getUserInfo($token['id_token']);
        $adminId = $this->module->Model->OAuthConnect->getByParty('microsoft', $oauthUser['id']);
        if (!$admin = $this->model->AdminManager->get($adminId))
            return $this->auth->prepare($admin);
        $this->auth->auth2FA($admin);
        return false;
    }
}