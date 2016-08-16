<?php

/*
 * MySQL class
 * http://www.pleets.org
 *
 * Copyright 2014, Pleets Apps
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */

namespace Pleets\Sql;

class Mysql {

    private $dbhost = '';                           # default host
    private $dbuser = '';                           # default username
    private $dbpass = '';                           # default password
    private $dbname = '';                           # default database
    private $dbchar = '';                           # default charset

    private $dbconn = null;                         # connection

    private $errors = array();                      # Errors

    private $numRows;                               # Rows returned
    private $numFields;                             # Fields returned
    private $rowsAffected;                          # Rows affected

    private $result;                                # latest result (current buffer)
    private $arrayResult;                           # result array (SELECT statements)

    private $transac_mode = false;                  # transaction process
    private $transac_result = null;                 # result of transactions

    public function __construct($dbhost = null, $dbuser = null, $dbpass = null, $dbname = null, $auto_connect = true, $dbchar = "utf8")
    {
        $this->dbhost = is_null($dbhost) ? !defined('DBHOST') ? $this->dbhost : @DBHOST : $dbhost;
        $this->dbuser = is_null($dbuser) ? !defined('DBUSER') ? $this->dbuser : @DBUSER : $dbuser;
        $this->dbpass = is_null($dbpass) ? !defined('DBPASS') ? $this->dbpass : @DBPASS : $dbpass;
        $this->dbname = is_null($dbname) ? !defined('DBNAME') ? $this->dbname : @DBNAME : $dbname;

        $this->dbchar = is_null($dbchar) ? !defined('DBCHAR') ? $this->dbchar : @DBCHAR : $dbchar;

        if ($auto_connect)
        {
            $this->dbconn = new \mysqli($this->dbhost,$this->dbuser,$this->dbpass,$this->dbname);

            if ($this->dbconn->connect_errno === false)
            {
                $this->errors = array(
                    "code" => $this->dbconn->connect_errno,
                    "message" => $this->dbconn->connect_error
                );

                if (count($this->errors))
                    throw new \Exception($this->errors["message"], $this->errors["code"]);
                else
                    throw new \Exception("Unknown error!");
            }
        }
    }

    /* Getters */

    public function getHostname() { return $this->dbhost; }
    public function getUsername() { return $this->dbuser; }
    public function getDatabase() { return $this->dbname; }
    public function getNumRows() { return $this->numRows; }
    public function getNumFields() { return $this->numFields; }
    public function getRowsAffected() { return $this->rowsAffected; }

    public function getArrayResult()
    {
        if ($this->arrayResult)
            return $this->arrayResult;

        return $this->toArray();
    }

    public function getErrors() { return $this->errors; }

    /* Setters */

    public function setHostname($dbhost) { $this->dbhost = $dbhost; }
    public function setUsername($dbuser) { $this->dbuser = $dbuser; }
    public function setPassword($dbpass) { $this->dbpass = $dbpass; }
    public function setDatabase($dbname) { $this->dbname = $dbname; }

    public function reconnect()
    {
        $this->dbconn = new \mysqli($this->dbhost,$this->dbuser,$this->dbpass,$this->dbname);

        if ($this->dbconn->connect_errno === false)
        {
            $this->errors = array(
                "code" => $this->dbconn->connect_errno,
                "message" => $this->dbconn->connect_error
            );

            if (count($this->errors))
                throw new \Exception($this->errors["message"], $this->errors["code"]);
            else
                throw new \Exception("Unknown error!");
        }

        return $this;
    }

    public function query($sql, Array $params = array())
    {
        $this->numRows = 0;
        $this->numFields = 0;
        $this->rowsAffected = 0;

        $this->arrayResult = null;

        $this->result = @$this->dbconn->query($sql);

        if (!$this->result)
        {
            $this->errors = array(
                "code" => 100,
                "message" => $this->dbconn->error
            );

            if (count($this->errors))
                throw new \Exception($this->errors["message"], $this->errors["code"]);
            else
                throw new \Exception("Unknown error!");
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

    function transaction($querys)
    {
        $this->begin_transaction();

        foreach ($querys as $sql)
        {
            $this->query($sql);
        }

        $this->end_transaction();
    }

    public function begin_transaction()
    {
        if ($this->transac_mode)
            throw new \Exception("Transaction mode has already started");

        if ($this->dbconn->begin_transaction() === false)
            throw new \Exception($this->dbconn->error);

        $this->transac_mode = true;

        return true;
    }

    public function end_transaction()
    {
        if (is_null($this->transac_result))
            throw new \Exception("There are not querys in this transaction");

        if ($this->transac_result)
            $this->dbconn->commit();
        else {
            $this->dbconn->rollback();
            return false;
        }
    }

    public function cancel()
    {
        $this->dbconn->close();
    }

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
            throw new \Exception('There are not data in the buffer!');

        $this->arrayResult = $data;

        return $data;
    }

    public function __destruct()
    {
        if ($this->dbconn !== false)
            $this->dbconn->close();
    }
}