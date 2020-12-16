<?php
namespace Module\Admin\Auth;

use Model\Loader;
use Module\Admin;
use Module\Admin\Auth;
use Module\Module;
use Npf\Core\App;

/**
 * Class Base
 * Enhanced curl and make more easy to use
 */
abstract class Base extends Admin\Base
{
    /**
     * @var Auth
     */
    protected $auth;
    /**
     * @var Module
     */
    protected $module;
    /**
     * @var Loader
     */
    protected $model;
    /**
     * @var Admin
     */
    protected $admin;

    /**
     * Base constructor.
     * @param App $app
     * @param Module $module
     * @param Admin $admin
     * @param Auth $auth
     */
    public function __construct(App &$app, Module &$module, Admin &$admin, Auth &$auth)
    {
        $this->auth = &$auth;
        parent::__construct($app, $module, $admin);
    }
}