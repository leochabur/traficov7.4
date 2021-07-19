<?php
require_once ('funciones/general.php'); 
$string = 'Paquéte con Pingüino y pacífico [además]; ... "asd" ? ¿';
//$string = '¿paquéte con pingüino y pacífico?';
echo urls_amigables($string) . "<br>"; 
echo sanear_string($string); 