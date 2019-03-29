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
     * Module path
     *
     * The path where all modules are located.
     *
     * @var string
     */
    protected $modulePath;

    /**
     * List of modules available
     *
     * Each module in this list must be a folder inside $modulePath
     *
     * @var array
     */
    private $modules;

    /**
     * The router instance
     *
     * @var Router
     */
    private $router;

    /**
     * Development or production mode
     *
     * @var boolean
     */
    private $devMode;

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
     * Returns the modules available
     *
     * @return array
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Returns the router instance
     *
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * Constructor
     *
     * @param array $config
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function __construct(Array $config)
    {
        # start sessions
        if (!isset($_SESSION))
            session_start();

        if (!array_key_exists('environment', $config))
            throw new \InvalidArgumentException("The 'environment' key was not defined");

        if (!array_key_exists('dev_mode', $config['environment']))
            throw new \InvalidArgumentException("The 'dev_mode' key was not defined");

        $this->devMode = $config["environment"]["dev_mode"];

        if (!array_key_exists('modules', $config))
            throw new \InvalidArgumentException("The 'modules' key was not defined");

        $this->modules = $config["modules"];

        # setting module path
        $this->modulePath = (!array_key_exists('module_path', $config['environment']))
            ? 'module'
            : $config['environment']['module_path'];

        #  setting development or production environment
        if ($this->devMode)
        {
            ini_set('display_errors', 1);
            error_reporting(-1);
        }
        else
        {
            ini_set('display_errors', 0);
            error_reporting(0);
        }

        # register autoloading functions for each module
        $this->autoload($this->modules);

        if (!array_key_exists('router', $config))
            throw new \InvalidArgumentException("The 'router' key was not defined");

        if (!array_key_exists('routes', $config["router"]))
            throw new \InvalidArgumentException("The 'routes' key was not defined");

        $this->router = new Router($config["router"]["routes"]);

        if (!array_key_exists('base_path', $config['environment']))
            throw new \InvalidArgumentException("The 'base_path' key was not defined");

        $this->router->setBasePath($config["environment"]["base_path"]);

        # load routes from config
        foreach ($config["router"]["routes"] as $name => $route)
        {
            if ($route instanceof \Zend\Router\Http\RouteInterface)
                $this->getRouter()->addZendRoute($name, $route);
            else
                $this->getRouter()->addRoute($route);
        }

        # load routes from each module
        foreach ($this->modules as $module)
        {
            if (file_exists($this->modulePath . "/$module/config/module.config.php"))
            {
                $module_config_file = require($this->modulePath . "/$module/config/module.config.php");

                if (!array_key_exists('router', $module_config_file))
                    throw new \RuntimeException("The 'router' key was not defined in the config file for module '$module'");

                if (!array_key_exists('routes', $module_config_file["router"]))
                    throw new \RuntimeException("The 'routes' key was not defined in the config file for module '$module'");

                $this->getRouter()->addRoute($module_config_file["router"]["routes"]);
            }
        }
    }

    /**
     * Loads module classes and autoloading functions
     *
     * Each module must have a Module.php inside them. The goal of this file is register an autoloading function
     * to load all its controller and model classes.
     *
     * @param array $modules
     *
     * @throws RuntimeException
     *
     * @return null
     */
    private function autoload(Array $modules)
    {
        if (count($modules))
        {
            foreach ($modules as $module)
            {
                /*
                 *  This instruction includes each module declared.
                 *  Each module has an autoloader to load its classes (controllers and models)
                 */
                if (file_exists($this->modulePath ."/". $module."/Module.php"))
                    include($this->modulePath ."/". $module."/Module.php");

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
        $module     = isset($_GET["module"])     ? $_GET["module"] : null;
        $controller = isset($_GET["controller"]) ? $_GET["controller"] : null;
        $view       = isset($_GET["view"])       ? $_GET["view"] : null;

        $request = new  \Zend\Http\Request();

        # build URI
        $uri = '';
        $uri .= !empty($module)     ? '/' . $module : "";
        $uri .= !empty($controller) ? '/' . $controller : "";
        $uri .= !empty($view)       ? '/' . $view : "";

        if (empty($uri))
            $uri = "/";

        $request->setUri($uri);

        $match = $this->router->getZendRouter()->match($request);

        if (!is_null($match))
        {
            $params = $match->getParams();
            $parts  = explode("\\", $params["controller"]);

            $module     = $parts[0];
            $controller = $parts[2];
            $view       = $params["action"];

            $this->router->setIdentifiers($module, $controller, $view);
        }
        else
            $this->router->setIdentifiers($module, $controller, $view);

        $this->router->run();
    }
}