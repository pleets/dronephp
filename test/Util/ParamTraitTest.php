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

use Drone\Util\ParamTrait;
use PHPUnit\Framework\TestCase;

class ParamTraitTest extends TestCase
{
    /**
     * Tests cheking and accessing to individual params
     *
     * @return null
     */
    public function testGettingIndividualParams()
    {
        $ctrl = new MyController();
        $ctrl->init([
            "user" => "fermius",
            "date" => new \DateTime("now")
        ]);

        $this->assertTrue($ctrl->isParam('date'));
        $this->assertEquals('fermius', $ctrl->getParam('user'));

        # alias
        $this->assertEquals('fermius', $ctrl->param('user'));
    }

    /**
     * Tests cheking and accessing to all params
     *
     * @return null
     */
    public function testGettingAllParams()
    {
        $ctrl = new MyController();

        $params = [
            "user" => "fermius",
            "age"  => 26
        ];

        $ctrl->init($params);

        $this->assertSame(["user" => "fermius", "age" => 26], $params);
    }
}

class MyController
{
    use ParamTrait;

    public function init($params)
    {
        $this->setParams($params);
    }
}