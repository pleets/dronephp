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
 * Router class
 *
 * This class build the route and calls to specific application controller
 */
class Router
{
    /**
     * List of routes
     *
     * @var array
     */
    private $routes;

    /**
     * The Identifiers builds the route
     *
     * @var array
     */
    private $identifiers;

    /**
     * Controller instance
     *
     * @var Drone\Mvc\AbstractionController
     */
    private $controller;

    /**
     * The base path of the application
     *
     * @var string
     */
    private $basePath;

    /**
     * Returns all routes built
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Returns all identifiers
     *
     * @return array
     */
    public function getIdentifiers()
    {
        return $this->identifiers;
    }

    /**
     * Returns the controller instance
     *
     * @return Drone\Mvc\AbstractionController
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Returns the base path of the application
     *
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath();
    }

    /**
     * Sets identifiers
     *
     * @param string $module
     * @param string $controller
     * @param string $view
     *
     * @return null
     */
    public function setIdentifiers($module, $controller, $view)
    {
        $this->identifiers = array(
            "module"        => $module,
            "controller"    => $controller,
            "view"          => $view
        );
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
     * Constructor
     *
     * @param  array $routes
     */
    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    /**
     * Builds the current route and calls the controller
     *
     * @throws Exception\PageNotFoundException
     *
     * @return  null
     */
    public function run()
    {
        /*
         *  Route builder:
         *  The route is constructed from the URL in the following order
         *  www.example.com/module/controller/view
         */

        $module = (is_null($this->identifiers["module"]) || empty($this->identifiers["module"]))
                    ? $this->routes["defaults"]["module"] : $this->identifiers["module"];

        if (!array_key_exists($module, $this->routes))
            throw new Exception\ModuleNotFoundException("The key '$module' does not exists in routes!");

        $controller = (is_null($this->identifiers["controller"]) || empty($this->identifiers["controller"]))
                    ? $this->routes[$module]["controller"] : $this->identifiers["controller"];

        $view = (is_null($this->identifiers["view"]) || empty($this->identifiers["view"]))
                    ? $this->routes[$module]["view"] : $this->identifiers["view"];

        $fqn_controller = '\\' . $module . "\Controller\\" . $controller;

        if (class_exists($fqn_controller))
            $this->controller = new $fqn_controller($module, $view, $this->basePath);
        else
            throw new Exception\ControllerNotFoundException("The control class '$fqn_controller' does not exists!");
    }

    /**
     * Adds a new route to router
     *
     * @param Array $route
     *
     * @throws LogicException
     *
     * @return string
     */
    public function addRoute(Array $route)
    {
        $key = array_keys($route);
        $key = array_shift($key);

        if (array_key_exists($key, $this->routes))
            throw new \LogicException("The key '$key' was already defined as route");

        $this->routes = array_merge($this->routes, $route);
    }
}