<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/29
 * Time: 10:05
 */

namespace Aw\Framework\Event;
class Event extends \Aw\Event
{
    const EVENT_REQUEST_CREATED = 100;
    const EVENT_ROUTE_MATCHED = 200;
    const EVENT_DISPATCHER_CREATED = 300;
    const EVENT_BEFORE_THROUGH_PRE_MIDDLEWARE = 400;
    const EVENT_AFTER_THROUGH_PRE_MIDDLEWARE = 500;
    const EVENT_BEFORE_INVOKE_ACTION = 600;

    const EVENT_AFTER_INVOKE_ACTION = 700;
    const EVENT_BEFORE_THROUGH_POST_MIDDLEWARE = 800;
    const EVENT_AFTER_THROUGH_POST_MIDDLEWARE = 900;


    const EVENT_BEFORE_SEND_RESPONSE = 1000;
    const EVENT_RESPONSE_404 = 1021;
    const EVENT_RESPONSE_500 = 1022;
    const EVENT_AFTER_SEND_RESPONSE = 1023;
}