<?php

namespace App;

use Exception\LoginRequired;
use Exception\PermissionDenied;
use Module\Module;
use Npf\Core\App;
use Npf\Core\Container;
use Npf\Exception\DBQueryError;
use Npf\Exception\InternalError;
use ReflectionException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Base
 * @package App
 */
abstract class Base
{
    /**
     * @var App
     */
    protected $app;

    /**
     * @var Module
     */
    protected $module;

    /**
     * @var string Media Domain
     */
    protected $mediaDomain = '';

    /**
     * @var array Language List
     */
    protected $langMap = [
        'en' => 'English',
        'zhHans' => '简体中文',
        'zhHant' => '繁体中文',
    ];

    /**
     * @var bool
     */
    protected $onErrorGoBack = false;
    /**
     * @var string Current Lang
     */
    protected $lang = 'en';

    /**
     * @var string Base Path
     */
    protected $basePath = '';

    /**
     * @var array Login User
     */
    protected $admin = [];

    /**
     * @var string View Type
     */
    private $view = '';

    /**
     * @var bool Admin Action Permission Checking
     */
    protected $checkAction = false;

    /**
     * @var bool Admin Menu Permission Checking
     */
    protected $checkMenu = false;

    /**
     * Base constructor.
     * @param App $app
     * @param Module $module
     * @throws DBQueryError
     * @throws InternalError
     * @throws LoaderError
     * @throws LoginRequired
     * @throws PermissionDenied
     * @throws ReflectionException
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function __construct(App &$app, Module &$module)
    {
        $this->app = &$app;
        $this->module = &$module;
        $this->mediaDomain = $app->config('Misc')->get('cdnPath', '');

        $app->on('exception', function (App $app) {
            $this->view = '';
            $app->response->import($app->request->get('*'), true);
            $error = $app->response->get('error', '');
            $referer = $app->request->header('referer');
            if (!empty($error)) {
                switch ($error) {
                    case 'login_required':
                        $app->redirect("{$this->basePath}/");
                        break;
                    default:
                        $app->response->set('errorMessage', $this->module->I18n->translation($app->response->get('error', '')));
                }
            }
            if ($this->onErrorGoBack && $referer) {
                $app->redirect($referer);
            } elseif (!$app->request->isXHR()) {
                $this->viewTwig();
            } else
                $this->viewJson();
        });

        $app->on('critical', function (App $app) {
            $app->response->import($app->request->get('*'), true);
            $error = $app->response->get('error', '');
            if (!empty($error)) {
                switch ($error) {
                    case 'login_required':
                        $app->redirect("{$this->basePath}/");
                        break;
                    default:
                        $app->response->set('errorMessage', $this->module->I18n->translation($app->response->get('error', '')));
                }
            }
            $referer = $app->request->header('referer');
            $this->view = '';
            if ($this->onErrorGoBack && $referer) {
                $app->redirect($referer);
            } elseif (!$app->request->isXHR()) {
                $this->viewTwig();
            } else
                $this->viewJson();
        });

        $app->on('appEnd', function (App $app) {
            $app->response->add('isXHR', $app->request->isXHR());
            //重写错误处理，用于处理业务层面抛出的跳转
            if (!$app->request->isXHR())
                $this->viewTwig();
            else
                $this->viewJson();
        });

        $app->session->start();

        //Setup Language
        $this->lang = $module->I18n->setLang($app->session->get('lang', 'en'));

        if ($this->admin = $module->Admin->getAdmin(true))
            $app->response->add('logged', true);
        else
            $app->response->add('logged', false);

        //Permission Checking
        $module->Admin->verifyAction($this->checkAction, "{$this->basePath}/PermissionDenied");
        $module->Admin->verifyMenu($this->checkMenu, "{$this->basePath}/PermissionDenied");
    }

    /**
     * @return array
     */
    final protected function getMenu()
    {
        $path = __DIR__;
        $actions = [];
        $lists = $this->searchPhpFiles($path);
        sort($lists, SORT_STRING);
        foreach ($lists as $file) {
            $content = file_get_contents($file);
            if (preg_match('/protected\s+\$checkMenu\s+=\s+(true);/i', $content)
                && preg_match('/class\s+[A-Z]\w+\s+/i', $content)
            ) {
                $name = str_replace(["{$path}", '\\', ".php"], ['', '/', ''], $file);
                $actions[] = $name;
            }
        }
        return $actions;
    }

