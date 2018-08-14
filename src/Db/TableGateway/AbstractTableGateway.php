<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Db\TableGateway;

use Drone\Db\Driver\DriverAdapter;

/**
 * AbstractTableGateway class
 *
 * This class stores statically different connections from a TableGateway
 */
abstract class AbstractTableGateway
{
    /**
     * Driver collector
     *
     * @var DriverAdapter[]
     */
    private static $drivers;

    /**
     * Current driver identifier
     *
     * @var string
     */
    private $currentDriverIdentifier;

    /**
     * Returns all registered drivers
     *
     * @return DriverAdapter[]
     */
    public static function getDrivers()
    {
        return self::$drivers;
    }

    /**
     * Returns the current driver identifier
     *
     * @return string
     */
    public function getCurrentDriverIdentifier()
    {
        return $this->currentDriverIdentifier;
    }

    /**
     * Returns the current DriverAdapter
     *
     * @return DriverAdapter
     */
    public function getDriver()
    {
        return self::$drivers[$this->currentDriverIdentifier];
    }

    /**
     * Constructor
     *
     * @param string  $connection_identifier
     * @param boolean $auto_connect
     */
    public function __construct($connection_identifier = "default", $auto_connect = true)
    {
        $this->currentDriverIdentifier = $connection_identifier;

        if (!isset(self::$drivers[$connection_identifier]))
            self::$drivers[$connection_identifier] = new DriverAdapter($connection_identifier, $auto_connect);
    }
}