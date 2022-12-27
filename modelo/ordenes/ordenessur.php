<?php
  session_start();
  include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');
  include ('../../modelo/enviomail/sendordbjafn.php');
  include ('../../vista/paneles/viewpanel.php');
$accion = $_POST['accion'];
if ($accion == 'load') 
{
   $conn = conexcion(true);


   $optionsCiudades = "";
   $resultCity = mysqli_query($conn, "SELECT id, upper(ciudad) as city FROM ciudades c where id_estructura = $_SESSION[structure]  order by ciudad");
   while ($ct = mysqli_fetch_array($resultCity))
   {
      $optionsCiudades.= "<option value='$ct[id]'>$ct[city]</option>";
   }



   $sql = "SELECT cant_cond FROM estructuras WHERE id = $_SESSION[structure]";
   $result = $conn->query($sql);
   if ($data_cc = $result->fetch_array()){
       $cantTripulacion = $data_cc['cant_cond'];
   }
  
  $sql = "SELECT o.id, o.km, date_format(date(citacion),'%d/%m/%Y') as fservicio, finalizada, date_format(citacion, '%d/%m/%Y %H:%i') as hcitacion,
                date_format(salida, '%d/%m/%Y %H:%i') as hsalida, date_format(finalizacion, '%d/%m/%Y %H:%i') as hfinserv, date_format(llegada, '%d/%m/%Y %H:%i') as hllegada,
                o.nombre, if(em1.id = 1,concat(ch1.apellido, ', ',ch1.nombre),
                concat('(',em1.razon_social,') ', ch1.apellido, ', ',ch1.nombre)) as chofer1, upper(c.razon_social) as razon_social,
                concat(ch2.apellido, ', ',ch2.nombre) as chofer2, comentario, interno, m.id as id_micro, ch1.id_empleado as id_chofer1, ch2.id_empleado as id_chofer2,
                ori.id as idOrigen, des.id as idDestino,
                ori.ciudad as origen, des.ciudad as destino, if (o.borrada, 'checked','') as borrada, if (o.finalizada, 'checked','') as finalizada, vacio, cv.id as id_cli_vac, upper(cv.razon_social) as rsclivac, if(oa.id is null, 0, 1) as tiene_asoc, cod_servicio,
                date_format(citacion_real, '%d/%m/%Y %H:%i') as citacion_real, date_format(salida_real, '%d/%m/%Y %H:%i') as salida_real, date_format(finalizacion_real, '%d/%m/%Y %H:%i') as finalizacion_real, date_format(llegada_real, '%d/%m/%Y %H:%i') as llegada_real, cantpax
          FROM ordenes o
          INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id AND hhs.id_estructura_orden = o.id_estructura
          LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
          LEFT JOIN empleadores em1 ON (em1.id = ch1.id_empleador)
          inner join ciudades ori on (ori.id = id_ciudad_origen) and (ori.id_estructura = id_estructura_ciudad_origen)
          inner join ciudades des on (des.id = id_ciudad_destino) and (des.id_estructura = id_estructura_ciudad_destino)
          LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
          LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
          LEFT JOIN clientes cv ON ((cv.id = o.id_cliente_vacio) and (cv.id_estructura = o.id_estructura_cliente_vacio))
          LEFT JOIN unidades m ON (m.id = o.id_micro)
          left join ordenes_asocioadas oa on oa.id_orden = o.id and oa.id_estructura_orden = o.id_estructura
          WHERE o.id = $_POST[orden]";
  $result = $conn->query($sql);
  //die($result);
  if ($row = $result->fetch_array())
  {

    $sql = "SELECT date_format(fecha, '%d/%m/%Y') as fecha 
                FROM estadoDiagramasDiarios 
                WHERE (fecha = (SELECT date(citacion) FROM horarios_ordenes_sur where id_orden = $_POST[orden])) and (finalizado = 1) and (id_estructura = $_SESSION[structure])";
    $result = mysqli_query($conn, $sql);

    //return print($sql);
    $finalizado = mysql_num_rows($result);

    $fieldCita = 'hcitacion';
    $fieldSale = 'hsalida';
    $fieldLlega = 'hllegada';
    $fieldFin = 'hfinserv';

    if ($finalizado)
    {
        $fieldCita = 'citacion_real';
        $fieldSale = 'salida_real';
        $fieldLlega = 'llegada_real';
        $fieldFin = 'finalizacion_real';
    }

    $selectCliVac = '';
    if ($row['vacio']) //debe dar la opcion de seleccionar un cliente para afectar el vacio
    { 
      $selectCliVac = '<div class="form-group row mt-0">
                        <label for="cliente" class="col-3 col-form-label">Cliente Vacio</label>
                        <div class="col-6">
                          <select class="custom-select custom-select-sm" name="cliVacio">
                            <option value="'.$row['id_cli_vac'].'">'.$row['rsclivac'].'</option>
                            <option value="0"></option>';
      $resultCli = mysqli_query($conn, "SELECT id, upper(razon_social) as rz FROM clientes c where id_estructura = $_SESSION[structure] and activo order by razon_social");
      while ($cv = mysqli_fetch_array($resultCli))
      {
          $selectCliVac.= '<option value="'.$cv['id'].'">'.$cv['rz'].'</option>';
      }
      $selectCliVac.='</select>
      </div>
                  </div>  ';
    }

    $form = '  <form class="small" id="update">
                  <div class="form-group row mt-0">
                        <label for="fecha" class="col-3 col-form-label">Fecha Servicio</label>
                        <div class="col-4">
                          <input class="form-control form-control-sm" type="text" id="fecha" name="fecha" value="'.$row['fservicio'].'" required="true" readonly>
                        </div>
                  </div>    
                  <div class="form-group row mt-0">
                        <label for="cliente" class="col-3 col-form-label">Cliente</label>
                        <div class="col-6">
                          <input class="form-control form-control-sm" type="text" id="cliente" name="cliente" value="'.$row['razon_social'].'"  required="true" readonly>
                        </div>
                  </div>    
                  '.$selectCliVac.'   
                  <div class="form-group row">
                        <label for="nombre" class="col-3 col-form-label">Nombre Orden</label>
                        <div class="col-8">
                          <input class="form-control form-control-sm" type="text" id="nombre" name="nombre" value="'.$row['nombre'].'">
                        </div>
                  </div>
                  <div class="form-group row">
                        <label for="nombre" class="col-3 col-form-label">Origen</label>
                        <div class="col-3">
                            <select class="custom-select custom-select-sm" name="origen">
                              <option value="'.$row['idOrigen'].'">'.$row['origen'].'</option>
                              '.$optionsCiudades.'
                            </select>
                        </div>
                        <label for="nombre" class="col-2 col-form-label">Destino</label>
                        <div class="col-3">
                            <select class="custom-select custom-select-sm" name="destino">
                              <option value="'.$row['idDestino'].'">'.$row['destino'].'</option>
                              '.$optionsCiudades.'
                            </select>
                        </div>
                  </div>
                  <div class="form-group row">
                        <label for="citacion" class="col-3 col-form-label">H. Citacion</label>
                        <div class="col-3">
                          <input class="form-control form-control-sm horario" type="text" id="citacion" name="citacion" value="'.$row[$fieldCita].'">
                        </div>
                        <label for="salida" class="col-2 col-form-label">H. Salida</label>
                        <div class="col-3">
                          <input class="form-control form-control-sm horario" type="text" id="salida"  value="'.$row[$fieldSale].'" name="salida">
                        </div>          
                  </div>    
                  <div class="form-group row">
                        <label for="llegada" class="col-3 col-form-label">H. Llegada</label>
                        <div class="col-3">
                          <input class="form-control form-control-sm horario" type="text" id="llegada" name="llegada" value="'.$row[$fieldLlega].'">
                        </div>
                        <label for="finalizacion" class="col-2 col-form-label">H. Finalizacion</label>
                        <div class="col-3">
                          <input class="form-control form-control-sm horario" type="text" id="finalizacion" name="finalizacion" value="'.$row[$fieldFin].'">
                        </div>          
                  </div>       
                  <div class="form-group row">
                        <label for="interno" class="col-3 col-form-label">Interno</label>
                        <div class="col-3">
                          <select class="custom-select custom-select-sm interno" name="interno" id="interno">
                          <option value="'.$row['id_micro'].'">'.$row['interno'].'</option>
                          </select>
                        </div>   
                        <label for="interno" class="col-2 col-form-label">Codigo Servicio</label>
                        <div class="col-3">
                          <input class="form-control form-control-sm" type="text" id="cod_servicio" name="cod_servicio" value="'.$row['cod_servicio'].'">
                        </div>      
                  </div>                    
                  <div class="form-group row">
                        <label for="chofer1" class="col-3 col-form-label">Conductor 1</label>
                        <div class="col-4">
                          <select class="custom-select custom-select-sm chofer" name="chofer1" id="chofer1">
                          <option value="'.$row['id_chofer1'].'">'.$row['chofer1'].'</option>
                          </select>
                        </div>        
                  </div>  
                  <div class="form-group row">
                        <label for="chofer2" class="col-3 col-form-label">Conductor 2</label>
                        <div class="col-4">
                          <select class="custom-select custom-select-sm chofer" name="chofer2" id="chofer2">
                          <option value="'.$row['id_chofer2'].'">'.$row['chofer2'].'</option>
                          </select>
                        </div>        
                  </div>';
    $sql = "SELECT e.id_empleado, concat(apellido, ', ', nombre) as conductor
            FROM tripulacionXOrdenes t
            inner join empleados e on e.id_empleado = t.id_empleado
            where id_orden = $_POST[orden] and id_estructura_orden = $_SESSION[structure]";

    $result = $conn->query($sql);

      for ($i = 3; $i <= $cantTripulacion; $i++){
          $option = "<option value='0'></option>";
          if ($data = $result->fetch_array()){
              $option = "<option value='$data[0]'>".htmlentities($data[1])."</option>";
          }
          $form.="<div class='form-group row'>
                      <label for='chofer$i' class='col-3 col-form-label'>Conductor $i</label>
                        <div class='col-4'>
                          <select class='custom-select custom-select-sm chofer' id='chofer$i' name='chofer$i'>
                              $option
                          </select>
                        </div>   
                  </div>";
    }; 

    $form.='       <div class="form-group row">
                        <label for="km" class="col-3 col-form-label">Km</label>
                        <div class="col-3">
                          <input class="form-control form-control-sm" type="text" id="km" name="km" value="'.$row['km'].'" required="true">
                        </div>
                        <label for="pax" class="col-2 col-form-label">Pasajeros</label>
                        <div class="col-2">
                          <input class="form-control form-control-sm" type="text" id="pax" name="pax" value="'.$row['cantpax'].'" required="true">
                        </div>
                  </div>
                  <div class="form-group row">    
                    <div class="col-3">                             
                    </div>
                    <div class="col-4">
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" id="defaultInline1" name="finalizada" '.$row['finalizada'].'>
                        <label class="custom-control-label" for="defaultInline1">Finalizar</label>
                      </div>
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" id="defaultInline2" name="borrada">
                        <label class="custom-control-label" for="defaultInline2">Eliminar</label>
                      </div>    
                    </div>               
                  </div>                    
                  <div class="form-group row">
                        <div class="col-4">
                            <button class="btn btn-success btn-sm" id="save">Guardar</button>
                        </div>   
                  </div>   
                  <input type="hidden" name="orden" value="'.$_POST['orden'].'"/> 
                  <input type="hidden" name="accion" value="update"/> 
                  <input type="hidden" name="obs" value=""/> 
                </form>

                <script type="text/javascript">
                               v34(".horario").mask("00/00/0000 00:00");
                               $.post("/vista/ordenes/cargar_combo_conductores_sur.php", 
                                      function(data){
                                                      $(".chofer").append(data);
                                                    }
                                      );
                               $.post("/vista/ordenes/cargar_combo_internos.php", 
                                      function(data){
                                                      $(".interno").append(data);
                                                    }
                                      );                                      
                               $("#save").click(function(event){
                                                          event.preventDefault();
                                                          bootbox.confirm({
                                                              message: "Seguro modificar la orden de trabajo?",
                                                              buttons: {
                                                                  confirm: {
                                                                      label: "Si, modificar",
                                                                      className: "btn-success"
                                                                  },
                                                                  cancel: {
                                                                      label: "No, cancelar",
                                                                      className: "btn-danger"
                                                                  }
                                                              },
                                                              callback: function (result) {
                                                                  if (result){
                                                                          $.post("/modelo/ordenes/ordenessur.php",
                                                                                 $("#update").serialize(),
                                                                                 function(data){
                                                                                                var response = $.parseJSON(data);
                                                                                                if (response.ok){
                                                                                                  v34("#basicExampleModal").modal("hide"); 
                                                                                                  $("#cargar").trigger("click");                                                                                 
                                                                                                }
                                                                                                else{
                                                                                                    alert(response.msge);
                                                                                                }
                                                                                                
                                                                                  });                                                                    
                                                                  }
                                                                  else{
                                                                      v34("#basicExampleModal").modal("hide"); 
                                                                  }
                                                              }
                                                          });                                                    

                                  
                                });
                </script>';
        print $form;

  }
}
elseif($accion == 'check')
{
    $orden = $_POST['ord'];
    try
    {
        ejecutarSQLPDO("UPDATE ordenes SET checkeada = 1 WHERE id = $orden");
        print json_encode(['ok' => true, 'msge' => "UPDATE ordenes SET checkeada = 1 WHERE id = $orden"]);
        exit();
    }
    catch(Exception $e){
                          print json_encode(['ok' => false, 'msge' => $e->getMessage()]);
                          exit();
    }

}
elseif($accion == 'update')
{
  $id = $_POST['orden'];

  $nombre = $_POST['nombre'];

  $fecha = DateTime::createFromFormat('d/m/Y', $_POST['fecha']);
  $fecha = $fecha->format('Y-m-d');

  $hcitacion = DateTime::createFromFormat('d/m/Y H:i', $_POST['citacion']);
  $hcitacion = $hcitacion->format('Y-m-d H:i:s');

  $hsalida = DateTime::createFromFormat('d/m/Y H:i', $_POST['salida']);
  $hsalida = $hsalida->format('Y-m-d H:i:s');

  $hllegada = DateTime::createFromFormat('d/m/Y H:i', $_POST['llegada']);
  $hllegada = $hllegada->format('Y-m-d H:i:s');

  $hfinserv = DateTime::createFromFormat('d/m/Y H:i', $_POST['finalizacion']);
  $hfinserv = $hfinserv->format('Y-m-d H:i:s');  

  $km = $_POST['km'];
  $chofer1 = ($_POST['chofer1']) ? $_POST['chofer1'] : 'NULL';
  $chofer2 = ($_POST['chofer2']) ? $_POST['chofer2'] : 'NULL';
  $interno = ($_POST['interno']) ? $_POST['interno'] : 'NULL';

  $cantPax = (is_numeric($_POST['pax'])?$_POST['pax']:0);

  $conn = conexcion(true);
  try{  
          $conn->autocommit(FALSE);
          $cantTripulacion = getCantTripulacion($conn, $_SESSION['structure']);
          $clivacio = 'NULL';
          if (isset($_POST['clivac'])){
              $clivacio = $_POST['clivac'];
              $nombre = getNombreOrden($conn, $_POST['clivac'], $_SESSION['structure'], $nombre);
          }

          $final = isset($_POST['finalizada']) ? 1 : 0;
          $borra = isset($_POST['borrada']) ? 1 : 0;

          $campos = "cantpax = $cantPax, id_ciudad_origen = $_POST[origen], id_estructura_ciudad_origen = $_SESSION[structure], id_ciudad_destino = $_POST[destino], id_estructura_ciudad_destino = $_SESSION[structure], id_user = $_SESSION[userid], fecha_accion = now(), fservicio = '$fecha', nombre = '$nombre', km = $km, id_chofer_1 = $chofer1, id_chofer_2 = $chofer2, 
                     id_micro = $interno, id_cliente_vacio = $clivacio, finalizada = $final, borrada = $borra, comentario = '$_POST[obs]'";

          if (isset($_POST['cliVacio']))
          {
              if ($_POST['cliVacio'])
              {
                  $campos.= ", id_cliente_vacio = $_POST[cliVacio], id_estructura_cliente_vacio = $_SESSION[structure]";
              }
              else
              {
                $campos.= ", id_cliente_vacio = NULL, id_estructura_cliente_vacio = NULL";
              }
          }

          $backup  = "INSERT INTO ordenes_modificadas (SELECT * FROM ordenes WHERE ((id = $id) and (id_estructura = $_SESSION[structure])))";
          if (!$conn->query($backup))
              throw new Exception("Error al realizar el backup de la orden 215", 1);

          $update = "UPDATE ordenes SET $campos WHERE (id = $id) and (id_estructura = $_SESSION[structure])";
          if (!$conn->query($update))
              throw new Exception("Error al actualizar la orden 219 ".$conn->error."  SQL: ".$update, 1);      

          $delete = "DELETE FROM tripulacionXOrdenes WHERE id_orden = $id AND id_estructura_orden = $_SESSION[structure]";
          if (!$conn->query($delete))
              throw new Exception("Error al elimnar la tripulacion de la orden 223", 1);   
          
          for ($i = 3; $i <= $cantTripulacion; $i++){
              $conductor = $_POST["chofer$i"];
              if ($conductor){
                $insert = "INSERT INTO tripulacionXOrdenes (id_orden, id_estructura_orden, id_empleado) VALUES ($id, $_SESSION[structure], $conductor)";
                if (!$conn->query($insert))
                    throw new Exception("Error al insertar la tripulacion a la orden 230", 1);   
              }
          }  

          $sql = "SELECT date_format(fecha, '%d/%m/%Y') as fecha 
                      FROM estadoDiagramasDiarios 
                      WHERE (fecha = (SELECT date(citacion) FROM horarios_ordenes_sur where id_orden = $_POST[orden])) and (finalizado = 1) and (id_estructura = $_SESSION[structure])";
          $result = mysqli_query($conn, $sql);

          //return print($sql);
          $finalizado = mysql_num_rows($result);

          $fieldCita = 'citacion';
          $fieldSale = 'salida';
          $fieldLlega = 'llegada';
          $fieldFin = 'finalizacion';

          if ($finalizado)
          {
              $fieldCita = 'citacion_real';
              $fieldSale = 'salida_real';
              $fieldLlega = 'llegada_real';
              $fieldFin = 'finalizacion_real';
          }
                    
          $updateSur = "INSERT INTO horarios_ordenes_sur (citacion, salida, llegada, finalizacion, id_orden, id_estructura_orden, citacion_real, salida_real, llegada_real, finalizacion_real, cod_servicio)
                        VALUES  ('$hcitacion', '$hsalida', '$hllegada', '$hfinserv', $id, $_SESSION[structure], '$hcitacion', '$hsalida', '$hllegada', '$hfinserv', '$_POST[cod_servicio]') 
                        ON DUPLICATE KEY UPDATE $fieldCita = '$hcitacion', $fieldSale = '$hsalida', $fieldLlega = '$hllegada', $fieldFin = '$hfinserv', cod_servicio = '$_POST[cod_servicio]'";                        
          if (!$conn->query($updateSur))
              throw new Exception("Error al actualizar los horarios de la orden 236", 1);   
          $conn->commit();
          print json_encode(array('ok' => true));
  }
  catch (Exception $e){
                      $conn->rollback();
                      print json_encode(array('ok' => true, 'msge' => $e->getMessage()));
  }
}

