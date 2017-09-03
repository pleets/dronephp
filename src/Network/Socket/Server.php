<?php

namespace Drone\Socket;

class Server extends AbstractSocket
{
    /**
     * Reads a message from client
     *
     * @param resource
     *
     * @throws Exception
     *
     * @return string
     */
	public function read($socket)
	{
		if (($message = @socket_read($socket, 1024)) === false) 
		{
            $errno = socket_last_error();            
            $this->error($errno, socket_strerror($errno));

            throw new \RuntimeException("Could not read message from client socket");
		}

		return $message;
	}

    /**
     * Sends a message to client socket
     *
     * @param resource $socket
     * @param string $message
     *
     * @throws RuntimeException
     *
     * @return integer
     */
	public function send($socket, $message)
	{
		if (($bytes = @socket_write($socket, $message, strlen($message))) === false) 
		{
            $errno = socket_last_error();            
            $this->error($errno, socket_strerror($errno));

            throw new \RuntimeException("Could not send message to the client socket");
		}

		return $bytes;
	}

    /**
     * Sets socket to listening
     *
     * @param array $handlers
     *
     * @throws RuntimeException
     *
     * @return null
     */
	public function listen(Array $eventHandlers = array())
	{
		$event = $eventHandlers;
		$clousure = function(){};

		if (!array_key_exists('success', $event))
			$event["success"] = $clousure;

		if (!array_key_exists('error', $event))
			$event["error"] = $clousure;

		$listener = false;

		if (!($listener = @socket_listen($this->socket, 30))) 
		{
            $errno = socket_last_error();            
            $this->error($errno, socket_strerror($errno));

            throw new \RuntimeException("Could not set socket to listen");
		}
		else {

			echo "\n";
			echo "Server Started : " . date('Y-m-d H:i:s') . "\n";
			echo "Master socket  : " . $this->socket . "\n";
			echo "Listening on   : " . $this->host . " port " . $this->port . "\n\n";

			$socket = $this->socket;

			if (!($spawn = @socket_accept($this->socket))) 
			{
	            $errno = socket_last_error();
	            $this->error($errno, socket_strerror($errno));

	            throw new \RuntimeException("Could not accept incoming connection");
			}

			$input = $this->read($spawn);

			$input = trim($input);
			call_user_func($event["success"], $input, $this, $spawn);

			socket_close($spawn);
		}

		if (!$listener)
			call_user_func($event["error"], $this->getLastError());

		return $listener;
	}
}