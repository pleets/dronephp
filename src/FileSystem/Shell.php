<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2018 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <fermius.us@gmail.com>
 */

namespace Drone\FileSystem;

use Drone\Error\Errno;

/**
 * Shell class
 *
 * This class represents a system terminal
 */
class Shell implements ShellInterface
{
    use \Drone\Error\ErrorTrait;

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
        $this->cd($this->home);
    }

    /**
     * Iterative function for directories and files
     *
     * @param string   $directory
     * @param callable $fileCallback
     * @param callable $dirCallback
     * @param callable $callback
     *
     * @return array
     */
    public function getContents($directory, $fileCallback = null, $dirCallback = null, $callback = null)
    {
        $contents = $allContents = [];

        if (is_dir($directory))
        {
            foreach ($this->ls($directory) as $item)
            {
                if ($item != '.' && $item != '..')
                    $contents[] = $item;
            }

            if (count($contents) > 0)
            {
                foreach ($contents as $i)
                {
                    if (is_file($directory.'/'.$i))
                    {
                        $allContents[] = $i;

                        $this->buffer = $directory.'/'.$i;
                        call_user_func($fileCallback, $this);
                    }
                    else if (is_dir($directory.'/'.$i))
                    {
                        $directory_name = basename($directory).'/'.$i;

                        if (strpos($directory_name, './') === 0)
                        {
                            $from = './';
                            $from = '/'.preg_quote($from, '/').'/';
                            $directory_name = preg_replace($from, "", $directory_name, 1);
                        }

                        $allContents[$directory_name] =
                            $this->getContents($directory.'/'.$i, $fileCallback, $dirCallback);

                        $this->buffer = $directory.'/'.$i;
                        call_user_func($dirCallback, $this);
                    }
                }
            }
        }
        else if (is_file($directory))
            throw new \InvalidArgumentException("'$directory' is actually a file");
        else
            throw new \InvalidArgumentException("The directory '$directory' does not exists");

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
        $filesToReturn = [];

        $path = (is_null($path) || empty($path)) ? '.' : $path;

        if (is_file($path))
            $filesToReturn = array($path);
        else if (is_dir($path))
        {
            $pathIns = dir($path);

            if ($recursive)
                $filesToReturn = $this->getContents($path);
            else
            {
                while (($item = $pathIns->read()) !== false)
                {
                    if ($item != '.' && $item != '..')
                        $filesToReturn[] = $item;
                }

                $pathIns->close();
            }
        }
        else {

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
     * @param string|null $path
     *
     * @return boolean
     */
    public function cd($path = null)
    {
        $path = (is_null($path) || empty($path)) ? $this->home : $path;

        if (is_dir($path))
        {
            if (chdir($path))
                return true;
        }

        return false;
    }

    /**
     * Changes the file's date
     *
     * @param string
     *
     * @return boolean
     */
    public function touch($file)
    {
        $contents = file_exists($file) ? file_get_contents($file) : "";

        $hd = @fopen($file, "w+");

        if (!$hd || @fwrite($hd, $contents) === false)
        {
            $this->error(Errno::FILE_PERMISSION_DENIED, $file);
            return false;
        }

        @fclose($hd);

        return true;
    }

    /**
     * Deletes one or more files or directories
     *
     * @param string  $file
     * @param boolean $recursive
     *
     * @return boolean
     */
    public function rm($file, $recursive = false)
    {
        if (is_null($file))
            throw new \InvalidArgumentException("Missing first parameter");

        if (file_exists($file) && !$recursive)
            unlink($file);
        elseif (is_dir($file) && $recursive)
        {
            $that = $this;

            $this->getContents($file,
                # file's callback
                function() use ($that)
                {
                    unlink($that->getBuffer());
                },
                # folder's callback
                function() use ($that)
                {
                    rmdir($that->getBuffer());
                },
                # final callback
                function() use ($file)
                {
                    @rmdir($file);
                }
            );
        }

        return true;
    }

    /**
     * Copies one or more files or directories
     *
     * @param string       $file
     * @param string       $dest
     * @param boolean      $recursive
     *
     * @return boolean
     */
    public function cp($file, $dest, $recursive = false)
    {
        if (empty($file) || empty($dest))
            throw new \InvalidArgumentException("Missing parameters");

        if (is_dir($file) && !$recursive)
            throw new \RuntimeException("Ommiting directory <<$foo>>");

        $dest_exists = file_exists($dest);

        if (is_dir($file) && (is_dir($dest) || !$dest_exists))
        {
            $files = [
                "files"   => [],
                "folders" => []
            ];

            $files["folders"][] = is_dir($dest) ? basename($file) : $dest;

            if (!$dest_exists)
                mkdir($dest);

            $that = $this;

            $this->getContents($file,
                # file's callback
                function() use($that, &$files)
                {
                    $files["files"][] = $that->getBuffer();
                },
                # folder's callback
                function() use($that, &$files)
                {
                    $files["folders"][] = $that->getBuffer();
                },
                # final callback
                function() use (&$files, $file, $dest, $dest_exists)
                {
                    if ($dest_exists)
                    {
                        foreach ($files["folders"] as $folder)
                        {
                            if (!file_exists($dest.'/'.$folder))
                                mkdir("$dest/$folder", 0777, true);
                        }

                        if (count($files["files"]))
                        {
                            foreach ($files["files"] as $item)
                            {
                                if (!file_exists("$dest/$item"))
                                    copy($item, $dest.'/'.$item);
                            }
                        }
                    }
                    else
                    {
                        # directory previoulsy created
                        array_shift($files["folders"]);

                        $from = "$file/";
                        $from = '/'.preg_quote($from, '/').'/';

                        if (count($files["folders"]))
                        {
                            foreach ($files["folders"] as $folder)
                            {
                                $_folder = preg_replace($from, "", $folder, 1);

                                if (!file_exists($dest.'/'.$_folder))
                                    mkdir("$dest/$_folder", 0777, true);
                            }
                        }

                        if (count($files["files"]))
                        {
                            foreach ($files["files"] as $item)
                            {
                                $_item = preg_replace($from, "", $item, 1);

                                if (!file_exists("$dest/$_item"))
                                    copy($item, $dest.'/'.$_item);
                            }
                        }
                    }
                }
            );


        }
        else if (is_dir($dest))
            copy($file, $dest.'/'.$file);
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
        if (empty($oldfile) || empty($newfile))
            throw new \InvalidArgumentException("Missing parameters");

        if (is_dir($newfile))
            $newfile .= '/'.basename($oldfile);

        if ($oldfile == $newfile)
            throw new \Exception("'$oldfile' and '$newfile' are the same file");

        if(!rename($oldfile, $newfile))
            return false;

        return true;
    }

    /**
     * Creates a directory
     *
     * @param string       $dir
     * @param string|null  $dest
     * @param boolean|null $recursive
     *
     * @return boolean
     */
    public function mkdir($dir, $dest = null, $recursive = null)
    {
        if (empty($dir))
            throw new \InvalidArgumentException("Missing first parameter");

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
            throw new \RuntimeException("Missing first parameter");

        if (rmdir($dir))
            return true;
        else
            return false;
    }
}