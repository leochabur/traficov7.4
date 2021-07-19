<?php
  session_start();
  error_reporting(0);
  ////////////////// modulo para dar de alta y mdificar un conductore en la BD  /////////////////////
    include ('../controlador/bdadmin.php');
  include ('../controlador/ejecutar_sql.php');
  include_once ('../modelo/utils/dateutils.php');
  
  $accion = $_POST['accion'];

  if($accion == 'mvv')
  { 
      $conn = conexcion();
      $clase = $_POST['cls'];
      $sql = "SELECT id, clase_siguiente FROM av_clases_curso where codigo = '$clase'";
      $result = mysql_query($sql, $conn);
      if ($row = mysql_fetch_array($result))
      {
             try{
                 $ok = insert('av_clases_realizadas', 'id, id_clase, id_empleado, fecha_hora', "$row[id], $_SESSION[id_chofer], now()", $conn); 
                 print json_encode(array('status' => true, 'next' => $row['clase_siguiente']));
             } catch (Exception $e) {
                     print json_encode(array('status' => false, 'message' => $e->getMessage()));
             }

      }
  }
?>

