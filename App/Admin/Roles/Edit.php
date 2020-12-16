<?php

namespace App\Admin\Roles;

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
 * @package App\Admin\Roles
 */
final class Edit extends Base
{
    protected $checkAction = true;

    /**
     * GetClass
     * @param App $app
     * @param Module $module
     * @return void
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
        $actionList = $this->getActions();
        $actionList = array_combine($actionList, $actionList);
        $app->on('appBeforeClean', function (App $app) use ($module, $actionList) {
            $app->response->set('actionList', $actionList);
            $app->response->set('menuList', $module->Admin->Menu->listField(true));
        });

        $app->request->validate(['id' => 'must']);
        $id = $app->request->get('id');

        switch ($app->request->get('action')) {

            case 'submit':
                $app->request->validate([
                    'name' => ['min:3', 'max:50'],
                    'description' => ['min:1', 'max:300'],
                    'menus' => 'array+',
                    'actions' => ['optional', 'array'],
                ]);
                $module->Admin->Role->update(
                    $app->request->get('name'),
                    $app->request->get('description'),
                    $app->request->get('menus'),
                    $app->request->get('actions'), $id);
                $this->app->redirect("{$this->basePath}/Admin/Roles/");
                break;

            default:
                $info = $module->Admin->Role->get($id);
                if (!$info)
                    throw new ObjectMismatch('Admin Role not found');
                $app->response->import($info);
                break;
        }
    }
}