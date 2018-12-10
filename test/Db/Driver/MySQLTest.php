<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace DroneTest\Util;

use Drone\Db\Driver\MySQL;
use Drone\Db\Driver\Exception;
use PHPUnit\Framework\TestCase;

class MySQLTest extends TestCase
{
    /**
     * Database parameters
     */
    private $options = [
        "dbhost"       => "localhost",
        "dbuser"       => "root",
        "dbpass"       => "",
        "dbname"       => "test",
        "dbchar"       => "utf8",
        "dbport"       => "3306",
        "auto_connect" => false
    ];

    /*
    |--------------------------------------------------------------------------
    | Stablishing connections
    |--------------------------------------------------------------------------
    |
    | The following tests are related to the connection methods and its
    | exceptions and returned values.
    |
    */

    /**
     * Tests if we can connect to the database server
     *
     * @return null
     */
    public function testCanStablishConnection()
    {
        $conn = new MySQL($this->options);

        $mysqliObject = $conn->connect();

        $this->assertInstanceOf('\mysqli', $mysqliObject);
        $this->assertTrue($conn->isConnected());
    }

    /**
     * Tests if we can disconnect from the database server
     *
     * @return null
     */
    public function testCanDownConnection()
    {
        $conn = new MySQL($this->options);

        $conn->connect();
        $result = $conn->disconnect();

        $this->assertNotTrue($conn->isConnected());
        $this->assertTrue($result);
    }

    /**
     * Tests if we can disconnect from server when there is not a connection stablished
     *
     * @return null
     */
    public function testCannotDisconectWhenNotConnected()
    {
        $options = [
            "dbhost"       => "localhost",
            "dbuser"       => "root",
            "dbpass"       => "",
            "dbname"       => "test",
            "dbchar"       => "utf8",
            "dbport"       => "3306",
            "auto_connect" => false
        ];

        $conn = new MySQL($options);

        $errorObject = null;

        try {
            $conn->disconnect();
        }
        catch (\Exception $e)
        {
            $errorObject = ($e instanceof \LogicException);
        }
        finally
        {
            $this->assertNotTrue($conn->isConnected());
            $this->assertTrue($errorObject, $e->getMessage());
        }
    }

    /**
     * Tests if we can reconnect to the database server
     *
     * @return null
     */
    public function testCanStablishConnectionAgain()
    {
        $conn = new MySQL($this->options);

        $conn->connect();
        $mysqliObject = $conn->reconnect();

        $this->assertInstanceOf(\mysqli, $mysqliObject);
        $this->assertTrue($conn->isConnected());
    }

    /**
     * Tests if we can reconnect to the database server when there is not a connection stablished
     *
     * @return null
     */
    public function testCannotStablishConnectionAgain()
    {
        $conn = new MySQL($this->options);

        $errorObject = null;

        try {
            $conn->reconnect();
        }
        catch (\Exception $e)
        {
            $errorObject = ($e instanceof \LogicException);
        }
        finally
        {
            $this->assertTrue($errorObject, $e->getMessage());
            $this->assertNotTrue($conn->isConnected());
        }
    }

    /**
     * Tests if a connection failed throws a ConnectionException
     *
     * @return null
     */
    public function testCannotStablishConnection()
    {
        $this->options["dbhost"] = "myserver";   // this server does not exists

        $conn = new MySQL($this->options);

        $mysqliObject = $errorObject = null;

        try {
            $mysqliObject = $conn->connect();
        }
        catch (\Exception $e)
        {
            $errorObject = ($e instanceof ConnectionException);
        }
        finally
        {
            $this->assertTrue($errorObject, $e->getMessage());
            $this->assertNotTrue($conn->isConnected());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Quering and Transactions
    |--------------------------------------------------------------------------
    |
    | The following tests are related to query and transaction operations and its
    | exceptions and returned values.
    |
    */

    /**
     * Tests if we can execute DDL statements
     *
     * @return null
     */
    public function textCanExecuteDLLStatement()
    {
        $this->options["auto_connect"] = true;

        $conn = new MySQL($this->options);
        $sql = "CREATE TABLE MYTABLE (ID INTEGER AUTO_INCREMENT, DESCRIPTION VARCHAR(100))";
        $result = $conn->execute($sql);

        $this->assertTrue(is_resource($result));

        # properties modified by execute() method
        $this->assertEquals(0, $conn->getNumRows());
        $this->assertEquals(0, $conn->getNumFields());
        $this->assertEquals(0, $conn->getRowsAffected());
    }

    /**
     * Tests if we can execute DML statements
     *
     * @return null
     */
    public function textCanExecuteDMLStatement()
    {
        $this->options["auto_connect"] = true;

        $conn = new MySQL($this->options);
        $sql = "INSERT INTO MYTABLE VALUES (1, 'Hello world!')";
        $result = $conn->execute($sql);

        $this->assertTrue(is_resource($result));

        # properties modified by execute() method
        $this->assertEquals(0, $conn->getNumRows());
        $this->assertEquals(0, $conn->getNumFields());
        $this->assertEquals(1, $conn->getRowsAffected());
    }
}