<?php

chdir(dirname(__DIR__));

// Set localtime zone
date_default_timezone_set("America/Bogota");

// Memory limit
ini_set("memory_limit","256M");


/*
 *	Autoloader de recursos y librerías externads
 */

$directories = array("library");

/* autoload general*/
function LibraryLoader($name)
{
	global $directories;

	foreach ($directories as $dir)
	{
		$class =  dirname(__DIR__) . "/$dir/". str_replace('\\', '/', $name) . ".php";

		if (file_exists($class))
		{
			include $class;
			break;
		}
	}
}

// Librerías
spl_autoload_register("LibraryLoader");

// Run application
require_once("Framework/autoload.php");
$mvc = new Pleets\Mvc\Application();
$mvc->run();