<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Db\Driver;

/**
 * Oracle class
 *
 * This is a database driver class to connect to Oracle
 */
class Oracle extends AbstractDriver implements DriverInterface
{
    /**
     * {@inheritdoc}
     *
     * @var resource
     */
    protected $dbconn;

    /**
     * {@inheritdoc}
     *
     * @param array $options
     */
    public function __construct($options)
    {
        $this->driverName = 'Oci8';

        if (!array_key_exists("dbchar", $options)) {
            $options["dbchar"] = "AL32UTF8";
        }

        parent::__construct($options);

        $auto_connect = array_key_exists('auto_connect', $options) ? $options["auto_connect"] : true;

        if ($auto_connect) {
            $this->connect();
        }
    }

    /**
     * Connects to database
     *
     * @throws RuntimeException
     * @throws Exception\ConnectionException
     *
     * @return resource
     */
    public function connect()
    {
        if (!extension_loaded('oci8')) {
            throw new \RuntimeException("The Oci8 extension is not loaded");
        }

        $connection_string = (is_null($this->dbhost) || empty($this->dbhost))
            ? $this->dbname
            :
                (!is_null($this->dbport) && !empty($this->dbport))
                    ? $this->dbhost .":". $this->dbport ."/". $this->dbname
                    : $this->dbhost ."/". $this->dbname;

        $conn = @oci_connect($this->dbuser, $this->dbpass, $connection_string, $this->dbchar);

        if ($conn === false) {
            $error = oci_error();
            throw new Exception\ConnectionException($error["message"], $error["code"]);
        }

        $this->dbconn = $conn;

        return $this->dbconn;
    }

    /**
     * Excecutes a statement
     *
     * @param string $sql
     * @param array $params
     *
     * @throws Exception\InvalidQueryException
     *
     * @return resource
     */
    public function execute($sql, array $params = [])
    {
        $this->numRows = 0;
        $this->numFields = 0;
        $this->rowsAffected = 0;

        $this->arrayResult = null;

        $this->result = $stid = @oci_parse($this->dbconn, $sql);

        if (!$stid) {
            $error = $stid ? oci_error($stid) : oci_error();

            if (!empty($error)) {
                $error = [
                    "message" => "Could not prepare statement!",
                ];

                $this->error($error["message"]);
            } else {
                $this->error($error["code"], $error["message"]);
            }

            if (array_key_exists("code", $error)) {
                throw new Exception\InvalidQueryException($error["message"], $error["code"]);
            } else {
                throw new Exception\InvalidQueryException($error["message"]);
            }
        }

        # Bound variables
        if (count($params)) {
            $param_keys   = array_keys($params);
            $param_values = array_values($params);

            $param_count = count($params);

            for ($i = 0; $i < $param_count; $i++) {
                oci_bind_by_name($stid, $param_keys[$i], $param_values[$i], -1);
            }
        }

        $prev_error_handler = set_error_handler(['\Drone\Error\ErrorHandler', 'errorControlOperator'], E_ALL);

        // may be throw a Fatal error (Ex: Maximum execution time)
        $r = ($this->transac_mode) ? oci_execute($stid, OCI_NO_AUTO_COMMIT) : oci_execute($stid, OCI_COMMIT_ON_SUCCESS);

        set_error_handler($prev_error_handler);

        if (!$r) {
            $error = oci_error($stid);
            $this->error($error["code"], $error["message"]);

            throw new Exception\InvalidQueryException($error["message"], $error["code"]);
        }

        # This should be before of getArrayResult() because oci_fetch() is incremental.
        $this->rowsAffected = oci_num_rows($stid);

        $rows = $this->getArrayResult();

        $this->numRows = count($rows);
        $this->numFields = oci_num_fields($stid);

        if ($this->transac_mode) {
            $this->transac_result = is_null($this->transac_result) ? $stid : $this->transac_result && $stid;
        }

        $this->result = $stid;

        return $this->result;
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        return oci_commit($this->dbconn);
    }

    /**
     * {@inheritdoc}
     */
    public function rollback()
    {
        return oci_rollback($this->dbconn);
    }

    /**
     * {@inheritdoc}
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
        $data = [];

        if ($this->result) {
            while (($row = @oci_fetch_array($this->result, OCI_BOTH + OCI_RETURN_NULLS)) !== false) {
                $data[] = $row;
            }
        } else {             # This error is thrown because of 'execute' method has not been executed.
            throw new \LogicException('There are not data in the buffer!');
        }

        $this->arrayResult = $data;

        return $data;
    }

    /**
     * By default __destruct() disconnects to database
     *
     * @return null
     */
    public function __destruct()
    {
        if ($this->dbconn) {
            oci_close($this->dbconn);
        }
    }
}
