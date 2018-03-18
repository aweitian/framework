<?php
/**
 * 静态绑定了router变量
 */
namespace Aw\Framework;


use Aw\Framework\Bootstrap\BootProviders;
use Aw\Framework\Bootstrap\HandleExceptions;
use Aw\Framework\Bootstrap\LoadConfiguration;
use Aw\Framework\Bootstrap\LoadEnvironmentVariables;
use Aw\Framework\Bootstrap\RegisterAlias;
use Aw\Framework\Bootstrap\RegisterProviders;
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
        LoadEnvironmentVariables::class,
        LoadConfiguration::class,
        HandleExceptions::class,
        RegisterAlias::class,
        RegisterProviders::class,
        BootProviders::class,
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
     * Create a new HTTP kernel instance.
     *
     * @param  Application $app
     * @param  Router $router
     */
    public function __construct(Application $app, Router $router)
    {
        $this->app = $app;
        $this->router = $router;
        $this->app->instance('router', $router);
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
        return $this->router->setRequest($request)->run();
    }

    /**
     * At the end of request
     */
    public function terminal()
    {

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
