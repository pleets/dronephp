<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Dom\Element;

use Drone\Dom\Element;
use Exception;

class Form extends Element
{
    /**
     * Constructor
     *
     * @param array $options
     */
	public function __construct($options)
	{
		$options["startTag"] = 'form';
		$options["endTag"] = true;

		parent::__construct($options);
	}

    /**
     * Fill the form with passed values (apply for form controls)
     *
     * @param array $values
     */
	public function fill($values)
	{
		foreach ($values as $label => $value)
		{
			$this->setAttribute($label, "value", $value);
		}
	}
}