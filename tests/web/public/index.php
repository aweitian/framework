<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/9
 * Time: 13:11
 */
require_once __DIR__ . "/../../../vendor/autoload.php";
require_once __DIR__ . '/../app/ctl.php';

$app = new \Aw\Framework\Application(dirname(__DIR__));
$request = new \Aw\Http\Request('/');

$kernel = new \Aw\Framework\Kernel($app, new \Aw\Routing\Router\Router());
$kernel->bootstrap();

$response = $kernel->handle($request);

$response->send();

//$kernel->terminal();

print "\n==================================================\n";

$request = new \Aw\Http\Request('/foo/bar');

$response = $kernel->handle($request);

$response->send();

/**
 * @var \Aw\Routing\Router\Router $router
 */
$router = $app->make("router");

print "\n==================================================\n";

print $app->make('test')->v();


print "\n==================================================\n";

print $app->make('test-a');

print "\n==================================================\n";

print $app->make('ali')->ax();

