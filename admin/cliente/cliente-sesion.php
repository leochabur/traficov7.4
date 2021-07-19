<?php 

if (!isset($_SESSION))
{
	session_start();
}

$clienteLogueado= false;
$redirCliLogin 	= URL_CLIENTE.'login'; 
$redirCliLogout	= URL_CLIENTE.'logout'; 
$redirCliIndex	= URL_CLIENTE.'consulta';

$ClienteSesion = new ClienteSesion(PREF_SESS_CLI);

if ($ClienteSesion->getSesionBd()){
	$Cliente = Cliente::cargarPorId($ClienteSesion->getIdCliente());
	$clienteLogueado = true; 
}