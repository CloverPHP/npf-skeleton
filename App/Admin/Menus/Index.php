<?php

namespace App\Admin\Menus;

use App\Base;
use Module\Module;
use Npf\Core\App;
use Npf\Exception\DBQueryError;

/**
 * Class Index
 * @package App\Admin\Menus
 */
class Index extends Base
{
    protected $checkMenu = true;

    /**
     * @param App $app
     * @param Module $module
     * @throws DBQueryError
     */
    public function __invoke(App $app, Module $module)
    {
        $app->response->set('lists', $module->Admin->Menu->listMenu());
    }

}