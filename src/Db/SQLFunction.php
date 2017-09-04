<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

namespace Drone\Db;

class SQLFunction
{
    /**
     * The SQL function
     *
     * @var string
     */
    private $function;

    /**
     * The arguments for the SQL function
     *
     * @var string
     */
    private $arguments;

    /**
     * Returns the SQL function
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Returns the arguments for the SQL function
     *
     * @return string
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Constructor
     *
     * @param string $function
     * @param array $args
     *
     * @return null
     */
    public function __construct($function, Array $args)
    {
        $this->function  = $function;
        $this->arguments = $args;
    }

    /**
     * Returns the SQL statment
     *
     * @return string
     */
    public function getStatement()
    {
        $arguments = $this->arguments;

        foreach ($arguments as $key => $arg)
        {
            if (is_string($arg))
                $arguments[$key] = "'$arg'";
        }

        $arguments = implode(", ", array_values($arguments));

        return $this->function . '(' . $arguments . ')';
    }
}