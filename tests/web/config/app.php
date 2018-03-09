<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/9
 * Time: 13:28
 */
require_once dirname(__DIR__) . "/app/TestProvider.php";
require_once dirname(__DIR__) . "/app/RouterProvider.php";

return array(
    "providers" => array(
        App\Provider\test::class,
        \App\Provider\RouterProvider::class
    )
);