    /**
     * @return array
     */
    final protected function getActions()
    {
        $path = __DIR__;
        $actions = [];
        $lists = $this->searchPhpFiles($path);
        sort($lists, SORT_STRING);
        foreach ($lists as $file) {
            $content = file_get_contents($file);
            if (preg_match('/protected\s+\$checkAction\s+=\s+(true);/i', $content)
                && preg_match('/class\s+[A-Z]\w+\s+/i', $content)
            ) {
                $name = str_replace(["{$path}", '\\', ".php"], ['', '/', ''], $file);
                $actions[] = $name;
            }
        }
        return $actions;
    }

    /**
     * @param null $name
     * @return Container
     * @throws InternalError
     */
    final public function getSearch($name = null)
    {
        if (empty($name))
            $name = strtolower(str_replace('\\', '.', get_class($this)));
        switch ($this->app->request->get('action')) {
            case 'resetSearch':
                $this->app->session->del("search.{$name}");
                break;
            case 'search':
                $this->app->session->set("search.{$name}", $this->app->request->get('*'));
                break;
        }
        $search = new Container($this->app->session->get("search.{$name}", []), false, true);
        $this->app->on('appBeforeClean', function (App &$app) use ($search) {
            $app->response->import($search->get('*'), true);
        });
        return $search;
    }

    /**
     * @throws InternalError
     */
    final public function clearSession()
    {
        $lang = $this->app->session->get('lang', 'en');
        $theme = $this->app->session->get('theme', 'dark');
        $this->app->session->clear();
        $this->app->session->set('lang', $lang);
        $this->app->session->set('theme', $theme);
    }

    /**
     * @throws InternalError
     * @throws DBQueryError
     * @throws ReflectionException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    final protected function goBack()
    {
        $referer = $this->app->request->header('referer', '');
        $this->app->redirect($referer);
    }

    /**
     * @param $planText
     */
    final protected function viewPlain($planText)
    {
        if (!empty($this->view))
            return;
        $this->view = 'plain';
        $this->app->view->setPlain($planText);
    }

    /**
     */
    final protected function viewNone()
    {
        if (!empty($this->view))
            return;
        $this->view = 'none';
        $this->app->view->setNone();
    }

    /**
     */
    final protected function viewJson()
    {
        if (!empty($this->view))
            return;
        $this->view = 'json';
        $this->app->view->setJson();
    }

    /**
     * GetClass
     * @param string $twigName
     * @param string $authDefault
     * @param array $responses
     * @return void
     * @throws DBQueryError
     * @throws InternalError
     * @throws LoginRequired
     */
    protected function viewTwig($twigName = '', $authDefault = 'Auth/Login.twig', $responses = [])
    {
        if (!empty($this->view))
            return;
        $this->view = 'twig';
        $this->outputTwig(['admin' => $this->admin] + $responses);

        if (empty($twigName)) {
            $twigName = str_replace("\\", "/", get_class($this)) . '.twig';
            $twigName = str_replace("App/", "", $twigName);
            $twigPath = explode("/", $twigName);
            array_pop($twigPath);
            $twigPath = implode("/", $twigPath);
        }
        if (strpos($twigName, 'Auth') === false && strpos($twigName, 'Error') === false && empty($this->admin))
            $twigName = $authDefault;

        //Setup Environment data
        $this->app->view->setTwig($twigName, !empty($twigPath) ? "@Template/{$twigPath}" : null);
    }

    /**
     * @param array $responses
     * @throws DBQueryError
     * @throws InternalError
     * @throws LoginRequired
     */
    final public function outputTwig($responses = [])
    {
        if (!empty($this->admin))
            $this->app->response->add('panelMenus', $this->module->Admin->Menu->getMenus());
        $this->app->response->set('basePath', $this->basePath);
        $this->app->response->set('lang', $this->lang);
        $this->app->response->set('allLang', $this->langMap);
        $this->app->response->set('langName', $this->langMap[$this->lang]);
        $this->app->response->set('assetsSource', "{$this->mediaDomain}/assets");
        $this->app->response->set('imageSource', "{$this->mediaDomain}/images");
        $this->app->response->set('mediaSource', "{$this->mediaDomain}");
        foreach ($responses as $name => $response)
            $this->app->response->set($name, $response);
    }

    /**
     * @param $dir
     * @param array $results
     * @return array
     */
    protected function searchPhpFiles($dir, &$results = array())
    {
        $files = scandir($dir);
        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path) && substr(strtolower($path), -3) === 'php')
                $results[] = $path;
            else if ($value != "." && $value != "..") {
                $this->searchPhpFiles($path, $results);
            }
        }
        clearstatcache();
        return $results;
    }
}