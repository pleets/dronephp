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

use Drone\Mvc\AbstractionController;

/**
 * AbstractionModule class
 *
 * This is an abstract class required for each mvc module. The first code execution
 * in a route is the module, after the module loads the controller.
 */
abstract class AbstractionModule
{
    /**
     * The module's name
     *
     * @var string
     */
    protected $moduleName;

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
     * Constructor
     *
     * @param string                $moduleName
     * @param AbstractionController $controller
     */
    public function __construct($moduleName, AbstractionController $controller)
    {
        $this->moduleName = $moduleName;
        $this->init($controller);
    }

    /**
     * Abstract method to be executed before each controller in each module
     *
     * @param AbstractionController
     */
    public abstract function init(AbstractionController $controller);

    /**
     * Returns an array with application settings
     *
     * @return array
     */
    public function getConfig()
    {
        return include 'module/' . $this->getModuleName() . '/config/module.config.php';
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

        $class = "module/" . $module . "/source/" . implode("/", $nm) . ".php";

        if (file_exists($class))
            include $class;
    }
}