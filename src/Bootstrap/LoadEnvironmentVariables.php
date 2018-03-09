<?php

namespace Aw\Framework\Bootstrap;


use Aw\Dotnet;
use Aw\Framework\Application;

class LoadEnvironmentVariables
{
    /**
     * Bootstrap the given application.
     *
     * @param Application $app
     * @return void
     * @internal param Kernel $kernel
     * @internal param Application $app
     */
    public function bootstrap(Application $app)
    {
        $dotnet = new Dotnet($app->basePath() . DIRECTORY_SEPARATOR . ".env");
        $app->instance('env', $dotnet->load());
    }
}
