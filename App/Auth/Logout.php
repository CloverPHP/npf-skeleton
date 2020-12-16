<?php

namespace App\Auth;

use App\Base;
use Npf\Core\App;
use Module\Module;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Router
 * @package Application\Index
 */
final class Logout extends Base
{

    /**
     * GetClass
     * @param App $app
     * @param Module $module
     * @return void
     * @throws InternalError
     * @throws DBQueryError
     * @throws ReflectionException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function __invoke(App &$app, Module &$module)
    {
        $this->clearSession();
        if (!$app->request->isXHR())
            $app->redirect("{$this->basePath}/");
    }
}