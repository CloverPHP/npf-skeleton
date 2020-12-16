<?php

namespace App;

use Npf\Core\App;
use Npf\Core\Route;
use Module\Module;
use ReflectionException;

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

    private $staticMatches = [
        '/\/assets\/(.+)/' => 'Template/Static/assets/',
        '/\/images\/(.+)/' => 'Template/Static/images/',
        '/\/css\/(.+)/' => 'Template/Static/css/',
        '/\/js\/(.+)/' => 'Template/Static/js/',
    ];

    /**
     * Router constructor.
     * @param App $app
     * @param Route $route
     * @throws ReflectionException
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
            default:
                foreach ($this->staticMatches as $matchExp => $matchPath) {
                    $matches = [];
                    if (preg_match($matchExp, $requestUri, $matches)) {
                        $route->setStaticFile($app->getRootPath() . "{$matchPath}{$matches[1]}");
                        break;
                    }
                }
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