<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/29
 * Time: 10:18
 */

namespace Aw\Framework\Event;


use Aw\Http\Request;
use Aw\Routing\Dispatch\IDispatcher;
use Aw\Routing\Route;
use Aw\Routing\Router\Router;

class DispatcherCreated extends Event
{
    protected $dispatcher;
    protected $request;
    protected $route;
    protected $router;

    public function __construct(IDispatcher $dispatcher, Route $route, Request $request, Router $router)
    {
        $this->request = $request;
        $this->route = $route;
        $this->router = $router;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return IDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }
}