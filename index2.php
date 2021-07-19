<?php
     phpinfo();
  $json = file_get_contents('http://190.105.224.81/w3/sistemaneo/QMOVILCOMMCAR.ASP?ACCION=LOGIN&usuario=trafico2013&clave=trafico2013');
 // $json=str_replace("},]","}]",$json);

$data = json_decode($json);
echo "datos  $data";
$title = $data->Login;

//$data = unserialize( $json );


print($title);
?>

