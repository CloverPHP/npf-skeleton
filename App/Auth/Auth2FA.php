<?php

namespace App\Auth;

use App\Base;
use Exception\Auth2StepFactor;
use Exception\ObjectMismatch;
use Module\Module;
use Npf\Core\App;
use Npf\Core\Common;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;
use Npf\Exception\InvalidParams;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Login
 * @package App\Auth
 */
final class Auth2FA extends Base
{
    /**
     * GetClass
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
     * @throws Auth2StepFactor
     * @throws ObjectMismatch
     */
    final public function __invoke(App &$app, Module &$module)
    {
        $id = $app->session->get('admin_loginId');
        if (empty($id) || !Common::validateValue($id, 'number+'))
            $app->redirect("{$this->basePath}/");

        $action = $app->request->get('action', '');

        switch ($action) {

            case 'auth_otp':
                $app->request->validate([
                    'code' => ['number+', 'min' => 6],
                ]);
                $module->Admin->Auth->twoFactorLogin($id, $app->request->get('code'));
                $app->redirect("{$this->basePath}/");
                break;
        }
    }
}