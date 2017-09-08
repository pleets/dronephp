<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

namespace Drone\Db\Driver;

class Oracle extends AbstractDriver implements DriverInterface
{
    use \Drone\Error\ErrorTrait;

    /**
     * Constructor for Oracle driver
     *
     * @param array $options
     *
     * @throws RuntimeException if connect() found an error
     */
    public function __construct($options)
    {
        if (!array_key_exists("dbchar", $options))
            $options["dbchar"] = "AL32UTF8";

        parent::__construct($options);

        $auto_connect = array_key_exists('auto_connect', $options) ? $options["auto_connect"] : true;

        if ($auto_connect)
            $this->connect();
    }

    /**
     * Connects to database
     *
     * @throws RuntimeException
     *
     * @return resource
     */
    public function connect()
    {
        if (!extension_loaded('oci8'))
            throw new \RuntimeException("The Oci8 extension is not loaded");

        $connection_string = (is_null($this->dbhost) || empty($this->dbhost)) ? $this->dbname : $this->dbhost ."/". $this->dbname;
        $this->dbconn = @oci_connect($this->dbuser,  $this->dbpass, $connection_string, $this->dbchar);

        if ($this->dbconn === false)
        {
            $error = oci_error();
            $this->error($error["code"], $error["message"]);

            throw new \RuntimeException("Could not connect to Database");
        }

        return $this->dbconn;
    }

    /**
     * Excecutes a statement
     *
     * @param string $sql
     * @param array $params
     *
     * @throws RuntimeException
     *
     * @return resource
     */
    public function execute($sql, Array $params = [])
    {
        $this->numRows = 0;
        $this->numFields = 0;
        $this->rowsAffected = 0;

        $this->arrayResult = null;

        $this->result = $stid = oci_parse($this->dbconn, $sql);

        # Bound variables
        if (count($params))
        {
            $param_keys   = array_keys($params);
            $param_values = array_values($params);

            for ($i = 0; $i < count($params); $i++)
            {
                oci_bind_by_name($stid, $param_keys[$i], $param_values[$i], -1);
            }
        }

        $r = ($this->transac_mode) ? @oci_execute($stid, OCI_NO_AUTO_COMMIT) : @oci_execute($stid,  OCI_COMMIT_ON_SUCCESS);

        if (!$r)
        {
            $error = oci_error($this->result);
            $this->error($error["code"], $error["message"]);

            throw new \RuntimeException("Could not execute query");
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
     * Closes the connection
     *
     * @throws LogicException
     *
     * @return boolean
     */
    public function disconnect()
    {
        parent::disconnect();
        return oci_close($this->dbconn);
    }

    /**
     * Returns an array with the rows fetched
     *
     * @throws LogicException
     *
     * @return array
     */
    protected function toArray()
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
            /*
             * "This kind of exception should lead directly to a fix in your code"
             * So much production tests tell us this error is throwed because developers
             * execute toArray() before execute().
             *
             * Ref: http://php.net/manual/en/class.logicexception.php
             */
            throw new \LogicException('There are not data in the buffer!');

        $this->arrayResult = $data;

        return $data;
    }

    public function __destruct()
    {
        if ($this->dbconn)
            oci_close($this->dbconn);
    }
}