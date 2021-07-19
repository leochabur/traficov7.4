<?php
include('../../controlador/ejecutar_sql.php');
$status = "";
if ($_POST["action"] == "upload") {
    // obtenemos los datos del archivo
    $tamano = $_FILES["archivo"]['size'];
    $tipo = $_FILES["archivo"]['type'];
    $archivo = $_FILES["archivo"]['name'];
    $prefijo = substr(md5(uniqid(rand())),0,6);

    if ($archivo != "") {
        // guardamos el archivo a la carpeta files
        ejecutarSQL("INSERT INTO normativa (texto, file) VALUES ('$_POST[link]', '$archivo')");
        $destino =  "./".$archivo;
        if (copy($_FILES['archivo']['tmp_name'],$destino)) {
            header('Location: /vista/iso/upload.php');
        } else {
            $status = "Error al subir el archivo";
        }
    } else {
        $status = "Error al subir archivo";
    }
    echo $status;
}
?>

