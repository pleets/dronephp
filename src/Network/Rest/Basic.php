<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Network\Rest;

/**
 * Basic class
 *
 * Class for Basic access authetication
 */
class Basic extends AbstractRest
{
    /**
     * Requests client authentication
     *
     * @return null
     */
    public function request()
    {
        if (empty($_SERVER['PHP_AUTH_USER']))
        {
            $status = $this->http::HTTP_UNAUTHORIZED;

            $this->http->writeStatus($status);
            header('WWW-Authenticate: Basic realm="'.$this->realm.'"');
            die('Error ' . $status .' (' . $this->http->getStatusText($status) . ')!!');
        }
    }

    /**
     * Checks credentials
     *
     * @return boolean
     */
    public function authenticate()
    {

        if (!isset($_SERVER['PHP_AUTH_USER']))
        {
            $this->http->writeStatus($this->http::HTTP_UNAUTHORIZED);
            return false;
        }

        $username = $_SERVER['PHP_AUTH_USER'];

        if (!isset($this->whiteList[$username]))
        {
            $this->http->writeStatus($this->http::HTTP_UNAUTHORIZED);
            return false;
        }

        if ($this->whiteList[$username] !== $_SERVER['PHP_AUTH_PW'])
        {
            $this->http->writeStatus($this->http::HTTP_UNAUTHORIZED);
            return false;
        }

        $this->username = $username;

        return true;
    }

    /**
     * Shows the server response
     *
     * @return null
     */
    public function response()
    {
        $status = http_response_code();
        echo 'Error ' . $status .' (' . $this->http->getStatusText($status) . ')!!';
    }
}