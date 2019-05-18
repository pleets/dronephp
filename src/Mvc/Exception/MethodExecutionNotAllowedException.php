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
 * MethodExecutionNotAllowedException class
 *
 * This exception is thrown when the AbstractModule handler does not allow
 * the method execution in a controller.
 */
class MethodExecutionNotAllowedException extends Exception
{
}
