<?php 
	session_start();
	include '../controlador/ejecutar_sql.php';

	//el nivel 10 indica que el susuario es un usuario del sistema de toyota
	$sql = "SELECT id FROM usuarios WHERE user = '$_POST[usr]' AND password = '$_POST[pwd]' AND activo AND nivel >= 10";

	$result = ejecutarSQL($sql);
	if ($data = mysql_fetch_array($result))
	{
	    $_SESSION['auth'] = 1;
        $campos = "id, id_estructura, id_usuario, hinicio, hfin, host, estructura";
        $values = "1, $data[id], now(), now(), '$_SERVER[REMOTE_ADDR]', 1";
        $_SESSION['accid']=insert("accesousuarios", $campos, $values);
	    header('location:/toyota/drawgraph.php');
	}
	else{
	       header('location:/toyota/index.php');
	}

?>

