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