<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/9
 * Time: 13:35
 */

namespace App\Provider {

    use Aw\Framework\Providers\ServiceProvider;

    class test extends ServiceProvider
    {
        private $a;

        public function register()
        {
            $this->a = 111;
            $this->app->instance('test', $this);
        }

        public function v()
        {
            return $this->a;
        }

        public function boot()
        {
            $this->app->instance('test-a', $this->a);
        }
    }
}

