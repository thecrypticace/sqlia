<?php

namespace Sqlia\Console;

use function Sqlia\output;

class Supported extends AbstractCommand
{
    public $signature = "supported [driver]";
    public $description = "List or check for supported drivers";

    public function handle($driver)
    {
        $drivers = $driver
            ? [$this->drivers->named($driver)]
            : $this->drivers->all();

        foreach ($drivers as $driver) {
            output("{$driver->name()}: " . ($driver->supported() ? "<info>✓</info>" : "<fg=yellow>✗</>"));
        }

        output("");
    }

    public function descriptions()
    {
        return [
            "driver" => "The driver to check",
        ];
    }
}
