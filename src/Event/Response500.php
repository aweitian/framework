<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/29
 * Time: 10:05
 */

namespace Aw\Framework\Event;

use Aw\Http\Request;
use Aw\Http\Response;
use Aw\Routing\Route;
use Aw\Routing\Router\Router;
use Exception;

class Response500 extends Event
{
    protected $response;
    /**
     * @var Exception
     */
    protected $exception;
    protected $router;
    protected $route;
    protected $request;

    public function __construct(Response $response, Exception $exception, Route $route, Request $request, Router $router)
    {
        $this->response = $response;
        $this->exception = $exception;
        $this->route = $route;
        $this->router = $router;
        $this->request = $request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return Route
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
}