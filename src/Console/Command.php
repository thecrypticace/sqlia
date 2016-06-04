<?php

namespace Sqlia\Console;

interface Command
{
    // public function handle();

    public function descriptions() /* : string[] */;
    public function defaults() /* : string[] */;
}
