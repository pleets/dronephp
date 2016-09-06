<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\Db;

interface TableGatewayInterface
{
   public function select($where);
   public function insert($data);
   public function update($set, $where);
   public function delete($where);
}