<?php

namespace Module\Admin;

use Exception\InvalidLogin;
use Module\Admin\Auth\Facebook;
use Module\Admin\Auth\Google;
use Module\Admin\Auth\Microsoft;
use Module\Admin\Auth\Spotify;
use Npf\Core\Common;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;

/**
 * Class Menu
 * @package Module\Admin
 * @property Google $Google
 * @property Facebook $Facebook
 * @property Microsoft $Microsoft
 * @property Spotify $Spotify
 */
class Auth extends Base
{
    /**
     * @var array
     */
    private $browser = [];

    /**
     * @var array
     */
    private $components = [];

    /**
     * @param $name
     * @return mixed
     * @throws InternalError
     */
    final public function __get($name)
    {
        $className = "Module\\Admin\\OAuth\\{$name}";
        if (!isset($this->components[$name])) {
            if (class_exists($className))
                $this->components[$name] = new $className($this->app, $this->module, $this->admin, $this);
            else
                throw new InternalError("OAuth({$name}) not found.");
        }
        return $this->components[$name];
    }

    /**
     * @param array $admin
     * @return array
     * @throws DBQueryError
     * @throws InternalError
     */
    final public function prepare(array $admin)
    {
        $admin['role'] = $this->admin->Role->get($admin['roleid']);
        $admin['thirdParty'] = $this->model->OAuthConnect->listServiceByRoleId($admin['id']);
        $this->app->session->set('admin', $admin);
        $this->admin->retrieveAdmin();
        $this->log($admin);
        return $admin;
    }

    /**
     * @param $admin
     * @throws DBQueryError
     */
    final public function log($admin)
    {
        if (!$this->model->AdminLogin->addLog(
            $admin['id'],
            isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            $this->getBrowserName(),
            $this->getPlatformName(),
            Common::getClientIp()
        ))
            throw new DBQueryError('Unable add login log');
    }

    /**
     * @param $username
     * @param $password
     * @return mixed
     * @throws DBQueryError
     * @throws InternalError
     * @throws InvalidLogin
     */
    final public function login($username, $password)
    {
        $password = sha1($username . $password);
        if (!$admin = $this->model->AdminManager->getByLogin($username, $password))
            throw new InvalidLogin('Username/Password is wrong');
        return $this->prepare($admin);
    }

    /**
     * @return mixed
     */
    final public function getBrowserName()
    {
        if (!isset($this->browser['name'])) {
            $browser = $this->app->library->UserAgent->getBrowserName();
            $this->browser['name'] = $browser === null ? '(Unknow)' : $browser;
        }
        return $this->browser['name'];
    }

    /**
     * @return mixed
     */
    final public function getPlatformName()
    {
        if (!isset($this->browser['platform'])) {
            $platform = $this->app->library->UserAgent->getPlatformName();
            $this->browser['platform'] = $platform === null ? '(Unknow)' : $platform;
        }
        return $this->browser['platform'];
    }

}