<?php
  @session_start();
  error_reporting(E_ALL);
  include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');
  include ('../../modelo/enviomail/sendordbjafn.php');
  include ('../../modelsORM/call.php');
  include_once ('../../modelsORM/manager.php');
  include_once ('../../modelsORM/controller.php');  
  include_once ('../../modelsORM/src/ObservacionOrden.php');  

$accion = $_POST['accion'];
if ($accion == 'load') 
{
  try{
      $str = find('Estructura', $_SESSION[structure]);
      $orden = getOrden($str, $_POST[orden]);  
      //$comentario = getComentarioOrden($orden);
  }
  catch (Exception $e){ print $e->getMessage();}

 /*  $conn = conexcion(true);

  
  $sql = "SELECT o.id, o.km, date_format(fservicio,'%d/%m/%Y') as fservicio, finalizada, date_format(hcitacionreal, '%H:%i') as hcitacion,
                date_format(hsalidaplantareal, '%H:%i') as hsalida, date_format(hfinservicioreal, '%H:%i') as hfinserv, date_format(hllegadaplantareal, '%H:%i') as hllegada,
                o.nombre, if(em1.id = 1,concat(ch1.apellido, ', ',ch1.nombre),
                concat('(',em1.razon_social,') ', ch1.apellido, ', ',ch1.nombre)) as chofer1, upper(c.razon_social) as razon_social,
                concat(ch2.apellido, ', ',ch2.nombre) as chofer2, comentario, interno, m.id as id_micro, ch1.id_empleado as id_chofer1, ch2.id_empleado as id_chofer2,
                ori.ciudad as origen, des.ciudad as destino, if (o.borrada, 'checked','') as borrada, if (o.finalizada, 'checked','') as finalizada, 
                vacio, cv.id as id_cli_vac, upper(cv.razon_social) as rsclivac, cantpax as pax
          FROM ordenes o
          LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
          LEFT JOIN empleadores em1 ON (em1.id = ch1.id_empleador)
          inner join ciudades ori on (ori.id = id_ciudad_origen) and (ori.id_estructura = id_estructura_ciudad_origen)
          inner join ciudades des on (des.id = id_ciudad_destino) and (des.id_estructura = id_estructura_ciudad_destino)
          LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
          LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
          LEFT JOIN clientes cv ON ((cv.id = o.id_cliente_vacio) and (cv.id_estructura = o.id_estructura_cliente_vacio))
          LEFT JOIN unidades m ON (m.id = o.id_micro)
          WHERE o.id = $_POST[orden]";
  $result = $conn->query($sql);*/
 // die($sql);
  if ($orden){
    if ($orden->getClienteVacio()){
        $clivac = '<div class="form-group row">
                        <label for="clivac" class="col-3 col-form-label">Vacio Afectado a ...</label>
                        <div class="col-6">
                          <select class="custom-select custom-select-sm" name="clivac" id="clivac">
                              <option value="'.$orden->getClienteVacio()->getId().'">'.$orden->getClienteVacio().'</option>
                              '.clientesOptions().'
                          </select>                        
                        </div>
                  </div>';
    }

    $form = '  <form class="small" id="update">
                  <div class="form-group row">
                        <label for="fservicio" class="col-3 col-form-label">Fecha Servicio</label>
                        <div class="col-3">
                          <input class="form-control form-control-sm" type="text" id="fservicio" name="fservicio" value="'.$orden->getFservicio()->format('d/m/Y').'" required="true" readonly>
                        </div>
                  </div>
                  <div class="form-group row">
                        <label for="cliente" class="col-3 col-form-label">Cliente</label>
                        <div class="col-6">
                          <input class="form-control form-control-sm" type="text" id="cliente" name="cliente" value="'.$orden->getCliente().'"  required="true" readonly>
                        </div>
                  </div>     
                  '.$clivac.'   
                  <div class="form-group row">
                        <label for="nombre" class="col-3 col-form-label">Nombre Orden</label>
                        <div class="col-8">
                          <input class="form-control form-control-sm" type="text" id="nombre" name="nombre" value="'.$orden.'" readonly>
                        </div>
                  </div>
                  <div class="form-group row">
                        <label for="citacion" class="col-3 col-form-label">H. Citacion</label>
                        <div class="col-3">
                          <input class="form-control form-control-sm horario" type="text" id="citacion" name="citacion" value="'.$orden->getHcitacionReal()->format('H:i').'">
                        </div>
                        <label for="salida" class="col-2 col-form-label">H. Salida</label>
                        <div class="col-3">
                          <input class="form-control form-control-sm horario" type="text" id="salida"  value="'.$orden->getHsalidaPlantaReal()->format('H:i').'" name="salida">
                        </div>          
                  </div>    
                  <div class="form-group row">
                        <label for="llegada" class="col-3 col-form-label">H. Llegada</label>
                        <div class="col-3">
                          <input class="form-control form-control-sm horario" type="text" id="llegada" name="llegada" value="'.$orden->getHllegadaPlantaReal()->format('H:i').'">
                        </div>
                        <label for="finalizacion" class="col-2 col-form-label">H. Finalizacion</label>
                        <div class="col-3">
                          <input class="form-control form-control-sm horario" type="text" id="finalizacion" name="finalizacion" value="'.$orden->getHfinservicioReal()->format('H:i').'">
                        </div>          
                  </div>       
                  <div class="form-group row">
                        <label for="interno" class="col-3 col-form-label">Interno</label>
                        <div class="col-2">
                          <select class="custom-select custom-select-sm interno" name="interno" id="interno">
                          <option value="'.($orden->getUnidad()?$orden->getUnidad()->getId():"").'">'.$orden->getUnidad().'</option>
                          </select>
                        </div>        
                  </div>                    
                  <div class="form-group row">
                        <label for="chofer1" class="col-3 col-form-label">Conductor 1</label>
                        <div class="col-4">
                          <select class="custom-select custom-select-sm chofer" name="chofer1" id="chofer1">
                          <option value="'.($orden->getConductor1()?$orden->getConductor1()->getId():"").'">'.$orden->getConductor1().'</option>
                          </select>
                        </div>        
                  </div>  
                  <div class="form-group row">
                        <label for="chofer2" class="col-3 col-form-label">Conductor 2</label>
                        <div class="col-4">
                          <select class="custom-select custom-select-sm chofer" name="chofer2" id="chofer2">
                          <option value="'.($orden->getConductor2()?$orden->getConductor2()->getId():"").'">'.$orden->getConductor2().'</option>
                          </select>
                        </div>        
                  </div>
                  <div class="form-group row">
                        <label for="km" class="col-3 col-form-label">Km</label>
                        <div class="col-3">
                          <input class="form-control form-control-sm" type="text" id="km" name="km" value="'.$orden->getKm().'" required="true">
                        </div>
                        <label for="pax" class="col-3 col-form-label">Pax</label>
                        <div class="col-3">
                          <input class="form-control form-control-sm" type="text" id="pax" name="pax" value="'.$orden->getPasajeros().'" required="true">
                        </div>                        
                  </div>
                  <div class="form-group row">    
                    <div class="col-3">                             
                    </div>
                    <div class="col-4">
                      <div class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" id="defaultInline1" name="finalizada" '.($orden->getFinalizada()?"checked":"").'>
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
                  <input type="hidden" name="orden" value="'.$orden->getId().'"/> 
                  <input type="hidden" name="accion" value="update"/> 
                </form>

                <script type="text/javascript">
                               v34(".horario").mask("00:00");
                               $.post("/vista/ordenes/cargar_combo_conductores.php", 
                                      {orden: '.$orden->getId().'},
                                      function(data){
                                                      $(".chofer").append(data);
                                                    }
                                      );
                               $.post("/vista/ordenes/cargar_combo_internos.php", 
                                      {orden: '.$orden->getId().'},
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
                                                                          $.post("/modelo/ordenes/ordenesV2.php",
                                                                                 $("#update").serialize(),
                                                                                 function(data){
                                                                                                var response = $.parseJSON(data);
                                                                                                if (response.ok){
                                                                                                  v34("#basicExampleModal").modal("hide"); 
                                                                                                  v34("#loadOrdenes").click();                                                                                
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
elseif($accion == 'update')
{

  $id = $_POST['orden'];

  $nombre = $_POST['nombre'];


  $fecha = DateTime::createFromFormat('d/m/Y', $_POST['fservicio']);
  $fecha = $fecha->format('Y-m-d');

try{
  $hcitacion = DateTime::createFromFormat('H:i', $_POST['citacion']);
  $hcitacion = $hcitacion->format('H:i');

  $hsalida = DateTime::createFromFormat('H:i', $_POST['salida']);
  $hsalida = $hsalida->format('H:i');

  $hllegada = DateTime::createFromFormat('H:i', $_POST['llegada']);
  $hllegada = $hllegada->format('H:i');

  $hfinserv = DateTime::createFromFormat('H:i', $_POST['finalizacion']);
  $hfinserv = $hfinserv->format('H:i');  
}
catch (Exception $e){                      
                    print json_encode(array('ok' => false, 'msge' => $e->getMessage()));
                    exit();
                  }

  $km = $_POST['km'];
  $pax = $_POST['pax'];  
  $chofer1 = ($_POST['chofer1']) ? $_POST['chofer1'] : 'NULL';
  $chofer2 = ($_POST['chofer2']) ? $_POST['chofer2'] : 'NULL';
  $interno = ($_POST['interno']) ? $_POST['interno'] : 'NULL';

  $conn = conexcion(true);
  try{  

          $str = $_SESSION['structure'];
         /* $orden = getOrden($str, $_POST['orden']);  
          $comentario = getComentarioOrden($orden);
          $usuario = find('Usuario', $_SESSION['userid']);
          if ($comentario){
            $comentario->setComentario($_POST['observa']);
            $comentario->setUsuario($usuario);
          }
          else{
              if ($_POST['observa']){
                $comentario = new ObservacionOrden();
                $comentario->setComentario($_POST['observa']);
                $comentario->setOrden($orden);
                $comentario->setFechaAccion(new DateTime());
                $comentario->setUsuario($usuario);
                $entityManager->persist($comentario);
              }
          }*/

          $conn->autocommit(FALSE);
          $cantTripulacion = getCantTripulacion($conn, $_SESSION['structure']);
          $clivacio = 'NULL';
          if (isset($_POST['clivac'])){
              $clivacio = $_POST['clivac'];
              $nombre = getNombreOrden($conn, $_POST['clivac'], $_SESSION['structure'], $nombre);
          }

          $final = isset($_POST['finalizada']) ? 1 : 0;
          $borra = isset($_POST['borrada']) ? 1 : 0;

          $campos = "id_user = $_SESSION[userid], fecha_accion = now(), fservicio = '$fecha', nombre = '$nombre', km = $km, id_chofer_1 = $chofer1, id_chofer_2 = $chofer2, 
                     id_micro = $interno, id_cliente_vacio = $clivacio, finalizada = $final, borrada = $borra, comentario = '$_POST[obs]', cantpax = $pax, 
                     hllegadaplantareal = '$hllegada', hsalidaplantareal = '$hsalida', hfinservicioreal = '$hfinserv', hcitacionreal = '$hcitacion'";


          $backup  = "INSERT INTO ordenes_modificadas (SELECT * FROM ordenes WHERE ((id = $id) and (id_estructura = $_SESSION[structure])))";
          if (!$conn->query($backup))
              throw new Exception("Error al realizar el backup de la orden 215", 1);

          $update = "UPDATE ordenes SET $campos WHERE (id = $id) and (id_estructura = $_SESSION[structure])";
          if (!$conn->query($update))
              throw new Exception("Error al actualizar la orden 219 ".$conn->error."  SQL: ".$update, 1);     
       //   $entityManager->flush(); 
          $conn->commit();
          print json_encode(array('ok' => true));
  }
  catch (Exception $e){
                      $conn->rollback();
                      print json_encode(array('ok' => false, 'msge' => $e->getMessage()));
  }
}
elseif ($accion == 'check') {
    $id = $_POST['orden'];
   // $conn = conexcion(true);
    try
    {  
        $str = find('Estructura', $_SESSION[structure]);
        $user = find('Usuario', $_SESSION['userid']); 
        $orden = getOrden($str, $id);

        if ($orden->getFinalizada()){
            print json_encode(array('ok' => false, 'msge' => 'La orden ya ha sido finalizada, no se puede chequear!!'));     
            exit();            
        }        
        if ($orden->getCheckeada()){
            print json_encode(array('ok' => false, 'msge' => 'La orden ya ha sido chequeada!!'));     
            exit();           
        }

        $backup  = "INSERT INTO ordenes_modificadas (SELECT * FROM ordenes WHERE ((id = $id) and (id_estructura = $_SESSION[structure])))";
        ejecutarSQL($backup);

        $orden->setCheckeada(true);
        $orden->setUsuario($user);
        $orden->setFechaAccion(new DateTime());
        $entityManager->flush();
        print json_encode(array('ok' => true));
    }
    catch (Exception $e){
                       // $conn->rollback();
                        print json_encode(array('ok' => false, 'msge' => $e->getMessage()));
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
            throw new Exception("Error al generar la consulta 265 ".$sql_cli, 1);         
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







