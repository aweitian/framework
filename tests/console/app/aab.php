<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/9
 * Time: 17:41
 */
namespace App\Console;

use Aw\Framework\ConsoleKernel;

class aab
{
    protected $kernel;
    public function __construct(ConsoleKernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function qqq()
    {
        $ret = "output:aab@qqq";
        $this->kernel->output($ret);
    }

    public function tae()
    {
        return "foo-bar-kk";
    }
}