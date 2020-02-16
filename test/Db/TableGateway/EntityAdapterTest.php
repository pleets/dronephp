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
use Drone\Db\TableGateway\EntityAdapter;
use Drone\Db\TableGateway\TableGateway;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Dotenv\Dotenv;

class EntityAdapterTest extends TestCase
{
    /**
     * Database parameters
     */
    private $options = [
        "dbchar"       => "utf8",
        "dbport"       => "3306",
        "auto_connect" => false,
        "driver"       => 'Mysqli',  # needed for the DriverFactory
    ];

    public function setUp()
    {
        parent::setUp();

        $dotenv = new Dotenv();
        $dotenv->load(__DIR__.'/../../../.env.testing');

        $this->options['dbhost'] = $_ENV['DB_HOST'];
        $this->options['dbuser'] = $_ENV['DB_USER'];
        $this->options['dbpass'] = $_ENV['DB_PASS'];
        $this->options['dbname'] = $_ENV['DB_NAME'];
    }

    /*
    |--------------------------------------------------------------------------
    | Establishing connections
    |--------------------------------------------------------------------------
    |
    | The following tests are related to the connection mechanisms and its
    | exceptions and returned values.
    |
    */

    /**
     * Tests if we can connect to the database server through a EntityAdapter
     *
     * @return null
     */
    public function testCompositionWithTableGateway()
    {
        $entity = new User();
        $gateway = new TableGateway($entity, ["eadapter" => $this->options]);
        $entityAdapter = new EntityAdapter($gateway);

        $mysqliObject = $entityAdapter->getTableGateway()->getDb()->connect();

        $this->assertInstanceOf('\mysqli', $mysqliObject);
        $this->assertTrue($entityAdapter->getTableGateway()->getDb()->isConnected());
    }

    /**
     * Tests if we can execute DDL statements through the TableGateway accesor
     *
     * @return null
     */
    public function testCanExecuteStatementThroughTableGateway()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $entity = new User();
        $gateway = new UserGateway($entity, "eadapter");
        $entityAdapter = new EntityAdapter($gateway);

        $result = $entityAdapter->getTableGateway()->create();

        # mysqli
        $this->assertTrue(is_object($result));

        # properties modified by execute() method
        $this->assertEquals(0, $gateway->getDb()->getNumRows());
        $this->assertEquals(0, $gateway->getDb()->getNumFields());
        $this->assertEquals(0, $gateway->getDb()->getRowsAffected());
    }

    /*
    |--------------------------------------------------------------------------
    | ORM TOOL (Data mapper)
    |--------------------------------------------------------------------------
    |
    | The following tests are related to the object relational mapping.
    |
    */

    /**
     * Tests if we can execute INSERT statements through the EntityAdapter
     *
     * @return null
     */
    public function testINSERTING()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $entity = new User();
        $gateway = new TableGateway($entity, "eadapter");
        $entityAdapter = new EntityAdapter($gateway);

        $firstEntity = new User();
        $firstEntity->exchangeArray([
            "ID" => 1, "USERNAME" => "Dennis.Ritchie",
        ]);

        $secondEntity = new User();
        $secondEntity->exchangeArray([
            "ID" => 2, "USERNAME" => "Bjarne.Stroustrup",
        ]);

        $result = $entityAdapter->insert($firstEntity);
        $result = $entityAdapter->insert($secondEntity);

        $this->assertTrue(is_object($result));

        # properties modified by execute() method
        $this->assertEquals(0, $entityAdapter->getTableGateway()->getDb()->getNumRows());
        $this->assertEquals(0, $entityAdapter->getTableGateway()->getDb()->getNumFields());

        # here 1 is the latest affected row, could be 2 if auto_commit were false
        $this->assertEquals(1, $entityAdapter->getTableGateway()->getDb()->getRowsAffected());
    }

    /**
     * Tests if we can execute UPDATE statements through the EntityAdapter
     *
     * @return null
     */
    public function testUPDATING()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $entity = new User();
        $gateway = new TableGateway($entity, "eadapter");
        $entityAdapter = new EntityAdapter($gateway);

        $firstEntity = new User(["ID" => 1]);

        # tell to entity what changed
        $firstEntity->exchangeArray([
            "USERNAME" => "Rasmus.Lerdorf",
        ]);

        $result = $entityAdapter->update($firstEntity, ["ID" => 1]);

        $this->assertTrue(is_object($result));

        # properties modified by execute() method
        $this->assertEquals(0, $entityAdapter->getTableGateway()->getDb()->getNumRows());
        $this->assertEquals(0, $entityAdapter->getTableGateway()->getDb()->getNumFields());
        $this->assertEquals(1, $entityAdapter->getTableGateway()->getDb()->getRowsAffected());
    }

    /**
     * Tests if we can execute DELETE statements through the EntityAdapter
     *
     * @return null
     */
    public function testDELETING()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $entity = new User();
        $gateway = new TableGateway($entity, "eadapter");
        $entityAdapter = new EntityAdapter($gateway);

        # same behaviour to tablegateway
        $result = $entityAdapter->delete(["ID" => 2]);

        $this->assertTrue(is_object($result));

        # properties modified by execute() method
        $this->assertEquals(0, $entityAdapter->getTableGateway()->getDb()->getNumRows());
        $this->assertEquals(0, $entityAdapter->getTableGateway()->getDb()->getNumFields());
        $this->assertEquals(1, $entityAdapter->getTableGateway()->getDb()->getRowsAffected());
    }

    /**
     * Tests if we can execute SELECT statements through the EntityAdapter
     *
     * @return null
     */
    public function testSELECTING()
    {
        $options = $this->options;
        $options["auto_connect"] = true;

        $entity = new User();
        $gateway = new TableGateway($entity, "eadapter");
        $entityAdapter = new EntityAdapter($gateway);

        $rows = $entityAdapter->select([
            "ID" => 1,
        ]);

        $this->assertTrue(is_array($rows));

        # get only the first
        $user = array_shift($rows);

        $this->assertInstanceOf('DroneTest\Util\User', $user);

        $this->assertObjectHasAttribute("ID", $user);
        $this->assertObjectHasAttribute("USERNAME", $user);

        # properties modified by execute() method
        $this->assertEquals(1, $entityAdapter->getTableGateway()->getDb()->getNumRows());
        $this->assertEquals(2, $entityAdapter->getTableGateway()->getDb()->getNumFields());
        $this->assertEquals(0, $entityAdapter->getTableGateway()->getDb()->getRowsAffected());
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

        $entity = new User();
        $gateway = new UserGateway($entity, "eadapter");

        # remove all work
        $gateway->drop();
    }
}

class User extends Entity
{
    public $ID;
    public $USERNAME;

    public function __construct($data = [])
    {
        parent::__construct($data);
        $this->setTableName("USER");
    }
}

class UserGateway extends TableGateway
{
    public function create()
    {
        $sql = "CREATE TABLE USER (ID INTEGER(11) NOT NULL PRIMARY KEY AUTO_INCREMENT, USERNAME VARCHAR(30))";

        return $this->getDb()->execute($sql);
    }

    public function drop()
    {
        $sql = "DROP TABLE USER";

        return $this->getDb()->execute($sql);
    }
}
