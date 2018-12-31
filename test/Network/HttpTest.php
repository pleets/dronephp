<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace DroneTest\Network;

use Drone\Network\Http;
use PHPUnit\Framework\TestCase;

class HttpTest extends TestCase
{
    /**
     * Tests if we can write a HTTP status
     *
     * @return null
     */
    public function testWriteStatus()
    {
        $http = new Http();
        $header = $http->writeStatus($http::HTTP_NOT_FOUND);

        $this->assertEquals("HTTP/1.0 404 Not Found", $header);
    }

    /**
     * Tests gettting status text
     *
     * @return null
     */
    public function testGetStatusText()
    {
        $http = new Http();
        $text = $http->getStatusText($http::HTTP_NOT_FOUND);

        $this->assertEquals("Not Found", $text);
    }

    /**
     * Tests throwing exception on gettting status text
     *
     * @return null
     */
    public function testExceptionWhenStatusTextDoesNotExists()
    {
        $http = new Http();

        $errorObject = null;
        $message = "No exception";

        try
        {
            $text = $http->getStatusText(65431);
        }
        catch (\Exception $e)
        {
            $errorObject = ($e instanceof \RuntimeException);
            $message = $e->getMessage();
        }
        finally
        {
            $this->assertTrue($errorObject, $message);
        }
    }
}
