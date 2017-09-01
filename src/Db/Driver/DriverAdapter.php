<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Db\Driver;

class DriverAdapter
{
    /**
     * Driver identifier
     *
     * @var string
     */
    private $driverName;

    /**
     * Connection resource
     *
     * @var resource|object
     */
    private $db;

    /**
     * All supported drivers
     *
     * @var array
     */
    private $availableDrivers;

    /**
     * Returns the current driver identifier
     *
     * @return string
     */
    public function getDriverName()
    {
        return $this->driverName;
    }

    /**
     * Returns the current connection resource or object
     *
     * @return resource|object
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
     * @throws RuntimeException
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
            if (!file_exists("config/database.config.php"))
                throw new \RuntimeException("config/data.base.config.php is missing!");

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
            throw new \RuntimeException("The Database driver does not exists");
    }
}