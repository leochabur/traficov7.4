<?php
  $json = file_get_contents('http://190.105.224.81/w3/sistemaneo/QMOVILCOMMCAR.ASP?ACCION=LOGIN&usuario=trafico2013&clave=trafico2013');
$obj = json_decode($json);
print_r($obj);
?>

