<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Db;

use Drone\Db\Entity;
use Drone\Sql\Platform\SQLFunction;

class TableGateway extends AbstractTableGateway implements TableGatewayInterface
{
    /**
     * Entity instance
     *
     * @var Entity
     */
    private $entity;

    /**
     * Constructor
     *
     * @param Entity $entity
     *
     * @return null
     */
    public function __construct(Entity $entity, $auto_connect = true)
    {
        parent::__construct("default", $auto_connect);
        $this->entity = $entity;
    }

    /**
     * Returns the entity
     *
     * @return Drone_Db_Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Select statement
     *
     * @param array $where
     *
     * @return array With all results
     */
    public function select($where = [])
    {
        if (count($where))
        {
            $parsed_where = [];

            foreach ($where as $key => $value)
            {
                if (is_string($value))
                    $parsed_where[] = "$key = '$value'";
                else
                    $parsed_where[] = "$key = $value";
            }

            $where = "WHERE " . implode(" AND ", $parsed_where);
        }
        else
            $where = "";

        $table = $this->entity->getTableName();

        $sql = "SELECT *
                FROM {$table} $where";

        $result = $this->getDb()->query($sql);
        return $this->getDb()->getArrayResult();
    }

    /**
     * Insert statement
     *
     * @param array $data
     *
     * @return boolean
     */
    public function insert($data)
    {
        foreach ($data as $key => $value)
        {
            if (is_string($value))
                $value = "'$value'";
            if (is_null($value))
                $value = "null";
            if ($value instanceof SQLFunction)
                $value = $value->getStatement();

            $data[$key] = $value;
        }

        $cols = implode(",\r\n\t", array_keys($data));
        $vals = implode(",\r\n\t", array_values($data));

        $table = $this->entity->getTableName();

        $sql = "INSERT INTO {$table} \r\n(\r\n\t$cols\r\n) \r\nVALUES \r\n(\r\n\t$vals\r\n)";

        return $this->getDb()->query($sql);
    }

    /**
     * Update statement
     *
     * @param array $set
     * @param array $where
     *
     * @return boolean
     */
    public function update($set, $where)
    {
        $parsed_set = array();

        foreach ($set as $key => $value)
        {
            if (is_string($value))
                $value = "'$value'";
            if (is_null($value))
                $value = "null";

            $parsed_set[] = "$key = $value";
        }

        $parsed_set = implode(", ", $parsed_set);


        $parsed_where = array();

        foreach ($where as $key => $value)
        {
            if (is_string($value))
                $parsed_where[] = "$key = '$value'";
            else
                $parsed_where[] = "$key = $value";
        }

        $parsed_where = implode(" AND ", $parsed_where);

        $table = $this->entity->getTableName();

        $sql = "UPDATE {$table}
                SET $parsed_set
                WHERE $parsed_where";

        return $this->getDb()->query($sql);
    }

    /**
     * Delete statement
     *
     * @param array $where
     *
     * @return boolean
     */
    public function delete($where)
    {
        if (count($where))
        {
            $parsed_where = [];

            foreach ($where as $key => $value)
            {
                if (is_string($value))
                    $parsed_where[] = "$key = '$value'";
                else
                    $parsed_where[] = "$key = $value";
            }

            $where = "WHERE " . implode(" AND ", $parsed_where);
        }
        else
            throw new Exception("You cannot delete rows without WHERE clause!");

        $table = $this->entity->getTableName();

        $sql = "DELETE
                FROM {$table} $where";

        return $this->getDb()->query($sql);
    }
}