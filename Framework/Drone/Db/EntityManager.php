<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Db;

use Drone\Db\TableGateway;
use Drone\Db\Entity;

use Exception;

class EntityManager
{
    /**
     * @var TableGateway $tableGateway
     */
    private $tableGateway;

    /**
     * Constructor
     *
     * @param TableGateway $tableGateway
     *
     * @return null
     */
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    /**
     * Returns the tableGateway
     *
     * @return TableGateway
     */
    public function getTableGateway()
    {
        return $this->tableGateway;
    }

    /**
     * Returns a rowset with entity instances
     *
     * @param array $where
     *
     * @return Entity[]
     */
    public function select($where)
    {
        $result = $this->tableGateway->select($where);

        if (!count($result))
            return $result;

        $array_result = array();

        foreach ($result as $row)
        {
            $filtered_array = array();

            foreach ($row as $key => $value)
            {
                if (is_string($key))
                    $filtered_array[$key] = $value;
            }

            $user_entity = get_class($this->tableGateway->getEntity());

            $entity = new $user_entity();
            $entity->exchangeArray($filtered_array);

            $array_result[] = $entity;
        }

        return $array_result;
    }

    /**
     * Creates a row from an entity or array
     *
     * @param Entity|array $entity
     *
     * @return integer
     */
    public function insert($entity)
    {
        if ($entity instanceof Entity)
            $entity = get_object_vars($entity);
        else if (!is_array($entity))
            throw new Exception("Invalid type given. Drone\Db\Entity or Array expected");

        $result = $this->tableGateway->insert($entity);

        return $result;
    }

    /**
     * Updates an entity
     *
     * @param Entity|array $entity
     * @param array $where
     *
     * @return integer
     */
    public function update($entity, $where)
    {
        if ($entity instanceof Entity)
            $entity = get_object_vars($entity);
        else if (!is_array($entity))
            throw new Exception("Invalid type given. Drone\Db\Entity or Array expected");

        $result = $this->tableGateway->update($entity, $where);

        return $result;
    }

    /**
     * Deletes an entity
     *
     * @param Entity|array $entity
     *
     * @return integer
     */
    public function delete($entity)
    {
        if ($entity instanceof Entity)
            $entity = get_object_vars($entity);
        else if (!is_array($entity))
            throw new Exception("Invalid type given. Drone\Db\Entity or Array expected");

        $result = $this->tableGateway->delete($entity);

        return $result;
    }
}