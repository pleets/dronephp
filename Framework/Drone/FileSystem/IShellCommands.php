<?php

/*
 * PHP FileSystem Environment - ShellCommands interface
 * http://www.pleets.org
 *
 * Copyright 2014, Pleets Apps
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */

namespace Drone\FileSystem;

interface IShellCommands
{
   public function pwd();
   public function ls($path);
   public function cd($path);
   public function touch($file);
   public function rm($file);
   public function cp($source, $dest);
   public function mv($oldfile, $newfile);
   public function mkdir($dir, $dest);
   public function rmdir($dir);
}
