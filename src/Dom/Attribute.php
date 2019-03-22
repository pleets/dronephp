<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Dom;

/**
 * Attribute class
 *
 * To represent html attribute
 */
class Attribute
{
    /**
     * The name of the attribute
     *
     * @var string
     */
    protected $name;

    /**
     * The value of the attribute
     *
     * @var mixed
     */
    protected $value;

    /**
     * Gets the name attribute
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the value attribute
     *
     * @return array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the value attribute
     *
     * @param mixed $value
     *
     * @return null
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Constructor
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __construct($name, $value = null)
    {
        $this->name = $name;
        $this->value = $value;
    }
}