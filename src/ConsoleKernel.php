<?php
/**
 * 静态绑定了router变量
 */

namespace Aw\Framework;


use Aw\Filesystem\Condition;
use Aw\Filesystem\Filter;
use ReflectionClass;

class ConsoleKernel
{
    /**
     * The application implementation.
     *
     * @var Application
     */
    protected $app;

    /**
     * @var string
     */
    protected $path;

    protected $namespace = "\\App\\Console\\";
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
     * Create a new HTTP kernel instance.
     *
     * @param  Application $app
     * @param $path
     * @param null $namespace
     */
    public function __construct(Application $app, $path, $namespace = null)
    {
        $this->app = $app;
        $this->path = $path;
        if ($namespace) {
            $this->namespace = $namespace;
        }
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
     * @param $cmd
     * @return void
     */
    public function handle($cmd)
    {
        //return $this->router->setRequest($request)->run();
        if (count($cmd) == 1) {
            $this->showAllCmd();
            return;
        }
        $raw_arg = $cmd;
        $cmd = explode(":", $cmd[1], 2);
        if (count($cmd) == 2) {
            $ctl = $cmd[0];
            $act = $cmd[1];
        } else {
            $ctl = $cmd[0];
            $act = "help";
        }
        $ctl_class = $this->namespace . $ctl;
        if (!$this->load($ctl)) {
            $this->err("$ctl_class is no exists");
            return;
        }
        $rc = new ReflectionClass($ctl_class);
        $method = $act;
        if (!$rc->hasMethod($method)) {
            $this->err("Method $method not found");
            return;
        }
        $method_ins = $rc->getMethod($method);
        $method_ins->invokeArgs($rc->newInstance($this), array_slice($raw_arg, 2));
    }

    protected function load($ctl)
    {
        $ctl_class = $this->namespace . $ctl;
        if (class_exists($ctl_class))
            return true;
        if (file_exists($path = $this->path . DIRECTORY_SEPARATOR . $ctl . ".php")) {
            require_once $path;
            if (class_exists($ctl_class)) {
                return true;
            }
        }

        return false;
    }

    public function showAllCmd()
    {
        foreach (Filter::filterEndswith($this->path, '.php', Condition::create()->setReturnFilename()) as $ctl) {
            $ctl_class = $this->namespace . $ctl;
            if (!$this->load($ctl)) {
                //$this->err("$ctl_class is no exists");
                continue;
            }
            $this->output($ctl);
            $rc = new ReflectionClass($ctl_class);
            foreach ($rc->getMethods() as $method) {
                if ($method->name == "__construct")
                    continue;
                $this->output("    " . $ctl . ":" . $method->name);
            }

        }
    }

    public function output($msg, $line = "\n")
    {
        file_put_contents("php://stdout", $msg . $line);
    }

    public function err($msg, $line = "\n")
    {
        file_put_contents("php://stderr", $msg . $line);
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

}
