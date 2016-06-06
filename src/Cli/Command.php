<?php

namespace Sqlia\Cli;

use Symfony\Component\Process\Process;
use function Sqlia\warning;

class Command
{
    private $quiet = false;
    private $root = false;
    private $user = null;
    private $asynchronous = false;
    private $inBackground = false;

    public function run($command, callable $onError = null)
    {
        $command = $this->build($command);
        $process = new Process($command);
        $process->setTimeout(null);

        $output = '';
        $this->execute($process, function ($type, $line) use (&$output) {
            $output .= $line;
        });

        if (! $this->asynchronous && $process->getExitCode() > 0) {
            $onError = $onError ?: function ($exit, $output) {
                warning(trim($output));
            };

            $onError($process->getExitCode(), $output);
        }

        return trim($output);
    }

    private function execute($process, $callback)
    {
        if ($this->asynchronous) {
            $process->start($callback);
        } else {
            $process->run($callback);
        }
    }

    private function build($command)
    {
        if ($this->root) {
            $command = "sudo {$command}";
        } elseif ($this->user) {
            $command = "sudo -u {$this->user} {$command}";
        }

        if ($this->quiet) {
            $command = "{$command} > /dev/null 2>&1";
        }

        if ($this->inBackground) {
            $command = "{$command} &";
        }

        return $command;
    }

    public function quietly()
    {
        $this->quiet = true;

        return $this;
    }

    public function as($user)
    {
        $this->root = false;
        $this->user = $user;

        return $this;
    }

    public function asRoot()
    {
        $this->root = true;
        $this->user = null;

        return $this;
    }

    public function asCurrentUser()
    {
        return $this->as(user());
    }

    private function asynchronously()
    {
        $this->asynchronous = true;

        return $this;
    }

    public function inBackground()
    {
        $this->inBackground = true;

        return $this->asynchronously();
    }
}
