<?php

namespace App\Admin\Menus;

use App\Base;
use Exception\ObjectMismatch;
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
 * Class Edit
 * @package App\Admin\Menus
 */
class Edit extends Base
{
    protected $checkAction = true;

    /**
     * @param App $app
     * @param Module $module
     * @throws DBQueryError
     * @throws InternalError
     * @throws InvalidParams
     * @throws ObjectMismatch
     * @throws ReflectionException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(App $app, Module $module)
    {
        $app->on('appBeforeClean', function (App &$app) use ($module) {
            $app->response->add('parentMenu', [0 => $module->I18n->translation('Main')] + $module->Admin->Menu->listField(false, (int)$app->request->get('id') > 0 ? [(int)$app->request->get('id')] : null, 3));
        });

        $app->request->validate(['id' => ['number+']]);
        $id = (int)$app->request->get('id');

        switch ($app->request->get('action')) {

            case "submit":
                $app->request->validate([
                    'parentid' => 'must',
                    'name' => 'must',
                ]);
                $module->Admin->Menu->update(
                    $app->request->get('parentid'),
                    $app->request->get('name'),
                    $app->request->get('icon'),
                    $app->request->get('uri'),
                    $app->request->get('id')
                );
                $app->redirect("{$this->basePath}/Admin/Menus/");
                break;

            default:
                if (!$info = $module->Admin->Menu->get($id))
                    throw new ObjectMismatch('Menu not found');
                $app->response->import($info);
                break;
        }
    }
}