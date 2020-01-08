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

/**
 * StorableTrait Trait
 *
 * This is a helper tait that provides essential methods to store Exceptions.
 * All Exceptions that extends from this, will be stored with store() method.
 */
trait StorableTrait
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
        $this->outputFile = $value;
    }

    /**
     * Stores the exception
     *
     * By default exceptions are stored in a JSON file << $this->outputFile >>
     *
     * @return string|boolean
     */
    public function store()
    {
        $storage = new Storage($this->outputFile);

        $st = $storage->store($this);

        if (!$st) {
            $_errors = $st->getErrors();

            foreach ($_errors as $errno => $error) {
                $this->error($errno, $error);
            }
        }

        return $st;
    }
}
