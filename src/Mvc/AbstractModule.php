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
 * This is an abstract class to execute some code before method execution in a controller.
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
        $this->configFile = $configFile;
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
        $_file = $this->configFile;

        if (!file_exists($_file))
            throw new \RuntimeException("The config file '$_file' does not exists");

        return (array) include $this->configFile;
    }
}