<?php

namespace App\Index;

use Npf\Core\App;
use Module\Module;
use Npf\Exception\InternalError;

/**
 * Class PermissionDenied
 * @package App\Index
 */
final class PermissionDenied
{
    private $mediaDomain = '';

    /**
     * PermissionDenied constructor.
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
        $app->view->setView('twig', "@Template/Layout/Other/PermissionDenied.twig");
    }
}