<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/Drone
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\LayoutManager;

class Layout
{
	private $controller;
	private $view;

	private $title;

	public function __construct($controller)
	{
		// str_replace() is needed in linux systems
		$view = 'module/' . $controller->getModule()->getModuleName() .'/source/view/'. basename(str_replace('\\','/',get_class($controller))) . '/' . $controller->getMethod() . '.phtml';

		$this->controller = $controller;
		$this->view = $view;

		if (!file_exists($view))
			throw new \Exception("The 'view' template $view does not exists");

		$params = $controller->getParams();

		if ($controller->getTerminal())
			include $view;
		else {
			$config = $controller->getModule()->getConfig();
			include $config["view_manager"]["template_map"][$controller->getLayout()];
		}
	}

	public function getController() { return $this->controller; }
	public function getTitle() { return $this->title; }

	public function setTitle($title) { $this->title = $title; }

	public function content()
	{
		include $this->view;
	}

	public function param($paramName)
	{
		return $this->getController()->getParam($paramName);
	}

	public function isParam($paramName)
	{
		return $this->getController()->isParam($paramName);
	}

	public function getParams()
	{
		return $this->getController()->getParams();
	}

	public function basePath()
	{
		return $this->getController()->basePath;
	}

}