<?php

namespace App\Auth;

use App\Base;
use Exception\LoginRequired;
use Npf\Core\App;
use Module\Module;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Login
 * @package App\Auth
 */
final class Connect extends Base
{
    /**
     * GetClass
     * @param App $app
     * @param Module $module
     * @return void
     * @throws DBQueryError
     * @throws InternalError
     * @throws LoaderError
     * @throws LoginRequired
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final public function __invoke(App &$app, Module &$module)
    {
        $this->viewJson();
        $state = $app->request->get('state', '');
        if (!empty($state)) {
            $queryData = [];
            parse_str($state, $queryData);
            $app->request->addRequest($queryData);
        }

        $action = $app->request->get('action', '');
        switch ($action) {

            case 'google':
                $code = $app->request->get('code', '');
                $error = $app->request->get('error', '');
                if (empty($code) && empty($error))
                    $app->redirect($module->OAuth->Google->getOAuthUrl(
                        $app->request->getFullRequestUri(),
                        "action={$action}",
                        'consent'
                    ));
                elseif (!empty($code))
                    $module->Admin->Auth->Google->connect($app->request->get('code', ''));
                elseif (!empty($error))
                    $app->redirect("{$this->basePath}/");
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
                    $module->Admin->Auth->Spotify->connect($app->request->get('code', ''));
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
                    $module->Admin->Auth->Facebook->connect($app->request->get('code', ''));
                elseif (!empty($error))  //error
                    $app->redirect("{$this->basePath}/");
                break;

            case 'microsoft':
                $code = $app->request->get('code', '');
                $error = $app->request->get('error', '');
                if (empty($code) && empty($error))
                    $app->redirect($module->OAuth->Microsoft->getOAuthUrl(
                        $app->request->getFullRequestUri(),
                        "action={$action}",
                        'consent'
                    ));
                elseif (!empty($code))
                    $module->Admin->Auth->Microsoft->connect($app->request->get('code', ''));
                elseif (!empty($error))
                    $app->redirect("{$this->basePath}/");
                break;
        }
    }
}