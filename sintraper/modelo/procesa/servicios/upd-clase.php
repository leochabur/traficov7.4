<?
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
          update("servicios", $campos, $values, "id = $id");
          if ($values == 'i'){
             update("servicios", $campos, "'i'", "id = $id");
             $value = "IDA";
          }
          else{
               update("servicios", $campos, "'v'", "id = $id");
              $value = "VUELTA";
          }
  }
  @mysql_close($conn);
  echo $value;
?>

