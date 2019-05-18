<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Mvc;

use Drone\Mvc\Exception;

/**
 * AbstractionController class
 *
 * This class manages the interaction between models and views
 */
abstract class AbstractController
{
    /**
     * Current module instance
     *
     * @var AbstractModule
     */
    private $module;

    /**
     * Current method
     *
     * @var string
     */
    private $method = null;

    /**
     * Returns the current module instance
     *
     * @return AbstractModule
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Returns the current method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Sets module instance
     *
     * @param AbstractModule $module
     *
     * @return null
     */
    public function setModule(AbstractModule $module)
    {
        $this->module = $module;
    }

    /**
     * Sets the method attribute
     *
     * @param string $method
     *
     * @return null
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Executes the controller
     *
     * @return mixed
     */
    public function execute()
    {
        $method = $this->method;

        if (is_null($method)) {
            # This error is thrown because of 'setMethod' method has not been executed
            throw new \LogicException("No method has been setted to execute!");
        }

        if (!is_null($this->module) && !$this->module->executionIsAllowed()) {
            throw new Exception\MethodExecutionNotAllowedException("Method execution is not allowed");
        } else {
            if (method_exists($this, $method)) {
                $class = __CLASS__;

                $reflection = new \ReflectionMethod($this, $method);

                if (!$reflection->isPublic()) {
                    throw new Exception\PrivateMethodExecutionException(
                        "The method '$method' is not public in the control class '$class'"
                    );
                }

                return $this->$method();
            } else {
                $class = __CLASS__;
                throw new Exception\MethodNotFoundException(
                    "The method '$method' doesn't exists in the control class '$class'"
                );
            }
        }
    }

    /**
     * Returns the class name
     *
     * @return string
     */
    public static function getClassName()
    {
        return __CLASS__;
    }

    /**
     * Returns $_POST contents
     *
     * @return array
     */
    public function getPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST)) {
            $_POST = json_decode(file_get_contents('php://input'), true);
        }

        return (array) $_POST;
    }

    /**
     * Returns json contents
     *
     * @throws LogicException
     *
     * @return array
     */
    public function getJson()
    {
        if ($_SERVER['REQUEST_METHOD'] != 'JSON') {
            throw new \LogicException("Request method is not JSON");
        }

        $input =  file_get_contents('php://input');
        $array = explode("&", $input);

        $result = [];

        foreach ($array as $value) {
            $io = explode("=", $value);
            $result[$io[0]] = $io[1];
        }

        return $result;
    }

    /**
     * Checks if the current request is XmlHttpRequest (AJAX)
     *
     * @return boolean
     */
    public function isXmlHttpRequest()
    {
        # non standard (HTTP_X_REQUESTED_WITH is not a part of PHP)
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            return true;
        }
        return false;
    }

    /**
     * Checks if the current request is POST
     *
     * @return boolean
     */
    public function isPost()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            return true;
        }
        return false;
    }

    /**
     * Checks if the current request is GET
     *
     * @return boolean
     */
    public function isGet()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            return true;
        }
        return false;
    }
}
