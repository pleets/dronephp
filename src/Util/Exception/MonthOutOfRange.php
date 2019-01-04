<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Util\Exception;

/**
 * MonthOutOfRange Class
 *
 * This Exception is throwed when there is a undefined index on DateTime::MONTHS constant
 */
class MonthOutOfRange extends \RuntimeException
{
}