<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
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
     * Fills the form with all passed values
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