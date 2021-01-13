<?php

namespace App\Admin\Manager;

use App\Base;
use Exception\LoginRequired;
use Npf\Core\App;
use Module\Module;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;
use Npf\Exception\InvalidParams;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Add
 * @package App\Admin\Manager
 */
final class Add extends Base
{
    protected $checkAction = true;

    /**
     * @param App $app
     * @param Module $module
     * @return void
     * @throws DBQueryError
     * @throws InternalError
     * @throws InvalidParams
     * @throws LoaderError
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws LoginRequired
     */
    final public function __invoke(App &$app, Module &$module)
    {
        $app->on('appBeforeClean', function (App $app) use ($module) {
            $roles = $module->Admin->Role->listField();
            $roles[0] = 'Main';
            $app->response->set('roles', $roles);
        });

        switch ($app->request->get('action')) {
            case 'submit':
                $app->request->validate([
                    'roleid' => 'number',
                    'user' => ['min:1', 'mbmax:20'],
                    'name' => ['min:1', 'mbmax:20'],
                    'pass' => ['min:6', 'mbmax:20']
                ]);

                $module->Admin->Manager->add(
                    $app->request->get('roleid'),
                    $app->request->get('user'),
                    $app->request->get('pass'),
                    $app->request->get('name')
                );
                $this->app->redirect("{$this->basePath}/Admin/Manager/");
                break;
        }
    }
}