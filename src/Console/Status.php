<?php

namespace Sqlia\Console;

class Status extends AbstractCommand
{
    public $signature = "status [driver]";
    public $description = "Check the status of the Sqlia server";

    public function handle($driver = null)
    {
        $drivers = $driver
            ? [$this->drivers->named($driver)]
            : $this->drivers->all();

        foreach ($drivers as $driver) {
            output("{$driver->name()}: " . ($driver->running() ? "<info>✓</info>" : "<fg=yellow>✗</>"));
        }

        output("");
    }
}
