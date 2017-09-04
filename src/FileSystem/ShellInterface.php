<?php
/**
 * DronePHP (http://www.dronephp.com)
 *
 * @link      http://github.com/Pleets/DronePHP
 * @copyright Copyright (c) 2016-2017 Pleets. (http://www.pleets.org)
 * @license   http://www.dronephp.com/license
 * @author    Darío Rivera <dario@pleets.org>
 */

namespace Drone\FileSystem;

interface ShellInterface
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
