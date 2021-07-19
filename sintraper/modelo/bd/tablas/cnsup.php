<?
  session_start();
  ////////////////// modulo para dar de alta Codigos de Novedad /////////////////////

  include ('../../../controlador/ejecutar_sql.php');
  $accion = $_POST['accion'];
  
  if ($accion == 'sve'){ ///codigo para guardar ////
          $campos = "id, nov_text, activa, id_estructura, afecta_diagrama";
          $values = "'$_POST[type]', 1, $_SESSION[structure], 1";
          $ok = insert('cod_novedades', $campos, $values);
          print $ok;
  }
?>

