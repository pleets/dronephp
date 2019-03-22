<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace DroneTest\Dom\Element;

use Drone\Dom\Attribute;
use Drone\Dom\Element\Input;
use PHPUnit\Framework\TestCase;

class InputTest extends TestCase
{
    /**
     * Tests input tag constructor
     *
     * @return null
     */
    public function testInputTags()
    {
        $input = new Input();

        $this->assertEquals('<input', $input->getStartTag());
        $this->assertEquals('/>', $input->getEndTag());
    }

    /**
     * Tests adding attributes and children
     *
     * @return null
     */
    public function testInputAttributesAndChildren()
    {
        $input = new Input();

        $this->assertEquals(0, count($input->getAttributes()));
        $this->assertEquals(0, count($input->getChildren()));

        # adding an attribute
        $input->setAttribute(new Attribute("type", "hidden"));

        $attr = $input->getAttribute('type');
        $this->assertEquals("type", $attr->getName());
        $this->assertEquals("hidden", $attr->getValue());
    }
}