<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Dom\Element;

class Form extends AbstractElement
{
    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(Array $options)
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
    public function fill(Array $values)
    {
        foreach ($values as $label => $value)
        {
            $this->setAttribute($label, "value", $value);
        }
    }
}