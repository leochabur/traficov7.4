<?php
$raiz = "../../";
require_once ($raiz.'base.inc.php');
 
if (!isset($_POST["id"])){
	exit();
}

$idParada = $_POST["id"];

$bd = new BdConexion();
$sqlBaja ="DELETE FROM charter_parada
					WHERE Id = :id ; ";
	
$bd->query($sqlBaja);
$bd->bind(":id", $idParada);
$result = $bd->execute(); 
$bd= null;
$arrResult = array("bajaok"=>$result);
header("Content-Type: application/json;");
echo json_encode($arrResult);
?>
