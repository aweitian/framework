<?php
/**
 * provider 有两个阶段
 *      register
 *      boot
 *
 * many of these are "deferred" providers,
 * meaning they will not be loaded on every request,
 * but only when the services they provide are actually needed
 */
namespace Aw\Framework\Providers;


use Aw\Framework\Application;

abstract class ServiceProvider
{
    /**
     * The application instance.
     *
     * @var Application
     */
    protected $app;

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Create a new service provider instance.
     *
     * @param  Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Determine if the provider is deferred.
     *
     * @return bool
     */
    public function isDeferred()
    {
        return $this->defer;
    }
}
