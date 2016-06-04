<?php

namespace Sqlia\Console;

class Stop extends AbstractCommand
{
    public $signature = "stop driver";
    public $description = "Stop the Sqlia server";

    public function handle($quiet, $driver)
    {
        $quiet && $this->disableOutput();

        $driver = $this->drivers->named($driver);

        if (! $driver->supported()) {
            warning("This driver is not supported on your system.");

            return 1;
        }

        if (! $driver->running()) {
            warning("This driver is not running.");

            return 1;
        }

        $this->env->measureAndReport(function () use ($driver) {
            $driver->stop();
            $driver->uninstall();
        });
    }
}
