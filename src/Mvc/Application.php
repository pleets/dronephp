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

use Drone\FileSystem\Shell;

/**
 * Application class
 *
 * This is the main class for mvc pattern
 */
class Application
{
    /**
     * @var array
     */
    private $modules;

    /**
     * @var Drone\Mvc\Router
     */
    private $router;

    /**
     * @var boolean
     */
    private $devMode;

    /**
     * Returns the router instance
     *
     * @return Drone\Mvc\Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Prepares the app environment
     *
     * @return null
     */
    public function prepare()
    {
        # start sessions
        if (!isset($_SESSION))
            session_start();
    }

    /**
     * Constructor
     *
     * @param array $init_parameters
     */
    public function __construct($init_parameters)
    {
        $this->prepare();

        $this->devMode = $init_parameters["environment"]["dev_mode"];
        $this->modules = $init_parameters["modules"];

        /*
         *  DEV MODE:
         *  Set Development or production environment
         */

        if ($this->devMode)
        {
            ini_set('display_errors', 1);

            // See errors
            // error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

            // PHP 5.4
            // error_reporting(E_ALL);

            // Best way to view all possible errors
            error_reporting(-1);
        }
        else {
            ini_set('display_errors', 0);
            error_reporting(-1);
        }

        $this->loadModules($this->modules);

        $this->router = new Router($init_parameters["router"]["routes"]);
        $this->router->setBasePath($init_parameters["environment"]["base_path"]);

        # load routes from modules
        foreach ($this->modules as $module)
        {
            if (file_exists("module/$module/config/module.config.php"))
            {
                $module_config_file = require "module/$module/config/module.config.php";
                $this->getRouter()->addRoute($module_config_file["router"]["routes"]);
            }
        }
    }

    /**
     * Loads module classes and autoloading functions
     *
     * @param array $modules
     *
     * @throws RuntimeException
     *
     * @return null
     */
    private function loadModules($modules)
    {
        if ($modules)
        {
            foreach ($modules as $module)
            {
                /*
                 *  This instruction include each module declared in application.config.php
                 *  Each module has an autoloader to load its classes (controllers and models)
                 */
                if (file_exists("module/".$module."/Module.php"))
                    include("module/".$module."/Module.php");

                spl_autoload_register($module . "\Module::loader");
            }
        }
        else
            throw new \RuntimeException("The application must have at least one module");
    }

    /**
     * Runs the application
     *
     * @return null
     */
    public function run()
    {
        $module = isset($_GET["module"]) ? $_GET["module"] : null;
        $controller = isset($_GET["controller"]) ? $_GET["controller"] : null;
        $view = isset($_GET["view"]) ? $_GET["view"] : null;

        $this->router->setIdentifiers($module, $controller, $view);
        $this->router->run();
    }
}