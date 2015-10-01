<?php

		function getFiles($path)
		{
			$files = array();

		    if (is_dir($path))
		    {
		        if ($dh = opendir($path))
		        {
		            while (($file = readdir($dh)) !== false)
		            {
		                $_file = $path."/".$file;
		                if (is_file($_file) && $file!="." && $file!="..")
		                	$files[] = $_file;
		            }
		            closedir($dh);
		        }
		    }

		    return $files;
		}