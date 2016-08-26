<?php

namespace Drone\Mvc;

class Application
{
	protected $module;
	protected $controller;
	protected $view;

	private $settings;

	public function __construct()
	{
		// Start sessions
		if (!isset($_SESSION))
			session_start();

		$_SESSION["APP"] = __CLASS__;

		// Get settings from application.config.php
		$this->settings = require "config/application.config.php";
		$settings = $this->getSettings();

		/*
		 *	APP CONSTANTS
		 */

		if (!array_key_exists('app', $settings))
			throw new \Exception("The key 'app' does not exists in the configuration file");
		if (!array_key_exists('base_path', $settings["app"]))
			throw new \Exception("The key 'base_path' does not exists in the configuration file");

		define('BASEPATH', $settings["app"]["base_path"]);

		/*
		 *	DEV MODE:
		 *	Set Development or production environment
		 */

		$devMode = (!array_key_exists('development_environment', $settings["app"]) || !$settings["app"]["development_environment"]) ? false : true;

		if ($devMode)
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

		/*
		 *	GET URL PARAMETERS:
		 *	The route is constructed from the URL in the following order
		 *	www.example.com/module/controller/view
		 */

		$this->module = isset($_GET["module"]) ? $_GET["module"] : null;
		$this->controller = isset($_GET["controller"]) ? $_GET["controller"] : null;
		$this->view = isset($_GET["view"]) ? $_GET["view"] : null;

		$this->loadUserClasses($settings);

		$this->buildRoute($settings);
	}

	/* Getters */
	public function getModule() { return $this->module; }
	public function getController() { return $this->controller; }
	public function getView() { return $this->view; }

	public function getSettings() { return $this->settings; }

	/* Setters */
	public function setModule($module) { return $this->module = $module; }
	public function setController($controller) { return $this->controller = $controller; }
	public function setView($view) { return $this->view = $view; }

	private function loadUserClasses($settings)
	{
		/*
		 *	LOAD USER CLASSES:
		 *	Loads all classes that has been created for the user.
		 */

		$fileSystem = new \Drone\FileSystem\Shell();

		if (!array_key_exists('modules', $settings))
			throw new \Exception("The key 'modules' does not exists in the configuration file");

		if (count($settings["modules"]))
		{
			$mod = $this->getModule();

			foreach ($settings["modules"] as $module)
			{
				/* Load only Pleets componentes when the current module is Pleets */
				if ($mod == 'Pleets' && $module != 'Pleets')
					continue;
				else {
					// First include the Module class
					include("module/".$module."/Module.php");

					$controllers = $fileSystem->ls("module/".$module."/source/controller");

					// Load controllers for each module
					foreach ($controllers as $controller) {
						if (!in_array($controller, array('.', '..')))
							include("module/".$module."/source/controller/" . $controller);
					}

					$models = $fileSystem->ls("module/".$module."/source/model");

					// Load models for each module
					foreach ($models as $model) {
						if (!in_array($model, array('.', '..')))
							include("module/".$module."/source/model/" . $model);
				    }
				}
			}
		}
		else
			throw new \Exception("The application must have at least one module");
	}

	private function buildRoute($settings)
	{
		/*
		 *	PARSE ROUTE CONFIGURATION:
		 *
		 *	The file config/application.config.php should return an array that
		 *	contains the following array to assign the default route
		 *
		 *	   'router' => array(
		 *			'routes' => array(
		 *				'defaults' => array(
		 *					'module' => 'MyModule',
		 *					'controller' => 'MyController',
		 *					'view'     => 'MyView',
		 *				),
		 *			),
		 *	   ),
		 */

		if (is_null($this->getModule()))
		{
			if (!array_key_exists('router', $settings))
				throw new \Exception("The key 'router' does not exists in the configuration file");

			if (!array_key_exists('routes', $settings["router"]))
				throw new \Exception("The key 'routes' does not exists in the 'router' key in the configuration file");

			/*
			 *	GETS DEFAULT ROUTE
			 */

			if (!array_key_exists('defaults', $settings["router"]["routes"]))
				throw new \Exception("The key 'defaults' does not exists in '[router][routes]' key in the configuration file");

			if (!array_key_exists('module', $settings["router"]["routes"]["defaults"]))
				throw new \Exception("You must define the module in the 'defaults' route");

			if (!array_key_exists('controller', $settings["router"]["routes"]["defaults"]))
				throw new \Exception("You must define the controller in the 'defaults' route");

			if (!array_key_exists('view', $settings["router"]["routes"]["defaults"]))
				throw new \Exception("You must define the view in the 'defaults' route");
		}
		else {
			$module = $this->getModule();
			$moduleFileSettings = "module/$module/config/module.config.php";
			$moduleSettings = require $moduleFileSettings;

			if (!array_key_exists('router', $moduleSettings))
				throw new \Exception("The key 'router' does not exists in the configuration file $moduleFileSettings");

			if (!array_key_exists('routes', $moduleSettings["router"]))
				throw new \Exception("The key 'routes' does not exists in the 'router' key in the configuration file $moduleFileSettings");

			if (!array_key_exists($module, $moduleSettings["router"]["routes"]))
				throw new \Exception("The key '$module' does not exists in '[router][routes]' key in the configuration file $moduleFileSettings");

			$spModule = $moduleSettings["router"]["routes"][$module]["module"];
			if (!in_array($spModule, $settings['modules']))
				throw new \Exception("The module '$spModule' does not exist in the global configuration");

			if (!array_key_exists('module', $moduleSettings["router"]["routes"][$module]))
				throw new \Exception("You must define the module in the '$module' route");

			/*
			 *	GETS RELATIVE ROUTE TO MODULE
			 */

			if (is_null($this->getController()))
			{
				if (!array_key_exists('controller', $moduleSettings["router"]["routes"][$module]))
					throw new \Exception("You must define the controller in the '$module' route");

				if (!array_key_exists('view', $moduleSettings["router"]["routes"][$module]))
					throw new \Exception("You must define the view in the '$module' route");
			}
			if (is_null($this->getView()))
			{
				if (!array_key_exists('view', $moduleSettings["router"]["routes"][$module]))
					throw new \Exception("You must define the view in the '$module' route");
			}
		}


		/*
		 *	BUILD DEFAULT ROUTE
		 */

		if (is_null($this->getModule()))
		{
			$this->module = $settings['router']['routes']['defaults']['module'];
			$this->controller = $settings['router']['routes']['defaults']['controller'];
			$this->view = $settings['router']['routes']['defaults']['view'];
		}
		else if (is_null($this->getController()))
		{
			$this->module = $spModule;
			$this->controller = $moduleSettings['router']['routes'][$module]['controller'];
			$this->view = $moduleSettings['router']['routes'][$module]['view'];
		}
		else if (is_null($this->getView()))
		{
			$this->module = $spModule;
			$this->view = $moduleSettings['router']['routes'][$module]['view'];
		}
	}

	public function run()
	{
		try {
			if (!is_null($this->getController()))
			{
				$controller = '\\' . $this->getModule() . "\Controller\\" . $this->getController();

				if (class_exists($controller))
					$controller_instance = new $controller($this->getModule(), $this->getView());
				else
					throw new \Exception("The control class $controller does not exists");
			}
			else
				throw new \Exception("The control class is NULL");
		}
		catch (\Exception $e) {
			echo $e->getMessage();
		}
	}
}