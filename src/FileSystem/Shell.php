<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 */

namespace Drone\FileSystem;

use Exception;

class Shell implements ShellInterface
{
    /**
     * Home directory (~)
     *
     * @var string
     */
    private $home;

    /**
     * Result of last command (optional)
     *
     * @var string
     */
    private $buffer = null;

    /**
     * Returns the home attribute
     *
     * @return string
     */
    public function getHome()
    {
        return $this->home;
    }

    /**
     * Returns the buffer attribute
     *
     * @return mixed
     */
    public function getBuffer()
    {
        return $this->buffer;
    }

    /**
     * Constructor
     *
     * @param string $home
     */
    public function __construct($home = null)
    {
        $this->home = (is_null($home) || empty($home)) ? $this->pwd() : $home;
    }

    /**
     * Interative function of directories and files
     *
     * @param string   $handler
     * @param callable $fileCallback
     * @param callable $dirCallback
     * @param callable $callback
     *
     * @return array
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
            throw new Exception("The directory '$handler' does not exists");

        if (!is_null($callback))
            call_user_func($callback, $this);

        return $allContents;
    }

    /**
     * Returns the curent directory
     *
     * @return string|boolean
     */
    public function pwd()
    {
        if (getcwd())
            $this->buffer = getcwd();
        else
            return false;

        return $this->buffer;
    }

    /**
     * Returns a list with directory contents
     *
     * @param string|null $path
     * @param boolean     $recursive
     *
     * @return array
     */
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

    /**
     * Changes the current directory
     *
     * @param boolean|null $path
     *
     * @return boolean
     */
    public function cd($path = null)
    {
        $moveTo = (is_null($path) || empty($path)) ? $this->home : $path;

        if (is_dir($path))
        {
            if (chdir($moveTo))
                return true;
        }

        return false;
    }

    /**
     * Creates a file
     *
     * @param string
     *
     * @return boolean
     */
    public function touch($file)
    {
        if (file_exists($file))
        {
            if (!$openFile = fopen($file, 'w+'))
                return false;

            if (fwrite($openFile, ""))
                return true;

            fclose($openFile);
        }

        return false;
    }

    /**
     * Deletes one or more files
     *
     * @param string       $file
     * @param boolean|null $recursive
     *
     * @return boolean
     */
    public function rm($file, $recursive = null)
    {
        $recursive = is_null($recursive) ? false : $recursive;

        if (is_null($file))
            throw new Exception("Missing parameter for rm!");

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

        return true;
    }

    /**
     * Copies one or more files
     *
     * @param string       $file
     * @param string       $dest
     * @param boolean|null $recursive
     *
     * @return boolean
     */
    public function cp($file, $dest, $recursive = null)
    {
        $recursive = (is_null($recursive)) ? false : $recursive;

        if (empty($file) || empty($dest))
            throw new Exception("Missing parameters!");

        if (is_dir($dest))
        {
            if (!$recursive)
                copy($file, $dest.'/'.$file);
            else {

                $files = array();
                $files[0] = array();
                $files[1] = array();

                $_SESSION["BUFFER"]["EXO"]["cp"][2] = $dest;

                $that = $this;

                $this->getContents($file, function() use($that, &$files) {
                    $files[0][] = $that->getBuffer();
                }, function() use($that, &$files) {
                    $files[1][] = $that->getBuffer();
                }, function() use ($files, $dest) {

                    foreach ($files[1] as $item)
                    {
                        if (!file_exists($dest.'/'.$item))
                            mkdir("$dest/$item", 0777, true);
                    }

                    foreach ($files[0] as $item)
                    {
                        if (!file_exists("$dest/$files"))
                            copy($item, $dest.'/'.$item);
                    }
                });
            }
        }
        else
            copy($file, $dest);

        return true;
    }

    /**
     * Moves or renames files
     *
     * @param string $oldfile
     * @param string $newfile
     *
     * @return boolean
     */
    public function mv($oldfile, $newfile)
    {
        if (empty($oldfile))
            throw new Exception("Missing parameter for mv!");

        if (is_dir($newfile))
                $newfile .= '/'.basename($oldfile);

        if ($oldfile == $newfile)
            return $this;

        if(!rename($oldfile, $newfile))
            return false;

        return true;
    }

    /**
     * Creates a directory
     *
     * @param string       $dir
     * @param string|null  $dest
     * @param booelan|null $recursive
     *
     * @return boolean
     */
    public function mkdir($dir, $dest = null, $recursive = null)
    {
        if (empty($dir))
            throw new Exception("Missing parameter for mkdir!");

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
        return true;
    }

    /**
     * Deletes a directory
     *
     * @param string $dir
     *
     * @return boolean
     */
    public function rmdir($dir)
    {
        if (is_null($dir) || empty($dir))
            throw new Exception("Missing parameter for rmdir!");

        if (rmdir($dir))
            return true;
        else
            return false;
    }
}