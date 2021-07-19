<?php
$raiz = "../../";
require_once ($raiz.'base.inc.php');
 
if (!isset($_GET["id"])){
	exit();
}

$idHorario = $_GET["id"];

$bd = new BdConexion();
$sqlCharterParadas ="SELECT c.Id, c.Lat, c.Lng, c.Direccion
					FROM charter_parada c
					WHERE c.IdDiaHorario = :iddia
					ORDER BY c.Lat, c.Lng; ";
	
$bd->query($sqlCharterParadas);
$bd->bind(":iddia", $idHorario);
$bd->execute();
$charterParadas = $bd->getFilas();
$bd= null;

header("Content-Type: application/json;");
echo json_encode($charterParadas);
?>
