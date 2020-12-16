<?php

namespace App\Admin\Roles;

use Npf\Core\App;
use Module\Module;
use App\Base;
use Exception\ObjectMismatch;
use Npf\Exception\DBQueryError;

/**
 * Class Status
 * @package App\Admin\Roles
 */
final class Status extends Base
{
    protected $checkAction = true;

    /**
     * @param App $app
     * @param Module $module
     * @return void
     * @throws ObjectMismatch
     * @throws DBQueryError
     */
    final public function __invoke(App &$app, Module &$module)
    {
        $id = $app->request->get('id');
        if (!$module->Admin->Role->get($id))
            throw new ObjectMismatch('Role id not found');

        $module->Admin->Role->updateStatus((int)$app->request->get('status'), $id);
    }
}