<?php

namespace App\Index;

use Module\Module;
use Npf\Core\App;

/**
 * Class Robot
 * @package App\Index
 */
class Robots
{

    /**
     * Robot constructor.
     * @param App $app
     * @param Module $module
     */
    final public function __construct(App &$app, Module $module)
    {
        $app->view->setViewExpiry(3600);
    }

    /**
     * @param App $app
     * @param Module $module
     */
    final public function __invoke(App &$app, Module $module)
    {
        $app->view->setPlain("User-agent: *\nDisallow: /");
    }
}