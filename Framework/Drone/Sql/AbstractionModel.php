<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/Drone
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Sql;

abstract class AbstractionModel
{
    private $driver;
    private $db;
    private $availableDrivers;

    public function __construct($abstract_connection_string = "default", $auto_connect = true)
    {
		$dbsettings = include(__DIR__ . "/../../../config/database.config.php");

        # driver => className
        $this->availableDrivers = array(
            "Oci8"          => "Drone\Sql\Oracle",
            "Mysqli"        => "Drone\Sql\MySQL",
            "Sqlsrv"        => "Drone\Sql\SQLServer",
            // Drivers for future implementation
            //"Pdo_Mysql"     => "",
            //"Pgsql"         => "",
            //"Pdo_Sqlite"    => "",
            //"Pdo_Sqlite"    => "",
            //"Pdo_Pgsql"     => "",
        );

        $drv = $dbsettings[$abstract_connection_string]["driver"];

        if (array_key_exists($drv, $this->availableDrivers))
        {
            $driver = $this->getAvailableDrivers();

            $this->db = new $driver[$drv]($dbsettings[$abstract_connection_string]);
        }
        else
            throw new Exception("The Database driver does not exists");
	}

    /* Getters */

    public function getDriver() { return $this->driver; }
    public function getDb() { return $this->db; }
    public function getAvailableDrivers() { return $this->availableDrivers; }
}