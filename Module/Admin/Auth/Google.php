<?php

namespace Module\Admin\Auth;

use Exception\InvalidLogin;
use Exception\LoginRequired;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;

/**
 * Class Google
 * @package Module\Admin\OAuth
 */
class Google extends Base
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
        $token = $this->module->OAuth->Google->getAccessToken($this->app->request->getFullRequestUri(), $code);
        $oauthUser = $this->module->OAuth->Google->getUserInfo($token['id_token']);
        if (!$this->module->Model->OAuthConnect->getByParty('google', $oauthUser['id']))
            $this->module->Model->OAuthConnect->create($this->admin->getAdminId(), 'google', $oauthUser['id']);
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
        $token = $this->module->OAuth->Google->getAccessToken($this->app->request->getFullRequestUri(), $code);
        $oauthUser = $this->module->OAuth->Google->getUserInfo($token['id_token']);
        $adminId = $this->module->Model->OAuthConnect->getByParty('google', $oauthUser['id']);
        if (!$admin = $this->model->AdminManager->get($adminId))
            throw new InvalidLogin('Username/Password is wrong');
        return $this->auth->prepare($admin);
    }
}