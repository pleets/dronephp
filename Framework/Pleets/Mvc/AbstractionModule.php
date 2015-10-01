<?php

namespace Pleets\Mvc;

abstract class AbstractionModule
{
	protected $moduleName;

	public function __construct($moduleName)
	{
		$this->moduleName = $moduleName;
		$this->init();
	}

	public abstract function init();

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