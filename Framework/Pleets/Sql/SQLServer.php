<?php

/*
 * SQLServer class
 * http://www.pleets.org
 *
 * Copyright 2014, Pleets Apps
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */

namespace Pleets\Sql;

class SQLServer
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

		$db_info = array("Database" => $this->dbname, "UID" => $this->dbuser, "PWD" => $this->dbpass);
		$this->dbconn = sqlsrv_connect($this->dbhost, $db_info);

        if ($this->dbconn === false)
        {
            $this->errors = sqlsrv_errors();
                throw new \Exception("The database connection could not be started!");
        }
	}

    /* Getters */

    public function getHostname() { return $this->dbhost; }
    public function getUsername() { return $this->dbuser; }
    public function getDatabase() { return $this->dbname; }

    public function getErrors() { return $this->errors; }

    /* Setters */

    public function setDatabase($dbname) { $this->dbname = $dbname; }

    public function reconnect()
    {
        $db_info = array("Database" => $this->dbname, "UID" => $this->dbuser, "PWD" => $this->dbpass);
        $this->dbconn = sqlsrv_connect($this->dbhost, $db_info);

        if ($this->dbconn === false)
        {
            $this->errors = sqlsrv_errors();
            throw new \Exception("The database connection could not be started!");
        }

        return $this;
    }

    public function query($sql, Array $params = array())
    {
        $this->numRows = 0;
        $this->numFields = 0;
        $this->rowsAffected = 0;

        $this->result = sqlsrv_query($this->dbconn, $sql, $params, array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

        if ($this->result)
        {
            $this->numRows = sqlsrv_num_rows($this->result);
            $this->numFields = sqlsrv_num_fields($this->result);
            $this->rowsAffected = sqlsrv_rows_affected($this->result);
        }
        else {
            $this->errors = sqlsrv_errors();
            throw new \Exception("Could not perform the query to the database");
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
        if (sqlsrv_begin_transaction($this->dbconn) === false)
            throw new \Exception(sqlsrv_errors());

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
            sqlsrv_commit($this->dbconn);
        else {
            sqlsrv_rollback($this->dbconn);
            return false;
        }

        $this->result = $this->transac_result;

        $this->transac_result = null;
        $this->transac_mode = false;

        return true;
    }

    public function cancel()
    {
        sqlsrv_cancel($this->result);
    }

    public function toArray(Array $settings = array())
    {
        $utf8 = (isset($settings['encode_utf8']) && $settings['encode_utf8'] == true);
        $data = array();

        if ($this->result)
        {
            while ($row = sqlsrv_fetch_array($this->result))
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
		sqlsrv_close($this->dbconn);
	}
}