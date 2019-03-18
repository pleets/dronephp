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

use Drone\Error\Errno;
use Drone\Error\ErrorTrait;
use PHPUnit\Framework\TestCase;

class ErrorTraitTest extends TestCase
{
    /**
     * Tests if a class has errors
     *
     * @return null
     */
    public function testCheckingClassWhenHasErrors()
    {
        $ctrl = new MyController();
        $ctrl->getContentsFromFile('somefile.txt');

        $this->assertTrue($ctrl->hasErrors());
    }

    /**
     * Tests getting standard errors from a class
     *
     * @return null
     */
    public function testGettingStandardErrorsFromClass()
    {
        $ctrl = new MyController();
        $ctrl->getContentsFromFile('somefile.txt');

        # standard error (i.e. exists in Errno class)
        $errors = $ctrl->getErrors();

        $this->assertSame('FILE_NOT_FOUND', Errno::getErrorNameByCode(key($errors)));

        # message with replacement
        $this->assertSame('No such file or directory \'somefile.txt\'', array_shift($errors));

        $ctrl2 = new MyController();
        $ctrl2->getContentsFromFileWithoutFilenameOnError('somefile2.txt');

        # standard error (i.e. exists in Errno class)
        $errors = $ctrl2->getErrors();

        $this->assertSame('FILE_NOT_FOUND', Errno::getErrorNameByCode(key($errors)));

        # message without replacement
        $this->assertSame('No such file or directory', array_shift($errors));
    }

    /**
     * Tests getting non-standard errors from a class
     *
     * @return null
     */
    public function testGettingNonStandardErrorsFromClass()
    {
        $ctrl = new MyController();
        $ctrl->getContentsFromUrl('http://a34DvatgaAat4675sfASGag53665yaghaff546ghsfhSDF6767ssfGy.fdassdFSG');

        # non-standard error (i.e. not exists in Errno class)
        $errors = $ctrl->getErrors();

        $this->assertSame('URL not found', array_shift($errors));
    }

    /**
     * Tests wrong use of ErrorTrait
     *
     * @return null
     */
    public function testNonExistingError()
    {
        $ctrl = new MyController();

        $errorObject = null;
        $message = "No exception";

        try
        {
            $ctrl->addNonExistingError();
        }
        catch (\Exception $e)
        {
            $errorObject = ($e instanceof \LogicException);
            $message = $e->getMessage();
        }
        finally
        {
            $this->assertTrue($errorObject, $message);
        }
    }
}

class MyController
{
    use ErrorTrait;

    public function getContentsFromFile($filename)
    {
        if (file_exists($filename))
            return file_get_contents($filename);
        else
            $this->error(Errno::FILE_NOT_FOUND, $filename);
    }

    public function getContentsFromFileWithoutFilenameOnError($filename)
    {
        if (file_exists($filename))
            return file_get_contents($filename);
        else
            $this->error(Errno::FILE_NOT_FOUND);
    }

    public function getContentsFromUrl($filename)
    {
        $headers = get_headers($url);
        $exists = stripos($headers[0],"200 OK") ? true : false;

        if ($exists)
            return file_get_contents($filename);
        else
            $this->error("URL not found");
    }

    public function addNonExistingError()
    {
        $this->error(854155);
    }
}