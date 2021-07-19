<?php
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include_once('../../modelo/utils/dateutils.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];

  if($accion == 'dist'){
          $sql="SELECT te.id
                       FROM unidades u
                       inner join tipoeje te on te.id = u.id_tipoeje
                       where u.id = $_POST[uda]";
          $conn = conexcion();
          $result = mysql_query($sql, $conn);

          if ($row = mysql_fetch_array($result)){
             $tabla="$row[0]";
          }
          else{
                       $tabla="0";
          }
          mysql_free_result($result);
          mysql_close($conn);
          print json_encode($tabla);
  }
  elseif($accion == 'asign'){
         $conn = conexcion();
         $sql = "update unidades set id_tipoeje = $_POST[dist] where id = $_POST[uda]";
         mysql_query($sql, $conn);
         mysql_close($conn);
  }
?>

