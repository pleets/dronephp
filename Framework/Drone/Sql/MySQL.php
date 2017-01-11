<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Sql;

use mysqli;
use Exception;

class MySQL extends Driver implements DriverInterface
{
    /**
     * @return array
     */
    public function getArrayResult()
    {
        if ($this->arrayResult)
            return $this->arrayResult;

        return $this->toArray();
    }

    /**
     * Constructor for MySql driver
     *
     * @param array $options
     *
     * @throws Exception
     */
    public function __construct($options)
    {
        if (!extension_loaded('mysqli'))
            throw new Exception("The Mysqli extension is not loaded");

        if (!array_key_exists("Dbchar", $options))
            $options["dbchar"] = "utf8";

        parent::__construct($options);

        $auto_connect = array_key_exists('auto_connect', $options) ? $options["auto_connect"] : true;

        if ($auto_connect)
        {
            $this->dbconn = @new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);

            if ($this->dbconn->connect_errno)
            {
                $this->error(
                    $this->dbconn->connect_errno,
                    $this->dbconn->connect_error
                );

                if (count($this->errors))
                    throw new Exception($this->dbconn->connect_error, $this->dbconn->connect_errno);
                else
                    throw new Exception("Unknown error!");
            }
        }
    }

    /**
     * Reconnects to database
     *
     * @return boolean
     */
    public function reconnect()
    {
        if (!extension_loaded('mysqli'))
            throw new Exception("The Mysqli extension is not loaded");

        $this->dbconn = new mysqli($this->dbhost,$this->dbuser,$this->dbpass,$this->dbname);

        if ($this->dbconn->connect_errno === false)
        {
            $this->error(
                $this->dbconn->connect_errno,
                $this->dbconn->connect_error
            );

            return false;
        }

        return true;
    }

    /**
     * Excecutes a statement
     *
     * @return boolean
     */
    public function query($sql, $params = [])
    {
        $this->numRows = 0;
        $this->numFields = 0;
        $this->rowsAffected = 0;

        $this->arrayResult = null;

        $this->result = @$this->dbconn->query($sql);

        if (!$this->result)
        {
            $this->error(
                100, $this->dbconn->error
            );

            if (count($this->errors))
                throw new Exception($this->dbconn->error, 100);
            else
                throw new Exception("Unknown error!");
        }

        $rows = $this->getArrayResult();

        $this->numRows = $this->result->num_rows;
        $this->numFields = $this->result->field_count;

        if (is_object($this->result) && property_exists($this->result, 'affected_rows'))
            $this->rowsAffected = $this->result->affected_rows;

        if ($this->transac_mode)
            $this->transac_result = is_null($this->transac_result) ? $this->result: $this->transac_result && $this->result;

        return $this->result;
    }

    /**
     * Excecutes multiple statements as transaction
     *
     * @return boolean
     */
    public function transaction($querys)
    {
        $this->begin_transaction();

        foreach ($querys as $sql)
        {
            $this->query($sql);
        }

        return $this->end_transaction();
    }

    /**
     * Closes the connection
     *
     * @return boolean
     */
    public function cancel()
    {
        $this->dbconn->close();
    }

    /**
     * Returns an array with the rows fetched
     *
     * @throws Exception
     *
     * @return array
     */
    private function toArray()
    {
        $data = array();

        if ($this->result && !is_bool($this->result))
        {
            while ($row = $this->result->fetch_array(MYSQLI_BOTH))
            {
                $data[] = $row;
            }
        }
        else
            throw new Exception('There are not data in the buffer!');

        $this->arrayResult = $data;

        return $data;
    }

    public function __destruct()
    {
        if ($this->dbconn !== false)
            $this->dbconn->close();
    }
}