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

use Drone\Db\SQLFunction;
use PHPUnit\Framework\TestCase;

class SQLFunctionTest extends TestCase
{
    /**
     * Tests if we can build a ltrim statement with SQLFunction
     *
     * @return null
     */
    public function testOneArgumentBuildStatement()
    {
        $sql = new SQLFunction('ltrim', ['column_name']);
        $this->assertEquals('ltrim(\'column_name\')', $sql->getStatement());
        $this->assertSame('ltrim', $sql->getFunction());
        $this->assertSame(['column_name'], $sql->getArguments());
    }

    /**
     * Tests if we can build a to_date statement with SQLFunction
     *
     * @return null
     */
    public function testTwoArgumentsBuildStatement()
    {
        $sql = new SQLFunction('to_date', ['column_name', 'yyyy-mm-dd']);
        $this->assertEquals('to_date(\'column_name\', \'yyyy-mm-dd\')', $sql->getStatement());
    }
}
