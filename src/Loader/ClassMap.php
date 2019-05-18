<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Loader;

/**
 * AbstractModule class
 *
 * This class is an autoloader
 */
class ClassMap
{
    /**
     * The class path
     *
     * @var string
     */
    public static $path;

    /**
     * Creates an autoloader for module classes
     *
     * @param string $name
     * @param string $path
     *
     * @return null
     */
    public static function autoload($name)
    {
        $nm = explode('\\', $name);
        $module = array_shift($nm);

        $path = is_null(self::$path) ? '' : self::$path . DIRECTORY_SEPARATOR;

        $class = $path . implode(DIRECTORY_SEPARATOR, $nm) . ".php";

        if (file_exists($class)) {
            include $class;
        }
    }
}
