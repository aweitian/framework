<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/9
 * Time: 13:11
 */
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . '/app/gf.php';

$app = new \Aw\Framework\ConsoleApplication(dirname(__DIR__));


$kernel = new \Aw\Framework\ConsoleKernel($app, __DIR__.'/app');
$kernel->bootstrap();

$kernel->handle($argv);



