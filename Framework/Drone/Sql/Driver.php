<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Sql;

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
    protected $errors = array();

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
     * @return string
     */
    public function getDbhost()
    {
        return $this->dbhost;
    }

    /**
     * @return string
     */
    public function getDbuser()
    {
        return $this->dbuser;
    }

    /**
     * @return integer
     */
    public function getNumRows()
    {
        return $this->numRows;
    }

    /**
     * @return integer
     */
    public function getNumFields()
    {
        return $this->numFields;
    }

    /**
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
     * @param string
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
     * @param string
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
     * @param string
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
     * @param string
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
     * @param string
     *
     * @return null
     */
    public function setDbchar($value)
    {
        return $this->dbchar = $value;
    }

    /**
     * Constructor for connect and set connection parameters
     *
     * It accepts the follow parameters
     *  - an array with all parameters array['Dbhost' => 'myhost', 'Dbpass' => 'mypass', ...]
     *
     * @param array
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
     * @param string
     *
     * @return void
     */
    public function error($code, $message = null)
    {
        if (!array_key_exists($code, $this->errors))
            $this->errors[$message] = (is_null($message) && array_key_exists($code, $this->messagesTemplates)) ? $this->messagesTemplates[$code] : $message;
    }

    /**
     * Abstract commit
     */
    public function commit()
    {
        //
    }

    /**
     * Abstract rollback
     */
    public function rollback()
    {
        //
    }

    /**
     * Defines start point of a transaction
     *
     * @throws Exception
     *
     * @return boolean
     */
    public function begin_transaction()
    {
        if ($this->transac_mode)
        {
            $this->errors(self::TRANSAC_STARTED);
            return false;
        }

        $this->transac_mode = true;

        return true;
    }

    /**
     * Defines end point of a transaction
     *
     * @throws Exception
     *
     * @return boolean
     */
    public function end_transaction()
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