<?php

namespace Module\OAuth;

use Model\Loader;
use Module\Module;
use Module\OAuth;
use Npf\Core\App;
use Npf\Exception\InternalError;

/**
 * Class Base
 * @package Module\OAuth
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
     * @var OAuth
     */
    protected $OAuth;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * Config Name
     */
    protected $name = '';

    /**
     * Base constructor.
     * @param App $app
     * @param Module $module
     * @param OAuth $OAuth
     * @throws InternalError
     */
    final public function __construct(App &$app, Module $module, OAuth $OAuth)
    {
        $this->app = &$app;
        $this->module = &$module;
        $this->model = $module->Model;
        $this->OAuth = &$OAuth;
        $this->config = $app->config('Misc')->get('OAuth', [])[$this->name];
    }
}