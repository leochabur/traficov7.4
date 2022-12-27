<?php

$filename = $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "recorridos" . DIRECTORY_SEPARATOR . $_POST['filename'];


if (unlink($filename)) 
{
	print json_encode(['ok' => true]);
} else 
{
	print json_encode(['ok' => false]);
}

?>