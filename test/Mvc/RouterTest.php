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
use Drone\Mvc\AbstractionController;
use Drone\Mvc\AbstractionModule;
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

use Drone\Mvc\AbstractionController;
use Drone\Mvc\AbstractionModule;

class Module extends AbstractionModule
{
    public function init(AbstractionController $c)
    {
        // init
    }
}

/*
|--------------------------------------------------------------------------
| Controller class
|--------------------------------------------------------------------------
|
| This is a simple controller implementing AbstractionController.
|
*/

namespace App\Controller;

use Drone\Mvc\AbstractionController;

class Index extends AbstractionController
{
    public function about()
    {
        $this->setTerminal();
        return [];
    }
}