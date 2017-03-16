<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Db\Driver;

abstract class Driver
{
    /**#@+
     * Transaction constants
     * @var string
     */
    const TRANSAC_STARTED = 'transacStarted';
    const EMPTY_TRANSAC   = 'emptyTransac';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messagesTemplates = [
        self::TRANSAC_STARTED => 'Transaction mode has already started',
        self::EMPTY_TRANSAC   => 'There are not querys in this transaction'
    ];

    /**
     * @var string
     */
    protected $dbhost;

    /**
     * @var string
     */
    protected $dbuser;

    /**
     * @var string
     */
    protected $dbpass;

    /**
     * @var string
     */
    protected $dbname;

    /**
     * @var string
     */
    protected $dbchar;

    /**
     * Connection identifier
     *
     * @var resource|boolean
     */
    protected $dbconn;

    /**
     * Failure messages
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Rows returned on query() method
     *
     * @var integer
     */
    protected $numRows;

    /**
     * Fields returned on query() method
     *
     * @var integer
     */
    protected $numFields;

    /**
     * Rows affected returned on query() method
     *
     * @var integer
     */
    protected $rowsAffected;

    /**
     * Statement handle
     *
     * @var resource|boolean
     */
    protected $result;

    /**
     * Data stored in select statements
     *
     * @var array
     */
    protected $arrayResult;

    /**
     * Defines if consecutive querys are part of a transaction
     *
     * @var boolean
     */
    protected $transac_mode = false;

    /**
     * @var boolean
     */
    protected $transac_result = null;

    /**
     * Returns the dbhost attribute
     *
     * @return string
     */
    public function getDbhost()
    {
        return $this->dbhost;
    }

    /**
     * Returns the dbuser attribute
     *
     * @return string
     */
    public function getDbuser()
    {
        return $this->dbuser;
    }

    /**
     * Returns the dbname attribute
     *
     * @return string
     */
    public function getDbname()
    {
        return $this->dbname;
    }

    /**
     * Returns the numRows attribute
     *
     * @return integer
     */
    public function getNumRows()
    {
        return $this->numRows;
    }

    /**
     * Returns the numFields attribute
     *
     * @return integer
     */
    public function getNumFields()
    {
        return $this->numFields;
    }

    /**
     * Returns the rowsAffected attribute
     *
     * @return integer
     */
    public function getRowsAffected()
    {
        return $this->rowsAffected;
    }

    /**
     * Returns an array with all failure messages
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Sets dbhost attribute
     *
     * @param string $value
     *
     * @return null
     */
    public function setDbhost($value)
    {
        return $this->dbhost = $value;
    }

    /**
     * Sets dbuser attribute
     *
     * @param string $value
     *
     * @return null
     */
    public function setDbuser($value)
    {
        return $this->dbuser = $value;
    }

    /**
     * Sets dbpass attribute
     *
     * @param string $value
     *
     * @return null
     */
    public function setDbpass($value)
    {
        return $this->dbpass = $value;
    }

    /**
     * Sets dbname attribute
     *
     * @param string $value
     *
     * @return null
     */
    public function setDbname($value)
    {
        return $this->dbname = $value;
    }

    /**
     * Sets dbchar attribute
     *
     * @param string $value
     *
     * @return null
     */
    public function setDbchar($value)
    {
        return $this->dbchar = $value;
    }

    /**
     * Driver Constructor
     *
     * All modifiable attributes (i.e. with setter method) can be passed as key
     *
     * @param array $options
     */
    public function __construct($options)
    {
        foreach ($options as $option => $value)
        {
            if (property_exists(__CLASS__, strtolower($option)) && method_exists($this, 'set'.$option))
                $this->{'set'.$option}($value);
        }
	}

    /**
     * Adds an error
     *
     * @param string $code
     * @param string $message
     *
     * @return null
     */
    protected function error($code, $message = null)
    {
        if (!array_key_exists($code, $this->errors))
            $this->errors[$message] = (is_null($message) && array_key_exists($code, $this->messagesTemplates)) ? $this->messagesTemplates[$code] : $message;
    }

    /**
     * Returns true if there is a stablished connection
     *
     * @return boolean
     */
    public function isConnected()
    {
        return (is_resource($this->dbconn) || is_object($this->dbconn));
    }

    /**
     * Abstract connect
     *
     * @return resource
     */
    public abstract function connect();

    /**
     * Reconnects to the database
     *
     * @throws Exception
     * @return boolean
     */
    public function reconnect()
    {
        $this->disconnect();
        return $this->connect();
    }

    /**
     * Abstract commit
     *
     * @return boolean
     */
    public abstract function commit();

    /**
     * Abstract rollback
     *
     * @return boolean
     */
    public abstract function rollback();

    /**
     * Defines start point of a transaction
     *
     * @return boolean
     */
    public function beginTransaction()
    {
        if (!$this->isConnected())
            $this->connect();

        if ($this->transac_mode)
        {
            $this->error(self::TRANSAC_STARTED);
            return false;
        }

        $this->transac_mode = true;

        return true;
    }

    /**
     * Defines end point of a transaction
     *
     * @return boolean
     */
    public function endTransaction()
    {
        if (is_null($this->transac_result))
        {
            $this->error(self::EMPTY_TRANSAC);
            return false;
        }

        if ($this->transac_result)
            $this->commit();
        else {
            $this->rollback();
            return false;
        }

        $this->result = $this->transac_result;

        $this->transac_result = null;
        $this->transac_mode = false;

        return true;
    }
}