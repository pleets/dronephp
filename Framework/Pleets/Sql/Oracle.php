<?php

/*
 * Oracle class
 * http://www.pleets.org
 *
 * Copyright 2014, Pleets Apps
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */

namespace Pleets\Sql;

class Oracle
{
    private $dbhost = '';                           # default host
    private $dbuser = '';                           # default username
    private $dbpass = '';                           # default password
    private $dbname = '';                           # default database

    private $dbconn = null;                         # connection
    private $buffer = null;                         # buffer

    private $errors = array();

    public $numRows;                                # Rows returned
    public $numFields;
    public $rowsAffected;

    public $result;                                 # latest result

    private $transac_mode = false;                  # transaction process
    private $transac_result = null;                 # result of transactions

    public function __construct($dbhost = null, $dbuser = null, $dbpass = null, $dbname = null)
    {
        $this->dbhost = is_null($dbhost) ? !defined('DBHOST') ? $this->dbhost : @DBHOST : $dbhost;
        $this->dbuser = is_null($dbuser) ? !defined('DBUSER') ? $this->dbuser : @DBUSER : $dbuser;
        $this->dbpass = is_null($dbpass) ? !defined('DBPASS') ? $this->dbpass : @DBPASS : $dbpass;
        $this->dbname = is_null($dbname) ? !defined('DBNAME') ? $this->dbname : @DBNAME : $dbname;

        $connection_string = (is_null($this->dbhost) || empty($this->dbhost)) ? $this->dbname : $this->dbhost ."/". $this->dbname;
        $this->dbconn = oci_connect($this->dbuser,  $this->dbpass, $connection_string);

        if ($this->dbconn === false)
        {
            $this->errors = oci_error();

            if (count($this->errors))
                throw new \Exception($this->errors["message"], $this->errors["code"]);
            else
                throw new \Exception("Unknown error!");
        }
	}

    /* Getters */

    public function getHostname() { return $this->dbhost; }
    public function getUsername() { return $this->dbuser; }
    public function getDatabase() { return $this->dbname; }

    /* Setters */

    public function setHostname($dbhost) { $this->dbhost = $dbhost; }
    public function setUsername($dbuser) { $this->dbuser = $dbuser; }
    public function setPassword($dbpass) { $this->dbpass = $dbpass; }
    public function setDatabase($dbname) { $this->dbname = $dbname; }

    public function getErrors() { return $this->errors; }

    public function reconnect()
    {
        $connection_string = (is_null($this->dbhost) || empty($this->dbhost)) ? $this->dbname : $this->dbhost ."/". $this->dbname;
        $this->dbconn = oci_connect($this->dbuser,  $this->dbpass, $connection_string);

        if ($this->dbconn === false)
        {
            $this->errors = oci_error();

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

        $this->result = $stid = oci_parse($this->dbconn, $sql);

        $r = ($this->transac_mode) ? oci_execute($stid, OCI_NO_AUTO_COMMIT) : oci_execute($stid,  OCI_COMMIT_ON_SUCCESS);

        if (!$r)
        {
            $this->errors = oci_error($stid);

            if (count($this->errors))
                throw new \Exception($this->errors["message"], $this->errors["code"]);
            else
                throw new \Exception("Unknown error!");
        }

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

        $this->transac_mode = true;

        return true;
    }

    public function end_transaction()
    {
        if (is_null($this->transac_result))
            throw new \Exception("There are not querys in this transaction");

        if ($this->transac_result)
            oci_commit($this->dbconn);
        else {
            oci_rollback($this->dbconn);
            return false;
        }

        $this->result = $this->transac_result;

        $this->transac_result = null;
        $this->transac_mode = false;

        return true;
    }

    public function cancel()
    {
        oci_cancel($this->result);
    }

    public function toArray(Array $settings = array())
    {
        $utf8 = (isset($settings['encode_utf8']) && $settings['encode_utf8'] == true);
        $data = array();

        if ($this->result)
        {
            while ($row = oci_fetch_array($this->result, OCI_BOTH))
            {
                if ($utf8)
                {
                    $rowParsed = array();
                    foreach ($row as $key => $value)
                    {
                        $rowParsed[$key] = (!is_object($value)) ? utf8_encode($value) : $value;
                    }
                    $data[] = $rowParsed;
                }
                else
                    $data[] = $row;
            }
        }
        else
            throw new Exception('There are not data in the buffer!');

        return $data;
    }

	public function __destruct()
    {
		oci_close($this->dbconn);
	}
}