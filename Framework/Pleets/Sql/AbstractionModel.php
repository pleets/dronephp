<?php

/*
 * Sql abstraction class
 * http://www.pleets.org
 *
 * Copyright 2016, Pleets Apps
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */

namespace Pleets\Sql;

abstract class AbstractionModel
{
    private $driver;
    private $db;
    private $availableDrivers;

    public function __construct($abstract_connection_string = "default", $auto_connect = true)
    {
		$dbsettings = include(dirname(__FILE__) . "/../../../config/database.config.php");

        # driver => className
        $this->availableDrivers = array(
            "Oci8"          => "Pleets\Sql\Oracle",
            "Mysqli"        => "Pleets\Sql\MySQL",
            "Sqlsrv"        => "Pleets\Sql\SQLServer",
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

            $this->db = new $driver[$drv](
                $dbsettings[$abstract_connection_string]["host"],
                $dbsettings[$abstract_connection_string]["user"],
                $dbsettings[$abstract_connection_string]["password"],
                $dbsettings[$abstract_connection_string]["dbname"],
                $auto_connect
            );
        }
        else
            throw new Exception("The Database driver does not exists");
	}

    /* Getters */

    public function getDriver() { return $this->driver; }
    public function getDb() { return $this->db; }
    public function getAvailableDrivers() { return $this->availableDrivers; }
}