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

/**
 * AbstractModule class
 *
 * This is an abstract class required for each mvc module. The first code execution
 * in a route is the module, after the module loads the controller.
 */
abstract class AbstractModule
{
    /**
     * The module name
     *
     * @var string
     */
    protected $moduleName;

    /**
     * The module path
     *
     * The path where modules are located.
     *
     * @var string
     */
    protected $modulePath;

    /**
     * The controller path
     *
     * The path where controllers are located.
     *
     * @var string
     */
    protected $contollerPath;

    /**
     * The view path
     *
     * The path where views are located.
     *
     * @var string
     */
    protected $viewPath;

    /**
     * The Router instace
     *
     * @var string
     */
    protected $router;

    /**
     * Returns the moduleName attribute
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * Returns the modulePath attribute
     *
     * @return string
     */
    public function getModulePath()
    {
        return $this->modulePath;
    }

    /**
     * Returns the controllerPath attribute
     *
     * @return string
     */
    public function getControllerPath()
    {
        return $this->controllerPath;
    }

    /**
     * Returns the viewPath attribute
     *
     * @return string
     */
    public function getViewPath()
    {
        return $this->viewPath;
    }

    /**
     * Returns the Router instance
     *
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Sets the moduleName attribute
     *
     * @param string $moduleName
     *
     * @return null
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
    }

    /**
     * Sets the modulePath attribute
     *
     * @param string $modulePath
     *
     * @return null
     */
    public function setModulePath($modulePath)
    {
        $this->modulePath = $modulePath;
    }

    /**
     * Sets the controllerPath attribute
     *
     * @param string $controllerPath
     *
     * @return null
     */
    public function setControllerPath($controllerPath)
    {
        $this->controllerPath = $controllerPath;
    }

    /**
     * Sets the viewPath attribute
     *
     * @param string $viewPath
     *
     * @return null
     */
    public function setViewPath($viewPath)
    {
        $this->viewPath = $viewPath;
    }

    /**
     * Sets the Router instance
     *
     * @param Router $router
     *
     * @return null
     */
    public function setRouter($router)
    {
        $this->router = $router;
    }

    /**
     * Constructor
     *
     * @param string             $moduleName
     * @param AbstractController $controller
     * @param Router             $router
     */
    public function __construct($moduleName, AbstractController $controller, Router $router)
    {
        $this->moduleName = $moduleName;
        $this->router     = $router;
        $this->init($controller);
    }

    /**
     * Abstract method to be executed before each controller execution in each module
     *
     * @param AbstractController
     */
    public abstract function init(AbstractController $controller);

    /**
     * Returns an array with application settings
     *
     * @return array
     */
    public function getConfig()
    {
        return include(
            $this->router->getBasePath() .'/'. $this->modulePath .'/' . $this->getModuleName() . '/config/module.config.php'
        );
    }

    /**
     * Creates an autoloader for module classes
     *
     * @param string $name
     *
     * @return null
     */
    public static function loader($name)
    {
        $nm = explode('\\', $name);
        $module = array_shift($nm);

        $class = $this->router->getBasePath() .'/'. $this->modulePath ."/". $module . "/source/" . implode("/", $nm) . ".php";

        if (file_exists($class))
            include $class;
    }
}