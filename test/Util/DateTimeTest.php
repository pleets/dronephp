<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace DroneTest\Util;

use Drone\Util\DateTime as Dt;
use Drone\Util\Exception\MonthOutOfRange;
use PHPUnit\Framework\TestCase;

class DateTimeTest extends TestCase
{
    /**
     * Tests if we can get the month name
     *
     * @return null
     */
    public function testGetMonthName()
    {
        $this->assertEquals('June', Dt::getMonth(6));
    }

    /**
     * Tests throwing exception when month is out of rage
     *
     * @return null
     */
    public function testMonthOutOfRangeException()
    {
        $errorObject = null;
        $message = "No exception";

        try {
            $this->assertEquals('June', Dt::getMonth(13));
        } catch (\Exception $e) {
            $errorObject = ($e instanceof MonthOutOfRange);
            $message = $e->getMessage();
        } finally {
            $this->assertTrue($errorObject, $message);
        }
    }

    /**
     * Tests if we can get an instance of \DateTime by ::create()
     *
     * @return null
     */
    public function testGettingDateTimeInstance()
    {
        $dateTime = Dt::create([
            "day" => Dt::TODAY, "month" => dt::THIS_MONTH, "year" => dt::THIS_YEAR,
        ]);

        $this->assertTrue($dateTime instanceof \DateTime);
        $this->assertEquals($dateTime->format('d/m/Y'), date('d/m/Y'));
    }

    /**
     * Tests getting first and last day from some periods
     *
     * @return null
     */
    public function testFirstAndLastDays()
    {
        # First day of this month
        $dt = Dt::create([
            "day" => Dt::FIRST_DAY, "month" => dt::THIS_MONTH, "year" => dt::THIS_YEAR,
        ]);

        $dateTime = new \DateTime("first day of " . Dt::getMonth((int) date('m')) . " " . date('Y'));

        $this->assertEquals($dt->format('d/m/Y'), $dateTime->format('d/m/Y'));

        # Last day of this month
        $dt = Dt::create([
            "day" => Dt::LAST_DAY, "month" => dt::THIS_MONTH, "year" => dt::THIS_YEAR,
        ]);

        $dateTime = new \DateTime("last day of " . Dt::getMonth((int) date('m')) . " " . date('Y'));

        $this->assertEquals($dt->format('d/m/Y'), $dateTime->format('d/m/Y'));

        # first day of last month
        $dt = Dt::create([
            "day" => Dt::FIRST_DAY, "month" => dt::LAST_MONTH, "year" => dt::THIS_YEAR,
        ]);

        $dateTime = new \DateTime("first day of -1 month");

        $this->assertEquals($dt->format('d/m/Y'), $dateTime->format('d/m/Y'));

        # last day of last month
        $dt = Dt::create([
            "day" => Dt::LAST_DAY, "month" => dt::LAST_MONTH, "year" => dt::THIS_YEAR,
        ]);

        $dateTime = new \DateTime("last day of -1 month");

        $this->assertEquals($dt->format('d/m/Y'), $dateTime->format('d/m/Y'));

        # first day of June of last year
        $dt = Dt::create([
            "day" => Dt::FIRST_DAY, "month" => 6, "year" => dt::LAST_YEAR,
        ]);

        $dateTime = new \DateTime("first day of June " . (date('Y') - 1));

        $this->assertEquals($dt->format('d/m/Y'), $dateTime->format('d/m/Y'));

        # last day of June of last year
        $dt = Dt::create([
            "day" => Dt::LAST_DAY, "month" => 6, "year" => dt::LAST_YEAR,
        ]);

        $dateTime = new \DateTime("last day of June " . (date('Y') - 1));

        $this->assertEquals($dt->format('d/m/Y'), $dateTime->format('d/m/Y'));

        # 10th day of June of last year
        $dt = Dt::create([
            "day" => 10, "month" => 6, "year" => dt::LAST_YEAR,
        ]);

        $dateTime = new \DateTime("first day of June " . (date('Y') - 1));
        $dateTime->add(new \DateInterval('P9D'));

        $this->assertEquals($dt->format('d/m/Y'), $dateTime->format('d/m/Y'));
    }
}
