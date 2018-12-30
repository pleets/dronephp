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
use Drone\Db\Driver\Exception\ConnectionException;
use Drone\Db\Driver\Exception\InvalidQueryException;
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
        $conn = new MySQL($this->options);

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

        $this->assertInstanceOf('\mysqli', $mysqliObject);
        $this->assertTrue($conn->isConnected());
    }

    /**
     * Tests if we can reconnect to the database server when there is not a connection stablished
     *
     * @return null
     */
    public function testCannotStablishReconnection()
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
     * Tests if a failed connection throws a ConnectionException
     *
     * @return null
     */
    public function testCannotStablishConnection()
    {
        $options = $this->options;
        $options["dbhost"] = 'myserver';   // this server does not exists

        $conn = new MySQL($options);

        $errorObject = null;

        $message = "No exception";

        try
        {
            $conn->connect();
        }
        catch (\Exception $e)
        {
            $errorObject = ($e instanceof ConnectionException);
            $message = $e->getMessage();
        }
        finally
        {
            $this->assertTrue($errorObject, $message);
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
    public function testCanExecuteDLLStatement()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $conn = new MySQL($options);
        $sql = "CREATE TABLE MYTABLE (ID INTEGER(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, DESCRIPTION VARCHAR(100))";
        $result = $conn->execute($sql);

        $this->assertTrue(is_object($result));

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
    public function testCanExecuteDMLStatement()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $conn = new MySQL($options);
        $sql = "INSERT INTO MYTABLE (DESCRIPTION) VALUES ('Hello world!')";
        $result = $conn->execute($sql);

        $this->assertTrue(is_object($result));

        # properties modified by execute() method
        $this->assertEquals(0, $conn->getNumRows());
        $this->assertEquals(0, $conn->getNumFields());
        $this->assertEquals(1, $conn->getRowsAffected());
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

        $conn = new MySQL($options);

        $errorObject = null;
        $message = "No exception";

        try
        {
            $sql = "INSERT INTO MYTABLE (DESCRIPTION, WRONG) VALUES ('Hello world!')";
            $conn->execute($sql);
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

        $conn = new MySQL($options);
        $sql = "SELECT * FROM MYTABLE LIMIT 2";
        $conn->execute($sql);

        # properties modified by execute() method
        $this->assertEquals(1, $conn->getNumRows());
        $this->assertEquals(2, $conn->getNumFields());
        $this->assertEquals(0, $conn->getRowsAffected());

        $rowset = $conn->getArrayResult();    # array with results
        $row = array_shift($rowset);

        $this->assertArrayHasKey("ID", $row);
        $this->assertArrayHasKey("DESCRIPTION", $row);
    }

    /**
     * Tests if we can commit transactions
     *
     * @return null
     */
    public function testCommitBehavior()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $conn = new MySQL($options);
        $conn->autocommit(false);

        $sql = "INSERT INTO MYTABLE (DESCRIPTION) VALUES ('COMMIT_ROW_1')";
        $conn->execute($sql);

        $sql = "SELECT * FROM MYTABLE WHERE DESCRIPTION = 'COMMIT_ROW_1'";
        $conn->execute($sql);
        $rowcount = count($conn->getArrayResult());

        $this->assertTrue(($rowcount === 1));    # the row is available for now

        # properties modified by execute() method
        $this->assertEquals(1, $conn->getNumRows());
        $this->assertEquals(2, $conn->getNumFields());
        $this->assertEquals(0, $conn->getRowsAffected());    # nothing affected (autocommit = false)

        $this->assertTrue($conn->commit());

        # now let's to verify if the record exists after commit
        $sql = "SELECT * FROM MYTABLE WHERE DESCRIPTION = 'COMMIT_ROW_1'";
        $conn->execute($sql);
        $rowcount = count($conn->getArrayResult());

        $this->assertTrue(($rowcount === 1));    # the row is available
    }

    /**
     * Tests if we can rollback transactions
     *
     * @return null
     */
    public function testRollbackBehavior()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $conn = new MySQL($options);
        $conn->autocommit(false);

        $sql = "INSERT INTO MYTABLE (DESCRIPTION) VALUES ('ROLLBACK_ROW_1')";
        $conn->execute($sql);

        $sql = "SELECT * FROM MYTABLE WHERE DESCRIPTION = 'ROLLBACK_ROW_1'";
        $conn->execute($sql);
        $rowcount = count($conn->getArrayResult());

        $this->assertTrue(($rowcount === 1));    # the row is available for now

        # properties modified by execute() method
        $this->assertEquals(1, $conn->getNumRows());
        $this->assertEquals(2, $conn->getNumFields());
        $this->assertEquals(0, $conn->getRowsAffected());    # nothing affected (autocommit = false)

        $this->assertTrue($conn->rollback());

        # now let's to verify if the record exists after commit
        $sql = "SELECT * FROM MYTABLE WHERE DESCRIPTION = 'ROLLBACK_ROW_1'";
        $conn->execute($sql);
        $rowcount = count($conn->getArrayResult());

        $this->assertNotTrue(($rowcount === 1));    # the row is not available
    }

    /**
     * Tests if we can do a transaction with commiting changes
     *
     * @return null
     */
    public function testTransactionConfirmation()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $conn = new MySQL($options);
        $conn->autocommit(false);

        $sql = "INSERT INTO MYTABLE (DESCRIPTION) VALUES ('COMMIT_ROW_TRANSACTION_1')";
        $conn->execute($sql);

        $sql = "INSERT INTO MYTABLE (DESCRIPTION) VALUES ('COMMIT_ROW_TRANSACTION_2')";
        $conn->execute($sql);

        $sql = "INSERT INTO MYTABLE (DESCRIPTION) VALUES ('COMMIT_ROW_TRANSACTION_3')";
        $conn->execute($sql);

        $sql = "INSERT INTO MYTABLE (DESCRIPTION) VALUES ('COMMIT_ROW_TRANSACTION_4')";
        $conn->execute($sql);

        $sql = "SELECT * FROM MYTABLE WHERE DESCRIPTION LIKE 'COMMIT_ROW_TRANSACTION_%'";
        $conn->execute($sql);
        $rowcount = count($conn->getArrayResult());

        $this->assertTrue(($rowcount === 4));    # the rows are available for now

        # properties modified by execute() method
        $this->assertEquals(4, $conn->getNumRows());
        $this->assertEquals(2, $conn->getNumFields());
        $this->assertEquals(0, $conn->getRowsAffected());    # nothing affected (autocommit = false)

        $this->assertTrue($conn->commit());

        # now let's to verify if the record exists after commit
        $sql = "SELECT * FROM MYTABLE WHERE DESCRIPTION LIKE 'COMMIT_ROW_TRANSACTION_%'";
        $conn->execute($sql);
        $rowcount = count($conn->getArrayResult());

        $this->assertTrue(($rowcount === 4));    # the row is available
    }

    /**
     * Tests if we can do a transaction with reverting changes
     *
     * @return null
     */
    public function testTransactionReversion()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $conn = new MySQL($options);
        $conn->autocommit(false);

        $sql = "INSERT INTO MYTABLE (DESCRIPTION) VALUES ('ROLLBACK_ROW_TRANSACTION_1')";
        $conn->execute($sql);

        $sql = "INSERT INTO MYTABLE (DESCRIPTION) VALUES ('ROLLBACK_ROW_TRANSACTION_2')";
        $conn->execute($sql);

        $sql = "INSERT INTO MYTABLE (DESCRIPTION) VALUES ('ROLLBACK_ROW_TRANSACTION_3')";
        $conn->execute($sql);

        $sql = "INSERT INTO MYTABLE (DESCRIPTION) VALUES ('ROLLBACK_ROW_TRANSACTION_4')";
        $conn->execute($sql);

        $sql = "SELECT * FROM MYTABLE WHERE DESCRIPTION LIKE 'ROLLBACK_ROW_TRANSACTION_%'";
        $conn->execute($sql);
        $rowcount = count($conn->getArrayResult());

        $this->assertTrue(($rowcount === 4));    # the rows are available for now

        # properties modified by execute() method
        $this->assertEquals(4, $conn->getNumRows());
        $this->assertEquals(2, $conn->getNumFields());
        $this->assertEquals(0, $conn->getRowsAffected());    # nothing affected (autocommit = false)

        $this->assertTrue($conn->rollback());

        # now let's to verify if the record exists after commit
        $sql = "SELECT * FROM MYTABLE WHERE DESCRIPTION LIKE 'ROLLBACK_ROW_TRANSACTION_%'";
        $conn->execute($sql);
        $rowcount = count($conn->getArrayResult());

        $this->assertNotTrue(($rowcount === 4));    # the row is available
    }

    /**
     * Tests if we can do a transaction with the shortcut method
     *
     * @return null
     */
    public function testTransactionConfirmationShortcut()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $conn = new MySQL($options);

        # not necessary!
        # $conn->autocommit(false);

        # starts the transaction
        $conn->beginTransaction();

        $sql = "INSERT INTO MYTABLE (DESCRIPTION) VALUES ('TRANSACTION_SHORTCUT_1')";
        $conn->execute($sql);

        $sql = "INSERT INTO MYTABLE (DESCRIPTION) VALUES ('TRANSACTION_SHORTCUT_1')";
        $conn->execute($sql);

        $sql = "SELECT * FROM MYTABLE WHERE DESCRIPTION LIKE 'TRANSACTION_SHORTCUT_%'";
        $conn->execute($sql);
        $rowcount = count($conn->getArrayResult());

        $this->assertTrue(($rowcount === 2));    # the rows are available for now

        # properties modified by execute() method
        $this->assertEquals(2, $conn->getNumRows());
        $this->assertEquals(2, $conn->getNumFields());
        $this->assertEquals(0, $conn->getRowsAffected());    # nothing affected (autocommit = false)

        # ends the transaction
        $conn->endTransaction();

        # now let's to verify if the record exists after endTransaction()
        $sql = "SELECT * FROM MYTABLE WHERE DESCRIPTION LIKE 'TRANSACTION_SHORTCUT_%'";
        $conn->execute($sql);
        $rowcount = count($conn->getArrayResult());

        $this->assertTrue(($rowcount === 2));    # the row is available
    }

    /**
     * Tests if we can do a transaction with reverting changes
     *
     * @return null
     */
    public function testTransactionReversionShortcut()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $conn = new MySQL($options);

        # not necessary!
        # $conn->autocommit(false);

        # starts the transaction
        $conn->beginTransaction();

        try
        {
            $sql = "INSERT INTO MYTABLE (DESCRIPTION) VALUES ('TRANS_SHORTCUT_1')";
            $conn->execute($sql);

            $sql = "INSERT INTO MYTABLE (DESCRIPTION, WRONG) VALUES ('TRANS_SHORTCUT_2')";
            $conn->execute($sql);
        }
        catch (InvalidQueryException $e)
        {
            $message = $e->getMessage();
            #·not necessary!
            # $this->assertTrue($conn->rollback());
        }

        # starts the transaction
        $conn->endTransaction();

        # now let's to verify if the record exists after endTransaction()
        $sql = "SELECT * FROM MYTABLE WHERE DESCRIPTION LIKE 'TRANS_SHORTCUT_%'";
        $conn->execute($sql);
        $rowcount = count($conn->getArrayResult());

        $this->assertNotTrue(($rowcount === 0));    # the rows are not available
    }
}