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
 * Input class
 *
 * Represents a input element
 */
class Input extends AbstractElement
{
    /**
     * The node name of the element
     *
     * @var string
     */
    const NODE_NAME = 'INPUT';

    /**
     * Tells if the element has a end tag
     *
     * @var boolean
     */
    const HAS_END_TAG = false;
}
