<?php

namespace App\Admin\Manager;

use Npf\Core\App;
use Module\Module;
use App\Base;
use Npf\Exception\DBQueryError;

/**
 * Class Status
 * @package App\Admin\Manager
 */
final class Status extends Base
{
    protected $checkAction = true;

    /**
     * @param App $app
     * @param Module $module
     * @throws DBQueryError
     */
    final public function __invoke(App &$app, Module &$module)
    {
        $id = $app->request->get('id');
        $status = (int)$app->request->get('status');

        $module->Admin->Manager->updateStatus($status, $id);
    }
}