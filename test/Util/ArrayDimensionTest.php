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
     * Tests if returns the same array if it's unidimensional
     *
     * @return void
     */
    public function testReturnSameArray() : void
    {
        $multidimensional = [
            "foo" => [8, "bar"]
        ];

        $unidimensional = ArrayDimension::toUnidimensional($multidimensional, "");

        $this->assertEquals($multidimensional, $unidimensional);
    }

    /**
     * Tests if it's possible transform a simple two-dimensional array into an unidimensional
     *
     * @return void
     */
    public function testCanBeCreatedFromATwoDimensionalArray() : void
    {
        # two-dimensional array
        $multidimensional = [
            "foo" => [
                "bar" => 87
            ],
            "foo2" => [
                "bar2" => "value"
            ]
        ];

        $expected = [
            "foo_bar"   => 87,
            "foo2_bar2" => "value"
        ];

        $unidimensional = ArrayDimension::toUnidimensional($multidimensional, "_");

        $this->assertEquals($expected, $unidimensional);
    }

    /**
     * Tests if it's possible transform a simple three-dimensional array into an unidimensional
     *
     * @return void
     */
    public function testCanBeCreatedFromAThreeDimensionalArray() : void
    {
        # three-dimensional array
        $multidimensional = [
            "foo" => [
                "bar" => [
                    "abc" => false
                ]
            ],
            "foo2" => [
                "bar2" => [
                    "def" => 7854
                ]
            ]
        ];

        $expected = [
            "foo_bar_abc"   => false,
            "foo2_bar2_def" => 7854
        ];

        $unidimensional = ArrayDimension::toUnidimensional($multidimensional, "_");

        $this->assertEquals($expected, $unidimensional);
    }

    /**
     * Tests if it's possible transform a mixed n-dimensional array into an unidimensional
     *
     * @return void
     */
    public function testCanBeCreatedFromAMixedArray() : void
    {
        # mixed n-dimensional array
        $multidimensional = [
            "foo" => "value",
            "bar" => [
                "abc" => false
            ],
            "foo2" => [
                "bar2" => [
                    "def" => 7854
                ]
            ]
        ];

        $expected = [
            "foo"           => "value",
            "bar_abc"       => false,
            "foo2_bar2_def" => 7854
        ];

        $unidimensional = ArrayDimension::toUnidimensional($multidimensional, "_");

        $this->assertEquals($expected, $unidimensional);
    }
}