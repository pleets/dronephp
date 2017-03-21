<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Db\TableGateway;

use Drone\Db\Entity;
use Drone\Db\SQLFunction;
use Exception;
use DateTime;

class EntityAdapter
{
    /**
     * @var TableGateway $tableGateway
     */
    private $tableGateway;

    /**
     * Constructor
     *
     * @param TableGateway $tableGateway
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
     * @throws Exception
     * @return boolean
     */
    public function insert($entity)
    {
        if ($entity instanceof Entity)
            $entity = get_object_vars($entity);
        else if (!is_array($entity))
            throw new Exception("Invalid type given. Drone\Db\Entity or Array expected");

        $this->parseEntity($entity);

        $result = $this->tableGateway->insert($entity);

        return $result;
    }

    /**
     * Updates an entity
     *
     * @param Entity|array $entity
     * @param array $where
     *
     * @throws Exception
     * @return booelan
     */
    public function update($entity, $where)
    {
        if ($entity instanceof Entity)
            $entity = get_object_vars($entity);
        else if (!is_array($entity))
            throw new Exception("Invalid type given. Drone\Db\Entity or Array expected");

        $this->parseEntity($entity);

        $result = $this->tableGateway->update($entity, $where);

        return $result;
    }

    /**
     * Deletes an entity
     *
     * @param Entity|array $entity
     *
     * @throws Exception
     * @return boolean
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

    /**
     * Converts several objects to SQLFunction objects
     *
     * @param array $entity
     *
     * @return array
     */
    private function parseEntity(&$entity)
    {
        $drv = $this->getTableGateway()->getDriver()->getDriver();

        foreach ($entity as $field => $value)
        {
            if ($value instanceof DateTime)
            {
                switch ($drv)
                {
                    case 'Oci8':
                        $entity[$field] = new SQLFunction('TO_DATE', array($value->format('Y-m-d'), 'YYYY-MM-DD'));
                        break;
                    case 'Mysqli':
                        $entity[$field] = new SQLFunction('STR_TO_DATE', array($value->format('Y-m-d'), '%Y-%m-%d'));
                        break;
                    case 'Sqlsrv':
                        $entity[$field] = new SQLFunction('CONVERT', array('DATETIME', $value->format('Y-m-d')));
                        break;
                }
            }
        }

        return $entity;
    }
}