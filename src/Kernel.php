<?php
/**
 * 静态绑定了router变量
 */

namespace Aw\Framework;

use Aw\EventDispatcher;
use Aw\Framework\Event\AfterInvokeAction;
use Aw\Framework\Event\AfterPostMw;
use Aw\Framework\Event\AfterPreMw;
use Aw\Framework\Event\AfterSendResponse;
use Aw\Framework\Event\BeforeInvokeAction;
use Aw\Framework\Event\BeforePostMw;
use Aw\Framework\Event\BeforePreMw;
use Aw\Framework\Event\BeforeSendResponse;
use Aw\Framework\Event\DispatcherCreated;
use Aw\Framework\Event\Event;
use Aw\Framework\Event\RequestCreated;
use Aw\Framework\Event\Response404;
use Aw\Framework\Event\Response500;
use Aw\Framework\Event\RouterMatched;
use Aw\Http\Request;
use Aw\Http\Response;
use Aw\Routing\Router\Router;

class Kernel
{

    /**
     * The application implementation.
     *
     * @var Application
     */
    protected $app;

    /**
     * The router instance.
     *
     * @var \Aw\Routing\Router\Router
     */
    protected $router;

    /**
     * The bootstrap classes for the application.
     *
     * @var array
     */
    protected $bootstraps = array(
        'Aw\Framework\Bootstrap\LoadEnvironmentVariables',
        'Aw\Framework\Bootstrap\LoadConfiguration',
        'Aw\Framework\Bootstrap\RegisterAlias',
        'Aw\Framework\Bootstrap\RegisterProviders',
        'Aw\Framework\Bootstrap\BootProviders',
    );

    /**
     * @var array
     */
    protected $defined_Middleware = array();

    /**
     * @var array
     */
    protected $global_Middleware = array();

    /**
     * @var EventDispatcher
     */
    protected $emitter;

    /**
     * @var Response
     */
    protected $response;

    /**
     * Create a new HTTP kernel instance.
     *
     * @param  Application $app
     * @param  Router $router
     * @param EventDispatcher $dispatcher
     */
    public function __construct(Application $app, Router $router, EventDispatcher $dispatcher)
    {
        $this->app = $app;
        $this->router = $router;
        $this->app->instance('router', $router);
        $this->emitter = $dispatcher;
        $this->app->instance('emitter', $dispatcher);
    }

    /**
     * bootstrap the kernel
     * @return void
     */
    public function bootstrap()
    {
        foreach ($this->bootstraps as $bootstrap) {
            $boot = new $bootstrap();
            if (method_exists($boot, 'bootstrap')) {
                $boot->bootstrap($this->app);
            }
        }
    }

    /**
     * Handle an incoming HTTP request.
     *
     * @param  Request $request
     * @return Response
     */
    public function handle($request)
    {
        $this->emitter->dispatch(Event::EVENT_REQUEST_CREATED, new RequestCreated($request));
        $this->installRouterHooks();
        $response = $this->router->setRequest($request)->run();
        $this->emitter->dispatch(Event::EVENT_BEFORE_SEND_RESPONSE, new BeforeSendResponse($response));
        $this->response = $response;
        return $response;
    }

    protected function installRouterHooks()
    {
        $emitter = $this->emitter;
        $this->router->setCallbackResponse404(function ($r, $route, $request, $router) use ($emitter) {
            $emitter->dispatch(Event::EVENT_RESPONSE_404, new Response404($r, $route, $request, $router));
        });
        $this->router->setCallbackResponse500(function ($r, $e, $route, $request, $router) use ($emitter) {
            $emitter->dispatch(Event::EVENT_RESPONSE_500, new Response500($r, $e, $route, $request, $router));
        });
        $this->router->setCallbackRouterMatched(function ($route, $request, $router) use ($emitter) {
            $emitter->dispatch(Event::EVENT_ROUTE_MATCHED, new RouterMatched($route, $request, $router));
        });
        $this->router->setCallbackDispatcherCreated(function ($dispatch, $route, $request, $router) use ($emitter) {
            $emitter->dispatch(Event::EVENT_DISPATCHER_CREATED, new DispatcherCreated($dispatch, $route, $request, $router));
        });
        $this->router->setCallbackBeforeThroughPreMiddleware(function ($request, $route, $router) use ($emitter) {
            $emitter->dispatch(Event::EVENT_BEFORE_THROUGH_PRE_MIDDLEWARE, new BeforePreMw($request, $route, $router));
        });
        $this->router->setCallbackAfterThroughPreMiddleware(function ($request, $route, $router) use ($emitter) {
            $emitter->dispatch(Event::EVENT_AFTER_THROUGH_PRE_MIDDLEWARE, new AfterPreMw($request, $route, $router));
        });
        $this->router->setCallbackBeforeInvokeAction(function ($request, $route, $router) use ($emitter) {
            $emitter->dispatch(Event::EVENT_BEFORE_INVOKE_ACTION, new BeforeInvokeAction($request, $route, $router));
        });

        // after invoke action

        $this->router->setCallbackAfterInvokeAction(function (&$response, $request, $route, $router) use ($emitter) {
            $emitter->dispatch(Event::EVENT_AFTER_INVOKE_ACTION, new AfterInvokeAction($response, $request, $route, $router));
        });
        $this->router->setCallbackBeforeThroughPostMiddleware(function (&$response, $request, $route, $router) use ($emitter) {
            $emitter->dispatch(Event::EVENT_BEFORE_THROUGH_POST_MIDDLEWARE, new BeforePostMw($response, $request, $route, $router));
        });
        $this->router->setCallbackAfterThroughPostMiddleware(function (&$response, $request, $route, $router) use ($emitter) {
            $emitter->dispatch(Event::EVENT_AFTER_THROUGH_POST_MIDDLEWARE, new AfterPostMw($response, $request, $route, $router));
        });
    }

    /**
     * At the end of request
     */
    public function terminal()
    {
        $this->emitter->dispatch(Event::EVENT_AFTER_SEND_RESPONSE, new AfterSendResponse($this->response));
    }


    /**
     * Get the Laravel application instance.
     *
     * @return Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }
}
