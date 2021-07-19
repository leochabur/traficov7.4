<?
  session_start();
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];
  
  if ($accion == 'chge'){
     $ordenes = explode(',',$_POST['orders']);
     $campo='id_chofer_1, id_micro, id_user, fecha_accion';
     $value=($_POST[cond]?$_POST[cond]:'NULL').",".($_POST['int']?$_POST['int']:'NULL').", $_SESSION[userid], now()";
     $i=0;
     foreach ($ordenes as $valor) {
             backup('ordenes', 'ordenes_modificadas', "(id = $valor) and (id_estructura = ".STRUCTURED.")");
             update('ordenes', $campo, $value, "(id = '".$valor."') and (id_estructura = ".STRUCTURED.")");
             $i++;
     }
     print $i;
  }
  
?>

