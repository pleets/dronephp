<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
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
     * Returns the tableName property
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Sets the tableName property
     *
     * @param string $tableName
     *
     * @param null
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
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
                $this->$prop = $value;
            else
                throw new Exception("The property '$prop' does not exists in the class '$class'");
        }
    }
}