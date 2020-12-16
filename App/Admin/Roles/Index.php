<?php

namespace App\Admin\Roles;

use App\Base;
use Npf\Core\App;
use Module\Module;
use Npf\Exception\DBQueryError;

/**
 * Class Index
 * @package App\Admin\Roles
 */
final class Index extends Base
{
    protected $checkMenu = true;

    /**
     * @param App $app
     * @param Module $module
     * @throws DBQueryError
     */
    final public function __invoke(App &$app, Module &$module)
    {
        $app->response->add('lists', $module->Admin->Role->listRole());
    }
}