<?php

/*
 * TableGateway abstraction class
 * http://www.pleets.org
 *
 * Copyright 2016, Pleets Apps
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */

abstract class Entity
{
    public function exchangeArray($data)
    {
        foreach ($data as $prop => $value)
        {
            if (property_exists($this, $prop))
                $this->$prop = $value;
            else
                throw new Exception("The property '$prop' does not exists in the class!");
        }
    }
}