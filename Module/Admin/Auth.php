<?php

namespace Module\Admin;

use Exception\Auth2StepFactor;
use Exception\InvalidLogin;
use Exception\ObjectMismatch;
use Module\Admin\Auth\Facebook;
use Module\Admin\Auth\Google;
use Module\Admin\Auth\Microsoft;
use Module\Admin\Auth\Spotify;
use Npf\Core\Common;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
        $this->app->session->del('admin_loginId');
        $admin['role'] = $this->admin->Role->get($admin['roleid']);
        $admin['thirdParty'] = $this->model->OAuthConnect->listServiceByRoleId($admin['id']);
        $this->app->session->set('admin', $admin);
        $this->admin->retrieveAdmin();
        $this->log($admin);
        return $admin;
    }

    /**
     * @param array $admin
     * @throws DBQueryError
     * @throws InternalError
     * @throws LoaderError
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function auth2FA(array &$admin)
    {
        $admin['2fa'] = (int)$admin['2fa'];
        $this->app->session->set('admin_loginId', (int)$admin['id']);
        switch ($admin['2fa']) {
            case 1:
                $this->app->redirect("/Auth/Auth2FA");
                break;
            case 2:
                $this->app->redirect("/Auth/Enforce2FA");
                break;
        }
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
     * @throws ReflectionException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function login($username, $password)
    {
        $password = sha1($username . $password);
        if (!$admin = $this->model->AdminManager->getByLogin($username, $password))
            throw new InvalidLogin('Username/Password is wrong');
        $this->auth2FA($admin);
        return $this->prepare($admin);
    }

    /**
     * @param $id
     * @param $code
     * @return mixed
     * @throws Auth2StepFactor
     * @throws DBQueryError
     * @throws InternalError
     * @throws ObjectMismatch
     */
    final public function twoFactorLogin($id, $code)
    {
        if (!$admin = $this->model->AdminManager->get($id))
            throw new ObjectMismatch('Admin is not exists.');
        if (!$this->app->library->TwoFactorAuth->verifyCode($admin['secret'], $code))
            throw new Auth2StepFactor('Two Step Factor Authorize code is invalid');
        return $this->prepare($admin);
    }

    /**
     * @param $id
     * @param $code
     * @return mixed
     * @throws Auth2StepFactor
     * @throws DBQueryError
     * @throws InternalError
     * @throws ObjectMismatch
     */
    final public function enforce2FA($id, $code)
    {
        if (!$admin = $this->model->AdminManager->get($id))
            throw new ObjectMismatch('Admin is not exists.');
        if (!$this->app->library->TwoFactorAuth->verifyCode($admin['secret'], $code))
            throw new Auth2StepFactor();
        $this->model->AdminManager->active2FA($admin['id']);
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