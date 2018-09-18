<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/9
 * Time: 13:11
 */

use Aw\Arr;

require_once __DIR__ . "/../../vendor/autoload.php";

$app = new \Aw\Framework\ConsoleApplication(__DIR__);

$app->bind("connection", function () use ($app){
    $env = $app->make("env");
    $config = Arr::get($env, 'host', 'user', 'password', 'port', 'charset', 'database');
    return new \Aw\Db\Connection\Mysql($config);
}, true);

$kernel = new \Aw\Framework\ConsoleKernel($app, __DIR__ . '/app');
$kernel->bootstrap();
//默认是vendor/aweitian/framework/src/Console
$kernel->setNsPathMap(\Aw\Framework\ConsoleKernel::FRAMEWORK_NS, __DIR__ . "/../../src/Console");
$kernel->handle($argv);



