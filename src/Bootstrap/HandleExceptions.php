<?php

namespace Aw\Framework\Bootstrap;

use Aw\Framework\Application;
use Whoops\Handler\PlainTextHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class HandleExceptions
{
    /**
     * Bootstrap the given application.
     *
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app)
    {
        if ($app->make('config')->get('app.debug', false)) {
            $whoops = new Run;
            $whoops->pushHandler(new PrettyPageHandler);
            $whoops->register();
        } else {
            $whoops = new Run;
            $whoops->pushHandler(new PlainTextHandler);
            $whoops->register();
        }
    }

}
