<?php

namespace Module;

use Exception\CurrentPassInCorrect;
use Exception\LoginRequired;
use Exception\PermissionDenied;
use Model\Loader;
use Module\Admin\Auth;
use Module\Admin\Menu;
use Module\Admin\Manager;
use Module\Admin\Role;
use Npf\Core\App;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * @package Module
 * @property Auth $Auth
 * @property Manager $Manager
 * @property Menu $Menu
 * @property Role $Role
 */
class Admin
{
    /**
     * @var App
     */
    private $app;

    /**
     * @var Module
     */
    private $module;

    /**
     * @var Loader
     */
    private $model;

    /**
     * @var array
     */
    private $components = [];

    /**
     * @var array
     */
    private $admin = [];

    /**
     * Admin constructor.
     * @param App $app
     * @param Module $module
     * @throws InternalError
     */
    public function __construct(App &$app, Module &$module)
    {
        $this->app = &$app;
        $this->module = &$module;
        $this->model = &$module->Model;
        $this->retrieveAdmin();
    }

    /**
     * @throws InternalError
     */
    final public function retrieveAdmin()
    {
        $this->admin = $this->app->session->get('admin', []);
        if (!is_array($this->admin))
            $this->admin = [];
    }

    /**
     * Get Admin Id
     * @param bool $ignoreError
     * @return int|mixed
     * @throws LoginRequired
     */
    final public function getAdminId($ignoreError = false)
    {
        if (isset($this->admin['id']))
            return $this->admin['id'];
        elseif (!$ignoreError)
            throw new LoginRequired('Admin Login required');
        else
            return 0;
    }

    /**
     * Get Admin
     * @param $ignoreError
     * @return array
     * @throws LoginRequired
     */
    final public function getAdmin($ignoreError = false)
    {
        if (isset($this->admin['id']))
            return $this->admin;
        elseif (!$ignoreError)
            throw new LoginRequired('Admin Login required');
        else
            return [];
    }

    /**
     * @param $name
     * @return mixed
     * @throws InternalError
     */
    final public function __get($name)
    {
        $className = "Module\\Admin\\{$name}";
        if (!isset($this->components[$name])) {
            if (class_exists($className))
                $this->components[$name] = new $className($this->app, $this->module, $this);
            else
                throw new InternalError("Module({$name}) not found.");
        }
        return $this->components[$name];
    }

    /**
     * Change Admin Password
     * @param $oldPassword
     * @param $newPassword
     * @throws CurrentPassInCorrect
     * @throws DBQueryError
     * @throws LoginRequired
     */
    final public function changePassword($oldPassword, $newPassword)
    {
        $admin = $this->getAdminId();
        if (isset($admin['password']) && $admin['password'] === sha1($admin['username'] . $oldPassword))
            $this->module->Model->AdminManager->updatePassword(sha1($admin['username'] . $newPassword), $admin['id']);
        else
            throw new CurrentPassInCorrect('Your current password incorrect');
    }

    /**
     * Change Admin Name
     * @param $name
     * @throws DBQueryError
     * @throws InternalError
     * @throws LoginRequired
     */
    final public function changeName($name)
    {
        $admin = $this->getAdmin();
        $admin['name'] = $name;
        $this->module->Model->AdminManager->updateName($name, $admin['id']);
        $this->app->session->set('admin', $admin);
    }

    /**
     * Check Admin Action
     * @param $checkAction
     * @param string $errorRedirect
     * @return void
     * @throws DBQueryError
     * @throws InternalError
     * @throws LoaderError
     * @throws LoginRequired
     * @throws PermissionDenied
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function verifyAction($checkAction, $errorRedirect = '')
    {
        if (!$checkAction)
            return;
        elseif (!is_array($this->admin) || empty($this->admin))
            throw new LoginRequired('Login required admin');
        elseif (isset($this->admin['type']) && $this->admin['type'] === 'main')
            return;

        $action = preg_replace('/^\/Admin/', '', str_replace("\\", "/", $this->app->request->getUri()));
        if (!in_array($action, $this->admin['role']['actions'], true)) {
            if ($this->app->request->isXHR())
                throw new PermissionDenied('Permission Denied');
            else
                $this->app->redirect($errorRedirect);
        }
    }

    /**
     * Verify Access Menu Uri
     * @param $checkMenu
     * @param $errorRedirect
     * @throws DBQueryError
     * @throws InternalError
     * @throws LoaderError
     * @throws LoginRequired
     * @throws PermissionDenied
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function verifyMenu($checkMenu, $errorRedirect)
    {
        if (!$checkMenu)
            return;
        if (empty($uri))
            $uri = '/' . str_replace("\\", "/", $this->app->request->getUri());
        if (!$this->module->Admin->Menu->getMenuByUri($uri)) {
            if ($this->app->request->isXHR())
                throw new PermissionDenied('Permission Denied');
            else
                $this->app->redirect($errorRedirect);
        }
    }
}