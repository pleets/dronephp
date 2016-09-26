<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Db;

abstract class AbstractTableGateway
{
    /**
     * Handle
     *
     * @var Driver
     */
    private static $db;

    /**
     * Constructor
     *
     * @param string  $abstract_connection_string
     * @param boolean $auto_connect
     *
     * @return null
     */
    public function __construct($abstract_connection_string = "default", $auto_connect = true)
    {
		$dbsettings = include(__DIR__ . "/../../../config/database.config.php");

        $drivers = array(
            "Oci8"          => "Drone\Sql\Oracle",
            "Mysqli"        => "Drone\Sql\MySQL",
            "Sqlsrv"        => "Drone\Sql\SQLServer",
        );

        $drv = $dbsettings[$abstract_connection_string]["driver"];
        $dbsettings[$abstract_connection_string]["auto_connect"] = $auto_connect;

        if (!array_key_exists($drv, $drivers))
            throw new Exception("The Database driver '$drv' does not exists");

        if (array_key_exists($drv, $drivers) && !isset(self::$db))
            self::$db = new $drivers[$drv]($dbsettings[$abstract_connection_string]);
	}

    /**
     * Returns the handle instance
     *
     * @return Driver
     */
    public static function getDb()
    {
        return self::$db;
    }
}