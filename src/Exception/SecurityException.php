<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Exception;

/**
 * SecurityException Class
 *
 * This is a helper exception that represents an error in the program logic.
 * The goal of it is throw a LogicException that could cause an unexcepted
 * behavior of security in the aplication.
 *
 * For example, in a TableGateway for databases (ORM or DataMapper implementations),
 * the signature update(Array $set, Array $where) allow to use the following
 *
 * $entity->update(['name' => 'John Doe'], []);
 *
 * In theory, all data could be updated because developer not specified $where argument.
 * For security reasons an UPDATE statement should be ever a WHERE statement.
 */
class SecurityException extends \LogicException
{
}
