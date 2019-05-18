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

use Drone\Network\Http;

/**
 * Rest class
 *
 * Abstract class for REST. Basic and Digest muest be implemented.
 */
abstract class AbstractRest
{
    /**
     * Description of the protected area
     *
     * @var string
     */
    protected $realm;

    /**
     * White list for authentication
     *
     * @var array
     */
    protected $whiteList;

    /**
     * The username credential
     *
     * @var string
     */
    protected $username;

    /**
     * Server response
     *
     * @var string
     */
    protected $response;

    /**
     * HTTP instance
     *
     * @var Http
     */
    protected $http;

    /**
     * Returns the response attribute
     *
     * @return string
     */
    public function getRealm()
    {
        return $this->realm;
    }

    /**
     * Returns the white list for authentication
     *
     * @return array
     */
    public function getWhiteList()
    {
        return $this->whiteList;
    }

    /**
     * Returns the username credential
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Returns the response attribute
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Returns the HTTP instance
     *
     * @return Http
     */
    public function getHttp()
    {
        return $this->http;
    }

    /**
     * Sets realm attribute
     *
     * @param string $realm
     *
     * @return null
     */
    public function setRealm($realm)
    {
        $this->realm = $realm;
    }

    /**
     * Sets a white list for authentication
     *
     * @param array $whiteList
     *
     * @throws RuntimeException
     *
     * @return null
     */
    public function setWhiteList(array $whiteList)
    {
        if (empty($whiteList)) {
            throw new \RuntimeException("Empty whitelist!");
        }

        $this->whiteList = $whiteList;
    }

    /**
     * Sets the HTTP instance
     *
     * @param Http $http
     *
     * @return null
     */
    public function setHttp($http)
    {
        $this->http = $http;
    }

    /**
     * REST Constructor
     *
     * All modifiable attributes (i.e. with setter method) can be passed as key
     *
     * @param array $options
     */
    public function __construct($options)
    {
        foreach ($options as $option => $value) {
            if (property_exists(__CLASS__, strtolower($option)) && method_exists($this, 'set'.$option)) {
                $this->{'set'.$option}($value);
            }
        }

        # HTTP instance
        $this->http = new Http();
    }

    /**
     * Requests client authentication
     *
     * @return boolean
     */
    abstract public function request();

    /**
     * Checks credentials
     *
     * @return boolean
     */
    abstract public function authenticate();

    /**
     * Writes the response
     *
     * @return boolean
     */
    abstract public function response();
}
