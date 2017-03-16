<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/Drone
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\LayoutManager;

use Drone\Mvc\AbstractionController;
use Exception;

class Layout
{
	/**
	 * Controller instance
	 *
	 * @var AbstractionController
	 */
	private $controller;

	/**
	 * View path
	 *
	 * @var string
	 */
	private $view;

	/**
	 * Document title
	 *
	 * @var string
	 */
	private $title;

	/**
	 * Returns the instance of current controller
	 *
	 * @return AbstractionController
	 */
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * Returns the document title
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Sets the document title
	 *
	 * @return string
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * Constructor
	 *
	 * @param AbstractionController
	 */
	public function __construct(AbstractionController $controller)
	{
		// str_replace() is needed in linux systems
		$view = 'module/' . $controller->getModule()->getModuleName() .'/source/view/'. basename(str_replace('\\','/',get_class($controller))) . '/' . $controller->getMethod() . '.phtml';

		$this->controller = $controller;
		$this->view = $view;

		if (!file_exists($view) && !$controller->getTerminal())
			throw new Exception("The 'view' template $view does not exists");

		$params = $controller->getParams();

		if ($controller->getTerminal() && file_exists($view))
			include $view;
		else {
			$config = $controller->getModule()->getConfig();
			include $config["view_manager"]["template_map"][$controller->getLayout()];
		}
	}

	/**
	 * Includes the file view
	 *
	 * @return null
	 */
	public function content()
	{
		include $this->view;
	}

	/**
	 * Returns a user param
	 *
	 * This param is sent through the controller in a return statement
	 *
	 * @param string
	 *
	 * @return mixed
	 */
	public function param($paramName)
	{
		return $this->getController()->getParam($paramName);
	}

	/**
	 * Checks if a parameters exists
	 *
	 * @param string
	 *
	 * @return boolean
	 */
	public function isParam($paramName)
	{
		return $this->getController()->isParam($paramName);
	}

	/**
	 * Returns all parameters sent through the controller
	 *
	 * @return array
	 */
	public function getParams()
	{
		return $this->getController()->getParams();
	}

	/**
	 * Returns the base path of the application
	 *
	 * @return string
	 */
	public function basePath()
	{
		return $this->getController()->basePath;
	}
}