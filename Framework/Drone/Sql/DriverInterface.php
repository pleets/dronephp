<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/Drone
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Sql;

interface DriverInterface
{
   public function reconnect();
   public function transaction($array_of_sentences);
   public function begin_transaction();
   public function end_transaction();
   public function cancel();
}