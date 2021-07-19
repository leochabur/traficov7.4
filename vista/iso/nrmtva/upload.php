<?php
session_start();
set_time_limit(0);
include('../../../controlador/ejecutar_sql.php');
$status = "";
if ($_POST["action"] == "upload") {
    // obtenemos los datos del archivo
    $tamano = $_FILES["archivo"]['size'];
    $tipo = $_FILES["archivo"]['type'];
    $archivo = $_FILES["archivo"]['name'];
  //  $prefijo = substr(md5(uniqid(rand())),0,6);

    if ($archivo != "") {
        // guardamos el archivo a la carpeta files
        ejecutarSQL("INSERT INTO normativaMB (link, archivo, descripcion) VALUES ('$_POST[link]', '$archivo', '$_POST[desc]')");
        $destino =  "./".$archivo;
        if (copy($_FILES['archivo']['tmp_name'],$destino)) {
            header('Location: /vista/iso/nrmtva/upnsiso.php');
        } else {
            header('Location: /vista/iso/nrmtva/upnsiso.php');
        }
    } else {
        header('Location: /vista/iso/nrmtva/upnsiso.php');
    }
    //echo $status;
}
elseif($_POST['accion'] == 'del'){
    $sql = "delete FROM normativaMB where id = $_POST[id]";
    ejecutarSQL($sql);
}
?>

