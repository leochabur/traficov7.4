<?
  session_start();
  ////////////////// modulo para dar de alta Ciudades /////////////////////

  include ('../../../controlador/ejecutar_sql.php');
  $accion = $_POST['accion'];
  
  if ($accion == 'sve'){ ///codigo para guardar ////
          $campos = "id, id_estructura, id_provincia, ciudad";
          $values = "$_SESSION[structure], $_POST[pcia], '$_POST[city]'";
          $ok = insert('ciudades', $campos, $values);
          print $ok;
  }
?>

