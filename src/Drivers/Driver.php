<?php

namespace Sqlia\Drivers;

interface Driver
{
    /**
     * The user-facing name of the driver.
     *
     * @return string
     */
    public function name() : string;

    /**
     * Whether or not the driver could be supported by the current OS.
     *
     * @return bool
     */
    public function supported();

    /**
     * Whether or not the server is running.
     *
     * @return bool
     */
    public function running();

    /**
     * Remove any required software to run this driver.
     *
     * @return bool
     */
    public function uninstall();

    /**
     * Install any required software to run this driver.
     *
     * @return bool
     */
    public function install(/* array | ArrayAccess */ $options = []);

    /**
     * Start the server for this driver.
     *
     * @return bool
     */
    public function start(/* array | ArrayAccess */ $options = []);

    /**
     * Stop the server for this driver.
     *
     * @return bool
     */
    public function stop();

    /**
     * Recreate a set of databases.
     *
     * @return bool
     */
    public function refresh(/* array | ArrayAccess */ $options = []);
}
