<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Db\Driver;

class MySQL extends AbstractDriver implements DriverInterface
{
    use \Drone\Error\ErrorTrait;

    /**
     * Constructor for MySql driver
     *
     * @param array $options
     *
     * @throws RuntimeException
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
     *
     * @return boolean
     */
    public function connect()
    {
        if (!extension_loaded('mysqli'))
            throw new \RuntimeException("The Mysqli extension is not loaded");

        $this->dbconn = @new \mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);

        if ($this->dbconn->connect_errno)
        {
            /*
             * Sets $this->dbconn to NULL after trying to connect!. If NULL is not assigned to $this->dbconn,
             * a Warning message (Property access is not allowed yet) is showed after property is called.
             */
            $this->dbconn = null;
            $this->error($this->dbconn->connect_errno, $this->dbconn->connect_error);

            return false;
        }
        else
            $this->dbconn->set_charset($this->dbchar);

        return true;
    }

    /**
     * Excecutes a statement
     *
     * @return boolean
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
                    $this->result = $this->result->get_result();
            }
        }
        else
            $r = $this->result = @$this->dbconn->query($sql);

        if (!$r)
        {
            $this->error($this->dbconn->error);
            return false;
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
     * Commit definition
     *
     * @return boolean
     */
    public function commit()
    {
        return $this->dbconn->commit();
    }

    /**
     * Rollback definition
     *
     * @return boolean
     */
    public function rollback()
    {
        return $this->dbconn->rollback();
    }

    /**
     * Begins a transaction in SQLServer
     *
     * @return boolean
     */
    public function beginTransaction()
    {
        parent::beginTransaction();
        return $this->dbconn->autocommit(false);
    }

    /**
     * Closes the connection
     *
     * @return boolean
     */
    public function disconnect()
    {
        if ($this->dbconn !== false && !is_null($this->dbconn))
            return $this->dbconn->close();

        return true;
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

    public function __destruct()
    {
        if ($this->dbconn !== false && !is_null($this->dbconn))
            $this->dbconn->close();
    }
}