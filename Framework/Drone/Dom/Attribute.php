<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/Drone
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Dom;

class Attribute
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
	private $value;

    /**
     * Get name
     *
     * @return array
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get value
     *
     * @return array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set value
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
     */
	public function __construct($name, $value = null)
	{
        $this->name = $name;
		$this->value = $value;
	}
}