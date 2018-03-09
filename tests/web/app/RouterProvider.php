<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/9
 * Time: 13:35
 */

namespace App\Provider {

    use Aw\Framework\Providers\ServiceProvider;

    class RouterProvider extends ServiceProvider
    {
        public function register()
        {

        }

        public function boot()
        {
            include $this->app->basePath('routers.php');
        }
    }
}

