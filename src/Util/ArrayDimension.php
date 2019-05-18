<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Util;

/**
 * ArrayDimension class
 *
 * Utils functions to operate arrays
 */
class ArrayDimension
{
    /**
     * Converts a multidimensional array in a dimensional array
     *
     * @param array  $array
     * @param string $glue
     *
     * @return array
     */
    public static function toUnidimensional(Array $array, $glue)
    {
        $new_config = [];
        $again = false;

        foreach ($array as $param => $configure) {
            if (is_array($configure)) {
                foreach ($configure as $key => $value) {
                    $again = true;
                    $new_config[$param . $glue . $key] = $value;
                }
            } else {
                $new_config[$param] = $configure;
            }
        }

        return (!$again) ? $new_config : self::toUnidimensional($new_config, $glue);
    }

    /**
     * Search inside a multi-dimensional array a value by nested keys
     *
     * Default value will be returned if any key in the haystack array does not exists
     *
     * @param array $needle
     * @param array $haystack
     * @param mixed $value
     *
     * @return mixed
     */
    public static function ifdef(array $needle, array $haystack, $value)
    {
        $key = array_shift($needle);

        do {
            if (array_key_exists($key, $haystack)) {
                $haystack = $haystack[$key];
            } else {
                return $value;
            }

            $key = count($needle) ? array_shift($needle) : null;

            if (is_null($key)) {
                return $haystack;
            }
        } while (!is_null($key));
    }

    /**
     * Transforms an object to an array
     *
     * @param mixed $obj
     *
     * @return array
     */
    public static function objectToArray($obj)
    {
        if (is_object($obj)) {
            $obj = (array) $obj;
        }

        if (is_array($obj)) {
            $new = array();

            foreach ($obj as $key => $val) {
                $new[$key] = self::objectToArray($val);
            }
        } else {
            $new = $obj;
        }

        return $new;
    }
}
