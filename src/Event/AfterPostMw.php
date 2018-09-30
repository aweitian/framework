<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/29
 * Time: 10:18
 */

namespace Aw\Framework\Event;


use Aw\Http\Request;
use Aw\Routing\Route;
use Aw\Routing\Router\Router;

class AfterPostMw extends Event
{
    protected $request;
    protected $route;
    protected $router;
    protected $response;

    public function __construct(&$response, Request $request, Route $route, Router $router)
    {
        $this->response = &$response;
        $this->request = $request;
        $this->route = $route;
        $this->router = $router;
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
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }
}