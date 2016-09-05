<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Mvc;

use Exception as Exception;
use Drone\FileSystem\Shell as Shell;

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
	 * @return Drone\Mvc\Router
     */
	public function getRouter()
	{
		return $this->router;
	}

    /**
     * Prepare app environment
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
     * Check app.config structure
     *
     * @param array $parameters
     *
	 * @return null
     */
	public function verifyRequiredParameters(Array $required_tree, Array $parameters)
	{
		foreach ($required_tree as $key => $value)
		{
			$req_keys = array_keys($parameters);

			if (!in_array($key, $req_keys))
				throw new Exception("The key '$key' must be in the configuration!", 1);

			if (is_array($value))
				$this->verifyRequiredParameters($value, $parameters[$key]);
		}
	}

    /**
     * Constructor
     *
     * @param array $init_parameters
     */
	public function __construct($init_parameters)
	{
		$this->prepare();

		$this->verifyRequiredParameters(
			array(
				"modules" 		=> array(
					"{key}"		=> "{value}"
				),
				"router"		=> array(
					"routes"	=> array(
			            'defaults' => array(
			                'module' 		=> '{value}',
			                'controller'	=> '{value}',
			                'view' 			=> '{value}'
			            )
					)
				),
				"environment"	=> array(
					"base_path" => "{value}",
					"dev_mode"	=> "{value}"
				)
			),
		$init_parameters);

		$this->devMode = $init_parameters["environment"]["dev_mode"];
		$this->modules = $init_parameters["modules"];

		/*
		 *	DEV MODE:
		 *	Set Development or production environment
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

		$this->loadModules($this->modules, $init_parameters["router"]["routes"]["defaults"]["module"]);

		$this->router = new Router($init_parameters["router"]["routes"]);
		$this->router->setBasePath($init_parameters["environment"]["base_path"]);

		# load routes from modules
		foreach ($this->modules as $module)
		{
			$module_config_file = require "module/$module/config/module.config.php";
			$this->getRouter()->addRoute($module_config_file["router"]["routes"]);
		}
	}

    /**
     * Load user classes in each module
     *
     * @param array $modules
     *
	 * @return null
     */
	private function loadModules($modules, $module)
	{
		$fileSystem = new Shell();

		if ($modules)
		{
			$mod = array_key_exists('module', $_GET) ? $_GET["module"] : $module;

			foreach ($modules as $module)
			{
				/* Load only Pleets componentes when the current module is Pleets */
				if ($mod == 'Pleets' && $module != 'Pleets')
					continue;
				else {
					// First include the Module class
					include("module/".$module."/Module.php");

					$controllers = $fileSystem->ls("module/".$module."/source/controller");

					// Load controllers for each module
					foreach ($controllers as $controller)
					{
						if (!in_array($controller, array('.', '..')))
							include("module/".$module."/source/controller/" . $controller);
					}

					$models = $fileSystem->ls("module/".$module."/source/model");

					// Load models for each module
					foreach ($models as $model)
					{
						if (!in_array($model, array('.', '..')))
							include("module/".$module."/source/model/" . $model);
				    }
				}
			}
		}
		else
			throw new Exception("The application must have at least one module");
	}

    /**
     * Run application
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