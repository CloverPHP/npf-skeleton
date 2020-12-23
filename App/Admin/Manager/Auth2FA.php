<?php

namespace App\Admin\Manager;

use Npf\Core\App;
use Module\Module;
use App\Base;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;

/**
 * Class Status
 * @package App\Admin\Manager
 */
final class Auth2FA extends Base
{
    protected $checkAction = true;

    /**
     * @param App $app
     * @param Module $module
     * @throws DBQueryError
     * @throws InternalError
     */
    final public function __invoke(App &$app, Module &$module)
    {
        $id = $app->request->get('id');
        $status = (int)$app->request->get('status');
        $module->Admin->Manager->update2FA($status, $id);
    }
}