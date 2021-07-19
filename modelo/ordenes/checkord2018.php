<?
  session_start();
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');

  define(STRUCTURED, $_SESSION['structure']);

  
  $lat=$_POST[lat];
  $long=$_POST[long];
  $vel=$_POST[vel];
  if ($_POST[coche]){
   //  getLtLgInt($_POST[coche], $lat, $long, $vel);
  }
  else{
       $lat="null";
       $long="null";
  }
  $conn = conexcion();
  $resp = array();
  try{
      begin($conn);
      $cheq = insert("chequeo_ordenes", "id, hora_chequeo, id_user, latitud, longitud, id_orden, id_estructura_orden", "now(), $_SESSION[userid], $lat, $long, $_POST[orden], $_SESSION[structure]", $conn);

      $rpta = json_decode($_POST['data'], true);
  
      foreach ($rpta as $key=>$value) {
            foreach ($value as $preg=>$resp) {
                    insert("respuestas_chequeo", "id, valor, observaciones, id_pregunta, id_chequeo", "$resp[0], '$resp[1]', $preg, $cheq",$conn);
            }
      }
      backup("ordenes", "ordenes_modificadas", "(id = $_POST[orden]) and (id_estructura = $_SESSION[structure])", $conn);
      update("ordenes", "checkeada, id_user, fecha_accion", "1, $_SESSION[userid], now()", "(id = $_POST[orden]) and (id_estructura = $_SESSION[structure])", $conn);
      commit($conn);
      cerrarconexcion($conn);
      $resp[estado]=true;
      $resp[mje]='Orde chequeada correctamente';
      print json_encode($resp);
  }catch (Exception $e) {rollback($conn);
                         cerrarconexcion($conn);
                         $resp[estado]=false;
                         $resp[mje]='No se ha podido chequear la orden! Posibemente ya ha sido chequeada!';
                         $resp[sql]=$e->getMessage();
                         print json_encode($resp);
                         };
  
?>

