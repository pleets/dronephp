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
     *
     * @throws Exception\ChildNotFoundException
     *
     * @return null
     */
    public function fill(array $values)
    {
        foreach ($values as $label => $value) {
            $child = $this->getChild($label);

            if (is_null($child)) {
                throw new Exception\ChildNotFoundException(
                    "The child '$label' does not exists inside the form element"
                );
            }

            if (!$child->isFormControl()) {
                throw new Exception\NotFormControlException(
                    "The child '$label' is not a form control"
                );
            }

            $child->setAttribute(new Attribute("value", $value));
        }
    }
}
