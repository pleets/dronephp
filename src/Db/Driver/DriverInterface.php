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
 * DriverInterface Interface
 *
 * This interface could be used to define what methods should implement a driver class
 */
interface DriverInterface
{
    /**
     * Connects to a database
     *
     * This method would use connect()
     */
    public function connect();

    /**
     * Reconnects to the database
     *
     * This method would use connect()
     */
    public function reconnect();

    /**
     * Executes a statement
     *
     *@param string $sql
     */
    public function execute($sql);

    /**
     * Does commit to current statements
     */
    public function commit();

    /**
     * Does rollback to current statements
     */
    public function rollback();

    /**
     * Begins a transaction
     */
    public function beginTransaction();

    /**
     * Closes a transaction
     */
    public function endTransaction();

    /**
     * Sets the autocommit behavior
     * @param mixed $value
     */
    public function autocommit($value);

    /**
     * Disconnects to database
     */
    public function disconnect();
}
