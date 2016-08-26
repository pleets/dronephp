<?php

namespace Drone\Mvc;

abstract class AbstractionModule
{
	protected $moduleName;

	public function __construct($moduleName, $controller)
	{
		$this->moduleName = $moduleName;
		$this->init($controller);
	}

	public abstract function init($controller);

	public function getModuleName()
	{
		return $this->moduleName;
	}

	# Get system configuration
	public function getConfig()
	{
		return include 'module/' . $this->getModuleName() . '/config/module.config.php';
	}
}