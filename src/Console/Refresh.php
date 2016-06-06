<?php

namespace Sqlia\Console;

class Refresh extends AbstractCommand
{
    public $signature = "refresh driver [database]* [--port=] [--username=] [--password=]";
    public $description = "Drop & recreate the specified databases";

    public function handle($quiet, $driver, $port, $username, $password, $database = [])
    {
        $quiet && $this->disableOutput();

        $driver = $this->drivers->named($driver);

        if (! $driver->supported()) {
            warning("This driver is not supported on your system.");

            return;
        }

        if (! $driver->running()) {
            warning("This driver is not running. Please start it to refresh your databases.");

            return 1;
        }

        $options = array_replace_recursive(compact("port", "username", "password"), [
            "databases" => $database,
        ]);

        $this->env->measureAndReport(function () use ($driver, $options) {
            $driver->refresh($options);
        });
    }

    public function descriptions()
    {
        return [
            "driver" => "The driver to start",
            "--port" => "The port to start the driver on",
            "database" => "A list of databases to (re)create",
            "--username" => "The database user to create",
            "--password" => "The password for the user",
        ];
    }
}
