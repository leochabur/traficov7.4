<?php
  session_start();
  error_reporting(0);
  ////////////////// modulo para dar de alta Ciudades /////////////////////

  include ('../../../controlador/ejecutar_sql.php');
  $accion = $_POST['accion'];
  
  if ($accion == 'sve'){ ///codigo para guardar ////
    try{
          $campos = "id, razon_social, direccion, cuit_cuil, telefono, activo, id_estructura, id_localidad, mail";
          $values = "'$_POST[name]', '$_POST[dire]', '$_POST[cuit]', '$_POST[tele]', 1, $_SESSION[structure], $_POST[loca], '$_POST[mail]'";
          $ok = insert('empleadores', $campos, $values);
          $campos = "id, id_empleador, id_estructura";
          $values = "$ok, $_SESSION[structure]";
          $ok = insert('empleadoresporestructura', $campos, $values);
          print $ok;
        }
        catch(Exception $e){print $e->getMessage();}
  }
  elseif ($accion == 'edit')
  { ///codigo para guardar ////
    try
    {
          $state = (isset($_POST['act'])?1:0);
          $campos = "razon_social, direccion, cuit_cuil, telefono, activo, id_localidad, mail";
          $values = "'$_POST[name]', '$_POST[dire]', '$_POST[cuit]', '$_POST[tele]', $state, $_POST[loca], '$_POST[mail]'";
          $ok = update('empleadores', $campos, $values, "id = $_POST[idemp]");
          print json_encode(array('ok'=>true));
    }
    catch(Exception $e){print json_encode(array('ok'=>false));}
  }
?>

