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
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $response;

    /**
     * @var string
     */
    protected $realm;

    /**
     * @var array
     */
    protected $whiteList;

    /**
     * @var array
     */
    protected $username;

    /**
     * Returns the method attribute
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
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
     * Returns the response attribute
     *
     * @return string
     */
    public function getRealm()
    {
        return $this->realm;
    }

    /**
     * Returns the username attribute
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets method attribute
     *
     * @param string $method
     *
     * @return null
     */
    public function setMethod($method)
    {
        return $this->method = $method;
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
        return $this->realm = $realm;
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
        foreach ($options as $option => $value)
        {
            if (property_exists(__CLASS__, strtolower($option)) && method_exists($this, 'set'.$option))
            $this->{'set'.$option}($value);
        }

        # HTTP instance
        $this->http = new Http();
    }

    /**
     * Sets a white list for authentication
     *
     * @throws RuntimeException
     *
     * @return null
     */
    public function setWhiteList(array $whiteList)
    {
        if (empty($whiteList))
            throw new \RuntimeException("Empty whitelist!");

        $this->whiteList = $whiteList;
    }

    /**
     * Requests client authentication
     *
     * @return boolean
     */
    abstract function request();

    /**
     * Checks credentials
     *
     * @return boolean
     */
    abstract function authenticate();

    /**
     * Writes the response
     *
     * @return boolean
     */
    abstract function response();
}