<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Debug;

use Exception;

class Catcher
{
    /**
     * Output filename
     *
     * The catched events/vars are stored as json format
     *
     * @var string
     */
    protected $output;

    /**
     * Returns the output filename
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Sets output attribute
     *
     * @param string
     *
     * @return null
     */
    public function setOutput($value)
    {
        return $this->output = $value;
    }

    /**
     * Returns the exception id stored
     *
     * @return string
     */
    public function storeException(Exception $e)
    {
        $id = time();

        $data = (file_exists($this->output)) ? json_decode(file_get_contents($this->output), true) : array();
        $data[$id] = array(
            "message" => $e->getMessage(),
            "object"  => serialize($e)
        );

        $hd = fopen($this->output, "w");
        fwrite($hd, json_encode($data));
        fclose($hd);

        return $id;
    }
}