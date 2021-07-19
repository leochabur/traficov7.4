<?php
set_time_limit(0);
$archivo =  "C:/AppServ/www/export/controlador/tmpsql.log";

$conn = mysql_connect('rrhh.masterbus.net', 'xxmasterbus', 'master,07A');
mysql_select_db('trafico', $conn);

if(file_exists($archivo)) {
                          $file = fopen($archivo,'r');
                          while(!feof($file)) {
                                              $name = fgets($file);
                                              mysql_query($name, $conn);
                          }
                          fclose($file);
}
print($lineas);
/* Todas las lineas quedan almacenadas en $lineas
// Ahora eliminas la fila 15 por ejemplo, en el array sería la posicion 14 (empezamos por la 0)
unset($lineas[14]);
$lineas = array_values($lineas);
print_r($lineas);
// GUARDAMOS
$file = fopen($archivo, "w");
foreach( $lineas as $linea ) {
fwrite( $file, $linea );
}
fclose( $file );*/
     
?>

