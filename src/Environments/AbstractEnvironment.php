<?php

namespace Sqlia\Environments;

use Closure;
use Sqlia\Cli\Cli;
use Illuminate\Container\Container;
use function Sqlia\comment;

abstract class AbstractEnvironment implements Environment
{
    protected $cli;
    protected $container;

    public function __construct(Cli $cli, Container $container)
    {
        $this->cli = $cli;
        $this->container = $container;
    }

    public function measure(Closure $callback)
    {
        $timeStart = microtime(true);
        $scriptMemoryStart = $this->scriptMemoryUsage();
        $serverMemoryStart = $this->serverMemoryUsage();

        $result = $callback();

        $serverMemoryEnd = $this->serverMemoryUsage();
        $scriptMemoryEnd = $this->scriptMemoryUsage();
        $timeEnd = microtime(true);

        return (object) [
            "timeElapsed" => $timeEnd - $timeStart,
            "scriptMemory" => $scriptMemoryEnd - $scriptMemoryStart,
            "serverMemory" => $serverMemoryEnd - $serverMemoryStart,
        ];
    }

    public function measureAndReport(Closure $callback)
    {
        $results = $this->measure($callback);

        comment(sprintf("Time Elapsed: %.03fs", $results->timeElapsed));
        comment(sprintf("Script Memory Usage: %.03fMB", $results->scriptMemory / 1000.00 / 1000.00));
        comment(sprintf("Server Memory Usage: %.03fMB", $results->serverMemory / 1000.00 / 1000.00));
    }

    public function scriptMemoryUsage() : int
    {
        return memory_get_peak_usage();
    }

    protected function os()
    {
        return php_uname();
    }
}
