<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Mvc;

/**
 * ModuleFactory Class
 *
 * This class creates a module instance
 */
class ModuleFactory
{
    /**
     * Creates the module instance
     *
     * @param string             $module
     * @param array              $module_settings
     *
     * @return null
     */
    public static function create($module, array $module_settings = [])
    {
        if (empty($module)) {
            throw new \RuntimeException("The module name must be specified");
        }

        if (!is_string($module)) {
            throw new \InvalidArgumentException("Invalid type given. string expected");
        }

        /*
         * Module class instantiation
         *
         * Each module must have a class called Module in her namespace. This class
         * is initilized here and can change the behavior of a controller using
         * allowExecution() or disallowExecution().
         */
        $fqn_module = "\\" . $module . "\\Module";

        if (!class_exists($fqn_module)) {
            throw new Exception\ModuleNotFoundException("The module class '$fqn_module' does not exists!");
        }

        $module = new $fqn_module($module);

        if (array_key_exists('config', $module_settings) && !is_null($module_settings["config"])) {
            $module->setConfigFile($module_settings["config"]);
        }

        return $module;
    }
}
