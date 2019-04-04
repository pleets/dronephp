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
use Drone\Mvc\ModuleFactory;
use Drone\Mvc\Exception\MethodExecutionNotAllowedException;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    /**
     * Tests if the router can create an instance of a class
     *
     * @return null
     */
    public function testSimpleRouteMatch()
    {
        $router = new Router();

        # default route for App module
        $router->addRoute([
            'App' => [
                'module'     => 'App',
                'controller' => 'Index',
                'view'       => 'home'
            ],
        ]);

        $router->setIdentifiers('App', 'Index', 'home');
        $router->match();

        $ctrl = $router->getController();

        $this->assertEquals("App\Index", get_class($ctrl));
    }

    /**
     * Tests if the router can create an instance of a class with non-default class name builder
     *
     * @return null
     */
    public function testSimpleRouteMatchWithParticularName()
    {
        $router = new Router();

        # default route for App module
        $router->addRoute([
            'App' => [
                'module'     => 'App',
                'controller' => 'Index',
                'view'       => 'about'
            ],
        ]);

        $router->setIdentifiers('App', 'Index', 'about');

        $router->setClassNameBuilder(function($module, $class) {
            return "\\$module\Controller\\$class";
        });

        $router->match();

        $ctrl = $router->getController();

        $this->assertEquals("App\Controller\Index", get_class($ctrl));
    }

    /**
     * Tests method execution behaviour handled by the a module
     *
     * @return null
     */
    public function testModuleAndControllerComposition()
    {
        $router = new Router();

        # default route for App module
        $router->addRoute([
            'App' => [
                'module'     => 'App',
                'controller' => 'Index',
                'view'       => 'about'
            ],
        ]);

        $router->setIdentifiers('App', 'Index', 'about');

        $router->setClassNameBuilder(function($module, $class) {
            return "\\$module\Controller\\$class";
        });

        $router->match();

        # inject the module dependency to the controller
        $router->getController()->setModule(ModuleFactory::create("App"));

        $this->assertNotTrue($router->getController()->getModule()->executionIsAllowed());

        $errorObject = null;

        try {
            $router->run();
        }
        catch (\Exception $e)
        {
            $errorObject = ($e instanceof MethodExecutionNotAllowedException);
        }
        finally
        {
            $this->assertTrue($errorObject, $e->getMessage());
        }
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

namespace App;

use Drone\Mvc\AbstractController;

class Index extends AbstractController
{
    public function home()
    {
        return [];
    }
}

/*
|--------------------------------------------------------------------------
| Another Controller class
|--------------------------------------------------------------------------
|
| This is a simple controller implementing AbstractController. In order to
| build a framework behavior, we need all classes inside Controller namespace.
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

/*
|--------------------------------------------------------------------------
| Module Class
|--------------------------------------------------------------------------
|
| Each module could have a Module class that handles method execution. It's
| useful for execute some code before method execution or for stop it.
|
*/

namespace App;

use Drone\Mvc\AbstractController;
use Drone\Mvc\AbstractModule;

class Module extends AbstractModule
{
    public function init()
    {
        # disallowing method execution
        $this->disallowExecution();
    }
}