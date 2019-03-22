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

/**
 * Form class
 *
 * Represents a html Form element
 */
class Form extends AbstractElement
{
    /**
     * The node name of the element
     *
     * @var string
     */
    const NODE_NAME = 'FORM';

    /**
     * Tells if the element has a end tag
     *
     * @var boolean
     */
    const HAS_END_TAG = true;

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