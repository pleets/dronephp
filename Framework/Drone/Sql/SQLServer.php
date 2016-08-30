<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/Drone
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Sql;

class SQLServer
{
    private $dbhost = '';                           # default host
    private $dbuser = '';                           # default username
    private $dbpass = '';                           # default password
    private $dbname = '';                           # default database
    private $dbchar = '';                           # default charset (SQLSRV_ENC_CHAR, UTF-8)

    private $dbconn = null;                         # connection

    private $errors = array();                      # Errors

    private $numRows;                               # Rows returned
    private $numFields;                             # Fields returned
    private $rowsAffected;                          # Rows affected

    private $result;                                # latest result (current buffer)
    private $arrayResult;                           # result array (SELECT statements)

    private $transac_mode = false;                  # transaction process
    private $transac_result = null;                 # result of transactions

    public function __construct($dbhost = null, $dbuser = null, $dbpass = null, $dbname = null, $auto_connect = true, $dbchar = "SQLSRV_ENC_CHAR")
    {
        $this->dbhost = is_null($dbhost) ? !defined('DBHOST') ? $this->dbhost : @DBHOST : $dbhost;
        $this->dbuser = is_null($dbuser) ? !defined('DBUSER') ? $this->dbuser : @DBUSER : $dbuser;
        $this->dbpass = is_null($dbpass) ? !defined('DBPASS') ? $this->dbpass : @DBPASS : $dbpass;
        $this->dbname = is_null($dbname) ? !defined('DBNAME') ? $this->dbname : @DBNAME : $dbname;

        $this->dbchar = is_null($dbchar) ? !defined('DBCHAR') ? $this->dbchar : @DBCHAR : $dbchar;

        if ($auto_connect)
        {
    		$db_info = array("Database" => $this->dbname, "UID" => $this->dbuser, "PWD" => $this->dbpass, "CharacterSet" => $this->dbchar);
    		$this->dbconn = sqlsrv_connect($this->dbhost, $db_info);

            if ($this->dbconn === false)
            {
                $this->errors = sqlsrv_errors();

                if (count($this->errors))
                    throw new \Exception($this->errors[0]["message"], $this->errors[0]["code"]);
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
        $db_info = array("Database" => $this->dbname, "UID" => $this->dbuser, "PWD" => $this->dbpass, "CharacterSet" => $this->dbchar);
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

        $this->arrayResult = null;

        $this->result = sqlsrv_query($this->dbconn, $sql, $params, array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

        if (!$this->result)
        {
            $this->errors = sqlsrv_errors();

            if (count($this->errors))
                throw new \Exception($this->errors["message"], $this->errors["code"]);
            else
                throw new \Exception("Unknown error!");
        }

        $this->getArrayResult();

        $this->numRows = sqlsrv_num_rows($this->result);
        $this->numFields = sqlsrv_num_fields($this->result);
        $this->rowsAffected = sqlsrv_rows_affected($this->result);

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

    private function toArray()
    {
        $data = array();

        if ($this->result)
        {
            while ($row = sqlsrv_fetch_array($this->result))
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
        if ($this->dbconn)
		    sqlsrv_close($this->dbconn);
	}
}