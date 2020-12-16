<?php

namespace App\Index;

use Module\Module;
use Npf\Core\App;
use Npf\Exception\InternalError;

/**
 * Class Manifest
 * @package App\Index
 */
class Manifest
{
    private $mediaDomain = '';

    /**
     * Manifest constructor.
     * @param App $app
     * @throws InternalError
     */
    final public function __construct(App &$app)
    {
        $app->view->setView('json');
        $this->mediaDomain = $app->config('Misc')->get('cdnDomain', '');
    }

    /**
     * @param App $app
     * @param Module $module
     */
    final public function __invoke(App &$app, Module $module)
    {
        $app->view->setViewExpiry(3600);
        $app->response->add('name', 'Accounting');
        $app->response->add('short_name', 'Accounting');
        $app->response->add('start_url', '/');
        $app->response->add('scope', '/');
        $app->response->add('theme_color', '#343a40');
        $app->response->add('background_color', '#343a40');
        $app->response->add('display', 'standalone');
        $app->response->add('orientation', 'natural');
        $app->response->add('icons', [
            [
                'src' => "{$this->mediaDomain}/images/logo/36.png",
                'sizes' => '36x36',
                'type' => 'image/png',
                'density' => '0.75'
            ],
            [
                'src' => "{$this->mediaDomain}/images/logo/48.png",
                'sizes' => '48x48',
                'type' => 'image/png',
                'density' => '1'
            ],
            [
                'src' => "{$this->mediaDomain}/images/logo/72.png",
                'sizes' => '72x72',
                'type' => 'image/png',
                'density' => '1.5'
            ],
            [
                'src' => "{$this->mediaDomain}/images/logo/96.png",
                'sizes' => '96x96',
                'type' => 'image/png',
                'density' => '2.0'
            ],
            [
                'src' => "{$this->mediaDomain}/images/logo/144.png",
                'sizes' => '144x144',
                'type' => 'image/png',
                'density' => '3.0'
            ],
            [
                'src' => "{$this->mediaDomain}/images/logo/192.png",
                'sizes' => '192x192',
                'type' => 'image/png',
                'density' => '4.0',
                'purpose' => 'any maskable',
            ],
            [
                'src' => "{$this->mediaDomain}/images/logo/512.png",
                'sizes' => '512x512',
                'type' => 'image/png',
                'density' => '10.6'
            ],
        ]);
    }
}