<?php

namespace App\Admin\Menus;

use App\Base;
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
 * Class Add
 * @package App\Admin\Menus
 */
class Add extends Base
{
    protected $checkAction = true;

    /**
     * @param App $app
     * @param Module $module
     * @throws DBQueryError
     * @throws InternalError
     * @throws InvalidParams
     * @throws LoaderError
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __invoke(App $app, Module $module)
    {
        $app->on('appBeforeClean', function (App &$app) use ($module) {
            $app->response->add('parentMenu', [0 => $module->I18n->translation('Main')] + $module->Admin->Menu->listField(false, [], 3));
        });

        switch ($app->request->get('action')) {

            case 'submit':
                $app->request->validate([
                    'parentid' => 'number',
                    'name' => 'must',
                    'uri' => '',
                ]);

                $module->Admin->Menu->add(
                    $app->request->get('parentid'),
                    $app->request->get('name'),
                    $app->request->get('icon'),
                    $app->request->get('uri')
                );
                $this->app->redirect("{$this->basePath}/Admin/Menus/");
                break;
        }
    }

}