<?php

namespace App;

use Npf\Core\App;
use Module\Module;
use Npf\Exception\InternalError;
use Npf\Exception\InvalidParams;

/**
 * Class Index
 * @package App
 */
final class Index extends Base
{
    /**
     * GetClass
     * @param App $app
     * @param Module $module
     * @return void
     * @throws InternalError
     * @throws InvalidParams
     */
    final public function __invoke(App &$app, Module &$module)
    {
        if ($app->request->isXHR()) {
            $app->request->validate(['lang' => 'must']);
            $app->session->set('lang', $app->request->get('lang', 'en'));
        }
    }
}