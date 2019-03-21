<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Db;

/**
 * Entity class
 *
 * This class represents an abstract database entity, often a table
 */
abstract class Entity
{
    /**
     * the table's name
     *
     * @var string
     */
    private $tableName;

    /**
     * List of fields changed
     *
     * @var array
     */
    private $changedFields = [];

    /**
     * The connection parameters
     *
     * @var array
     */
    private $connectionParams = [];

    /**
     * Returns the tableName property
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Returns a list with the fields changed
     *
     * @return array
     */
    public function getChangedFields()
    {
        return $this->changedFields;
    }

    /**
     * Returns the connectionParams property
     *
     * @return array
     */
    public function getConnectionParams()
    {
        return $this->connectionParams;
    }

    /**
     * Sets the tableName property
     *
     * @param string $tableName
     *
     * @return null
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Sets the connectionParams property
     *
     * @param array $connectionParams
     *
     * @return null
     */
    public function setConnectionParams($connectionParams)
    {
        $this->connectionParams = $connectionParams;
    }

    /**
     * Sets all entity properties passed in the array
     *
     * @param array $data
     *
     * @return null
     */
    public function exchangeArray($data)
    {
        foreach ($data as $prop => $value)
        {
            if (property_exists($this, $prop))
            {
                $this->$prop = $value;

                if (!in_array($prop, $this->changedFields))
                    $this->changedFields[] = $prop;
            }
            else
                throw new \LogicException("The property '$prop' does not exists in the class ' " . get_class($this) . " '");
        }
    }

    /**
     * Constructor
     *
     * @param array $data
     *
     * @return null
     */
    public function __construct($data)
    {
        foreach ($data as $prop => $value)
        {
            $this->$prop = $value;
        }
    }
}