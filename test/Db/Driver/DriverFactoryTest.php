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

use Drone\Db\Driver\DriverFactory;
use PHPUnit\Framework\TestCase;

class DriverFactoryTest extends TestCase
{
    /**
     * Tests if we can build a ltrim statement with SQLFunction
     *
     * @return null
     */
    public function testDriverCreation()
    {
        $driver = DriverFactory::create([
            "dbhost"       => "localhost",
            "dbuser"       => "root",
            "dbpass"       => "",
            "dbname"       => "test",
            "dbchar"       => "utf8",
            "dbport"       => "3306",
            'driver'       => 'Mysqli',
            "auto_connect" => false
        ]);

        $this->assertInstanceOf('\Drone\Db\Driver\MySQL', $driver);
        $this->assertEquals('localhost', $driver->getDbhost());
        $this->assertEquals('root', $driver->getDbuser());
        $this->assertEquals('test', $driver->getDbname());
        $this->assertEquals('3306', $driver->getDbport());
    }

    /**
     * Tests handling when driver does not exists
     *
     * @expectedException RuntimeException
     */
    public function testWrongDriverCreation()
    {
        $driver = DriverFactory::create([
            "dbhost"       => "localhost",
            "dbuser"       => "root",
            "dbpass"       => "",
            "dbname"       => "test",
            "dbchar"       => "utf8",
            "dbport"       => "3306",
            'driver'       => 'fooBarDriver',
            "auto_connect" => false
        ]);
    }
}