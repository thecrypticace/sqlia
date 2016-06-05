<?php

namespace Sqlia\Drivers;

use Sqlia\Environments\MacOS;

class Postgres extends AbstractDriver
{
    public function name() : string
    {
        return "Postgres";
    }

    public function supported()
    {
        return stripos($this->cli->run("which psql"), "not found") === false
            || stripos($this->cli->asRoot()->run("which psql"), "not found") === false;
    }

    public function install(/* array | ArrayAccess */ $options = [])
    {
        info("Creating temporary disk...");
        $this->disk->mount([
            "size" => "512M",
        ]);

        info("Installing Postgres...");
        $this->cli->as("postgres")->run("pg_ctl initdb -D {$this->path()}");
    }

    public function uninstall()
    {
        info("Removing temporary disk");
        $this->disk->unmount();
    }

    public function start(/* array | ArrayAccess */ $options = [])
    {
        $path = $this->path();

        info("Starting Postgres...");
        $port = $options["port"] ?? 3344;
        $username = $options["username"] ?? "sqlia";
        $password = $options["password"] ?? "sqlia";

        $this->cli->as("postgres")->inBackground()->run(
            "pg_ctl start -D {$path}" .
            " -l {$path}/error.log" .
            " -o \"" .
            " -h {$path}/postgres.sock" .
            " -p {$port}" .
            " -u {$username}" .
            " \""
        );

        // FIXME: Poll for availability
        info("Waiting for PostgresSQL to become available...");
        sleep(2);
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
            // TODO: Run queries
        }
        output("");
    }

    public function running()
    {
        return false;
    }

    public function stop()
    {
        output($this->cli->run("pg_ctl -D {$this->path()} stop -s -m fast"));
    }

    private function query($user, $port, $password, $sql)
    {
        // TODO: Run queries
        return "";
    }
}
