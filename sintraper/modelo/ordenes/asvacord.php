<?
  session_start();
  include ('../../controlador/ejecutar_sql.php');

  $id = $_POST['orden'];
  $cliente = $_POST['cliente'];
  $estructura = $_SESSION['structure'];
  $sql =     "SELECT * FROM ordenes where (id = $id) and (id_estructura = $estructura) and (vacio)";
  $sql_cli = "SELECT razon_social FROM clientes c where id = $cliente and id_estructura = ".$_SESSION['structure'];
  $conn = conexcion();
  $result = mysql_query($sql, $conn);
  $result_cli = mysql_query($sql_cli, $conn);
  mysql_close($conn);
  if(mysql_num_rows($result) > 0){
          $data = mysql_fetch_array($result);
          $crono = $data['nombre'];
          $data_cli = mysql_fetch_array($result_cli);
          $nom_cliente = $data_cli['razon_social'];
          $pos = strpos($crono, '~');
          if ($pos){
             $crono=substr_replace($crono, "($nom_cliente)", ($pos+1), strlen($crono));
          }
          else{
               $crono.= "~($nom_cliente)";
          }
          $campo='nombre, id_cliente_vacio, id_estructura_cliente_vacio, id_user, fecha_accion';
          $value="'$crono', $cliente, $estructura, $_SESSION[userid], now()";
          backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".$_SESSION['structure'].")");
          update('ordenes', $campo, $value, "(id = '".$id."') and (id_estructura = ".$_SESSION['structure'].")");
          print $crono;
  }
?>

