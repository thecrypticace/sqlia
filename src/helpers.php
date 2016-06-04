<?php

use Illuminate\Container\Container;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Output the given text to the console.
 *
 * @param  string  $output
 * @return void
 */
function info($output)
{
    output("<info>{$output}</info>");
}

/**
 * Output the given text to the console.
 *
 * @param  string  $output
 * @return void
 */
function warning($output)
{
    output("<fg=red>{$output}</>");
}

/**
 * Output the given text to the console.
 *
 * @param  string  $output
 * @return void
 */
function comment($output)
{
    output("<fg=yellow>{$output}</>");
}

/**
 * Output the given text to the console.
 *
 * @param  string  $output
 * @return void
 */
function output($output)
{
    if (isset($_ENV["APP_ENV"]) && $_ENV["APP_ENV"] === "testing") {
        return;
    }

    resolve(OutputInterface::class)->writeln($output);
}

/**
 * Verify that the script is currently running as "sudo".
 *
 * @return void
 */
function should_be_sudo()
{
    if (! isset($_SERVER["SUDO_USER"])) {
        throw new RuntimeException("This command must be run with sudo.");
    }
}

/**
 * Resolve an instance from the container.
 *
 * @return mixed
 */
function resolve($class)
{
    return Container::getInstance()->make($class);
}

/**
 * Tap the given value.
 *
 * @param  mixed  $value
 * @param  callable  $callback
 * @return mixed
 */
function tap($value, callable $callback)
{
    $callback($value);

    return $value;
}

/**
 * Get the user.
 */
function user()
{
    if (! isset($_SERVER["SUDO_USER"])) {
        return $_SERVER["USER"];
    }

    return $_SERVER["SUDO_USER"];
}
