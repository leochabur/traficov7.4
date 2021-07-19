<?php
  session_start();
  include_once('../../controlador/ejecutar_sql.php');
  include_once('../../controlador/bdadmin.php');
  define(STRUCTURED, $_SESSION['structure']);
  $conn = conexcion();

  ///el formato viene dado por codigo-valor  (donde codigo representa que campo se va a cambiar de la orden y valor el id de la orden
  $data  = explode("-",$_POST['id']);
  $campo = $data[0]; // nombre del campo
  $id    = $data[1]; // id del registro
  
  //el value viene en formato ABCDEFGH-id_conductor | interno-id_coche
  $value = explode('-', $_POST[value]);
  $id_cond = $value[1];

  if ($campo == 'chc1'){
     $campo="id_chofer_1";
  }
  elseif($campo == 'chc2'){
     $campo="id_chofer_2";
  }
  elseif($campo == 'chin'){
    $campo = "id_micro";
    $interno = "NULL";
    if($id_cond)
    {
      $sqlMicro = "SELECT interno FROM unidades WHERE id = $id_cond";
      $resultMicro = mysql_query($sqlMicro, $conn);
      if ($rowMicro = mysql_fetch_array($resultMicro))
      {
          $interno = $rowMicro['interno'];
      }
      comunicateUpdateInterno($id, $conn, $id_cond, 'Update BUS From Mod. Pax (Supervisores)');
    }
    else
    {
      comunicateUpdateInterno($id, $conn, false, 'Update BUS From Mod. Pax (Supervisores)'); //envia el interno en null
    }
 /*   $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://admtickets.masterbus.net/api/integrations/traffic/trips/".$id,
      CURLOPT_CUSTOMREQUEST => "PATCH",
      CURLOPT_POSTFIELDS =>"{'trips': {'bus_id': $interno}}",
      CURLOPT_RETURNTRANSFER => 1, 
      CURLOPT_HTTPHEADER => array(
        "Authorization: Bearer d8Ypl7DMuQsHjjW/INIHxRXjiV1BSezxrmbTV8EWZvk=",
        "Content-Type: text/plain"
      ),
    ));
    $response = curl_exec($curl);                                  
    $json = json_decode($response, true);
    if (isset($json['success']))
    {
        $result = $json['success'];
        $message = 'Update BUS From Sup.';
    }
    else
    {
        $result = 0;
        $message = $response;
    }

    curl_close($curl);      
    $sql = "INSERT INTO estadocomunicaciones (fecha, orden, estado, errorMessage) VALUES (now(), $id, $result, '$message')";
    mysql_query($sql, $conn); */
  }

  try{
         $campo.=", id_user, fecha_accion";
         backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")", $conn);
         $values="'$id_cond', $_SESSION[userid], now()";
         update('ordenes', $campo, $values, "(id = $id) and (id_estructura = ".STRUCTURED.")", $conn);
         if (($data[0] == 'chc1') || ($data[0] == 'chc2'))
         {
                       $result = ejecutarSQL("select if (e.id_empleador=(SELECT valor FROM opciones where (id_estructura = 1) and (opcion = 'empleador-master')),
                                                         upper(concat(e.apellido,', ',e.nombre)),
                                                         upper(concat(e.apellido,', ',e.nombre,' (', em.razon_social,')'))) as emple
                                                from empleados e
                                                inner join empleadores em on em.id = e.id_empleador
                                                where (e.id_empleado = $id_cond)", $conn);
                       if ($data = mysql_fetch_array($result)){
                          $valor = $data['emple'];
                       }
         }
         if ($data[0] == 'chin'){
            $valor = $value[0];
         }

         print $valor;
  }catch (Exception $e){die ($e->getMessage());}


?>
