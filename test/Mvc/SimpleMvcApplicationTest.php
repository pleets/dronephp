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
use Drone\Mvc\View;
use Drone\Mvc\ModuleFactory;
use PHPUnit\Framework\TestCase;

class SimpleMvcApplicationTest extends TestCase
{
    /**
     * Tests if we can create a simple Mvc application
     *
     * @return null
     */
    public function testMakingMvcApplication()
    {
        $router = new Router();

        $router->addRoute([
            'AppRoute' => [
                'module'     => 'Master',
                'controller' => 'Admin',
                'view'       => 'index'
            ],
        ]);

        // you should code the request to match /Master/Admin/index
        $router->setIdentifiers('Master', 'Admin', 'index');

        $router->setClassNameBuilder(function($module, $class) {
            return "\\$module\Controller\\$class";
        });

        \Drone\Loader\ClassMap::$path = 'test-skeleton/module/Master/source';
        spl_autoload_register("Drone\Loader\ClassMap::autoload");

        $router->match();
        $ctrl = $router->getController();

        # inject the module dependency to the controller
        $router->getController()->setModule(ModuleFactory::create("Master", [
            "config"  => 'test-skeleton/module/Master/config/config.php'
        ]));

        $result = $router->run();

        $this->assertSame(["message" => "Hello world!"], $result);

        $router->addRoute([
            'AppRouteView' => [
                'module'     => 'Master',
                'controller' => 'Admin',
                'view'       => 'withView'
            ],
        ]);

        $router->setIdentifiers('Master', 'Admin', 'withView');
        $router->match();
        $result = $router->run();

        $this->assertTrue($result instanceof View);

        $result->setPath("test-skeleton/module/Master/source/view/Admin");

        $this->assertSame("<h1>Hello world!</h1>", $result->getContents());
    }
}