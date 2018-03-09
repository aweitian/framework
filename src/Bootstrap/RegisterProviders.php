<?php

namespace Aw\Framework\Bootstrap;

use Aw\Framework\Application;

class RegisterProviders
{
    /**
     * Bootstrap the given application.
     *
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        $app->registerConfiguredProviders();
    }

}
