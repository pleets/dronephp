<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace DroneTest\Error;

use Drone\Exception\Exception as ExceptionException;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    /**
     * Tests if an exception could be stored
     *
     * @return null
     */
    public function testExceptionStoring()
    {
        try
        {
            throw new ExceptionException("This is an storable exception");
        }
        catch (ExceptionException $e)
        {
            mkdir('exceptions');

            $file = 'exceptions/exception-' . date('dmY') . '.json';
            $storage = new \Drone\Exception\Storage($file);

            $errorCode = $storage->store($e);

            $this->assertTrue(is_string($errorCode));

            $json_object  = (file_exists($file)) ? json_decode(file_get_contents($file)) : array();
            $array_object = \Drone\Util\ArrayDimension::objectToArray($json_object);

            $error_qty = array_keys($array_object);

            $this->assertEquals(1, count($error_qty));

            $firstError = array_shift(array_keys($array_object));
            $unserialized = unserialize($array_object[$firstError]["object"]);

            $this->assertEquals("This is an storable exception", $unserialized->getMessage());
        }

        $shell = new \Drone\FileSystem\Shell();
        $shell->rm('exceptions', true);
    }

    /**
     * Tests if an exception could be stored when the file target does not exists
     *
     * @return null
     */
    public function testExceptionStoringFail()
    {
        try
        {
            throw new ExceptionException("This is an storable exception too");
        }
        catch (ExceptionException $e)
        {
            $date = date('dmY');
            $file = 'nofolder/exception-' . $date . '.json';
            $storage = new \Drone\Exception\Storage($file);

            $errorCode = $storage->store($e);

            $this->assertNotTrue(is_string($errorCode));

            $errors = $storage->getErrors();
            $error = array_shift($errors);

            $this->assertEquals("No such file or directory 'nofolder/'", $error);
        }
    }

    /**
     * Tests if several exceptions could be stored
     *
     * @return null
     */
    public function testCumulativeExceptionStoring()
    {
        mkdir('exceptions');
        $date = date('dmY');

        try
        {
            throw new ExceptionException("This is an storable exception");
        }
        catch (ExceptionException $e)
        {
            $file = 'exceptions/exception-' . $date . '.json';
            $storage = new \Drone\Exception\Storage($file);
            $storage->store($e);
        }

        try
        {
            throw new ExceptionException("This is an storable exception too");
        }
        catch (ExceptionException $e)
        {
            $file = 'exceptions/exception-' . $date . '.json';
            $storage = new \Drone\Exception\Storage($file);
            $storage->store($e);

            $json_object  = (file_exists($file)) ? json_decode(file_get_contents($file)) : array();
            $array_object = \Drone\Util\ArrayDimension::objectToArray($json_object);

            $error_qty = array_keys($array_object);

            $this->assertEquals(2, count($error_qty));

            $errorKeys = array_keys($array_object);

            $firstError  = array_shift($errorKeys);
            $secondError = array_shift($errorKeys);

            $firstUnserialized  = unserialize($array_object[$firstError]["object"]);
            $secondUnserialized = unserialize($array_object[$secondError]["object"]);

            $this->assertEquals("This is an storable exception", $firstUnserialized->getMessage());
            $this->assertEquals("This is an storable exception too", $secondUnserialized->getMessage());
        }

        $shell = new \Drone\FileSystem\Shell();
        $shell->rm('exceptions', true);
    }
}