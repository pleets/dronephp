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
            $ht = $this->http;

            $this->http->writeStatus($ht::HTTP_UNAUTHORIZED);
            header('WWW-Authenticate: Basic realm="'.$this->realm.'"');
            die('Error ' . $ht::HTTP_UNAUTHORIZED .' (' . $this->http->getStatusText($ht::HTTP_UNAUTHORIZED) . ')!!');
        }
    }

    /**
     * Checks credentials
     *
     * @return boolean
     */
    public function authenticate()
    {
        $ht = $this->http;

        if (!isset($_SERVER['PHP_AUTH_USER']))
        {
            $this->http->writeStatus($ht::HTTP_UNAUTHORIZED);
            return false;
        }

        $username = $_SERVER['PHP_AUTH_USER'];

        if (!isset($this->whiteList[$username]))
        {
            $this->http->writeStatus($ht::HTTP_UNAUTHORIZED);
            return false;
        }

        if ($this->whiteList[$username] !== $_SERVER['PHP_AUTH_PW'])
        {
            $this->http->writeStatus($ht::HTTP_UNAUTHORIZED);
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
        $this->response = 'Error ' . $status .' (' . $this->http->getStatusText($status) . ')!!';
        echo $this->response;
    }
}