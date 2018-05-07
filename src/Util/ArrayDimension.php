<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
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

        foreach ($array as $param => $configure)
        {
            if (is_array($configure))
            {
                foreach ($configure as $key => $value)
                {
                    $again = true;
                    $new_config[$param . $glue . $key] = $value;
                }
            }
            else
                $new_config[$param] = $configure;
        }

        return (!$again) ? $new_config : self::toUnidimensional($new_config, $glue);
    }
}