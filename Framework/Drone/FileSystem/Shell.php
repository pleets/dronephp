<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/Drone
 * @copyright Copyright (c) 2014-2016 DronePHP. (http://www.dronephp.com)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\FileSystem;

class Shell implements IShellCommands
{
	private $home = null;				# Home path. Equivalent to ~
	private $buffer = null;				# Buffer

	public function __construct($home = null)
	{
		$this->home = (is_null($home) || empty($home)) ? $this->pwd() : $home;
	}

	public function getHome()
	{
		return $this->home;
	}

	public function getBuffer()
	{
		return $this->buffer;
	}

	/*
	 *	Recursive function to list files and directories
	 */
	public function getContents($handler, $fileCallback, $dirCallback, $callback = null)
	{
		$contents = array();

		if (is_dir($handler))
		{
			$filesForHandler = $this->ls($handler);

			foreach ($filesForHandler as $item)
			{
				if ($item != '.' && $item != '..')
					$contents[] = $item;
			}

			$allContents = $contents;

			if (count($contents) > 0)
			{
				foreach ($contents as $i)
				{
					if (is_file($handler.'/'.$i))
					{
						$allContents[] = $handler.'/'.$i;

						$this->buffer = $handler.'/'.$i;
						call_user_func($fileCallback, $this);
					}
					elseif (is_dir($handler.'/'.$i))
					{
						$allContents[] = $handler.'/'.$i;

						$this->buffer = $handler.'/'.$i;
						$this->getContents($handler.'/'.$i, $fileCallback, $dirCallback);

						$directory = scandir($handler);

						if (!count($directory) < 3)
							$this->buffer = $handler.'/'.$i;

						call_user_func($dirCallback, $this);
					}
				}
			}
		}
		else
			throw new \Exception("The directory '$handler' does not exists");

		if (!is_null($callback))
			call_user_func($callback, $this);

		return $allContents;
	}

	public function pwd()
	{
		if (getcwd())
			$this->buffer = getcwd();
		else
			return false;
		return $this->buffer;
	}

	public function ls($path = null, $recursive = false)
	{
		$filesToReturn = array();

		$path = (is_null($path) || empty($path)) ? '.' : $path;

		if (is_file($path))
			$filesToReturn = array($path);
		elseif (is_dir($path))
		{
			$pathIns = dir($path);

			if ($recursive)
			{
				# Declare variables
				$dirs = $files = array();

				$this->getContents($path, function($event) use (&$files) {
					$files[] = $event->getBuffer();
				}, function($event) use (&$dirs) {
					$dirs[] = $event->getBuffer();
				});

				foreach ($dirs as $item) {
					$filesToReturn[] = $item;
				}

				foreach ($files as $item) {
					$filesToReturn[] = $item;
				}
			}
			else {
				while (false !== ($item = $pathIns->read())) {
					$filesToReturn[] = $item;
				}
				$pathIns->close();
			}
		}
		else {

			$pathIns = dir('.');
			$contents = $this->ls();

			foreach ($contents as $item)
			{
				if (!empty($path))
					if (!strlen(stristr($item, $path)) > 0)
						continue;
				if (strstr($item,'~') === false && $item != '.' && $item != '..')
					$filesToReturn[] = $item;
			}
		}

		return $filesToReturn;
	}

	public function cd($path = null)
	{
		$moveTo = (is_null($path) || empty($path)) ? $this->home : $path;

		if (is_dir($path))
		{
			if (!chdir($moveTo))
				return false;
		}

		return $this;
	}

	public function touch($file)
	{
		if (!file_exists($file))
		{
			if (!$openFile = fopen($file, 'w+'))
				return false;
			fwrite($openFile, "");
			fclose($openFile);
		}
		else
			return false;
		return $this;
	}

	public function rm($file = null, $recursive = null)
	{
		$recursive = is_null($recursive) ? false : $recursive;

		if (is_null($file))
			throw new \Exception("Missing parameter for rm!");

		if (file_exists($file) && !$recursive)
			unlink($file);
		elseif (is_dir($file) && $recursive)
		{
			$that = $this;

			$this->getContents($file, function() use ($that) {
				unlink($that->getBuffer());
			}, function() use ($that) {
				rmdir($that->getBuffer());
			}, function() use ($file) {
				@rmdir($file);
			});
		}
		return $this;
	}

	public function cp($file = null, $dest, $recursive = null)
	{
		$recursive = (is_null($recursive)) ? false : $recursive;

		if (empty($file) || empty($dest))
			throw new Exception("Missing parameters!");

		if (is_dir($file))
		{
			if ( (!file_exists($dest) || (file_exists($dest) && is_file($dest)) ) && $recursive)
				mkdir($dest, 0777, true);

            if ($handle = opendir($file)) {

                while (false !== ($item = readdir($handle))) {

                    if (strstr($item,'~') === false && $item != '.' && $item != '..')
                    {
                    	if (is_dir($file ."/". $item) && $recursive)
                    	{
                    		mkdir($dest ."/". $item, 0777, true);
                    		$this->cp($file ."/". $item, $dest ."/". $item, true);
                    	}
                    	elseif (is_file($file ."/". $item))
                    		$this->cp($file ."/". $item, $dest ."/". $item);
                    }
                }

                closedir($handle);
            }
		}
		else
		{
			if (file_exists($dest) && is_dir($dest))
				copy($file, $dest.'/'. basename($file));
			else
				copy($file, $dest);
		}

		return $this;
	}

	public function mv($oldfile = null, $newfile)
	{
		if (empty($oldfile))
			throw new \Exception("Missing parameter for mv!");

		if (is_dir($newfile))
				$newfile .= '/'.basename($oldfile);

		if ($oldfile == $newfile)
			return $this;

		if(!rename($oldfile, $newfile))
			return false;

		return $this;
	}

	public function mkdir($dir = null, $dest = null, $recursive = null)
	{
		if (empty($dir))
			throw new \Exception("Missing parameter for mkdir!");

		if (empty($dest))
			$dest = '.';

		$recursive = (is_null($recursive)) ? false : $recursive;

		if ($recursive)
			mkdir("$dest/$dir", 0777, true);
		else {
			if (!is_dir($dir))
			{
				if(!mkdir("$dir", 0777))
					return false;
			}
		}
		return $this;
	}

	public function rmdir($dir = null)
	{
		if (is_null($dir) || empty($dir))
			throw new \Exception("Missing parameter for rmdir!");

		if (rmdir($dir))
			return $this;
		else
			return false;
	}
}