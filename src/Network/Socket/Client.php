<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\Network\Socket;

/**
 * Client class
 *
 * Client socket implementation
 */
class Client extends AbstractSocket
{
    /**
     * Connects to socket server
     *
     * @return boolean
     */
    public function connect()
    {
        if (!($connected = @socket_connect($this->socket, $this->host, $this->port))) {
            $errno = socket_last_error();
            $this->error(socket_last_error(), socket_strerror($errno));
            return false;
        }

        return $connected;
    }

    /**
     * Reads a message from server
     *
     * @return string|boolean
     */
    public function read()
    {
        if (($message = @socket_read($this->socket, 1024)) === false) {
            $errno = socket_last_error();
            $this->error(socket_last_error(), socket_strerror($errno));
            return false;
        }

        return $message;
    }

    /**
     * Sends a message to server socket
     *
     * @param string $message
     *
     * @return integer|boolean
     */
    public function send($message)
    {
        if (($bytes = @socket_write($this->socket, $message, strlen($message))) === false) {
            $errno = socket_last_error();
            $this->error(socket_last_error(), socket_strerror($errno));
            return false;
        }

        return $bytes;
    }
}
