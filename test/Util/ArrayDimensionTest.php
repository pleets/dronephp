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

use Drone\Util\ArrayDimension;
use PHPUnit\Framework\TestCase;

class ArrayDimensionTest extends TestCase
{
    /**
     * Test if the compound key is created
     *
     * @return void
     */
    public function testCanBeCreatedWithAnEmptyGlue() : void
    {
        $multidimensional = [
            "foo" => [
                "bar" => 87
            ]
        ];

        $unidimensional = ArrayDimension::toUnidimensional($multidimensional, "");

        $this->assertTrue(array_key_exists("foobar", $unidimensional), true);
    }
}