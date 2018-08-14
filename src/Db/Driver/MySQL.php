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
 * MySQL class
 *
 * This is a database driver class to connect to MySQL
 */
class MySQL extends AbstractDriver implements DriverInterface
{
    /**
     * {@inheritDoc}
     */
    public function __construct($options)
    {
        if (!array_key_exists("dbchar", $options))
            $options["dbchar"] = "utf8";

        parent::__construct($options);

        $auto_connect = array_key_exists('auto_connect', $options) ? $options["auto_connect"] : true;

        if ($auto_connect)
            $this->connect();
    }

    /**
     * Connects to database
     *
     * @throws RuntimeException
     * @throws Exception\ConnectionException
     *
     * @return mysqli
     */
    public function connect()
    {
        if (!extension_loaded('mysqli'))
            throw new \RuntimeException("The Mysqli extension is not loaded");

        if (!is_null($this->dbport) && !empty($this->dbport))
            $this->dbconn = @new \mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname, $this->dbport);
        else
            $this->dbconn = @new \mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);

        if ($this->dbconn->connect_errno)
        {
            /*
             * Use ever mysqli_connect_errno() and mysqli_connect_error()
             * over $this->dbconn->errno and $this->dbconn->error to prevent
             * the warning message "Property access is not allowed yet".
             */
            throw new Exception\ConnectionException(mysqli_connect_error(), mysqli_connect_errno());
        }
        else
            $this->dbconn->set_charset($this->dbchar);

        return $this->dbconn;
    }

    /**
     * Excecutes a statement
     *
     * @param string $sql
     * @param array $params
     *
     * @throws RuntimeException
     * @throws Exception\InvalidQueryException
     *
     * @return resource
     */
    public function execute($sql, Array $params = [])
    {
        $this->numRows = 0;
        $this->numFields = 0;
        $this->rowsAffected = 0;

        $this->arrayResult = null;

        # Bound variables
        if (count($params))
        {
            $this->result = $stmt = @$this->dbconn->prepare($sql);

            if (!$stmt)
            {
                $this->error($this->dbconn->errno, $this->dbconn->error);
                throw new Exception\InvalidQueryException($this->dbconn->error, $this->dbconn->errno);
            }

            $param_values = array_values($params);

            $n_params = count($param_values);
            $bind_values = [];
            $bind_types = "";

            for ($i = 0; $i < $n_params; $i++)
            {
                if (is_string($param_values[$i]))
                    $bind_types .= 's';
                else if(is_float($param_values[$i]))
                    $bind_types .= 'd';
                # [POSSIBLE BUG] - To Future revision (What about non-string and non-decimal types ?)
                else
                    $bind_types .= 's';

                $bind_values[] = '$param_values[' . $i . ']';
            }

            $values = implode(', ', $bind_values);
            eval('$stmt->bind_param(\'' . $bind_types . '\', ' . $values . ');');

            $r = $stmt->execute();

            if ($r)
            {
                if (is_object($stmt) && get_class($stmt) == 'mysqli_stmt')
                {
                    $res = $this->result->get_result();

                    /*
                     * if $res is false then there aren't results.
                     * It is useful to prevent rollback transactions on insert statements because
                     * insert statement do not free results.
                     */
                    if ($res)
                        $this->result = $res;
                }
            }
        }
        else
        {
            $prev_error_handler = set_error_handler(['\Drone\Error\ErrorHandler', 'errorControlOperator'], E_ALL);

            // may be throw a Fatal error (Ex: Maximum execution time)
            $r = $this->result = $this->dbconn->query($sql);

            set_error_handler($prev_error_handler);
        }

        if (!$r)
        {
            $this->error($this->dbconn->errno, $this->dbconn->error);
            throw new Exception\InvalidQueryException($this->dbconn->error, $this->dbconn->errno);
        }

        if (is_object($this->result) && property_exists($this->result, 'num_rows'))
            $this->numRows = $this->result->num_rows;

        if (is_object($this->result) && property_exists($this->result, 'field_count'))
            $this->numFields = $this->result->field_count;

        if (is_object($this->result) && property_exists($this->result, 'affected_rows'))
            $this->rowsAffected = $this->result->affected_rows;

        if ($this->transac_mode)
            $this->transac_result = is_null($this->transac_result) ? $this->result: $this->transac_result && $this->result;

        return $this->result;
    }

    /**
     * {@inheritDoc}
     */
    public function commit()
    {
        return $this->dbconn->commit();
    }

    /**
     * {@inheritDoc}
     */
    public function rollback()
    {
        return $this->dbconn->rollback();
    }

    /**
     * {@inheritDoc}
     */
    public function disconnect()
    {
        parent::disconnect();
        return $this->dbconn->close();
    }

    /**
     * {@inheritDoc}
     */
    public function beginTransaction()
    {
        parent::beginTransaction();
        $this->dbconn->autocommit(false);
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

        if ($this->result && !is_bool($this->result))
        {
            while ($row = $this->result->fetch_array(MYSQLI_BOTH))
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

    /**
     * By default __destruct() disconnects to database
     *
     * @return null
     */
    public function __destruct()
    {
        # prevent "Property access is not allowed yet" with @ on failure connections
        if ($this->dbconn !== false && !is_null($this->dbconn))
            @$this->dbconn->close();
    }
}