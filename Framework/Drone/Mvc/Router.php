<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/Drone
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Mvc;

use Exception as Exception;

class Router
{
    /**
     * @var array
     */
	private $routes;

    /**
     * Prepare identifiers to build the route
     *
     * @var array
     */
	private $identifiers;

    /**
     * @var object
     */
	private $controller;

    /**
     * @var string
     */
	private $basePath;

    /**
     * @return array
     */
	public function getRoutes()
	{
		return $this->routes;
	}

    /**
     * @return array
     */
	public function getIdentifiers()
	{
		return $this->identifiers;
	}

    /**
     * @return object
     */
	public function getController()
	{
		return $this->controller;
	}

    /**
     * @return string
     */
    public function getBasePath()
	{
		return $this->basePath();
	}

    /**
     * @param string $module
     * @param string $controller
     * @param string $view
     *
     * @return null
     */
	public function setIdentifiers($module, $controller, $view)
	{
		$this->identifiers = array(
			"module"		=> $module,
			"controller"	=> $controller,
			"view"			=> $view
		);
	}

    /**
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
     * Build current route and call the controller
     *
     * @return  null
     */
	public function run()
	{
		/*
		 *	Route builder:
		 *	The route is constructed from the URL in the following order
		 *	www.example.com/module/controller/view
		 */

		$module = (is_null($this->identifiers["module"]) || empty($this->identifiers["module"]))
					? $this->routes["defaults"]["module"] : $this->identifiers["module"];

		$controller = (is_null($this->identifiers["controller"]) || empty($this->identifiers["controller"]))
					? $this->routes["defaults"]["controller"] : $this->identifiers["controller"];

		$view = (is_null($this->identifiers["view"]) || empty($this->identifiers["view"]))
					? $this->routes["defaults"]["view"] : $this->identifiers["view"];

		$fqn_controller = '\\' . $module . "\Controller\\" . $controller;

		if (class_exists($fqn_controller))
			$this->controller = new $fqn_controller($module, $view, $this->basePath);
		else
			throw new Exception("The control class '$fqn_controller' does not exists!", 1);
	}

    /**
     * @param Array $routes
     *
     * @return string
     */
	public function addRoute($route)
	{
		$this->routes = array_merge($this->routes, $route);
	}
}