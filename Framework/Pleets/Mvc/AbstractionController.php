<?php

namespace Pleets\Mvc;

abstract class AbstractionController
{
	private $module;
	private $method = null;

	private $params;

	private $layout = "default";
	private $terminal = false;

	public function __construct($module, $method)
	{
		$this->parseRequestParameters($_GET);

		/* Module class:
		 * Each module must have a class called Module in her namesapce. This class
		 * is initilized here, and contains several configurations and methods for
		 * controllers.
		 */
		$fqn = "\\" . $module . "\\Module";

		$this->module = new $fqn($module, $this);

		if (!is_null($method))
		{
			if (method_exists($this, $method))
			{
				$this->method = $method;

				// Get the return value of the method (parameters sent to the view)
				$this->params = $this->$method();
			}
			else {
				$class = __CLASS__;
				throw new \Exception("The '$method' method doesn't exists in the $class control class");
			}
		}
	}

	/* Getters */
	public function getModule() { return $this->module; }
	public function getMethod() { return $this->method; }
	public function getParams() { return $this->params; }

	public function getParam($param)
	{
		$parameters = $this->getParams();
		return $parameters[$param];
	}

	public function isParam($param) 
	{
		$parameters = $this->getParams();

		if (array_key_exists($param, $parameters))
			return true;

		return false;
	}

	public function getTerminal() { return $this->terminal; }
	public function getLayout() { return $this->layout; }

	public static function getClassName() { return __CLASS__; }

	/* Setters */
	public function setMethod($method) { $this->method = $method; }

	public function isXmlHttpRequest()
	{
	   if (isset($_SERVER['CONTENT_TYPE']))
			return true;
	   return false;
	}

	public function isPost()
	{
		if ($_SERVER["REQUEST_METHOD"] == "POST")
			return true;
		return false;
	}

	public function setTerminal($terminal = true)
	{
		$this->terminal = $terminal;
	}

	public function setLayout($layout)
	{
		$this->layout = $layout;
	}

	private function parseRequestParameters($get)
	{
		if (array_key_exists('params', $_GET))
		{
			$params = explode("/", $_GET["params"]);
			
			$vars = $values = array();

			$i = 1;
			foreach ($params as $item) 
			{
				if ($i % 2 != 0)
					$vars[] = $item;
				else
					$values[] = $item;
				$i++;
			}

			for ($i = 0; $i < count($vars); $i++) 
			{
				if (array_key_exists($i, $values)) 
					$_GET[$vars[$i]] = $values[$i];
				else
					$_GET[$vars[$i]] = '';
			}

			unset($_GET["params"]);
		}
	}

	public function __destruct()
	{
		if (!is_null($this->getMethod()))
			$layoutManager = new \Pleets\LayoutManager\Layout($this);
	}
}