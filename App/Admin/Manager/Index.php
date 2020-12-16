<?php

namespace App\Admin\Manager;

use App\Base;
use Npf\Core\App;
use Module\Module;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;

/**
 * Class Index
 * @package App\Admin\Manager
 */
final class Index extends Base
{
    protected $checkMenu = true;

    /**
     * @param App $app
     * @param Module $module
     * @throws DBQueryError
     * @throws InternalError
     */
    final public function __invoke(App &$app, Module &$module)
    {
        $app->on('appBeforeClean', function (App $app) use ($module) {
            $app->response->set('roles', $module->Admin->Role->listField());
        });

        $search = $this->getSearch();
        $app->response->import($module->Admin->Manager->search(
            $search->get('name'),
            $search->get('roleid'),
            (int)$search->get('page', 1),
            (int)$search->get('pageSize', 20)
        ));
    }
}