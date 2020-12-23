<?php

namespace App\Profile;

use App\Base;
use Exception\Auth2StepFactor;
use Exception\CurrentPassInCorrect;
use Exception\LoginRequired;
use Module\Module;
use Npf\Core\App;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;
use Npf\Exception\InvalidParams;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

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
     * @throws InternalError
     * @throws InvalidParams
     * @throws LoaderError
     * @throws LoginRequired
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws Auth2StepFactor
     */
    public function __invoke(App &$app, Module &$module)
    {
        $action = $app->request->get('action');
        switch ($action) {

            case 'active2FA':
                $app->request->validate([
                    'code' => ['min:1', 'max:50'],
                ]);

                $module->Admin->active2FA(
                    $app->request->get('code')
                );
                $app->redirect('/Profile/');
                break;

            case 'deactivate2FA':
                $module->Admin->deactivate2FA();
                $app->redirect('/Profile/');
                break;

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