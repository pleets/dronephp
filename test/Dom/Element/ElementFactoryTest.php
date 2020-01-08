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

use Drone\Dom\Element\ElementFactory;
use PHPUnit\Framework\TestCase;

class ElementFactoryTest extends TestCase
{
    /**
     * Tests if will we get an Element instance from factory
     *
     * @return null
     */
    public function testFormCreationFromFactory()
    {
        $form = ElementFactory::create('FORM', [
            "action" => 'someurl',
            "method" => 'post',
        ], [
            "input" => [
                "username" => [
                    "type"      => 'text',
                    "maxlength" => 15,
                    "minlength" => 5,
                ],
                "password" => [
                    "type"      => 'password',
                    "maxlength" => 15,
                    "minlength" => 5,
                ],
            ],
        ]);

        $this->assertEquals('<form>', $form->getStartTag());
        $this->assertEquals('</form>', $form->getEndTag());
        $this->assertTrue($form->hasAttribute('action'));
        $this->assertTrue($form->hasAttribute('method'));
        $this->assertTrue($form->hasChild('username'));
        $this->assertTrue($form->hasChild('password'));

        $actionAttr = $form->getAttribute('action');

        $this->assertEquals('action', $actionAttr->getName());
        $this->assertEquals('someurl', $actionAttr->getValue());

        $inputElem = $form->getChild('username');

        $this->assertEquals('<input', $inputElem->getStartTag());
        $this->assertEquals('/>', $inputElem->getEndTag());

        $typeAttrFromInput = $inputElem->getAttribute('type');
        $this->assertEquals('type', $typeAttrFromInput->getName());
        $this->assertEquals('text', $typeAttrFromInput->getValue());
    }
}
