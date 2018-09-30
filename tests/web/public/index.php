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
$emitter = new \Aw\EventDispatcher();


///////////////  TEST EVENT ///////////////////////
$emitter->addListener(\Aw\Framework\Event\Event::EVENT_REQUEST_CREATED, function (\Aw\Framework\Event\RequestCreated $event) {
    print "EVENT_REQUEST_CREATED:" . $event->getRequest()->getPath() . " -- (0) -- \n";
});
$emitter->addListener(\Aw\Framework\Event\Event::EVENT_REQUEST_CREATED, function (\Aw\Framework\Event\RequestCreated $event) {
    print "EVENT_REQUEST_CREATED:" . $event->getRequest()->getPath() . " -- (1) -- \n";
});
$emitter->addListener(\Aw\Framework\Event\Event::EVENT_ROUTE_MATCHED, function (\Aw\Framework\Event\RouterMatched $event) {
    print "EVENT_ROUTE_MATCHED:" . $event->getRoute()->getName() . "\n";
    $route = $event->getRoute();
    if ($route instanceof \Aw\Routing\Route) {
        $matcher = $route->getMatch();
        if ($matcher instanceof \Aw\Routing\Matcher\Mapca) {
            $parse = $matcher->matcher;
            if ($parse instanceof \Aw\Routing\Parse\Pmcai) {
                $ctl = $parse->getControl();
                var_dump($ctl);
            }
        }
    }
});
$emitter->addListener(\Aw\Framework\Event\Event::EVENT_BEFORE_THROUGH_PRE_MIDDLEWARE, function (\Aw\Framework\Event\BeforePreMw $event) {
    print "EVENT_BEFORE_THROUGH_PRE_MIDDLEWARE:" . $event->getRoute()->getName() . "\n";
});
$emitter->addListener(\Aw\Framework\Event\Event::EVENT_AFTER_THROUGH_PRE_MIDDLEWARE, function (\Aw\Framework\Event\AfterPreMw $event) {
    print "EVENT_AFTER_THROUGH_PRE_MIDDLEWARE:" . $event->getRoute()->getName() . "\n";
});
$emitter->addListener(\Aw\Framework\Event\Event::EVENT_BEFORE_INVOKE_ACTION, function (\Aw\Framework\Event\BeforeInvokeAction $event) {
    print "EVENT_BEFORE_INVOKE_ACTION:" . $event->getRoute()->getName() . "\n";
});
$emitter->addListener(\Aw\Framework\Event\Event::EVENT_AFTER_INVOKE_ACTION, function (\Aw\Framework\Event\AfterInvokeAction $event) {
    print "EVENT_AFTER_INVOKE_ACTION" . " -- (0) --\n";
    $response = $event->getResponse();
    if ($response instanceof \Aw\Http\Response) {
        $response = $response->getContent();
    }
    $event->setResponse("hooked(" . $response . ")");
});
$emitter->addListener(\Aw\Framework\Event\Event::EVENT_AFTER_INVOKE_ACTION, function (\Aw\Framework\Event\AfterInvokeAction $event) {
    print "EVENT_AFTER_INVOKE_ACTION" . " -- (1) --\n";
    $response = $event->getResponse();
    if ($response instanceof \Aw\Http\Response) {
        $response = $response->getContent();
    }
    $event->setResponse($response . "again");
});
$emitter->addListener(\Aw\Framework\Event\Event::EVENT_BEFORE_SEND_RESPONSE, function (\Aw\Framework\Event\BeforeSendResponse $event) {
    print "EVENT_BEFORE_SEND_RESPONSE" . "\n";;
    $event->setResponse($event->getResponse()->getContent() . " and again by send  response\n");
});
$emitter->addListener(\Aw\Framework\Event\Event::EVENT_AFTER_SEND_RESPONSE, function (\Aw\Framework\Event\AfterSendResponse $event) {
    print "EVENT_AFTER_SEND_RESPONSE" . "\n";;
});
///////////////  TEST EVENT ///////////////////////

$kernel = new \Aw\Framework\Kernel($app, new \Aw\Routing\Router\Router(), $emitter);
$kernel->bootstrap();

$response = $kernel->handle($request);

$response->send();

$kernel->terminal();

print "\n==================================================\n";

$request = new \Aw\Http\Request('/foo/bar');

$response = $kernel->handle($request);

$response->send();
$kernel->terminal();


print "\n==================================================\n";

$request = new \Aw\Http\Request('/not/found');
$emitter->addListener(\Aw\Framework\Event\Event::EVENT_RESPONSE_404, function (\Aw\Framework\Event\Response404 $event) {
    print "EVENT_RESPONSE_404" . "\n";
    $event->getResponse()->setContent("catch the 404");
});
$emitter->addListener(\Aw\Framework\Event\Event::EVENT_RESPONSE_500, function (\Aw\Framework\Event\Response500 $event) {
    print "EVENT_RESPONSE_500" . "\n";
    $event->getResponse()->setContent("catch the 500");
});
$response = $kernel->handle($request);

$response->send();
$kernel->terminal();

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

