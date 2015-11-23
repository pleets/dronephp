<?php

/*
 * MySQL class
 * http://www.pleets.org
 *
 * Copyright 2014, Pleets Apps
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */

namespace Pleets\Sql;

class Mysql {

    private $dbconn = NULL;                 # connection (Object Sql)
    private $buffer = NULL;                 # buffer

    public function __construct($_dbhost=NULL,$_dbuser=NULL,$_dbpass=NULL,$_dbname=NULL) {

        # Default parameters
        $this->dbhost = '';               # default host
        $this->dbuser = '';               # default username
        $this->dbpass = '';               # default password
        $this->dbname = '';               # default database

        /* Properties assigment. The order for this is the following:
        - If $_dbhost parameter in the constructor function is not NULL, it is assigned to dbhost propertie.
        - If $_dbhost parameter is NULL and exists a HOST constant in the namespace, it is assigned to dbhost propertie.
        - If $_dbhost parameter is NULL and HOST constant does not exists, default parameter is assigned to dbhost propertie.
        - Previos rules are same for dbuser, dbpass and dbname properties. */

        $this->dbhost = is_null($_dbhost) ? !defined('HOST') ? $this->dbhost : @HOST : $_dbhost;
        $this->dbuser = is_null($_dbuser) ? !defined('USER') ? $this->dbuser : @USER : $_dbuser;
        $this->dbpass = is_null($_dbpass) ? !defined('PASS') ? $this->dbpass : @PASS : $_dbpass;
        $this->dbname = is_null($_dbname) ? !defined('NAME') ? $this->dbname : @NAME : $_dbname;

        # Update connection
        $conn = new \mysqli($this->dbhost,$this->dbuser,$this->dbpass,$this->dbname);
        $this->dbconn = ($conn->connect_errno) ? false: $conn;
    }

    # Catch buffer
    public function get() {
        return $this->buffer;
    }

    public function query($query)
    {
        if ($result = $this->dbconn->query($query))
        {
            $this->buffer = $result;
            return $result;
        }
        else
            return false;
    }

    public function multiquery($query)
    {
        if ($result = $this->dbconn->multi_query($query)) {
            $this->buffer = $result;
            return $this;
        }
        else
        return false;
    }

    public function fetch($type='array',$result_type='assoc')
    {
        $resultType = $result_type;       # save a reference to the initial value

        # Define the result type to fetch array
        switch (strtolower($result_type))
        {
            case 'assoc':
                $result_type = MYSQLI_ASSOC;
                break;
            case 'num':
                $result_type = MYSQLI_NUM;
                break;
            case 'both':
                $result_type = MYSQLI_BOTH;
                break;
            default:
                return false;
                break;
        }

        # Data type to return
        switch (strtolower($type))
        {
            case 'array':
                $array = array();

                if ($this->buffer->num_rows > 1)
                {
                    switch ($resultType)
                    {
                        case 'assoc':
                            # Fetch a result row as an associative array
                            while ($row = $this->buffer->fetch_assoc())
                            {
                                $array[] = $row;
                            }

                            if (count($array) === 0)
                                return false;
                            break;
                        case 'num':
                            # Get a result row as an enumerated array
                            while ($row = $this->buffer->fetch_row())
                            {
                                $array[] = $row;
                            }

                            if (count($array) === 0)
                                return false;
                            break;
                        case 'both':
                            # Get a result row as an enumerated array
                            while ($row = $this->buffer->fetch_array()) {
                                $array[] = $row;
                            }

                            if (count($array) === 0)
                                return false;
                            break;
                        default:
                            return false;
                            break;
                    }
                }
                else
                    $array = $this->buffer->fetch_array($result_type);

                return $array;
                break;
            case 'object':
                $array = array();

                while ($row = $this->buffer->fetch_object())
                {
                    $array[] = $row;
                }

                if (count($array) === 0)
                    return false;

                return $array;
                break;
            default:
                return false;
                break;
        }
    }

    public function toArray()
    {
        return $this->fetch();
    }

    public function __destruct() {
        if ($this->dbconn !== false)
        $this->dbconn->close();
    }
}