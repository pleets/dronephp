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
        1 => 'Failed to open stream \'%file%\', Permission Denied!',
        2 => 'No such file or directory \'%file%\'',
        3 => 'File exists \'%file%\'',
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
     * This member stores all failure messages as an array of key/value pairs.
     *
     * key:   The the code of the error (usually in Errno class). Also the ERROR_CONSTANT.
     * value: The error message.
     *
     * If the key is not an integer, the error is not in Errno class.
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
     * Returns true if there is at least one error or false if not
     *
     * @return array
     */
    public function hasErrors()
    {
        return (bool) count($this->errors);
    }

    /**
     * Adds an error
     *
     * When the error is standard (i.e. exists in Errno class) the behavior is as follow:
     * - _error(int ERROR_CONSTANT): Adds a standard error without replace the wildcard
     * - _error(int ERROR_CONSTANT, string $message): Adds a standard error replacing the wildcard
     *
     * When the error is non-standard (i.e. is not a member of Errno class) the behavior is as follow:
     * - _error(int $code, string $message): Adds a non-standard error
     * - _error(string $message): Adds a non-standard error creating a generated base64 code
     *
     * @param integer|string $code
     * @param string         $message
     *
     * @return null
     */
    private function errorCall($code, $message = null)
    {
        if (!is_null($code) && !is_integer($code) && !is_string($code)) {
            throw new \InvalidArgumentException("Invalid type given. Integer or string expected");
        }

        if (is_null($code)) {
            $code = preg_replace('/=|\/|\+/', "", base64_encode($message));
        } else {
            if (!array_key_exists($code, $this->standardErrors) && empty($message)) {
                /*
                 * Non-standard errors must have a message to describe the error, make sure
                 * you execute the error() method with a message as the second parameter.
                 */
                throw new \LogicException('The message does not be empty in non-standard errors!');
            }
        }

        if (!array_key_exists($code, $this->errors)) {
            $this->errors[$code] = (array_key_exists($code, $this->standardErrors))
                ?
                    is_null($message)
                        ? preg_replace('/\s\'%[a-zA-Z]*%\'/', $message, $this->standardErrors[$code])
                        # if $message is not null it will replace the %file% wildcard
                        : preg_replace('/%[a-zA-Z]*%/', $message, $this->standardErrors[$code])
                : $message;
        }
    }

    public function __call($method, $arguments)
    {
        if ($method == 'error') {
            switch (count($arguments)) {
                case 1:
                    if (is_integer($arguments[0])) {
                        return call_user_func([$this, 'errorCall'], array_shift($arguments));
                    } else {
                        return call_user_func([$this, 'errorCall'], null, array_shift($arguments));
                    }
                    break;
                case 2:
                    return call_user_func([$this, 'errorCall'], $arguments[0], $arguments[1]);
                    break;
            }
        }
    }
}
