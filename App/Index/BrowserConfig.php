<?php

namespace App\Index;

use Module\Module;
use Npf\Core\App;
use Npf\Exception\InternalError;

/**
 * Class BrowserConfig
 * Enhanced curl and make more easy to use
 */
class BrowserConfig
{
    private $mediaDomain = '';

    /**
     * BrowserConfig constructor.
     * @param App $app
     * @throws InternalError
     */
    final public function __construct(App &$app)
    {
        $app->view->setView('xml', 'browserconfig');
        $this->mediaDomain = $app->config('Misc')->get('cdnDomain', '');
    }

    /**
     * @param App $app
     * @param Module $module
     */
    final public function __invoke(App &$app, Module $module)
    {
        $app->view->setViewExpiry(3600);
        $app->response->add('msapplication', [
            'tile' => [
                'square70x70logo' => [
                    '@attributes' => [
                        'src' => "{$this->mediaDomain}/images/logo/70.png"
                    ],
                ],
                'square150x150logo' => [
                    '@attributes' => [
                        'src' => "{$this->mediaDomain}/images/logo/150.png"
                    ],
                ],
                'square310x310logo' => [
                    '@attributes' => [
                        'src' => "{$this->mediaDomain}/images/logo/310.png"
                    ],
                ],
                'TileColor' => '#343a40',
            ]
        ]);
    }
}