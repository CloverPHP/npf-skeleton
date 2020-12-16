<?php

namespace App\Auth;

use App\Base;
use Exception\InvalidLogin;
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
 * Class Login
 * @package App\Auth
 */
final class Login extends Base
{
    /**
     * GetClass
     * @param App $app
     * @param Module $module
     * @return void
     * @throws DBQueryError
     * @throws InternalError
     * @throws InvalidLogin
     * @throws InvalidParams
     * @throws LoaderError
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function __invoke(App &$app, Module &$module)
    {
        $this->clearSession();
        $state = $app->request->get('state', '');
        if (!empty($state)) {
            $queryData = [];
            parse_str($state, $queryData);
            $app->request->addRequest($queryData);
        }
        $action = $app->request->get('action', '');

        switch ($action) {

            case 'user':
                $app->request->validate([
                    'user' => 'must',
                    'pass' => 'must',
                ]);
                $user = $app->request->get('user', '');
                $pass = $app->request->get('pass', '');
                $module->Admin->Auth->login($user, $pass);
                break;

            case 'google':
                $code = $app->request->get('code', '');
                $error = $app->request->get('error', '');
                if (empty($code) && empty($error))
                    $app->redirect($module->OAuth->Google->getOAuthUrl(
                        $app->request->getFullRequestUri(),
                        'action=google',
                        'none'
                    ));
                elseif (!empty($code))
                    $module->Admin->Auth->Google->login($app->request->get('code', ''));
                elseif (!empty($error)) {
                    if ($error === 'interaction_required')
                        $app->redirect($module->OAuth->Google->getOAuthUrl(
                            $app->request->getFullRequestUri(),
                            'action=google',
                            'consent'
                        ));
                    else
                        $app->redirect("{$this->basePath}/");
                }
                break;

            case 'spotify':
                $code = $app->request->get('code', '');
                $error = $app->request->get('error', '');
                if (empty($code) && empty($error))
                    $app->redirect($module->OAuth->Spotify->getOAuthUrl(
                        $app->request->getFullRequestUri(),
                        "action={$action}"
                    ));
                elseif (!empty($code))
                    $module->Admin->Auth->Spotify->login($app->request->get('code', ''));
                elseif (!empty($error))  //error
                    $app->redirect("{$this->basePath}/");
                break;

            case 'facebook':
                $code = $app->request->get('code', '');
                $error = $app->request->get('error_code', '');
                if (empty($code) && empty($error))
                    $app->redirect($module->OAuth->Facebook->getOAuthUrl(
                        $app->request->getFullRequestUri(),
                        "action={$action}"
                    ));
                elseif (!empty($code))
                    $module->Admin->Auth->Facebook->login($app->request->get('code', ''));
                elseif (!empty($error))  //error
                    $app->redirect("{$this->basePath}/");
                break;

            case 'microsoft':
                $code = $app->request->get('code', '');
                $error = $app->request->get('error', '');
                if (empty($code) && empty($error))
                    $app->redirect($module->OAuth->Microsoft->getOAuthUrl(
                        $app->request->getFullRequestUri(),
                        "action={$action}"
                    ));
                elseif (!empty($code))
                    $module->Admin->Auth->Microsoft->login($app->request->get('code', ''));
                elseif (!empty($error)) {
                    if (in_array($error, ['interaction_required', 'login_required', 'consent_required']))
                        $app->redirect($module->OAuth->Microsoft->getOAuthUrl(
                            $app->request->getFullRequestUri(),
                            "action={$action}",
                            'consent'
                        ));
                }
                break;
        }

        if ($app->request->getPathInfo() === "{$this->basePath}/Auth/Login")
            $app->redirect("{$this->basePath}/");
    }
}