<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

namespace Drone\Db\Driver;

interface DriverInterface
{
   public function connect();
   public function reconnect();
   public function execute($sql);
   public function commit();
   public function rollback();
   public function beginTransaction();
   public function endTransaction();
   public function disconnect();
}