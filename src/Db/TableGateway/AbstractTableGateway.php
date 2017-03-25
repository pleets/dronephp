<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Db\TableGateway;

use Drone\Db\Driver\DriverAdapter;
use Exception;

abstract class AbstractTableGateway
{
    /**
     * Driver connection
     *
     * @var DriverAdapter
     */
    private static $driver;

    /**
     * Returns the DriverAdapter
     *
     * @return DriverAdapter
     */
    public static function getDriver()
    {
        return self::$driver;
    }

    /**
     * Constructor
     *
     * @param string  $connection_identifier
     * @param boolean $auto_connect
     */
    public function __construct($connection_identifier = "default", $auto_connect = true)
    {
        if (!isset(self::$driver))
            self::$driver = new DriverAdapter($connection_identifier, $auto_connect);
    }
}