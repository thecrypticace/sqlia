<?php

namespace Sqlia\Console;

use function Sqlia\warning;

class Start extends AbstractCommand
{
    public $signature = "start driver [--port=] [--username=] [--password=]";
    public $description = "Start the Sqlia server";

    public function handle($driver, $port, $username, $password, $quiet)
    {
        $quiet && $this->disableOutput();

        $driver = $this->drivers->named($driver);

        if (! $driver->supported()) {
            warning("This driver is not supported on your system.");

            return 1;
        }

        $options = array_replace_recursive(compact("port", "username", "password"), [
            //
        ]);

        $this->env->measureAndReport(function () use ($driver, $options) {
            $driver->install($options);
            $driver->start($options);
        });
    }

    public function descriptions()
    {
        return [
            "driver" => "The driver to start",
            "--port" => "The port to start the driver on",
            "--username" => "The database user to create",
            "--password" => "The password for the user",
        ];
    }
}
