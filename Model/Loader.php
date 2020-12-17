<?php

namespace Model;

use Npf\Core\App;
use \Npf\Core\Model;
use Npf\Exception\InternalError;

/**
 * Class Loader
 * @property Admin $Admin
 * @property AdminManager $AdminManager
 * @property AdminMenu $AdminMenu
 * @property AdminRole $AdminRole
 * @property AdminLogin $AdminLogin
 * @property OAuthConnect $OAuthConnect
 */
final class Loader extends Model
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var array
     */
    private $components = [];


    /**
     * Loader constructor.
     * @param App $app
     */
    final public function __construct(App &$app)
    {
        parent::__construct($app);
    }

    /**
     * @param $name
     * @return mixed
     * @throws InternalError
     */
    final public function __get($name)
    {
        if (!isset($this->components[$name]))
            $this->components[$name] = $this->app->model($name);
        return $this->components[$name];
    }
}