<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Dom\Element;

use Drone\Dom\Attribute;

/**
 * ElementFactory class
 *
 * Create an instance of the specified element
 */
class ElementFactory extends AbstractElement
{
    /**
     * Creates an instance of an element
     *
     * @param string $node_name
     * @param array $attributes
     * @param array $elements
     *
     * @return AbstractElement
     */
    public static function create($node_name, Array $attributes = null, Array $elements = null)
    {
        $element = ucfirst(strtolower($node_name));
        $className = "\Drone\Dom\Element\\" . $element;
        $instance = new $className;

        if (count($attributes)) {
            foreach ($attributes as $name => $value) {
                if (!is_string($name)) {
                    throw new \InvalidArgumentException("Attribute only accepts strings as names");
                }

                $instance->setAttribute(new Attribute($name, $value));
            }
        }

        if (count($elements)) {
            foreach ($elements as $_node_name => $element) {
                foreach ($element as $label => $_attributes) {
                    $instance->setChild($label, self::create($_node_name, $_attributes));
                }
            }
        }

        return $instance;
    }
}
