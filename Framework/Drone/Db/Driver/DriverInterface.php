<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Db\Driver;

interface DriverInterface
{
   public function connect();
   public function reconnect();
   public function commit();
   public function rollback();
   public function transaction($array_of_sentences);
   public function beginTransaction();
   public function endTransaction();
   public function disconnect();
}