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
 * This class manges the interaction between models and views
 */
abstract class AbstractionController
{
    use \Drone\Util\ParamTrait;

    /**
     * Current module
     *
     * @var object
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
    private $terminal = false;

    /**
     * Indicates if controller should show the views
     *
     * @var boolean
     */
    private $showView = true;

    /**
     * Defines starting execution
     *
     * When this parameter is true, the constructor executes the method of the specified controller
     * The only way to stop init execution is throw the method stopExecution() inside a module class
     *
     * @var boolean
     */
    private $initExecution = true;

    /**
     * Base path
     *
     * @var string
     */
    private $basePath;

    /**
     * Returns the current module
     *
     * @return object
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
     * Returns the base path
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
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

        $result = array();

        foreach ($array as $value)
        {
            $io = explode("=", $value);
            $result[$io[0]] = $io[1];
        }

        return $result;
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
     * Constructor
     *
     * @param string $module
     * @param string $method
     * @param string $basePath
     *
     * @throws Exception\PageNotFoundException
     */
    public function __construct($module, $method, $basePath)
    {
        $this->basePath = $basePath;
        $this->parseRequestParameters($_GET);

        /* Module class:
         * Each module must have a class called Module in her namesapce. This class
         * is initilized here, and contains several configurations and methods for
         * controllers.
         */
        $fqn = "\\" . $module . "\\Module";

        $this->module = new $fqn($module, $this);

        # detects method change inside Module.php
        if (!is_null($this->getMethod()))
            $method = $this->getMethod();

        if (!is_null($method) && $this->initExecution)
        {
            if (method_exists($this, $method))
            {
                $class = __CLASS__;

                $reflection = new \ReflectionMethod($this, $method);

                if (!$reflection->isPublic())
                    throw new Exception\PageNotFoundException("The method '$method' is not public in the control class '$class'");

                $this->method = $method;

                // Get the return value of the method (parameters sent to the view)
                $this->params = $this->$method();

                if (!is_null($this->getMethod()))
                {
                    $params = $this->getParams();

                    $layout_params = (count($params) && array_key_exists('::Layout', $params)) ? $params["::Layout"] : [];

                    $layoutManager = new Layout($layout_params);
                    $layoutManager->fromController($this);
                }
            }
            else {
                $class = __CLASS__;
                throw new Exception\PageNotFoundException("The method '$method' doesn't exists in the control class '$class'");
            }
        }
    }

    /**
     * Stops the execution of the specified method inside of __construct()
     *
     * @return null
     */
    public function stopExecution()
    {
        $this->initExecution = false;
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

    /**
     * Parses requests parameters
     *
     * Searches for URI formed as follows /var1/value1/var2/value2
     *
     * @param string $get
     *
     * @return null
     */
    private function parseRequestParameters($get)
    {
        if (array_key_exists('params', $_GET))
        {
            $params = explode("/", $_GET["params"]);

            $vars = $values = array();

            $i = 1;
            foreach ($params as $item)
            {
                if ($i % 2 != 0)
                    $vars[] = $item;
                else
                    $values[] = $item;
                $i++;
            }

            $vars_count = count($vars);

            for ($i = 0; $i < $vars_count; $i++)
            {
                if (array_key_exists($i, $values))
                    $_GET[$vars[$i]] = $values[$i];
                else
                    $_GET[$vars[$i]] = '';
            }

            unset($_GET["params"]);
        }
    }
}