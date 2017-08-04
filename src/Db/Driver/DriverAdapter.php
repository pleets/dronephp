<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Db\Driver;

use Exception;

class DriverAdapter
{
    /**
     * Driver identifier
     *
     * @var string
     */
    private $driver;

    /**
     * Connection resource
     *
     * @var resource
     */
    private $db;

    /**
     * All supported drivers
     *
     * @var array
     */
    private $availableDrivers;

    /**
     * Returns the current driver
     *
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Returns the current connection resource
     *
     * @return resource
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Returns all supported drivers
     *
     * @return array
     */
    public function getAvailableDrivers()
    {
        return $this->availableDrivers;
    }

    /**
     * Constructor
     *
     * @param string|array  $connection_identifier
     * @param boolean       $auto_connect
     *
     * @throws Exception
     */
    public function __construct($connection_identifier = "default", $auto_connect = true)
    {
        # driver => className
        $this->availableDrivers = [
            "Oci8"   => "Drone\Db\Driver\Oracle",
            "Mysqli" => "Drone\Db\Driver\MySQL",
            "Sqlsrv" => "Drone\Db\Driver\SQLServer",
        ];

        if (gettype($connection_identifier) == 'array')
            $connection_array = $connection_identifier;
        else
        {
            # Take connection parameters from configuration file
            $dbsettings = include("config/database.config.php");
            $connection_array = $dbsettings[$connection_identifier];
        }

        $drv = $connection_array["driver"];
        $connection_array["auto_connect"] = $auto_connect;

        if (array_key_exists($drv, $this->availableDrivers))
        {
            $driver = $this->getAvailableDrivers();

            $this->driver = $drv;
            $this->db = new $driver[$drv]($connection_array);
        }
        else
            throw new Exception("The Database driver does not exists");
    }
}