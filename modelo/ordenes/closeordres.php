<?
  session_start();
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];
  
  if ($accion == 'cpy'){
     $ordenes = explode(',',$_POST['orders']);
     $campo='finalizada, id_user, fecha_accion';
     $value="1, $_SESSION[userid], now()";
     $i=0;
     foreach ($ordenes as $valor) {
             backup('ordenes', 'ordenes_modificadas', "(id = $valor) and (id_estructura = ".STRUCTURED.")");
             update('ordenes', $campo, $value, "(id = '".$valor."') and (id_estructura = ".STRUCTURED.")");
             $i++;
     }
     print $i;
  }
  
?>