function getCantTripulacion($conn, $str){
  $sql = "SELECT cant_cond FROM estructuras WHERE id = $str";
  $result = $conn->query($sql);
  if (!$result)
    throw new Exception("Error al generar la consulta 250", 1);
    
  if ($row = $result->fetch_array()){
         $cantTripulacion = $row[0];
  }  
  return $cantTripulacion;
}

function getNombreOrden($conn, $cliente, $str, $nomOrden)
{
    $nombre = $nomOrden;
    if ($cliente){
         $sql_cli = "SELECT razon_social FROM clientes where id = $cliente and id_estructura = $str";
         $result = $conn->query($sql_cli);
         if (!$result)
            throw new Exception("Error al generar la consulta 265", 1);         
         $data_cli = $result->fetch_array($result);
         $nom_cliente = $data_cli['razon_social'];
         $pos = strpos($nombre, '~');
         if ($pos){
            $nombre=substr_replace($nombre, "($nom_cliente)", ($pos+1), strlen($nombre));
         }
         else{
              $nombre.= "~($nom_cliente)";
         }
    }
    else{
         $pos = strpos($nombre,'~');
         if ($pos){
            $nombre = substr($nombre, 0, ($pos));
         }
    }
    return $nombre;
}


 /* 


  if (isset($_POST['borrada'])){
    /*  $sql = "SELECT * FROM estadoDiagramasDiarios e where fecha = '$fecha' and id_estado = 1";
      $result = mysql_query($sql, $conn);
      if ($data = mysql_fetch_array($result)){
         $_SESSION['senmail'] = 1;
      }
      else{
          $_SESSION['senmail'] = 0;
      }*
  }

 $sql = "SELECT id_orden_vacio FROM ordenesAsocVacios where id_orden = $id and id_estructura_orden = $_SESSION[structure]";  ///recupera todas las ordenes de vacios asociadas
  $result = ejecutarSQL($sql, $conn);
  $ordenes_vacios = "";
  while ($row = mysql_fetch_array($result)){
        if ($ordenes_vacios){
           $ordenes_vacios.= ",$row[0]";
        }
        else{
             $ordenes_vacios = "$row[0]";
        }
  }
  
  if ($ordenes_vacios){  ///significa que al menos tiene una orden asociada
     /*$campos = "id_user, fecha_accion, id_chofer_1, id_chofer_2, id_micro, borrada";
     $values = "$_SESSION[userid], now(), $chofer1, $chofer2, $interno, $borra";
     backup('ordenes', 'ordenes_modificadas', "(id in ($ordenes_vacios)) and (id_estructura = $_SESSION[structure])", $conn);
     $res = update("ordenes", $campos, $values, "(id in ($ordenes_vacios)) and (id_estructura = $_SESSION[structure])", $conn);*/
  //}
     /*
       if ($_POST['asocia']){
     //$conn = conexcion();
     $sql = "SELECT o.id, o.id_estructura
             FROM ordenes_asocioadas oa
             INNER JOIN ordenes o ON o.id = oa.id_orden_asociada and o.id_estructura = oa.id_esructura_orden_asociada
             WHERE id_orden = $id and id_estructura_orden = $_SESSION[structure]";
     try{
        // begin($conn);
         $campos = "id_chofer_1, id_chofer_2, id_micro";
         $values = "$chofer1, $chofer2, $interno";
         $result = ejecutarSQL($sql, $conn);
         while ($row = mysql_fetch_array($result)){
               backup('ordenes', 'ordenes_modificadas', "(id = $row[id]) and (id_estructura = $row[id_estructura])", $conn);
               update("ordenes", $campos, $values, "(id = $row[id]) and (id_estructura = $row[id_estructura])", $conn);
         }
       //  commit($conn);
     }catch (Exception $e) {
                          //  roolback($conn);
                           }

    /*if ($borra && $res){
     sentMail($id);
  }  

  }*/
?>







