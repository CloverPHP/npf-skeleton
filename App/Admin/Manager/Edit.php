<?php

namespace App\Admin\Manager;

use App\Base;
use Exception\LoginRequired;
use Exception\ObjectMismatch;
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
 * Class Edit
 * @package App\Admin\Manager
 */
final class Edit extends Base
{
    protected $checkAction = true;

    /**
     * @param App $app
     * @param Module $module
     * @throws DBQueryError
     * @throws InternalError
     * @throws InvalidParams
     * @throws LoaderError
     * @throws ObjectMismatch
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

        $app->request->validate(['id' => 'must']);
        $id = $app->request->get('id');

        switch ($app->request->get('action')) {

            case 'submit':
                $app->request->validate([
                    'roleid' => 'number',
                    'name' => ['min:1', 'mbmax:20'],
                ]);

                $password = $app->request->get('pass');
                if ($password !== '' && strlen($password) < 6)
                    throw new InvalidParams('Password must more then 6 character');

                $module->Admin->Manager->update(
                    $app->request->get('roleid'),
                    $password,
                    $app->request->get('name'),
                    $app->request->get('id')
                );
                $this->app->redirect("{$this->basePath}/Admin/Manager/");
                break;

            default:
                if (!$info = $module->Admin->Manager->get($id))
                    throw new ObjectMismatch('Admin not found');
                unset($info['pass']);
                $app->response->import($info);
                break;
        }
    }
}