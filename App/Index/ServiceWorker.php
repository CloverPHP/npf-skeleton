<?php

namespace App\Index;

use Module\Module;
use Npf\Core\App;
use Npf\Exception\InternalError;

/**
 * Class ServiceWorker
 * Enhanced curl and make more easy to use
 */
class ServiceWorker
{
    private $mediaDomain = '';

    /**
     * ServiceWorker constructor.
     * @param App $app
     * @param Module $module
     * @throws InternalError
     */
    final public function __construct(App &$app, Module $module)
    {
        //Setup Language
        $module->I18n->setLang($app->session->get('lang', 'en'));
        $this->mediaDomain = $app->config('Misc')->get('cdnDomain', '');
    }

    /**
     * @param App $app
     * @param Module $module
     * @throws InternalError
     */
    final public function __invoke(App &$app, Module $module)
    {
        $app->view->setViewExpiry(3600);
        $app->response->header('Content-Type', 'text/javascript; charset=utf-8');
        $app->response->add('cachesList', [
            "/Offline",
            "/manifest.json",

            //Logo
            "{$this->mediaDomain}/assets/images/startman.svg",
            "{$this->mediaDomain}/assets/images/logo.png",
            "{$this->mediaDomain}/images/logo/favicon.ico",

            //CSS
            "{$this->mediaDomain}/assets/css/vendor/jquery-jvectormap-1.2.2.css",
            "{$this->mediaDomain}/css/waitMe.min.css",
            "{$this->mediaDomain}/assets/css/icons.min.css",
            "{$this->mediaDomain}/assets/css/app-modern.min.css",
            "{$this->mediaDomain}/assets/css/app-modern-dark.min.css",

            //Script
            "{$this->mediaDomain}/assets/js/vendor.min.js",
            "{$this->mediaDomain}/assets/js/app.min.js",
            "{$this->mediaDomain}/js/waitMe.min.js",
            "{$this->mediaDomain}/js/facebook.js",
        ]);
        $app->view->setView('twig', "@Template/Layout/Other/ServiceWorker.twig");
    }
}