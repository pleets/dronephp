<?php

/*
 * TableGateway interface
 * http://www.pleets.org
 *
 * Copyright 2016, Pleets Apps
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */

interface TableGatewayInterface
{
   public function select($where);
   public function insert($data);
   public function update($set, $where);
   public function delete($where);
}