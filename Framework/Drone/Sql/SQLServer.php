<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Sql;

use Exception;

class SQLServer extends Driver implements DriverInterface
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
     * Constructor for Oracle driver
     *
     * @param array
     *
     * @throws Exception
     */
    public function __construct($options)
    {
        if (!array_key_exists("Dbchar", $options))
            $options["dbchar"] = "SQLSRV_ENC_CHAR";

        parent::__construct($options);

        $auto_connect = array_key_exists('auto_connect', $options) ? $options["auto_connect"] : true;

        if ($auto_connect)
        {
    		$db_info = array("Database" => $this->dbname, "UID" => $this->dbuser, "PWD" => $this->dbpass, "CharacterSet" => $this->dbchar);
    		$this->dbconn = sqlsrv_connect($this->dbhost, $db_info);

            if ($this->dbconn === false)
            {
                $errors = sqlsrv_errors();

                foreach ($errors as $error)
                {
                    $this->error(
                        $error["code"], $error["message"]
                    );
                }

                if (count($this->errors))
                    throw new Exception($errors[0]["message"], $errors[0]["code"]);
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
        $db_info = array("Database" => $this->dbname, "UID" => $this->dbuser, "PWD" => $this->dbpass, "CharacterSet" => $this->dbchar);
        $this->dbconn = sqlsrv_connect($this->dbhost, $db_info);

        if ($this->dbconn === false)
        {
            $errors = sqlsrv_errors();

            foreach ($errors as $error)
            {
                $this->error(
                    $error["code"], $error["message"]
                );
            }

            return false;
        }

        return true;
    }

    /**
     * Excecutes a statement
     *
     * @return boolean
     */
    public function query($sql, Array $params = array())
    {
        $this->numRows = 0;
        $this->numFields = 0;
        $this->rowsAffected = 0;

        $this->arrayResult = null;

        $this->result = sqlsrv_query($this->dbconn, $sql, $params, array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

        if (!$this->result)
        {
            $errors = sqlsrv_errors();

            foreach ($errors as $error)
            {
                $this->error(
                    $error["code"], $error["message"]
                );
            }

            if (count($this->errors))
                throw new Exception($errors[0]["message"], $[0]["code"]);
            else
                throw new Exception("Unknown error!");
        }

        $this->getArrayResult();

        $this->numRows = sqlsrv_num_rows($this->result);
        $this->numFields = sqlsrv_num_fields($this->result);
        $this->rowsAffected = sqlsrv_rows_affected($this->result);

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

        $this->end_transaction();
    }

    /**
     * Commit definition
     *
     * @return boolean
     */
    public function commit()
    {
        return sqlsrv_commit($this->dbconn);
    }

    /**
     * Rollback definition
     *
     * @return boolean
     */
    public function rollback()
    {
        return sqlsrv_rollback($this->dbconn);
    }

    /**
     * Begin a transaction in SQLServer
     *
     * @return boolean
     */
    public function begin_transaction()
    {
        if (sqlsrv_begin_transaction($this->dbconn) === false)
        {
            $errors = sqlsrv_errors();

            foreach ($errors as $error)
            {
                $this->error(
                    $error["code"], $error["message"]
                );
            }

            return false;
        }

        return parent::begin_transaction();
    }

    /**
     * Close connection
     *
     * @return boolean
     */
    public function cancel()
    {
        sqlsrv_cancel($this->result);
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