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
use PHPUnit\Framework\TestCase;

class ErrnoTest extends TestCase
{
    /**
     * Tests if will we get the error name from a given code
     *
     * @return null
     */
    public function testGettingErrorNameByCode()
    {
        $errno = Errno::getErrorNameByCode(2);
        $this->assertSame('FILE_NOT_FOUND', $errno);
    }
}