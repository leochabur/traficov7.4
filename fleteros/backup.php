<?
$filename = "mybase.sql";

// Cabezeras para forzar al navegador a guardar el archivo
header("Pragma: no-cache");
header("Expires: 0");
header("Content-Transfer-Encoding: binary");
header("Content-type: application/force-download");
header("Content-Disposition: attachment; filename=$filename");

$usuario="masterbus"; // Usuario de la base de datos, un ejemplo podria ser 'root'
$passwd="master,07a"; // Contraseña asignada al usuario
$bd="rrhh"; // Nombre de la Base de Datos a exportar

// Funciones para exportar la base de datos
// para windows
//$executa = "c:\mysql\bin\mysqldump.exe -u $usuario --password=$passwd --opt $bd";

//para Unix
$executa = "mysqldump -u $usuario --password=$passwd --opt $bd";
system($executa, $resultado);

// Comprobar si se a realizado bien, si no es asi, mostrará un mensaje de error
if ($resultado) { echo "<H1>Error ejecutando comando: $executa</H1>\n"; }

?>
