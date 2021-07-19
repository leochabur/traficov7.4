<?php
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  define('STRUCTURED', $_SESSION['structure']);
  $accion = $_POST['accion'];

  if ($accion == 'list')
  { ///codigo para guardar ////
     $conn = conexcion(true);
     $sql = "SELECT interno, nueva_patente, patente, upper(e.razon_social) as propietario, 
                    tu.id as idtipo, tipo, url_inferior, url_superior, u.id as unidad
             FROM unidades u
             INNER JOIN empleadores e ON e.id = u.id_propietario 
             INNER JOIN tipounidad tu ON (tu.id = u.id_tipounidad) and (tu.id_estructura = u.id_estructura_tipounidad)
             INNER JOIN layout_unidades lu on lu.id_unidad = u.id
             WHERE u.activo
             ORDER BY interno";

     $result = mysqli_query($conn, $sql);

    $tabla = "<table id='example' class='table table-zebra' align='center' border='0' width='100%'>
               <thead>
                    <tr>                         
                          <th>Interno</th>
                          <th>Dominio</th>
                          <th>Tipo</th>
                          <th>Ver Layout</th>
                      </tr>
               </thead>
               <tbody>";

    $uploadDir = '/layout'; 
    while($data = mysqli_fetch_array($result))
    {



        
        $ui = '<a target="_blank" href="'.$uploadDir.'/'.$data['url_inferior'].'"><i class="fas fa-eye fa-2x"></i></a>';
        

        $tabla.="<tr data-id='$data[unidad]'>
                    <td>$data[interno]</td>
                    <td>".($data['patente']?$data['patente']:$data['nueva_patente'])."</td>                          
                    <td>$data[tipo]</td>
                    <td class='INF'>$ui</td>
                 </tr>";
    }
    $tabla.="</tbody>
            </table>
            ";


     mysqli_free_result($result);
     mysqli_close($conn);
     print $tabla;
  }
  elseif($accion == 'laysup')
  {
      $uploadDir = realpath(__DIR__ . '/../../layout'); 

       
      $fileName = basename($_FILES["file"]["name"]); 
      $targetFilePath = $fileName; 
      $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); 
      $allowTypes = array('pdf', 'jpg'); 
      if(!in_array($fileType, $allowTypes))
      { 
          $response = array( 
              'status' => 0, 
              'message' => 'Tipo de archivo invalido! (Solo se permite PDF o JPG)'
          ); 
          print json_encode($response);
          exit;
      }
      $nombreArchivo = 'LAYOUT_'.$_POST['unidad'].'_'.$_POST['location'].'.'.$fileType;


                                       
      if(move_uploaded_file($_FILES["file"]["tmp_name"], ($uploadDir."/".$nombreArchivo)))
      { 
          $response['message'] = 'Carga realizada exitosamente';
          $response['status'] = 1;
          $field = 'url_inferior';
         // $resource = ($uploadDir."/".$nombreArchivo);
          if ($_POST['location'] == 'SUP')
          {
            $field = 'url_superior';
          }
          $sql = "INSERT INTO layout_unidades (id_unidad, $field) VALUES($_POST[unidad], \"$nombreArchivo\") 
                  ON DUPLICATE KEY UPDATE $field = \"$nombreArchivo\"";
          ejecutarSql($sql);
          $response['status'] = 1;
          $response['location'] = $_POST['location'];
          $response['path'] = '/layout/'.$nombreArchivo;

      }
      else
      { 
          $response['message'] = 'Error al subir el archivo'; 
          $response['status'] = 0;
      } 

      echo json_encode($response);
  }
  elseif($accion == 'sveevt'){
     $conn = conexcion();
     $sql = "SELECT *
             FROM unidades
             WHERE (interno = $_POST[n_interno]) and (id_propietario = $_POST[propietario]) and (id_estructura_propietario,$_SESSION[structure])";
     $result = mysql_query($sql, $conn);
     cerrarconexcion($conn);
     if (mysql_fetch_array($result)){
        $ok = "0"; //codigo de error para indicar que ya existe el numero de interno que se esta intentando asignar
     }
     else{
          $ok = insert('unidades', 'id, interno, id_estructura, procesado, id_propietario, id_estructura_propietario', "$_POST[n_interno], $_SESSION[structure], 0, $_POST[propietario], $_SESSION[structure]"); //agrega una unidad pendiente de procesamiento por parte del sector  seg vial
     }
     print json_encode($ok);
  }
  elseif($accion == 'upduda'){
          $video = $_POST['video'] ? 1 : 0;
          $banio = $_POST['banio'] ? 1 : 0;
          $bar = $_POST['bar'] ? 1 : 0;
          $activo = $_POST['activo'] ? 1 : 0;
          
          $campos = "marca_motor, anio, patente, nueva_patente, marca, modelo, cantasientos, video, bar, banio, activo, consumo, id_propietario, id_estructura_propietario";
          $values = "'$_POST[motor]', '$_POST[anio]', '$_POST[dominio]', '$_POST[n_dominio]', '$_POST[marca]', '$_POST[modelo]', '$_POST[cantas]', $video, $bar, $banio, $activo, '$_POST[consumo]', $_POST[propietario], $_SESSION[structure]";
          
          if ($_POST['calidad']){
             $campos.=", id_calidadcoche, id_estructura_calidadcoche";
             $values.=", $_POST[calidad], $_SESSION[structure]";
          }
          if ($_POST['tipo']){
             $campos.=", id_tipounidad, id_estructura_tipounidad";
             $values.=", $_POST[tipo], $_SESSION[structure]";
          }
          print update("unidades", $campos, $values, "(id = $_POST[id_unidad])and(id_estructura = $_SESSION[structure])");

  }
  elseif($accion == 'change'){

                 if ($_POST['st'] == 'up'){
                    update("unidades", "activo", "1", "(id = $_POST[coche])");
                 }
                 elseif ($_POST['st'] == 'down'){
                    update("unidades", "activo", "0", "(id = $_POST[coche])");
                 }
  }
?>

