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
use Drone\Dom\Element\Form;
use Drone\Dom\Element\Input;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    /**
     * Tests form tag constructor
     *
     * @return null
     */
    public function testFormTags()
    {
        $form = new Form();

        $this->assertEquals('<form>', $form->getStartTag());
        $this->assertEquals('</form>', $form->getEndTag());
    }

    /**
     * Tests adding attributes and children
     *
     * @return null
     */
    public function testFormAttributesAndChildren()
    {
        $form = new Form();

        $this->assertEquals(0, count($form->getAttributes()));
        $this->assertEquals(0, count($form->getChildren()));

        # adding an attribute
        $form->setAttribute(new Attribute("action", "someurl"));

        $this->assertTrue($form->hasAttribute('action'));

        $attr = $form->getAttribute('action');
        $this->assertEquals("action", $attr->getName());
        $this->assertEquals("someurl", $attr->getValue());

        # adding an element
        $input = new Input();
        $input->setAttribute(new Attribute("type", "hidden"));

        $form->setChild("someinput", $input);

        $_input = $form->getChild('someinput');
        $this->assertInstanceOf('\Drone\Dom\Element\Input', $_input);
    }
}
