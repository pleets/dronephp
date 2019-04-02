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
    use \Drone\Util\ParamTrait;

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
     * Layout name
     *
     * @var string
     */
    private $layout = "default";

    /**
     * Terminal mode
     *
     * @var boolean
     */
    private $terminal = true;

    /**
     * Indicates if the controller must show the views
     *
     * @var boolean
     */
    private $showView = true;

    /**
     * Defines method execution
     *
     * The only way to stop method execution is executing stopExecution() before execute().
     *
     * @var boolean
     */
    private $allowExecution = true;

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
     * Returns the mode of visualization
     *
     * @return boolean
     */
    public function getTerminal()
    {
        return $this->terminal;
    }

    /**
     * Returns the mode of viewing
     *
     * @return boolean
     */
    public function getShowView()
    {
        return $this->showView;
    }

    /**
     * Returns the current layout
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Sets the terminal mode
     *
     * @param boolean $terminal
     *
     * @return null
     */
    public function setTerminal($terminal = true)
    {
        $this->terminal = $terminal;
    }

    /**
     * Sets the showView parameter
     *
     * @param boolean $show
     *
     * @return null
     */
    public function setShowView($show = true)
    {
        $this->showView = $show;
    }

    /**
     * Sets layout name
     *
     * @param string $layout
     *
     * @return null
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
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
     * Creates the module instance
     *
     * @param string $module
     * @param Router $router
     *
     * @return null
     */
    public function createModuleInstance($module, Router $router)
    {
        if (!is_null($module))
        {
            /*
             * Module class instantiation
             *
             * Each module must have a class called Module in her namesapce. This class
             * is initilized here and can change the behavior of a controller using
             * stopExecution(), setMethod() or other methods.
             */

            $fqn_module = "\\" . $module . "\\Module";

            if (!class_exists($fqn_module))
                throw new Exception\ModuleNotFoundException("The module class '$fqn_module' does not exists!");

            $this->module = new $fqn_module($module, $this, $router);
        }
    }

    /**
     * Executes the controller
     *
     * @return null
     */
    public function execute()
    {
        $method = $this->method;

        if (is_null($method))
            # This error is thrown because of 'setMethod' method has not been executed
            throw new \LogicException("No method has been setted to execute!");

        if ($this->allowExecution)
        {
            if (method_exists($this, $method))
            {
                $class = __CLASS__;

                $reflection = new \ReflectionMethod($this, $method);

                if (!$reflection->isPublic())
                    throw new Exception\PrivateMethodExecutionException("The method '$method' is not public in the control class '$class'");

                # Get the returned value of the method to send to the view
                $this->params = $this->$method();

                # The only way to manage views is through an AbstractModule
                if (!is_null($this->module))
                {
                    $params = $this->getParams();

                    $layout_params = (count($params) && array_key_exists('::Layout', $params)) ? $params["::Layout"] : [];

                    $layoutManager = new Layout($layout_params);
                    $layoutManager->fromController($this);
                }
            }
            else
            {
                $class = __CLASS__;
                throw new Exception\MethodNotFoundException("The method '$method' doesn't exists in the control class '$class'");
            }
        }
    }

    /**
     * Stops the execution of the specified method
     *
     * The only way to stop method execution before execute()
     *
     * @return null
     */
    public function stopExecution()
    {
        $this->allowExecution = false;
    }

    /**
     * Checks if allowExecution is true
     *
     * @return null
     */
    public function executionIsAllowed()
    {
        return $this->allowExecution;
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($_POST))
            $_POST = json_decode(file_get_contents('php://input'), true);

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
        if ($_SERVER['REQUEST_METHOD'] != 'JSON')
            throw new \LogicException("Request method is not JSON");

        $input =  file_get_contents('php://input');
        $array = explode("&", $input);

        $result = [];

        foreach ($array as $value)
        {
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
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']))
            return true;
        return false;
    }

    /**
     * Checks if the current request is POST
     *
     * @return boolean
     */
    public function isPost()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST")
            return true;
        return false;
    }

    /**
     * Checks if the current request is GET
     *
     * @return boolean
     */
    public function isGet()
    {
        if ($_SERVER["REQUEST_METHOD"] == "GET")
            return true;
        return false;
    }
}