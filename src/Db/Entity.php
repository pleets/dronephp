<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Db;

use Exception;

abstract class Entity
{
    /**
     * @var string
     */
    private $tableName;

    /**
     * @var array
     */
    private $changedFields = [];

    /**
     * @var string
     */
    private $connectionIdentifier = "default";

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
     * @return string
     */
    public function getChangedFields()
    {
        return $this->changedFields;
    }

    /**
     * Returns the connectionIdentifier property
     *
     * @return string
     */
    public function getConnectionIdentifier()
    {
        return $this->connectionIdentifier;
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
     * Sets the connectionIdentifier property
     *
     * @param string $connectionIdentifier
     *
     * @return null
     */
    public function setConnectionIdentifier($connectionIdentifier)
    {
        $this->connectionIdentifier = $connectionIdentifier;
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
        $class = get_class($this);

        foreach ($data as $prop => $value)
        {
            if (property_exists($this, $prop))
            {
                $this->$prop = $value;

                if (!in_array($prop, $this->changedFields))
                    $this->changedFields[] = $prop;
            }
            else
                throw new Exception("The property '$prop' does not exists in the class '$class'");
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
        $class = get_class($this);

        foreach ($data as $prop => $value)
        {
            $this->$prop = $value;
        }
    }
}