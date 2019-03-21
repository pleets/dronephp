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

use Drone\Db\Entity;
use Drone\Db\Driver\MySQL;
use Drone\Db\TableGateway\TableGateway;
use Drone\Db\TableGateway\AbstractTableGateway;
use Drone\Db\Driver\Exception\ConnectionException;
use Drone\Db\Driver\Exception\InvalidQueryException;
use PHPUnit\Framework\TestCase;

class TableGatewayTest extends TestCase
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
        "auto_connect" => false,
        "driver"       => 'Mysqli'  # needed for the DriverFactory
    ];

    /*
    |--------------------------------------------------------------------------
    | Stablishing connections
    |--------------------------------------------------------------------------
    |
    | The following tests are related to the connection mechanisms and its
    | exceptions and returned values.
    |
    */

    /**
     * Tests if we can connect to the database server through a TableGateway
     *
     * @return null
     */
    public function testCanStablishConnection()
    {
        $entity = new MyEntity();
        $gateway = new TableGateway($entity, ["default" => $this->options]);

        $mysqliObject = $gateway->getDb()->connect();

        $this->assertInstanceOf('\mysqli', $mysqliObject);
        $this->assertTrue($gateway->getDb()->isConnected());
    }

    /**
     * Tests if a failed connection throws a RuntimeException when connection exists
     *
     * @return null
     */
    public function testCannotStablishConnectionWhenExists()
    {
        $options = $this->options;
        $options["dbhost"] = 'myserver';   // this server does not exists

        $errorObject = null;

        $message = "No exception";

        try
        {
            $entity = new MyEntity();
            $gateway = new TableGateway($entity, ["default" => $options]);
        }
        catch (\Exception $e)
        {
            $errorObject = ($e instanceof \RuntimeException);
            $message = $e->getMessage();
        }
        finally
        {
            $this->assertTrue($errorObject, $message);
        }
    }

    /**
     * Tests if a failed connection throws a ConnectionException
     *
     * @return null
     */
    public function testCannotStablishConnection()
    {
        $options = $this->options;
        $options["dbhost"] = 'myserver';   // this server does not exists

        $entity = new MyEntity();
        $gateway = new TableGateway($entity, ["other" => $options]);

        $errorObject = null;

        $message = "No exception";

        try
        {
            $gateway->getDb()->connect();
        }
        catch (\Exception $e)
        {
            $errorObject = ($e instanceof ConnectionException);
            $message = $e->getMessage();
        }
        finally
        {
            $this->assertTrue($errorObject, $message);
            $this->assertNotTrue($gateway->getDb()->isConnected());
        }
    }

    /**
     * Tests if we get created and not created connections
     *
     * @return null
     */
    public function testGettingConnections()
    {
        $db = AbstractTableGateway::getDriver('default');
        $this->assertTrue(($db instanceof MySQL));
        $this->assertTrue($db->isConnected());

        $db = AbstractTableGateway::getDriver('other');
        $this->assertTrue(($db instanceof MySQL));
        $this->assertNotTrue($db->isConnected());

        $errorObject = null;

        $message = "No exception";

        try
        {
            AbstractTableGateway::getDriver('other3');
        }
        catch (\Exception $e)
        {
            $errorObject = ($e instanceof \RuntimeException);
            $message = $e->getMessage();
        }
        finally
        {
            $this->assertTrue($errorObject, $message);
        }
    }

    /**
     * Tests if we can create a table gateway with an existing connection
     *
     * @return null
     */
    public function testGatewayCreationWithExistingConnection()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $entity = new MyEntity();
        $gateway = new TableGateway($entity, "default");

        $this->assertTrue($gateway->getDb()->isConnected());
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
    public function testCanExecuteDLLStatement()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $entity = new MyEntity();
        $gateway = new MyEntityGateway($entity, "default");

        $result = $gateway->create();

        # mysqli
        $this->assertTrue(is_object($result));

        # properties modified by execute() method
        $this->assertEquals(0, $gateway->getDb()->getNumRows());
        $this->assertEquals(0, $gateway->getDb()->getNumFields());
        $this->assertEquals(0, $gateway->getDb()->getRowsAffected());
    }

    /**
     * Tests if we can execute DML statements
     *
     * @return null
     */
    public function testCanExecuteDMLStatement()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $entity = new MyEntity();
        $gateway = new MyEntityGateway($entity, "default");

        # mysqli
        $result = $gateway->customDML(); // insert statement

        $this->assertTrue(is_object($result));

        # properties modified by execute() method
        $this->assertEquals(0, $gateway->getDb()->getNumRows());
        $this->assertEquals(0, $gateway->getDb()->getNumFields());
        $this->assertEquals(1, $gateway->getDb()->getRowsAffected());
    }

    /**
     * Tests if a wrong query execution throws an InvalidQueryException
     *
     * @return null
     */
    public function testGettingInvalidQueryException()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $entity = new MyEntity();
        $gateway = new MyEntityGateway($entity, "default");

        $errorObject = null;
        $message = "No exception";

        try
        {
            $gateway->wrongDML();
        }
        catch (\Exception $e)
        {
            $errorObject = ($e instanceof InvalidQueryException);
            $message = $e->getMessage();
        }
        finally
        {
            $this->assertTrue($errorObject, $message);
        }
    }

    /**
     * Tests getting results
     *
     * @return null
     */
    public function testGettingResults()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $entity = new MyEntity();
        $gateway = new MyEntityGateway($entity, "default");

        $rows = $gateway->getResults();

        # get only the first
        $row = array_shift($rows);

        $this->assertArrayHasKey("ID", $row);
        $this->assertArrayHasKey("DESCRIPTION", $row);

        # properties modified by execute() method
        $this->assertEquals(1, $gateway->getDb()->getNumRows());
        $this->assertEquals(2, $gateway->getDb()->getNumFields());
        $this->assertEquals(0, $gateway->getDb()->getRowsAffected());
    }

    # AS YOU CAN SEE IN THE ABOVE TEST, IT'S EASY TO ACCESS TO THE DRIVER (HERE MYSQL) USING THE
    # getDb() METHOD. A COMPLETE TEST FOR A DRIVER IS AVAILABLE IN MySQLTest.php. ALL TRANSACTION
    # BEHAVIOR OF TABLE GATEWAY IS RELATED TO THE DRIVER. ONLY GET THE DRIVER AND LET'S DO IT.

    /*
    |--------------------------------------------------------------------------
    | TABLE GATEWAY
    |--------------------------------------------------------------------------
    |
    | The following tests are related to table gateway.
    |
    */

    /**
     * Tests if we can execute INSERT statements through the TableGateway
     *
     * @return null
     */
    public function testINSERTING()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $entity = new MyEntity();

        # Here we can use the generic table gatway or ours
        $gateway = new TableGateway($entity, "default");

        $result = $gateway->insert(["ID" => 500, "DESCRIPTION" => "NEW ELEMENT ONE"]);
        $result = $gateway->insert(["ID" => 501, "DESCRIPTION" => "NEW ELEMENT TWO"]);

        $this->assertTrue(is_object($result));

        # properties modified by execute() method
        $this->assertEquals(0, $gateway->getDb()->getNumRows());
        $this->assertEquals(0, $gateway->getDb()->getNumFields());

        # here 1 is the latest affected row, could be 2 if auto_commit were false
        $this->assertEquals(1, $gateway->getDb()->getRowsAffected());
    }

    /**
     * Tests if we can execute UPDATE statements through the TableGateway
     *
     * @return null
     */
    public function testUPDATING()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $entity = new MyEntity();

        # Here we can use the generic table gatway or ours
        $gateway = new TableGateway($entity, "default");

        $result = $gateway->update(["DESCRIPTION" => "NEW ELEMENT MODIFIED"], ["ID" => 500]);

        $this->assertTrue(is_object($result));

        # properties modified by execute() method
        $this->assertEquals(0, $gateway->getDb()->getNumRows());
        $this->assertEquals(0, $gateway->getDb()->getNumFields());
        $this->assertEquals(1, $gateway->getDb()->getRowsAffected());
    }

    /**
     * Tests if we can execute DELETE statements through the TableGateway
     *
     * @return null
     */
    public function testDELETING()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $entity = new MyEntity();

        # Here we can use the generic table gatway or ours
        $gateway = new TableGateway($entity, "default");

        $result = $gateway->delete(["ID" => 500]);

        $this->assertTrue(is_object($result));

        # properties modified by execute() method
        $this->assertEquals(0, $gateway->getDb()->getNumRows());
        $this->assertEquals(0, $gateway->getDb()->getNumFields());
        $this->assertEquals(1, $gateway->getDb()->getRowsAffected());
    }

    /**
     * Tests if we can execute SELECT statements through the TableGateway
     *
     * @return null
     */
    public function testSELECTING()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $entity = new MyEntity();

        # Here we can use the generic table gatway or ours
        $gateway = new TableGateway($entity, "default");

        $rows = $gateway->select(["ID" => 501]);

        $this->assertTrue(is_array($rows));

        # get only the first
        $row = array_shift($rows);

        $this->assertArrayHasKey("ID", $row);
        $this->assertArrayHasKey("DESCRIPTION", $row);

        # properties modified by execute() method
        $this->assertEquals(1, $gateway->getDb()->getNumRows());
        $this->assertEquals(2, $gateway->getDb()->getNumFields());
        $this->assertEquals(0, $gateway->getDb()->getRowsAffected());

        $this->endTests();
    }

    /**
     * Function to leave all in order, you can execute tests again without problems.
     *
     * @return null
     */
    private function endTests()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $entity = new MyEntity();
        $gateway = new MyEntityGateway($entity, "default");

        # remove all work
        $gateway->drop();
    }
}

class MyEntity extends Entity
{
    public $ID;
    public $DESCRIPTION;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->setTableName("MYTABLE");
   }
}

class MyEntityGateway extends TableGateway
{
    public function create()
    {
        $sql = "CREATE TABLE MYTABLE (ID INTEGER(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, DESCRIPTION VARCHAR(100))";
        return $this->getDb()->execute($sql);
    }

    public function drop()
    {
        $sql = "DROP TABLE MYTABLE";
        return $this->getDb()->execute($sql);
    }

    public function customDML()
    {
        $sql = "INSERT INTO MYTABLE VALUES(1000, 'Some data')";
        return $this->getDb()->execute($sql);
    }

    public function wrongDML()
    {
        $sql = "INSERT INTO MYTABLE (DESCRIPTION, WRONG) VALUES ('Hello world!')";
        return $this->getDb()->execute($sql);
    }

    public function getResults()
    {
        $sql = "SELECT * FROM MYTABLE WHERE ID = 1000";
        $this->getDb()->execute($sql);
        return $this->getDb()->getArrayResult();
    }
}