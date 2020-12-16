<?php


namespace Module\Admin;


use Model\Loader;
use Module\Admin;
use Module\Module;
use Npf\Core\App;

/**
 * Class Base
 * Enhanced curl and make more easy to use
 */
abstract class Base
{
    /**
     * @var App
     */
    protected $app;
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
     */
    public function __construct(App &$app, Module $module, Admin $admin)
    {
        $this->app = &$app;
        $this->module = &$module;
        $this->model = &$module->Model;
        $this->admin = &$admin;
    }
}