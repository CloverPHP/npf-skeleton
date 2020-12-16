<?php

namespace App\Index;

use Npf\Core\App;
use Npf\Core\Route;
use Module\Module;

/**
 * Class Router
 * @package App
 */
final class Router
{
    /**
     * @var App
     */
    private $app;

    /**
     * Router constructor.
     * @param App $app
     * @param Route $route
     */
    final public function __construct(App &$app, Route &$route)
    {
        $this->app = &$app;
        $requestUri = $route->getRequestUri();
        switch (strtolower($requestUri)) {
            case '/service-worker.js':
                $route->setAppClass('Index\ServiceWorker');
                break;
            case '/robots.txt':
                $route->setAppClass('Index\Robots');
                break;
            case '/manifest.json':
                $route->setAppClass('Index\Manifest');
                break;
            case  '/browserconfig.xml':
                $route->setAppClass('Index\BrowserConfig');
                break;
        }
    }

    /**
     * GetClass
     * @param App $app
     * @return array
     */
    final public function __invoke(App &$app)
    {
        $module = new Module($app);

        $app->view->addTwigExtension($module->I18n);
        return [&$module];
    }
}