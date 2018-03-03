<?php

namespace Aw\Framework\Bootstrap;

use Aw\Config;
use Aw\Framework\Application;

class LoadConfiguration
{
    /**
     * Bootstrap the given application.
     *
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $config = new Config();
        $config->loadFiles($app->configPath());
        $app->instance('config', $config);

        date_default_timezone_set($config->get('app.timezone', 'UTC'));

//        mb_internal_encoding('UTF-8');
    }

}
