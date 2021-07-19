<?php


$match = "/\([A-Z]\)$/";
$cadena = "RECORRIDO NUEVO (A)";
//echo preg_match($match, $cadena); 
echo substr($cadena, -2, 1);
?>
