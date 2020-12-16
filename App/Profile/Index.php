<?php

namespace App\Profile;

use App\Base;
use Exception\CurrentPassInCorrect;
use Exception\LoginRequired;
use Module\Module;
use Npf\Core\App;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;
use Npf\Exception\InvalidParams;

/**
 * Class Index
 * @package App\Profile
 */
class Index extends Base
{
    /**
     * @param App $app
     * @param Module $module
     * @throws CurrentPassInCorrect
     * @throws DBQueryError
     * @throws InvalidParams
     * @throws LoginRequired
     * @throws InternalError
     */
    public function __invoke(App &$app, Module &$module)
    {
        $action = $app->request->get('action');
        switch ($action) {

            case 'password':
                $app->request->validate([
                    'oldpsw' => ['min:1', 'max:20'],
                    'newpsw' => ['min:1', 'max:20'],
                ]);

                $module->Admin->changePassword(
                    $app->request->get('oldpsw'),
                    $app->request->get('newpsw')
                );
                break;

            case 'name':
                $app->request->validate([
                    'name' => ['min:1', 'max:50'],
                ]);

                $module->Admin->changeName(
                    $app->request->get('name')
                );
                break;
        }
    }

}