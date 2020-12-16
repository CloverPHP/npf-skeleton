<?php

namespace Module;

use Model\Loader;
use Npf\Core\App;
use Npf\Exception\InternalError;

/**
 * Class Module
 * @property Admin $Admin
 * @property OAuth $OAuth
 * @property I18n $I18n
 * @property JwtSess $JwtSess
 * @property Loader $Model
 * @property Reporter $Reporter
 */
final class Module
    {
    /**
     * @var App
     */
    private $app;

    /**
     * @var array
     */
    private $components = [];

    /**
     * Model constructor.
     * @param App $app
     */
    final public function __construct(App &$app)
    {
        $this->app = &$app;
    }

    /**
     * @param $name
     * @return mixed
     * @throws InternalError
     */
    final public function __get($name)
    {
        if ($name === 'Model')
            $className = "Model\\Loader";
        else
            $className = "Module\\{$name}";
        if (!isset($this->components[$name])) {
            if (class_exists($className))
                $this->components[$name] = new $className($this->app, $this);
            else
                throw new InternalError("Module({$name}) not found.");
        }
        return $this->components[$name];
    }
}