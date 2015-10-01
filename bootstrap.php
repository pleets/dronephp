<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

// Application configuration
$appConfig = include("config/application.config.php");

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = array_key_exists('development_environment', $appConfig) ? $appConfig["development_environment"] : false;

if (!array_key_exists('modules', $appConfig) || !count($appConfig["modules"]))
	throw new \Exception("There are not modules for the application", 1);

$dirs = array();

foreach ($appConfig["modules"] as $moduleName) 
{
	$dirs[] = 'module/' . $moduleName . '/model';
	$dirs[] = 'module/' . $moduleName . '/model/Entity';
}

$config = Setup::createAnnotationMetadataConfiguration($dirs, $isDevMode);
// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);

// Database configuration
$dbConfig = include("config/database.config.php");

if (!array_key_exists('database', $dbConfig))
	throw new \Exception("The database info is not set", 1);

// obtaining the entity manager
return EntityManager::create($dbConfig["database"], $config);
