<?
  session_start();
  ////////////////// modulo para dar de alta Ciudades /////////////////////

  include ('../../../controlador/ejecutar_sql.php');
  $accion = $_POST['accion'];
  
  if ($accion == 'sve'){ ///codigo para guardar ////
          $campos = "id, id_estructura, turno";
          $values = "$_SESSION[structure], '$_POST[turno]'";
          $ok = insert('turnos', $campos, $values);
          print $ok;
  }
?>

