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
    const APP_NS = "\\App\\Console\\";
    const FRAMEWORK_NS = "\\Aw\\Framework\\Console\\";
    /**
     * The ConsoleApplication implementation.
     *
     * @var ConsoleApplication
     */
    protected $app;

    /**
     * @var string
     */
    protected $path;

    protected $ns_path_map = array();

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
     * @param  ConsoleApplication $app
     * @param $path
     * @param string|array $namespace
     */
    public function __construct(ConsoleApplication $app, $path, $namespace = null)
    {
        $this->app = $app;
        $this->path = $path;
        $this->setNsPathMap($namespace ? $namespace : self::APP_NS, $path);
        $this->setNsPathMap(self::FRAMEWORK_NS, $app->basePath("vendor/aweitian/framework/src/Console"));
    }

    public function setNsPathMap($ns, $path)
    {
        $this->ns_path_map[$ns] = $path;
    }

    public function removeNsPathMap($ns)
    {
        unset($this->ns_path_map[$ns]);
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

        $found = false;
        $ctl_class = $ctl;
        $namespace = array_keys($this->ns_path_map);
        foreach ($namespace as $ns) {
            $ctl_class = $ns . $ctl;
            if ($this->load($ctl, $ns)) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $this->err("$ctl is no exists in path " . implode($namespace, "|"));
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

    protected function load($ctl, $ns)
    {
        $ctl_class = $ns . $ctl;
        if (class_exists($ctl_class))
            return true;
        if (!array_key_exists($ns, $this->ns_path_map))
            return false;
        if (file_exists($path = $this->ns_path_map[$ns] . DIRECTORY_SEPARATOR . $ctl . ".php")) {
            require_once $path;
            if (class_exists($ctl_class)) {
                return true;
            }
        }

        return false;
    }

    public function showAllCmd($ctl_filter = "")
    {
        $folders = $this->ns_path_map;
        foreach ($folders as $ns => $folder) {
            foreach (Filter::filterEndswith($folder, '.php', Condition::create()->setReturnFilename()) as $ctl) {
                $ctl_class = $ns . $ctl;

                if (!$this->load($ctl, $ns)) {
                    continue;
                }

                if ($ctl_filter) {
                    if ($ctl_filter !== $ctl) {
                        continue;
                    }
                }
                $this->output($ctl);
                $rc = new ReflectionClass($ctl_class);
                foreach ($rc->getMethods() as $method) {
                    if ($method->name == "__construct")
                        continue;
                    if ($method->isPublic())
                        $this->output("    " . $ctl . ":" . $method->name);
                }
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
