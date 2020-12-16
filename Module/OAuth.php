<?php

namespace Module;

use Module\OAuth\Facebook;
use Module\OAuth\Google;
use Module\OAuth\Microsoft;
use Module\OAuth\Spotify;
Use Npf\Core\App;
use Npf\Exception\InternalError;

/**
 * Class OAuth
 * @package Module
 * @property Google $Google
 * @property Facebook $Facebook
 * @property Spotify $Spotify
 * @property Microsoft $Microsoft
 */
class OAuth
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
     * @var array
     */
    private $components = [];

    /**
     * Account constructor.
     * @param App $app
     * @param Module $module
     */
    final public function __construct(App &$app, Module &$module)
    {
        $this->app = &$app;
        $this->module = &$module;
    }

    /**
     * @param $name
     * @return mixed
     * @throws InternalError
     */
    final public function __get($name)
    {
        $className = "Module\\OAuth\\{$name}";
        if (!isset($this->components[$name])) {
            if (class_exists($className))
                $this->components[$name] = new $className($this->app, $this->module, $this);
            else
                throw new InternalError("OAuth({$name}) not found.");
        }
        return $this->components[$name];
    }
}