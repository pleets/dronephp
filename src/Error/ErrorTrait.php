<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

namespace Drone\Error;

/**
 * ErrorTrait trait
 *
 * Standard error management for some classes
 */
trait ErrorTrait
{
    /**
     * Common errors
     *
     * @var array
     */
    protected $standardErrors = [

        # File errros
        1 => 'Failed to open stream: \'%file%\', Permission Denied!',
        2 => 'No such file or directory %file%',
        3 => 'File exists %file%',
        4 => 'Stream \'%file%\' is Not a directory',

        # JSON errors
        10 => 'Failed to decode JSON file \'%file%\'',
        11 => 'Failed to encode JSON file \'%file%\'',

        # Database related errors
        20 => 'The transaction was already started',
        21 => 'The transaction has not been started',
        22 => 'Transaction cannot be empty',
    ];

    /**
     * Failure messages
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Returns an array with all failure messages
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Adds an error
     *
     * @param string $code
     * @param string $message
     *
     * @return null
     */
    protected function error($code, $message = null)
    {
        if (!array_key_exists($code, $this->errors))
            $this->errors[$code] = (array_key_exists($code, $this->standardErrors))
                ?
                    is_null($message)
                        ? $this->standardErrors[$code]
                        : preg_replace('/%[a-zA-Z]*%/', $message, $this->standardErrors[$code])
                : $message;
    }
}