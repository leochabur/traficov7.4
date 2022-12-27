<?php
    include('../../controlador/bdadmin.php');
    $status = "";
     
    if ($_POST["action"] == "upload") 
    {


        // obtenemos los datos del archivo
        $tamano = $_FILES["archivo"]['size'];
        $tipo = $_FILES["archivo"]['type'];
        $archivo = $_FILES["archivo"]['name'];
        $prefijo = substr(md5(uniqid(rand())),0,6);

        if ($archivo != "")
        {


            $conn = conexcion(true);
            // guardamos el archivo a la carpeta files
            mysqli_query($conn, "INSERT INTO normativa (texto, file, fechaAlta) VALUES ('$_POST[link]', '$archivo', now())");
            $id = mysqli_insert_id($conn);

            foreach ($_POST['sectores'] as $sector)
            {
                $insert = "INSERT INTO normativaXSector (id_sector, id_normativa) VALUES ($sector, $id)";
                mysqli_query($conn, $insert);
            }

            $destino =  "./".$archivo;
            if (copy($_FILES['archivo']['tmp_name'],$destino)) 
            {

                header('Location: /vista/iso/upnseg.php');
            } 
            else 
            {
                $status = "Error al subir el archivo";
                header('Location: /vista/iso/upnseg.php');
            }
        } 
        else 
        {
            $status = "Error al subir archivo";
            header('Location: /vista/iso/upnseg.php');
        }
        echo $status;
    }
    elseif ($_POST["action"] == "update") 
    {
        try
        {


                $conn = conexcion(true);

                $activo = isset($_POST["activo"])?1:0;

                $update = "UPDATE normativa SET texto = '$_POST[link]', activo = $activo WHERE id = $_POST[idrecurso]";

                mysqli_query($conn, $update);

                $delete = "DELETE FROM normativaXSector WHERE id_normativa = $_POST[idrecurso]";

                mysqli_query($conn, $delete);

                foreach ($_POST['sectores'] as $sector)
                {
                    $insert = "INSERT INTO normativaXSector (id_sector, id_normativa) VALUES ($sector, $_POST[idrecurso])";
                    mysqli_query($conn, $insert);
                }

                print json_encode(['ok' => true]);
        }
        catch (Exception $e)
        {
                            print json_encode(['ok' => false, 'message' => $e->getMessage]);
        }


    }
?>

