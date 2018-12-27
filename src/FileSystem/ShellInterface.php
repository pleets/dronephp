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

/**
 * ShellInterface Interface
 *
 * This interface defines the basic commands that should be allowed in a shell
 */
interface ShellInterface
{
    /**
     * Prints the name of current/workinf directory
     */
    public function pwd();

    /**
     * Lists all directory contents
     *
     * @param string $path
     */
    public function ls($path);

    /**
     * Changes the current/working directory
     *
     * @param string $path
     */
    public function cd($path);

    /**
     * Creates a file
     *
     * @param string $file
     */
    public function touch($file);

    /**
     * Deletes files or directories
     *
     * @param string $file
     */
    public function rm($file);

    /**
     * Prints the name of current/workinf directory
     *
     * @param string $source
     * @param string $dest
     */
    public function cp($source, $dest);

    /**
     * Moves or renames files
     *
     * @param string $oldfile
     * @param string $newfile
     */
    public function mv($oldfile, $newfile);

    /**
     * Creates directories
     *
     * @param string $dir
     * @param string $dest
     */
    public function mkdir($dir, $dest);

    /**
     * Deletes empty directories
     *
     * @param string $dir
     */
    public function rmdir($dir);
}