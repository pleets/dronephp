<?php

if (isset($_POST["data"])) {
	
	// Se escribe el archivo buffer
	$data = $_POST["data"];
	$filename  = dirname(__FILE__).'/buffer';
	file_put_contents($filename, $_POST["data"]);

	// Se reemplazan los caracteres de escape y se sobreescribe
	$contents = file_get_contents($filename);
	$contents = str_replace("\\", "", $contents);
	file_put_contents($filename, $contents);
	echo $contents;
}
else echo "NO DATA";