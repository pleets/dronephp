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
use Drone\Mvc\AbstractController;
use Drone\Mvc\AbstractModule;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    /**
     * Tests if the router can create an instance of a controller
     *
     * @return null
     */
    public function testSimpleRouteMatching()
    {
        $router = new Router();

        $router->addRoute([
            'defaults' => [
                'module'     => 'App',
                'controller' => 'Index',
                'view'       => 'Home'
            ]
        ]);

        # default route for App module
        $router->addRoute([
            'App' => [
                'module'     => 'App',
                'controller' => 'Index',
                'view'       => 'about'
            ],
        ]);

        $router->setIdentifiers('App', 'Index', 'about');
        $router->run();

        $ctrl = $router->getController();

        $this->assertEquals("App\Controller\Index", get_class($ctrl));
    }
}

/*
|--------------------------------------------------------------------------
| Module Class
|--------------------------------------------------------------------------
|
| Each module must have a Module class.
|
*/

namespace App;

use Drone\Mvc\AbstractController;
use Drone\Mvc\AbstractModule;

class Module extends AbstractModule
{
    public function init(AbstractController $c)
    {
        // init
    }
}

/*
|--------------------------------------------------------------------------
| Controller class
|--------------------------------------------------------------------------
|
| This is a simple controller implementing AbstractController.
|
*/

namespace App\Controller;

use Drone\Mvc\AbstractController;

class Index extends AbstractController
{
    public function about()
    {
        return [];
    }
}