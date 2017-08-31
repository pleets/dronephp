<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Exception;

class Exception extends \Exception
{
    use \Drone\Error\ErrorTrait;

    /**
     * Local file when exceptions will be stored
     *
     * @var string
     */
    protected $outputFile;

    /**
     * Returns the outputFile attribute
     *
     * @return string
     */
    public function getOutputFile()
    {
        return $this->outputFile;
    }

    /**
     * Sets outputFile attribute
     *
     * @param string $value
     *
     * @return null
     */
    public function setOutputFile($value)
    {
        return $this->outputFile = $value;
    }

    /**
     * Constructor
     *
     * @param array $data
     *
     * @return null
     */
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Stores the exception
     *
     * @return string
     */
    public function store()
    {
        # simple way to generate a unique id
        $id = time() . uniqid();

        # creates a new array with exceptions or gets the current collector
        $data = (file_exists($this->outputFile)) ? json_decode(file_get_contents($this->outputFile), true) : [];

        $e = $this;

        $data[$id] = [
            "message" => $e->getMessage(),
            "object"  => serialize($e)
        ];

        if (($encoded_data = json_encode($data)) === false)
        {
            $this->error("Failed to parse error to JSON object!");
            return false;
        }

        $hd = @fopen($this->outputFile, "w+");

        if (!$hd || !@fwrite($hd, $encoded_data))
        {
            $this->error(\Drone\Error\Errno::EACCES, $this->outputFile);
            return false;
        }

        @fclose($hd);

        return $id;
    }
}