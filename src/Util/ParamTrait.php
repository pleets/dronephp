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
 * ParamTrait trait
 *
 * Standard parameters management
 */
trait ParamTrait
{
    /**
     * Current parameters
     *
     * @var array
     */
    protected $params = [];

    /**
     * Returns all parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Gets a particular parameter
     *
     * @param string $param
     *
     * @return string
     */
    public function getParam($param)
    {
        $parameters = $this->getParams();
        return $parameters[$param];
    }

    /**
     * Sets all parameters
     *
     * @param array $params
     *
     * @return null
     */
    public function setParams(Array $params)
    {
        $this->params = $params;
    }

    /**
     * Checks if a parameter exists
     *
     * @param string $param
     *
     * @return boolean
     */
    public function isParam($param)
    {
        $parameters = $this->getParams();

        if (array_key_exists($param, $parameters))
            return true;

        return false;
    }

    /**
     * Returns a param (alias for getParam())
     *
     * @param string $paramName
     *
     * @return mixed
     */
    public function param($paramName)
    {
        return $this->getParam($paramName);
    }
}