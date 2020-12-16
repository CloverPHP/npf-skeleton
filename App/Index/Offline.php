<?php

namespace App\Index;

use Module\Module;
use Npf\Core\App;
use Npf\Exception\InternalError;

/**
 * Class Offline
 * @package App\Index
 */
class Offline
{
    private $mediaDomain = '';

    /**
     * Offline constructor.
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
        $app->response->set('assetsSource', "{$this->mediaDomain}/assets");
        $app->response->set('imageSource', "{$this->mediaDomain}/images");
        $app->response->set('mediaSource', "{$this->mediaDomain}");
        $app->view->setView('twig', "@Template/Layout/Other/Offline.twig");
    }
}