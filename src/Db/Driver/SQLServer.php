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
 * SQLServer class
 *
 * This is a database driver class to connect to SQLServer
 */
class SQLServer extends AbstractDriver implements DriverInterface
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
        $this->driverName = 'Sqlsrv';

        if (!array_key_exists("dbchar", $options)) {
            $options["dbchar"] = "UTF-8";
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
        if (!extension_loaded('sqlsrv')) {
            throw new \RuntimeException("The Sqlsrv extension is not loaded");
        }

        if (!is_null($this->dbport) && !empty($this->dbport)) {
            $this->dbhost .= ', ' . $this->dbport;
        }

        $db_info = [
	    "Database" => $this->dbname,
	    "UID" => $this->dbuser,
	    "PWD" => $this->dbpass,
	    "CharacterSet" => $this->dbchar,
        ];
        $conn = sqlsrv_connect($this->dbhost, $db_info);

        if ($conn === false) {
            $errors = sqlsrv_errors();

            $previousException = null;

            foreach ($errors as $error) {
                $previousException = new Exception\ConnectionException(
                    $error["message"],
                    $error["code"],
                    $previousException
                );
            }

            throw $previousException;
        }

        $this->dbconn = $conn;

        return $this->dbconn;
    }

    /**
     * Executes a statement
     *
     * @param string $sql
     * @param array $params
     *
     * @throws Exception\InvalidQueryException
     *
     * @return mixed
     */
    public function execute($sql, array $params = [])
    {
        $this->numRows = 0;
        $this->numFields = 0;
        $this->rowsAffected = 0;

        $this->arrayResult = null;

        // (/**/)
        $clean_code = preg_replace('/(\s)*\/\*([^*]|[\r\n]|(\*+([^*\/]|[\r\n])))*\*+\//', '', $sql);

        // (--)
        $clean_code = preg_replace('/(\s)*--.*\n/', "", $clean_code);

        # clean other characters starting senteces
        $clean_code = preg_replace('/^[\n\t\s]*/', "", $clean_code);

        # indicates if SQL is a selection statement
        $isSelectStm = (preg_match('/^SELECT/i', $clean_code));

        # indicates if SQL is a insert statement
        $isInsertStm = (preg_match('/^INSERT/i', $clean_code));

        # indicates if SQL is a insert statement
        $isUpdateStm = (preg_match('/^UPDATE/i', $clean_code));

        # indicates if SQL is a insert statement
        $isDeleteStm = (preg_match('/^DELETE/i', $clean_code));

        # Bound variables
        if (count($params)) {
            $stmt = sqlsrv_prepare($this->dbconn, $sql, $params);

            if (!$stmt) {
                $errors = sqlsrv_errors();

                foreach ($errors as $error) {
                    $this->error($error["code"], $error["message"]);
                }

                throw new Exception\InvalidQueryException($error["message"], $error["code"]);
            }

            $exec = sqlsrv_execute($stmt);
        } else {
            if ($isSelectStm) {
                $exec = $this->result = sqlsrv_query(
                    $this->dbconn,
                    $sql,
                    $params,
                    ["Scrollable" => SQLSRV_CURSOR_KEYSET]
                );
            } else {
                $exec = $this->result = sqlsrv_query($this->dbconn, $sql, $params);
            }
        }

        if ($exec === false) {
            $errors = sqlsrv_errors();

            foreach ($errors as $error) {
                $this->error($error["code"], $error["message"]);
            }

            throw new Exception\InvalidQueryException($error["message"], $error["code"]);
        }

        $this->getArrayResult();

        $this->numRows = sqlsrv_has_rows($this->result) ? sqlsrv_num_rows($this->result) : $this->numRows;
        $this->numFields = sqlsrv_num_fields($this->result);

        if ($isInsertStm || $isUpdateStm || $isDeleteStm) {
            $this->rowsAffected = sqlsrv_rows_affected($this->result);
        }

        if ($this->transac_mode) {
            $this->transac_result = is_null($this->transac_result)
                ? $this->result
                : $this->transac_result && $this->result;
        }

        return $this->result;
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        return sqlsrv_commit($this->dbconn);
    }

    /**
     * {@inheritdoc}
     */
    public function rollback()
    {
        return sqlsrv_rollback($this->dbconn);
    }

    /**
     * {@inheritdoc}
     */
    public function disconnect()
    {
        parent::disconnect();

        return sqlsrv_close($this->dbconn);
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction()
    {
        if (sqlsrv_begin_transaction($this->dbconn) === false) {
            $errors = sqlsrv_errors();

            foreach ($errors as $error) {
                $this->error($error["code"], $error["message"]);
            }

            throw new \RuntimeException("Could not begin transaction");
        }

        parent::beginTransaction();
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
            while ($row = sqlsrv_fetch_array($this->result)) {
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
            sqlsrv_close($this->dbconn);
        }
    }
}
