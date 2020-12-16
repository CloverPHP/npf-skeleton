<?php

namespace App\Admin\Menus;

use App\Base;
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
 * Class Move
 * @package App\Admin\Menus\Item
 */
final class Move extends Base
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
    final public function __invoke(App &$app, Module &$module)
    {
        $app->request->validate(['id' => 'number+']);
        $move = $app->request->get('move');
        if($move === 'up'){
            $module->Admin->Menu->moveUp(
                (int)$app->request->get('id')
            );
        }else{
            $module->Admin->Menu->moveDown(
                (int)$app->request->get('id')
            );
        }
        $app->redirect("{$this->basePath}/Admin/Menus/");
    }
}