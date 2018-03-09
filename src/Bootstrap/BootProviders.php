<?php

namespace Aw\Framework\Bootstrap;

use Aw\Framework\Application;


class BootProviders
{
    /**
     * Bootstrap the given application.
     *
     * @param Application $app
     * @return void
     * @internal param Kernel $kernel
     * @internal param Application $app
     * @internal param Router $router
     */
    public function bootstrap(Application $app)
    {
        $app->boot();
    }

}
