<?php
  session_start();
       error_reporting(1);
  include ('../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);

  $accion = $_POST['accion'];


  if ($accion == 'cnd')  /////cambiar conductor
  { 
     $id = $_POST['id']; 
     if (($_POST['nc'] == 1) || ($_POST['nc'] == 2))
     {
         $value = ($_POST['ch']?$_POST['ch']:'NULL');
         $campo.="id_chofer_$_POST[nc], id_user, fecha_accion";
         $value ="$value, $_SESSION[userid], now()";     
         try
         {
             backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
             update('ordenes', $campo, $value, "(id = $id) and (id_estructura = ".STRUCTURED.")");
             print json_encode(array('ok' => true )); 
          }
          catch (Exception $e){ 
                              print json_encode(array('ok'=>false));
          }
          
      }
      elseif($_POST['nc'] == 3)
      {
        try
        {
          if ($_POST['txo'])
          { 
              if ($_POST['ch'])
                ejecutarSQL("UPDATE tripulacionXOrdenes set id_estructura_orden =".STRUCTURED.", id_empleado = $_POST[ch] WHERE id = $_POST[txo]");
              else
                ejecutarSQL("DELETE FROM tripulacionXOrdenes WHERE id = $_POST[txo]");
          }
          else
          {
              $sql = "INSERT INTO  tripulacionXOrdenes (id_orden, id_estructura_orden, id_empleado) VALUES ($id, ".STRUCTURED.", $_POST[ch])";
              ejecutarSQL($sql);
          }
          print json_encode(array('ok' => true )); 
        }
        catch(Exception $e)
                          { 
                              print json_encode(array('ok' => false, 'msge'=>$e->getMessage())); 
                          }
      }   
  }
  elseif ($accion == 'cche'){
      try
      {
           $id = $_POST['id'];  
           $value = ($_POST['int']?$_POST['int']:'NULL');

           $campo.='id_micro, id_user, fecha_accion';
           $value="$value, $_SESSION[userid], now()";
           backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
           update('ordenes', $campo, $value, "(id = $id) and (id_estructura = ".STRUCTURED.")");
           print json_encode(array('ok' => true)); 
       }
       catch(Exception $e)
                    { 
                        print json_encode(array('ok' => false)); 
                    }
  }
  elseif ($accion == 'ckm'){
      try
      {
           $id = $_POST['id'];  
           $value = (is_numeric($_POST['km'])?$_POST['km']:'NULL');

           $campo.='km, id_user, fecha_accion';
           $value="$value, $_SESSION[userid], now()";
           backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
           update('ordenes', $campo, $value, "(id = $id) and (id_estructura = ".STRUCTURED.")");
           print json_encode(array('ok' => true)); 
       }
       catch(Exception $e)
                    { 
                        print json_encode(array('ok' => false)); 
                    }
  }  
  elseif ($accion == 'chor')
  {
      try
      {
           $fecha = DateTime::createFromFormat('d/m/Y H:i', $_POST['val']);
           $id = $_POST['id'];  
           
           $value = $fecha->format('Y-m-d H:i:0');
           $campo = ($_POST['sl'] == 's'?'salida':'llegada');
           $update = "UPDATE horarios_ordenes_sur SET $campo = '$value' WHERE id = $id";

           $ok = ejecutarSQL($update);
           print json_encode(array('ok' => $ok)); 
       }
       catch(Exception $e)
                    { 
                        print json_encode(array('ok' => false, 'message' => $e->getMessage())); 
                    }
  }
  elseif ($accion == 'stt'){
      try
      {
           $id = $_POST['id'];  
           $value = ($_POST['st']?1:0);

           $campo.='borrada, id_user, fecha_accion';
           $value="$value, $_SESSION[userid], now()";
           backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
           update('ordenes', $campo, $value, "(id = $id) and (id_estructura = ".STRUCTURED.")");
           print json_encode(array('ok' => true)); 
       }
       catch(Exception $e)
                    { 
                        print json_encode(array('ok' => false, 'message' => $e->getMessage())); 
                    }
  }  
  elseif ($accion == 'cname'){
      try
      {
           $id = $_POST['id'];  
           
           backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
           $conn = conexcion(true);
           $stmt = $conn->prepare("UPDATE ordenes SET nombre = ? WHERE id = $id");
           $stmt->bind_param('s', $value);
           $value = $_POST['km'];
           $stmt->execute();
           $stmt->close();
           $conn->close();
           print json_encode(array('ok' => true)); 
       }
       catch(Exception $e)
                    { 
                        print json_encode(array('ok' => false, 'message' => $e->getMessage())); 
                    }
  }
  elseif ($accion == 'chclem'){
      try
      {
           $id = $_POST['id'];  
           $value = $_POST['cli']?$_POST['cli']:'NULL';
           $campo.='id_cliente_vacio, id_estructura_cliente_vacio, id_user, fecha_accion';
           $value="$value, $_SESSION[structure], $_SESSION[userid], now()";
           backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
           update('ordenes', $campo, $value, "(id = $id) and (id_estructura = ".STRUCTURED.")");
           print json_encode(array('ok' => true)); 
       }
       catch(Exception $e)
                    { 
                        print json_encode(array('ok' => false)); 
                    }
  }    
?>