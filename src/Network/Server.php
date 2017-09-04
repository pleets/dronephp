<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

namespace Drone\Network;

class Server
{
    /**
     * Returns http host
     *
     * @return string
     */
    public static function getHost()
    {
        return $_SERVER["HTTP_HOST"];
    }

    /**
     * Returns server port
     *
     * @return string
     */
    public static function getServerPort()
    {
        return $_SERVER["SERVER_PORT"];
    }

    /**
     * Returns client port
     *
     * @return string
     */
    public static function getClientPort()
    {
        return $_SERVER["REMOTE_PORT"];
    }

    /**
     * Returns server ip
     *
     * @return string
     */
    public static function getServerIP()
    {
        return $_SERVER["SERVER_ADDR"];
    }

    /**
     * Returns client ip
     *
     * @return string
     */
    public static function getClientIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            return $_SERVER['HTTP_CLIENT_IP'];

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            return $_SERVER['HTTP_X_FORWARDED_FOR'];

        return $_SERVER['REMOTE_ADDR'];
    }
}