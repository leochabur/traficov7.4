<?php
  session_start();
  include ('../../../controlador/bdadmin.php');
  include_once ('../../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);

  $data  = explode("-",$_POST['id']);

  $id    = $data[1]; // id del registro
  $reg = $data[0];
  $value = $_POST['value']; // valor por el cual reemplazar
  
  $campos=$reg;
  $values=$value;
  
  if($reg == 'claseServicio_id'){
          $campos.=", claseServicio_id_estructura";
          $values.=", $_SESSION[structure]";
          update("cronogramas", $campos, $values, "id = $id");
          $sql = "SELECT clase FROM claseservicio where id = $value and id_estructura = $_SESSION[structure]";
          $conn = conexcion();
          $resu = mysql_query($sql, $conn);
          $value='';
          if ($data = mysql_fetch_array($resu)){
             $value = $data[0];
          }
  }
  elseif($reg == 'ciudades_id_origen'){
          $campos.=", ciudades_id_estructura_origen";
          $values.=", $_SESSION[structure]";
          update("cronogramas", $campos, $values, "id = $id");
          $sql = "SELECT ciudad FROM ciudades where id = $value and id_estructura = $_SESSION[structure]";
          $conn = conexcion();
          $resu = mysql_query($sql, $conn);
          $value='';
          if ($data = mysql_fetch_array($resu)){
             $value = $data[0];
          }
  }
  elseif($reg == 'ciudades_id_destino'){
          $campos.=", ciudades_id_estructura_destino";
          $values.=", $_SESSION[structure]";
          update("cronogramas", $campos, $values, "id = $id");
          $sql = "SELECT ciudad FROM ciudades where id = $value and id_estructura = $_SESSION[structure]";
          $conn = conexcion();
          $resu = mysql_query($sql, $conn);
          $value='';
          if ($data = mysql_fetch_array($resu)){
             $value = $data[0];
          }
  }
  elseif($reg == 'nombre'){
          $campos=$reg;
          $values="'$value'";
          update("cronogramas", $campos, $values, "id = $id");
  }
  elseif($reg == 'id_turno'){
         /* $campos.=", ciudades_id_estructura_destino";
          $values.=", $_SESSION[structure]";    */
          update("servicios", $campos, $values, "id = $id");
          $sql = "SELECT turno FROM turnos where id = $value and id_estructura = $_SESSION[structure]";
          $conn = conexcion();
          $resu = mysql_query($sql, $conn);
          $value='';
          if ($data = mysql_fetch_array($resu)){
             $value = $data[0];
          }
  }
  elseif($reg == 'id_TipoServicio'){
         /* $campos.=", ciudades_id_estructura_destino";
          $values.=", $_SESSION[structure]";    */
          update("servicios", $campos, $values, "id = $id");
          $sql = "SELECT tipo FROM tiposervicio where id = $value and id_estructura = $_SESSION[structure]";
          $conn = conexcion();
          $resu = mysql_query($sql, $conn);
          $value='';
          if ($data = mysql_fetch_array($resu)){
             $value = $data[0];
          }
  }
  elseif($reg == 'i_v'){
         /* $campos.=", ciudades_id_estructura_destino";
          $values.=", $_SESSION[structure]";    */
          //update("servicios", $campos, $values, "id = $id");
          if ($values == 'i'){
             update("servicios", $campos, "'i'", "id = $id");
             $value = "IDA";
          }
          else{
               update("servicios", $campos, "'v'", "id = $id");
              $value = "VUELTA";
          }
  }
  elseif($reg == 'est_dest'){
          $conn = conexcion();
          $sql = "SELECT * from controlexternoservicios WHERE id_servicio = $id and id_estructura_servicio = $_SESSION[structure]";
          $result = mysql_query($sql, $conn);
          if ($row = mysql_fetch_array($result)){
             if ($value){
                $upd = "UPDATE controlexternoservicios SET id_estructua_destino = $value, vigente = 1 WHERE id = $row[id]";
                mysql_query($upd, $conn);
             }
             else{
                $upd = "UPDATE controlexternoservicios SET vigente = 0 WHERE id = $row[id]";
                mysql_query($upd, $conn);
             }
          }
          else{
               if ($value){
                  $insert="INSERT INTO controlexternoservicios (id_servicio, id_estructura_servicio, id_estructua_destino, vigente, fecha_alta, usr_alta)
                           VALUES ($id, $_SESSION[structure], $value, 1, now(), $_SESSION[userid])";
                  mysql_query($insert, $conn) or die (mysql_error($conn));
                //  die($insert);
               }
          }
          $sql="SELECT nombre FROM estructuras where id = $value";
          $result = mysql_query($sql, $conn);
          if ($row = mysql_fetch_array($result))
             $value = $row[0];
          else
              $value="";
  }
  elseif($reg == 'tipoServicio'){
          $value='-';
          update("cronogramas", $campos, "'$values'", "id = $id");
          if ($values == 'charter'){
             $value = "Servicio con Reserva";
          }
  }
  @mysql_close($conn);
  echo $value;
?>

