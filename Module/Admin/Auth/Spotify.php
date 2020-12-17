<?php

namespace Module\Admin\Auth;

use Exception\InvalidLogin;
use Exception\LoginRequired;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;

/**
 * Class Facebook
 * @package Module\Admin\OAuth
 */
class Spotify extends Base
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
        $token = $this->module->OAuth->Spotify->getAccessToken($this->app->request->getFullRequestUri(), $code);
        $oauthUser = $this->module->OAuth->Spotify->getUserInfo($token['access_token']);
        if (!$this->module->Model->OAuthConnect->getByParty('spotify', $oauthUser['id']))
            $this->module->Model->OAuthConnect->create($this->admin->getAdminId(), 'spotify', $oauthUser['id']);
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
        $token = $this->module->OAuth->Spotify->getAccessToken($this->app->request->getFullRequestUri(), $code);
        $oauthUser = $this->module->OAuth->Spotify->getUserInfo($token['access_token']);
        $adminId = $this->module->Model->OAuthConnect->getByParty('spotify', $oauthUser['id']);
        if (!$admin = $this->model->AdminManager->get($adminId))
            throw new InvalidLogin('Username/Password is wrong');
        return $this->auth->prepare($admin);
    }
}