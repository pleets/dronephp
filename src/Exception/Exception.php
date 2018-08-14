<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Exception;

use Drone\Error\Errno;

/**
 * Exception class
 *
 * This is a standard exception that implements StorableTrait. Developers can use
 * this exception to separate controller exceptions in the business logic.
 */
class Exception extends \Exception
{
    use \Drone\Exception\StorableTrait;

    /**
     * Constructor
     *
     * @param string         $message
     * @param integer        $code
     * @param Exception|null $previous
     *
     * @return null
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}