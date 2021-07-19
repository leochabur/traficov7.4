<?
  session_start();
  ////////////////// modulo para dar de alta Ciudades /////////////////////

  include ('../../../controlador/ejecutar_sql.php');
  $accion = $_POST['accion'];
  
  if ($accion == 'sve'){ ///codigo para guardar ////
          $campos = "id, razon_social, direccion, telefono, activo, id_estructura";
          $values = "'$_POST[name]', '$_POST[dire]', '$_POST[tele]', 1, $_SESSION[structure]";
          $ok = insert('empleadores', $campos, $values);
          $campos = "id, id_empleador, id_estructura";
          $values = "$ok, $_SESSION[structure]";
          $ok = insert('empleadoresporestructura', $campos, $values);
          print $ok;
  }
?>

