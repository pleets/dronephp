<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Util;

/**
 * DateTime layer
 *
 * This class creates a \DateTime object
 */
class DateTime
{
    /**
     * Constants for days
     *
     * @var integer
     */
    const TODAY      = 0;
    const FIRST_DAY  = -1;
    const LAST_DAY   = -2;

    /**
     * Constants for months
     *
     * @var integer
     */
    const THIS_MONTH = -10;
    const LAST_MONTH = -11;

    /**
     * Constants for years
     *
     * @var integer
     */
    const THIS_YEAR  = -20;
    const LAST_YEAR  = -21;

    /**
     * Array of valid months
     *
     * @var string[]
     */
    const MONTHS = [
        1 => 'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December',
    ];

    /**
     * Returns the name of a month
     *
     * @param string $month_number
     *
     * @throws Exception\MonthOutOfRange
     *
     * @return string
     */
    public static function getMonth($month_number)
    {
        if (!array_key_exists($month_number, self::MONTHS)) {
            throw new Exception\MonthOutOfRange("Invalid Month");
        }

        return self::MONTHS[$month_number];
    }

    /**
     * Creates a \DateTime instance
     *
     * @param array $date
     *
     * @return \DateTime
     */
    public static function create(array $date = [])
    {
        if (!array_key_exists('day', $date)) {
            $date["day"] = self::TODAY;
        }

        if (!array_key_exists('month', $date)) {
            $date["month"] = self::THIS_MONTH;
        }

        if (!array_key_exists('year', $date)) {
            $date["year"] = self::THIS_YEAR;
        }

        switch ($date['year']) {
            case self::THIS_YEAR:
                $year = (int) date('Y');
                break;

            case self::LAST_YEAR:
                $year = ((int) date('Y'))- 1;
                break;

            default:
                $year = (int) $date["year"];
                break;
        }

        $month = ($date['month'] == self::THIS_MONTH)
            ? self::MONTHS[(int) date('m')]
            : (
                ($date['month'] == self::LAST_MONTH)
                ? '-1 month'
                : self::getMonth($date['month'])
            )
        ;

        switch ($date['day']) {
            case self::TODAY:
                $day = (int) date('d');
                break;

            case self::FIRST_DAY:
                $day = 'first day of';
                break;

            case self::LAST_DAY:
                $day = 'last day of';
                break;

            default:
                $day = (int) $date["day"];
                break;
        }

        if (is_string($day)) {
            $d = new \DateTime("$day $month $year");
        } else {
            if (in_array($month, self::MONTHS)) {
                $d = new \DateTime("first day of $month $year");
            } else {
                $d = new \DateTime("first day of $month");
                $d->setDate($year, (int) $d->format('m'), (int) $d->format('d'));
            }

            $d->add(new \DateInterval('P'.($day - 1).'D'));
        }

        return $d;
    }
}
