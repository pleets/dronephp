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

use Drone\Mvc\Router;
use Drone\Mvc\ModuleFactory;
use PHPUnit\Framework\TestCase;

class ModuleTest extends TestCase
{
    /**
     * Tests getting module configuration
     *
     * @return null
     */
    public function testGettingConfiguration()
    {
        include "test-skeleton/module/Master/Module.php";

        $module = ModuleFactory::create("Master", [
            "config"  => 'test-skeleton/module/Master/config/config.php'
        ]);

        $this->assertSame(
            'test-skeleton/module/Master/config/config.php',
            $module->getConfigFile()
        );

        $this->assertSame(["baz" => "foo"], $module->getConfig());
    }
}