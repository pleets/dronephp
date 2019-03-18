<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Error;

/**
 * ErrorHandler class
 *
 * This class handles errors with user defined functions
 */
class ErrorHandler
{
    /**
     * Handles errors and transform it to exceptions
     *
     * @param integer $errno
     * @param string $errstr
     * @param string $errfile
     * @param integer $errline
     *
     * @throws RuntimeException
     *
     * @return null|boolean
     */
    public static function toException($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno))
        {
            // This error code is not included in error_reporting, so let it fall
            // through to the standard PHP error handler
            return false;
        }

        throw new \RuntimeException("<strong>Error:</strong> $errstr in <strong>$errfile</strong> on line <strong>$errline</strong>");
    }

    /**
     * Better way to use the error-control operator @
     *
     * By default @ operator hides Fatal errors (Ex: Maximum time execution).
     * The errorControlOperator could be setted as handler with set_error_handler($callable $e, E_ALL)
     * and it takes the same behavior of @, but it does not ignore Fatal errors.
     *
     * @param integer $errno
     * @param string  $errstr
     * @param string  $errfile
     * @param integer $errline
     *
     * @return null|boolean
     */
    public static function errorControlOperator($errno, $errstr, $errfile, $errline)
    {
        if (!(error_reporting() & $errno))
        {
            // This error code is not included in error_reporting, so let it fall
            // through to the standard PHP error handler
            return false;
        }
    }
}