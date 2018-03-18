<?php

namespace Aw\Framework\Bootstrap;

use Aw\Config;
use Aw\Framework\Application;

class RegisterAlias
{
    /**
     * Merge alias to application.
     *
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $config = $app->make('config');
        $app->setAlias($config->get('alias', array()));
    }
}
