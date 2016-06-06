<?php

namespace Sqlia\Drivers;

use Sqlia\Environments\MacOS;

use function Sqlia\info;
use function Sqlia\output;
use function Sqlia\comment;
use function Sqlia\warning;

class MySQL extends AbstractDriver
{
    public function name() : string
    {
        return "MySQL";
    }

    public function supported()
    {
        return stripos($this->cli->run("which mysql"), "not found") === false
            || stripos($this->cli->asRoot()->run("which mysql"), "not found") === false;
    }

    public function install(/* array | ArrayAccess */ $options = [])
    {
        info("Creating temporary disk...");
        $this->disk->mount([
            "size" => "512M",
        ]);

        info("Installing MySQL...");

        $path = $this->path();
        output($this->cli->run("mkdir -p {$path}/data"));

        $owner = "mysql:root";

        if ($this->env instanceof MacOS) {
            $owner = "mysql:wheel";
        }

        output($this->cli->asRoot()->run("chown -R {$owner} {$path}"));
        $this->cli->as("mysql")->run("mysql_install_db --user=mysql --datadir={$path}/data");
    }

    public function uninstall()
    {
        info("Removing temporary disk");
        $this->disk->unmount();
    }

    public function start(/* array | ArrayAccess */ $options = [])
    {
        $path = $this->path();

        info("Starting MySQL...");
        $port = $options["port"] ?? 3344;
        $this->cli->as("mysql")->inBackground()->run(
            "mysqld" .
            " --verbose" .
            " --datadir={$path}/data" .
            " --pid-file={$path}/mysql.pid" .
            " --socket={$path}/mysql.sock" .
            " --port={$port}" .
            " --bind-address=0.0.0.0" .
            " --innodb_flush_log_at_trx_commit=2" .
            " --log-error={$path}/error.log" .
            " --performance-schema=FALSE"
        );

        // FIXME: Poll for MySQL availability
        info("Waiting for MySQL to become available...");
        sleep(2);

        $username = $options["username"] ?? "sqlia";
        $password = $options["password"] ?? "sqlia";

        if ($username == "root") {
            warning("You cannot change the root user's password");

            return;
        }

        info("Setting up root user");
        $this->cli->run("mysqladmin -u root --host=0.0.0.0 --port={$port} password root");
        $this->query("root", $port, "root", "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' IDENTIFIED BY 'root';");

        info("Adding user “{$username}” with password “{$password}”...");
        $this->query("root", $port, "root", "GRANT ALL PRIVILEGES ON *.* TO '{$username}'@'%' IDENTIFIED BY '{$password}';");
        $this->query("root", $port, "root", "FLUSH PRIVILEGES;");
    }

    public function refresh(/* array | ArrayAccess */ $options = [])
    {
        $port = $options["port"] ?? 3344;
        $databases = $options["databases"] ?? [];

        if (empty($databases)) {
            comment("No databases to create...");

            return;
        }

        info("Recreating databases...");

        foreach ($databases as $database) {
            comment("{$database}");
            $this->query("root", $port, "root", "SET FOREIGN_KEY_CHECKS = 0; drop database if exists {$database};");
            $this->query("root", $port, "root", "create database {$database};");
        }
        output("");
    }

    public function running()
    {
        return ! empty($this->pid());
    }

    public function stop()
    {
        $pid = $this->pid();

        info("Terminating In-Memory MySQL...");
        info("PID {$pid}");

        output($this->cli->asRoot()->run("kill -s term {$pid}"));
    }

    private function pid()
    {
        return trim($this->cli->asRoot()->run("cat {$this->path()}/mysql.pid 2>/dev/null"));
    }

    private function query($user, $port, $password, $sql)
    {
        return $this->cli->run(
            "mysql -u {$user} --host=0.0.0.0 --port={$port} --password={$password} -e " .
            escapeshellarg($sql)
        );
    }
}
