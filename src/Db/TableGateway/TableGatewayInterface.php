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

/**
 * DriverInterface Interface
 *
 * This interface defines the four basic operations for persistent storage (CRUD)
 */
interface TableGatewayInterface
{
    /**
     * Select statement
     *
     * @param array $where
     */
    public function select(Array $where);

    /**
     * Insert statement
     *
     * @param array $data
     */
    public function insert(Array $data);

    /**
     * Update statement
     *
     * @param array $set
     * @param array $where
     */
    public function update(Array $set, Array $where);

    /**
     * Delete statement
     *
     * @param array $where
     */
    public function delete(Array $where);
}
