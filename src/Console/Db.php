<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/18
 * Time: 9:19
 */

namespace Aw\Framework\Console;


use Aw\Arr;
use Aw\Db\Connection\Mysql;
use Aw\Filesystem\Filesystem;
use Aw\Filesystem\Filter;
use Aw\Framework\ConsoleApplication;
use Aw\Framework\ConsoleKernel;

class Db
{
    protected $kernel;
    /**
     * @var ConsoleApplication
     */
    protected $app;

    public function __construct(ConsoleKernel $kernel)
    {
        $this->kernel = $kernel;
        $this->app = $kernel->getApplication();
    }

    public function help()
    {
        $db_dir = __DIR__ . "/../../database";
        $ver_path = $db_dir . "/.ver";
        if (!file_exists($ver_path)) {
            $version = 0;
        } else {
            $version = file_get_contents($ver_path);
        }
        $this->kernel->output("");

        $this->kernel->output("Current version:$version");
        $this->kernel->output("=================================");
        $this->kernel->showAllCmd("Db");
    }

    public function listBackup()
    {
        $that = $this;
        $bak_dir = __DIR__ . "/../../database/bak";
        Filter::filter($bak_dir, function ($path) use ($that) {
            if (preg_match("#^\d{14}\.sql$#", pathinfo($path, PATHINFO_BASENAME))) {
                $that->kernel->output(pathinfo($path, PATHINFO_FILENAME));
                return true;
            }
            return 0;
        });
        return;
        //$this->kernel->output(var_export($ret, true));
    }

    public function reset($name = false, $force = false)
    {
        if (!$name) {
            $this->kernel->output("Db:reset 20180608141839");
            return;
        }
        if (PHP_OS !== "WINNT") {
            if (!$force) {
                $this->kernel->output("You may run this cmd in online environment,please type `Db:reset back_name true` to confirm");
                return;
            }
        }

        $bak_dir = __DIR__ . "/../../database/bak";
        /**
         * @var Mysql $connection
         */
        $connection = $this->app->make("connection");
        if (file_exists($bak_dir . "/$name.sql")) {
            $sql = file_get_contents($bak_dir . "/$name.sql");
            if (trim($sql)) {
                $connection->exec($sql);
                $this->kernel->output("$bak_dir/$name.sql is executed.");
            }
        } else {
            $this->kernel->output("$bak_dir/$name.sql is not exists");
            return;
        }
    }

    public function backup()
    {
        $backup = date("YmdHis");
        $env = $this->app->make("env");
        $db_name = $env["database"];
        $db_user = $env["user"];
        $db_pass = $env["password"];
        $bak_dir = __DIR__ . "/../../database/bak";
        if (!is_dir($bak_dir)) {
            Filesystem::createDir($bak_dir);
        }
        $bak_dir = realpath($bak_dir);
        if (!$bak_dir) {
            $this->kernel->output($bak_dir . " create failed..");
            return false;
        }
        chdir($bak_dir);
        $cmd = "mysqldump -u{$db_user} -p{$db_pass} {$db_name} > {$backup}.sql";
        passthru($cmd);
        $this->kernel->output($bak_dir . DIRECTORY_SEPARATOR . $backup . ".sql is generated.");
    }

    public function makeMigrate()
    {
        $db_dir = __DIR__ . "/../../database/versions";
        if (!is_dir($db_dir)) {
            mkdir($db_dir);
        }
        $sql_file = $db_dir . "/" . date("YmdHis") . ".sql";
        file_put_contents($sql_file, "");
        $this->kernel->output($sql_file . " is made.");
    }

    public function createDb()
    {
        $env = $this->app->make("env");
        $db_name = $env["database"];
        $sql = "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET UTF8 COLLATE utf8_general_ci;";
        $config = Arr::get($env, 'host', 'user', 'password', 'port', 'charset');
//        var_dump($config);exit;
        $connection = new Mysql($config);
        $connection->exec($sql);
        $this->kernel->output("done");
    }

    public function migrate($force = false)
    {
        if (PHP_OS !== "WINNT") {
            if (!$force) {
                $this->kernel->output("You may run this cmd in online environment,please type `Db:migrate true` to confirm");
                return;
            }
        }
        /**
         * @var Mysql $connection
         */
        $connection = $this->app->make("connection");
        $db_dir = __DIR__ . "/../../database";
        $ver_path = $db_dir . "/.ver";
        if (!file_exists($ver_path)) {
            file_put_contents($ver_path, 0);
            if (file_exists($db_dir . "/init.sql")) {
                $connection->exec(file_get_contents($db_dir . "/init.sql"));
                $this->kernel->output($db_dir . "/init.sql" . "is being migrate");
            }
        }
        $ver = file_get_contents($ver_path);
        $dir = Filter::filterEndswith($db_dir . "/versions", '.sql', Condition::create()
            ->setReturnFilename());
//        $this->kernel->output(var_export($dir, true));
        foreach ($dir as $file) {
            $f = $file;
            if ($f <= $ver)
                continue;
            $path = realpath($db_dir . "/versions/" . $file . ".sql");
            $sql = file_get_contents($path);
            if (trim($sql)) {
                $connection->exec($sql);
                $this->kernel->output($path . " is being migrate.");
            } else {
                $this->kernel->output($path . " is skipped.");
            }
            $ver = $file;
        }
        file_put_contents($ver_path, $ver);
        $this->kernel->output("done,new version is " . $ver);
    }
}