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

use Drone\Mvc\AbstractController;
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    /**
     * Tests controller execution
     *
     * @return null
     */
    public function testInstantiationAndMethodExecution()
    {
        /*
         * Explicit method execution
         */

        $ctrl = new \App\Controller\Home;
        $params = $ctrl->about();

        $expected = ["greeting" => "Hello World!"];
        $this->assertSame($expected, $params);

        /*
         * Implicit method execution
         */

        $ctrl = new \App\Controller\Home;
        $ctrl->setMethod('about');
        $ctrl->execute();
        $params = $ctrl->getParams();

        $expected = ["greeting" => "Hello World!"];
        $this->assertSame($expected, $params);
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

class Home extends AbstractController
{
    public function about()
    {
        return ["greeting" => "Hello World!"];
    }
}