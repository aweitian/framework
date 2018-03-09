<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/9
 * Time: 17:20
 */

use Aw\Routing\Router\Router;

/**
 * @var Router $router
 */
$router = $this->app->make('router');

$router->get('/',function (){
    return 'bal';
});

$router->pmcai();

