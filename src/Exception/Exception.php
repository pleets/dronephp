<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

namespace Drone\Exception;

use Drone\Error\Errno;

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
     * By default exceptions are stores in the specific JSON file << $this->outputFile >>
     *
     * @return string|boolean
     */
    public function store()
    {
        # simple way to generate a unique id
        $id = time() . uniqid();

        $data = [];

        if (file_exists($this->outputFile))
        {
            $string = file_get_contents($this->outputFile);

            if (!empty($string))
            {
                $data   = json_decode($string, true);

                # json_encode can be return TRUE, FALSE or NULL (http://php.net/manual/en/function.json-decode.php)
                if (is_null($data) || $data === false)
                {
                    $this->error(Errno::JSON_DECODE_ERROR, $this->outputFile);
                    return false;
                }
            }
        }

        $data[$id] = [
            "message" => $this->getMessage(),
            "object"  => serialize($this)
        ];

        if (($encoded_data = json_encode($data)) === false)
        {
            $this->error(Errno::JSON_ENCODE_ERROR, $this->outputFile);
            return false;
        }

        $hd = @fopen($this->outputFile, "w+");

        if (!$hd || !@fwrite($hd, $encoded_data))
        {
            $this->error(Errno::FILE_PERMISSION_DENIED, $this->outputFile);
            return false;
        }

        @fclose($hd);

        return $id;
    }
}