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

use Drone\Db\Driver\DriverFactory;
use Drone\Db\Entity;

/**
 * AbstractTableGateway class
 *
 * This class stores statically different connections from a TableGateway
 */
abstract class AbstractTableGateway
{
    /**
     * Entity instance
     *
     * @var Entity
     */
    protected $entity;

    /**
     * Driver collector
     *
     * @var AbstractDriver[]
     */
    private static $drivers;

    /**
     * Current connection
     *
     * @var string
     */
    protected $currentConnection;

    /**
     * Returns the entity
     *
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Returns all registered drivers
     *
     * @return AbstractDriver[]
     */
    public static function getDrivers()
    {
        return self::$drivers;
    }

    /**
     * Returns the current connection identifier
     *
     * @return string
     */
    public function getCurrentConnection()
    {
        return $this->currentConnection;
    }

    /**
     * Constructor
     *
     * @param Entity       $entity
     * @param array|string $connection
     *
     * @throws \RuntimeException
     */
    public function __construct(Entity $entity, $connection)
    {
        $this->entity = $entity;

        if (is_string($connection))
        {
            $this->currentConnection = $connection;
            $this->getDriver($connection);
        }
        else if (is_array($connection))
        {
            $identifier = key($connection);
            $connection_options = $connection[$identifier];

            $this->currentConnection = $identifier;

            if (!array_key_exists('driver', $connection_options))
                throw new \RuntimeException("The database driver key has not been declared");

            if (!isset(self::$drivers[$identifier]))
                self::$drivers[$identifier] = DriverFactory::create($connection_options);
            else
                throw new \RuntimeException("The database connection already exists");
        }
        else
            throw new \InvalidArgumentException("Invalid type given. Array or string expected");
    }

    /**
     * Returns the a particular connection
     *
     * @param string
     *
     * @return AbstractDriver
     *
     * @throws \RuntimeException
     */
    public static function getDriver($identifier)
    {
        if (!array_key_exists($identifier, self::$drivers))
            throw new \RuntimeException("The database connection does not exists");

        return self::$drivers[$identifier];
    }

    /**
     * Returns true if the connection exists
     *
     * @param string
     *
     * @return boolean
     *
     * @throws \RuntimeException
     */
    public static function hasDriver($identifier)
    {
        if (!array_key_exists($identifier, self::$drivers))
            return false;

        return true;
    }

    /**
     * Returns the current connection driver
     *
     * @return AbstractDriver
     *
     * @throws \RuntimeException
     */
    public function getCurrentDriver()
    {
        return self::$drivers[$this->currentConnection];
    }

    /**
     * Alias for getCurrentDriver()
     *
     * @return AbstractDriver
     *
     * @throws \RuntimeException
     */
    public function getDb()
    {
        return $this->getCurrentDriver();
    }
}