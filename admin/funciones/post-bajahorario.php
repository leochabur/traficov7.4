<?php
$raiz = "../../";
require_once ($raiz.'base.inc.php');
 
if (!isset($_POST["id"])){
	exit();
}

$idHorario = $_POST["id"];

$bd = new BdConexion();
$sqlBaja ="DELETE FROM charter_dia_horario
					WHERE Id = :id ; ";
	
$bd->query($sqlBaja);
$bd->bind(":id", $idHorario);
$result = $bd->execute(); 
$bd= null;
$arrResult = array("bajaok"=>$result);
header("Content-Type: application/json;");
echo json_encode($arrResult);
?>
