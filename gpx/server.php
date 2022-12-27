<?php

 include '../controlador/ejecutar_sql.php';

$filename = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "gpx" . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $_POST['filename'];


if (unlink($filename)) 
{
	ejecutarSQL("DELETE FROM cronogramas_gpx WHERE id = $_POST[gpxid]");
	
	print json_encode(['ok' => true]);
} else 
{
	print json_encode(['ok' => false]);
}

?>