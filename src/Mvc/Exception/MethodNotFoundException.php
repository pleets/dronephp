<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Mvc\Exception;

use Exception;

/**
 * MethodNotFoundException class
 *
 * This exception is thrown when an method not exists in a class and it's executed dynamically
 */
class MethodNotFoundException extends Exception
{
}