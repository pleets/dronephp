<?php

/*
 *	App Autoloader
 */

function FrameworkLoader($name)
{

	$class = __DIR__ . "/". str_replace('\\', '/', $name) . ".php";

	if (file_exists($class))
		include $class;
}

spl_autoload_register("FrameworkLoader");

# load vendor classes
if (file_exists(__DIR__ . '/../vendor/autoload.php'))
	require_once(__DIR__ . '/../vendor/autoload.php');