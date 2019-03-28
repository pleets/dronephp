<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Exception;

use Drone\Error\Errno;

/**
 * Storage class
 *
 * This is a helper class to store exceptions
 */
class Storage
{
    use \Drone\Error\ErrorTrait;

    /**
     * Output file
     *
     * @var string
     */
    protected $outputFile;

    /**
     * Constructor
     *
     * @param string $outputFile
     *
     * @return null
     */
    public function __construct($outputFile)
    {
        $this->outputFile = $outputFile;
    }

    /**
     * Stores the exception serializing the object
     *
     * @param Exception $exception
     *
     * @return string|boolean
     */
    public function store(\Exception $exception)
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

                # json_encode can return TRUE, FALSE or NULL (http://php.net/manual/en/function.json-decode.php)
                if (is_null($data) || $data === false)
                {
                    $this->error(Errno::JSON_DECODE_ERROR, $this->outputFile);
                    return false;
                }
            }
        }
        else
        {
            $directory = strstr($this->outputFile, basename($this->outputFile), true);

            if (!file_exists($directory))
            {
                $this->error(Errno::FILE_NOT_FOUND, $directory);
                return false;
            }
        }

        $data[$id] = [
            "message" => $exception->getMessage(),
            "object"  => serialize($exception)
        ];

        if (!function_exists('mb_detect_encoding'))
            throw new \RuntimeException("mbstring library is not installed!");

        /*
         * Encodes to UTF8 all messages. It ensures JSON encoding.
         */
        if (!mb_detect_encoding($data[$id]["message"], 'UTF-8', true))
            $data[$id]["message"] = utf8_encode($data[$id]["message"]);

        if (!mb_detect_encoding($data[$id]["object"], 'UTF-8', true))
            $data[$id]["object"] = utf8_decode($data[$id]["object"]);

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