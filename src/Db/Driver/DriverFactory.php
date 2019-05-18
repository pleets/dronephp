<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Db\Driver;

/**
 * DriverFactory Class
 *
 * This class makes a database connection with a specified driver, i.e. creates the driver instance.
 */
class DriverFactory
{
    /**
     * Constructor
     *
     * @param array $connection_options
     *
     * @return AbstractDriver
     *
     * @throws \RuntimeException
     */
    public static function create($connection_options)
    {
        $drivers = [
            "Oci8"   => "Drone\Db\Driver\Oracle",
            "Mysqli" => "Drone\Db\Driver\MySQL",
            "Sqlsrv" => "Drone\Db\Driver\SQLServer",
        ];

        if (!array_key_exists('driver', $connection_options)) {
            throw new \RuntimeException("The database driver key has not been declared");
        }

        $drv = $connection_options["driver"];

        if (array_key_exists($drv, $drivers)) {
            return new $drivers[$drv]($connection_options);
        } else {
            throw new \RuntimeException("The database driver does not exists");
        }
    }
}
