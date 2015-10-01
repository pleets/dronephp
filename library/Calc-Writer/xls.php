<?php

$name = isset($_GET["name"]) ? $_GET["name"] : "reporte";

header("Content-type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: filename=$name.xls");
header("Pragma: no-cache");
header("Expires: 0");

$buffer = dirname(__FILE__)."/buffer";

?>

<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title><?= $name ?></title>
	</head>
	<body>
		<?php include($buffer); ?>
		<h6>MEDICADISOFT <?= date("Y")?></h6>
	</body>
</html>