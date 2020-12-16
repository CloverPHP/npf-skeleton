<?php

namespace Module\Admin\Auth;

use Exception\InvalidLogin;
use Exception\LoginRequired;
use Npf\Core\Common;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;

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
     * @return mixed
     * @throws DBQueryError
     * @throws InternalError
     * @throws InvalidLogin
     */
    final public function login($code)
    {
        $token = $this->module->OAuth->Microsoft->getAccessToken($this->app->request->getFullRequestUri(), $code);
        $oauthUser = $this->module->OAuth->Microsoft->getUserInfo($token['id_token']);
        $adminId = $this->module->Model->OAuthConnect->getByParty('microsoft', $oauthUser['id']);
        if (!$admin = $this->model->AdminManager->get($adminId))
        return $this->auth->prepare($admin);
    }
}