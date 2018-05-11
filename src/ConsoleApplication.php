<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/4
 * Time: 17:44
 */

namespace Aw\Framework;

use Aw\Container;
use Aw\Framework\Providers\ServiceProvider;

class ConsoleApplication extends Application
{
    /**
     * Boot the application's service providers.
     *
     * @return void
     */
    public function boot()
    {
        $providers = $this->make('config')->get('app.console_providers', array());
        foreach ($providers as $provider) {
            $provider = $this->make($provider);
            if ($provider && method_exists($provider, 'boot')) {
                $provider->boot();
            }
        }
    }
}