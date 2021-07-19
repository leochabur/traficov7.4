<?php
// incluye los archivos principales de configuracion + bd
require_once ("config.php");

// configuración
require_once (RUTA_LIB .'bdconexion.config.php');
// usuario
require_once (RUTA_LIB .'clases/Usuario.clase.php');

// Cliente
require_once (RUTA_LIB . "clases/Cliente.clase.php");
// Sesion
require_once (RUTA_LIB . "clases/ClienteSesion.clase.php");

// Sesion Cliente
require_once ('cliente/cliente-sesion.php');

$webTitulo = WEB_TITULO;
$webDescrip = WEB_DESCRIP;