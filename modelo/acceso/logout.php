<?php
	session_start();
	include('../../controlador/ejecutar_sql.php');
	update("accesousuarios", "hfin", "now()", "(id = $_SESSION[accid]) and (id_estructura = $_SESSION[structure])");
    session_unset();
    session_destroy();
    header("Location: /index.php");
?>
