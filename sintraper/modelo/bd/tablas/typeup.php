<?
  session_start();
  ////////////////// modulo para dar de alta Ciudades /////////////////////

  include ('../../../controlador/ejecutar_sql.php');
  $accion = $_POST['accion'];
  
  if ($accion == 'sve'){ ///codigo para guardar ////
          $campos = "id, id_estructura, tipo";
          $values = "$_SESSION[structure], '$_POST[tipo]'";
          $ok = insert('tiposervicio', $campos, $values);
          print $ok;
  }
?>

