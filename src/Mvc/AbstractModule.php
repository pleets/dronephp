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
     * The base path of the application
     *
     * @var string
     */
    private $basePath;

    /**
     * The module path
     *
     * The path where modules are located.
     *
     * @var string
     */
    protected $modulePath;

    /**
     * The class path
     *
     * The path where classes are located (often controllers and models).
     *
     * @var string
     */
    protected $classPath;

    /**
     * The view path
     *
     * The path where views are located.
     *
     * @var string
     */
    protected $viewPath;

    /**
     * Config file for the module
     *
     * @var string
     */
    protected $configFile;

    /**
     * Defines method execution in the controller
     *
     * @var boolean
     */
    private $executionAllowed = true;

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
     * Returns the base path of the application
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
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
     * Returns the classPath attribute
     *
     * @return string
     */
    public function getClassPath()
    {
        return $this->classPath;
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
     * Returns the configFile attribute
     *
     * @return string
     */
    public function getConfigFile()
    {
        return $this->configFile;
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
     * Sets the basePath attribute
     *
     * @param string $basePath
     *
     * @return null
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
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
     * Sets the classPath attribute
     *
     * @param string $classPath
     *
     * @return null
     */
    public function setClassPath($classPath)
    {
        $this->classPath = $classPath;
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
     * Sets the configFile attribute
     *
     * @param string $configFile
     *
     * @throws \RuntimeException
     *
     * @return null
     */
    public function setConfigFile($configFile)
    {
        $_file = $this->basePath .'/'. $this->modulePath .'/'. $this->moduleName .'/'. $configFile;

        if (!file_exists($_file))
            throw new \RuntimeException("The file '$_file' does not exists");

        $this->configFile =
            $this->basePath .'/'. $this->modulePath .'/'. $this->moduleName .'/'. $configFile;
    }

    /**
     * Constructor
     *
     * @param string $moduleName
     */
    public function __construct($moduleName)
    {
        $this->moduleName = $moduleName;
        $this->init();
    }

    /**
     * Abstract method to be executed before each method execution in the controller
     *
     * @param AbstractController
     */
    public abstract function init();

    /**
     * Checks if executionAllowed is true
     *
     * @return null
     */
    public function executionIsAllowed()
    {
        return $this->executionAllowed;
    }

    /**
     * Disallow the method execution in a controller
     *
     * @return null
     */
    public function disallowExecution()
    {
        $this->executionAllowed = false;
    }

    /**
     * Allow the method execution in a controller
     *
     * @return null
     */
    public function allowExecution()
    {
        $this->executionAllowed = true;
    }

    /**
     * Returns the module configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return (array) include $this->configFile;
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

        $class = "";

        if (!empty($this->basePath))
            $class .= $this->basePath .'/';

        if (!empty($this->modulePath))
            $class .= $this->modulePath .'/';

        $class .= $module ."/";

        if (!empty($this->classPath))
            $class .= $this->classPath .'/';

        $class .= implode("/", $nm) . ".php";

        if (file_exists($class))
            include $class;
    }
}