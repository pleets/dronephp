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

class Oracle extends Driver implements DriverInterface
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
            $options["dbchar"] = "AL32UTF8";

        parent::__construct($options);

        $auto_connect = array_key_exists('auto_connect', $options) ? $options["auto_connect"] : true;

        if ($auto_connect)
        {
            $connection_string = (is_null($this->dbhost) || empty($this->dbhost)) ? $this->dbname : $this->dbhost ."/". $this->dbname;
            $this->dbconn = @oci_connect($this->dbuser,  $this->dbpass, $connection_string, $this->dbchar);

            if ($this->dbconn === false)
            {
                $error = oci_error();

                $this->error(
                    $error["code"], $error["message"]
                );

                if (count($this->errors))
                    throw new Exception($error["message"], $error["code"]);
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
        $connection_string = (is_null($this->dbhost) || empty($this->dbhost)) ? $this->dbname : $this->dbhost ."/". $this->dbname;
        $this->dbconn = @oci_connect($this->dbuser,  $this->dbpass, $connection_string, $this->dbchar);

        if ($this->dbconn === false)
        {
            $error = oci_error();

            $this->error(
                $error["code"], $error["message"]
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
    public function query($sql, Array $params = array())
    {
        $this->numRows = 0;
        $this->numFields = 0;
        $this->rowsAffected = 0;

        $this->arrayResult = null;

        $this->result = $stid = oci_parse($this->dbconn, $sql);

        # Bound variables
        if (count($params))
        {
            foreach ($params as $var => $value)
            {
                oci_bind_by_name($stid, $var, $value);
            }
        }

        $r = ($this->transac_mode) ? @oci_execute($stid, OCI_NO_AUTO_COMMIT) : @oci_execute($stid,  OCI_COMMIT_ON_SUCCESS);

        if (!$r)
        {
            $error = oci_error($this->result);

            $this->error(
                $error["code"], $error["message"]
            );

            if (count($this->errors))
                throw new Exception($error["message"], $error["code"]);
            else
                throw new Exception("Unknown error!");
        }

        # This should be before of getArrayResult() because oci_fetch() is incremental.
        $this->rowsAffected = oci_num_rows($stid);

        $rows = $this->getArrayResult();

        $this->numRows = count($rows);
        $this->numFields = oci_num_fields($stid);

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
     * Commit definition
     *
     * @return boolean
     */
    public function commit()
    {
        return oci_commit($this->dbconn);
    }

    /**
     * Rollback definition
     *
     * @return boolean
     */
    public function rollback()
    {
        return oci_rollback($this->dbconn);
    }

    /**
     * Close connection
     *
     * @return boolean
     */
    public function cancel()
    {
        oci_cancel($this->result);
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
            while ( ($row = @oci_fetch_array($this->result, OCI_BOTH + OCI_RETURN_NULLS)) !== false )
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
		    oci_close($this->dbconn);
	}
}