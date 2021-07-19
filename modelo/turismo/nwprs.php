<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
     set_time_limit(0);
     session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }

include ('../../modelsORM/src/turismo/MovimientoDebito.php');
include ('../../modelsORM/src/turismo/MovimientoCredito.php');



/*function validateTime($time)
{
    $pattern="/^([0-1][0-9]|[2][0-3])[\:]([0-5][0-9])[\:]([0-5][0-9])$/";
    if(preg_match($pattern,$time))
        return true;
    return false;
}*/

  include($_SERVER['DOCUMENT_ROOT'].'/modelo/enviomail/sendmail.php');
  include($_SERVER['DOCUMENT_ROOT'].'/modelo/utils/dateutils.php');
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');  
  include($_SERVER['DOCUMENT_ROOT'].'/modelsORM/src/turismo/Viaje.php');
  include($_SERVER['DOCUMENT_ROOT'].'/modelsORM/src/turismo/Presupuesto.php');  
  include($_SERVER['DOCUMENT_ROOT'].'/modelsORM/call.php');
  include_once($_SERVER['DOCUMENT_ROOT'].'/modelsORM/controller.php');  
  include_once($_SERVER['DOCUMENT_ROOT'].'/modelsORM/manager.php');    
    use Symfony\Component\Validator\Validation;
  //define(STRUCTURED, $_SESSION['structure']);
  
  $accion = $_POST['accion'];
  if ($accion == 'addsrv'){

  
     $fsalida = DateTime::createFromFormat('d/m/Y', $_POST['fida']);

     $fregreso = DateTime::createFromFormat('d/m/Y', $_POST['fregreso']);

     $hSalidaIda = DateTime::createFromFormat('H:i', $_POST['hsida']);
     $hLlegadaIda = DateTime::createFromFormat('H:i', $_POST['hllida']);
       
     $hSalidaRegreso = DateTime::createFromFormat('H:i', $_POST['hsreg']);
     $hLlegadaRegreso = DateTime::createFromFormat('H:i', $_POST['hllreg']);
     
     
     $origen = find('Ciudad', $_POST['origen']);
     $destino = find('Ciudad', $_POST['destino']);
     $srv = new Viaje();
     $srv->setOrigen($origen);
     $srv->setDestino($destino);
     $srv->setLugarSalida($_POST['lsalida']);
     $srv->setLugarLlegada($_POST['lllegada']);
     $srv->setHSalida($hSalidaIda);
     $srv->setHLlegada($hLlegadaIda);
     $srv->setPax($_POST['pax']);
     $srv->setKm($_POST['km']);
     $srv->setObservaciones($_POST['obs']);
     $srv->setFSalida($fsalida);
     if ($_POST['fregreso']){
        $srv->setFRegreso($fregreso);
        $srv->setHSalidaRegreso($hSalidaRegreso);
        $srv->setHLlegadaRegreso($hLlegadaRegreso);
     }
     $servicios = call('ServicioViaje', 'findAll');
     foreach($servicios as $servicio){
        if (isset($_POST['srv-'.$servicio->getId()])){
           $srv->addServicio($servicio);
        }
     }
     
     
     
     $entityManager->persist($srv);
     $entityManager->flush();
     $response = array();
     $response['ok'] = true;
     $response['fsalida'] = $_POST['fida'];
     $response['origen'] = strtoupper($origen->getCiudad());
     $response['destino'] = strtoupper($destino->getCiudad());
     $response['hsalida'] = $_POST['hsida'];
     $response['pax'] = $_POST['pax'];
     $response['id'] = $srv->getId();
     print json_encode($response);
  }
  elseif ($accion == 'addps'){
      if (!$_POST['cliente']){
          $response = array('status' => false, 'message' => 'Debe seleccionar un cliente!!');
          print json_encode($response);
          exit();
      }
      $validator = Validation::createValidatorBuilder()
                              ->addMethodMapping('loadValidatorMetadata')
                              ->getValidator();

      $fpedido = DateTime::createFromFormat('d/m/Y', $_POST[fpedido]);
      $pres = new Presupuesto();
      $pres->setFechaSolicitud($fpedido);
      if ($_POST['fresp']){
        $frspta = DateTime::createFromFormat('d/m/Y', $_POST[fresp]);
        $pres->setFechaInforme($frspta);        
      }
      if ($_POST['fconf']){
        $fconf = DateTime::createFromFormat('d/m/Y', $_POST[fconf]);
        $pres->setFechaConfeccion($fconf);        
      }

      $cliente = find('Cliente', $_POST['cliente']);
      $canal =   find('CanalPedido', $_POST['canal']); 
      $usuario =   find('Usuario', $_SESSION['userid']);       
      $estructura =   find('Estructura', $_SESSION['structure']);            
      $pagoAnticipado = (isset($_POST['pagoanti'])?true:false);
      $emiteComprobante = (isset($_POST['efc'])?true:false);
      $requiereOC = (isset($_POST['requiereOC'])?true:false);      
      $pres->setCliente($cliente);
      $pres->setUsuario($usuario);
      $pres->setCanalPedido($canal);
      $pres->setEstructura($estructura);      
      $pres->setPagoAnticipado($pagoAnticipado);
      $pres->setEmiteComprobante($emiteComprobante);
      $pres->setConfConOrdenCompra($requiereOC);
      $pres->setObservaciones($_POST['observa']);
      $pres->setMailContacto($_POST['mailcontacto']);
      $pres->setTelefonoContacto($_POST['telcontacto']);
      $pres->setNombreContacto($_POST['nomcontacto']);  
      $pres->setPax($_POST['pax']);        

      if (is_numeric($_POST['precioNeto'])){
          $pres->setMontoSIva($_POST['precioNeto']);
          $pres->setIva($_POST['iva']);
          $pres->setMontoFinal($_POST['preciofinal']);
          $estado = find('EstadoPresupuesto', 1);
      }
      else{
        $estado = find('EstadoPresupuesto', 3);
      }
      $pres->setEstado($estado);

      $gastos = gastosPresupuestos();
      foreach ($gastos as $gasto) {
        if (isset($_POST['gas-'.$gasto->getId()])){
          $pres->addGastoACargo($gasto);
        }
      }

      $viajes = explode(',', $_POST['vjes']);
      foreach ($viajes as $idViaje) {
          $viaje =   find('Viaje', $idViaje);   
          if ($viaje){
            $viaje->setPresupuesto($pres);
            $pres->addViaje($viaje);
          }          
      } 
      $errors = $validator->validate($pres);      
      if (count($errors) > 0) 
      {
          $errores = "";
          foreach($errors as $error)
          {
              $errores.= $error->getMessage();
          }
          $response = array('status' => false, 'message' => $errores);
          print json_encode($response);
          exit();
      }

     $entityManager->persist($pres);
     $entityManager->flush();

     $response = array('status' => true );
     print json_encode($response);       
  }
  elseif($accion == 'listp'){
    $desde = ''; 
    $hasta = '';
    $cliente = '';
    $numero = '';

    if (!($_POST['desde']))
      $desde = DateTime::createFromFormat('d/m/Y', $_POST['desde']);

    if (!($_POST['hasta']))
      $hasta = DateTime::createFromFormat('d/m/Y', $_POST['hasta']);  

    if ($_POST['clientes'])
      $cliente = $_POST['clientes'];

    if ($_POST['numero'])
      $numero = $_POST['numero'];        

    if ((($desde) || ($hasta) || ($cliente) || ($numero)))
          $dql.=" WHERE ";
    //die("desde $desde hasta $hasta cliente $cliente numero $numero");

    if ($desde){
          $dql.=" p.fecha_solicitud >= '".$desde->format('Y-m-d')."'";
          $set = true;
    }

    if ($hasta){
        if ($set)
            $dql.= " AND ";
        $dql.="p.fecha_solicitud <= '".$hasta->format('Y-m-d')."'";
        $set = true;
    }

    if ($cliente){
          if ($set)
            $dql.=" AND ";
          $dql.="p.id_cliente = ".$cliente;
          $set = true;
    }

    if ($numero){
          if ($set)
            $dql.= " AND ";
          $dql.=" p.id = ".$numero;
          $set = true;
          $parameters['id'] = $numero;
    }

    if (isset($_POST['pendiente'])){
      if (!$dql)
        $dql=" WHERE ";
      if ($set)
        $dql.=" AND ";
      $dql.=" (confirmado) AND (NOT facturado)";
    }




    $sql = "SELECT p.id, lpad(p.id, 6, '0') as numero , 
                   upper(razon_social) as cliente, 
                   date_format(fecha_solicitud, '%d/%m/%Y') as fecha, 
                   montoFinal, 
                   pax, 
                   confirmado, 
                   pagoanticipado, 
                   p.id_cliente, 
                   ep.estado, 
                   pagoanticipado, 
                   nombreContacto, 
                   telefonoContacto,
                  if (emitecomprobante, ' *', '') as emite, emitecomprobante, 
                  ep.id as status_p,
                  facturado
            FROM tur_presupuestos p
            INNER JOIN clientes c ON c.id = p.id_cliente 
            LEFT JOIN tur_estados_presupuesto ep ON ep.id = p.id_estado
            ".$dql." ORDER BY p.fecha_solicitud";

 //   die($sql);

    $result = ejecutarSQL($sql) or die($sql);
    $tabla = "<table class='table table-zebra table-hover' id='prsList' width='100%'>
              <thead>
                <tr>
                  <th>Numero</th>
                  <th>Cliente</th>
                  <th>Fecha</th>
                  <th>Estado</th>                  
                  <th>Monto Final</th>
                  <th>Pago Anticipado</th>                  
                  <th>Pasajeros</th>
                  <th>Contacto</th>
                  <th>Telefono</th>                     
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>";
    while ($row = mysql_fetch_array($result)){
      $tabla.="<tr data-id='$row[id]'>
                <td id='nro'>$row[numero]$row[emite]</td>
                <td>$row[cliente]</td>
                <td>$row[fecha]</td>
                <td>$row[estado]</td>                
                <td>$row[montoFinal]</td>
                <td>".($row[pagoanticipado]?'SI':'NO')."</td>     
                <td>$row[pax]</td>                           
                <td>$row[nombreContacto]</td>
                <td>$row[telefonoContacto]</td>                
                <td>
                  <a href='viewService' title='Ver servicios cargados' class='view'><i class='fas fa-bus fa-lg'></i></a>
                  <a href='/vista/turismo/nwprs.php?psn=".$row['id']."' title='Modificar presupuesto'><i class='fas fa-edit fa-lg'></i></a>
                  <a href='' data-confirmado='$row[confirmado]' data-cliente='$row[id_cliente]' data-state='$row[status_p]' class='confirm' title='Confirmar presupuesto' data-pagoanticipado='$row[pagoanticipado]' data-presupuesto='$row[id]'><i class='far fa-check-square fa-lg'></i></a>
                  <a href='#' title='Facturar Presupuesto' data-state='$row[status_p]' data-emite='$row[emitecomprobante]' data-presupuesto='$row[id]' data-factu='$row[facturado]' class='factu'><i class='fas fa-print fa-lg'></i></a>                  
                  <a href='#' title='Marcar presupesto como rechazado'><i class='far fa-window-close fa-lg'></i></a>
                </td>
               </tr>";
    }
    $tabla.="</tbody>
             </table>";

    $tabla.="<script>
                $('#fechap').datepicker({dateFormat:'dd/mm/yy', autoOpen:false});

                $('.botoncete').button().click(function(){
                                                        var data = $('#dataconfirm').serialize();
                                                        $.post('/modelo/turismo/nwprs.php',
                                                               data,
                                                               function(response){
                                                                  var resp = $.parseJSON(response);
                                                                  if (resp.ok){
                                                                    $('#dataconfirm')[0].reset();
                                                                    $('#confirm').dialog('close');
                                                                  }
                                                                  else{
                                                                    alert(resp.message);  
                                                                    alert('Se han producido errores al confirmar el presupuesto');
                                                                  }
                                                               });
                                                      });                
             </script>
             ";


    $script = "<script>
                      $('#prsList .view').click(function(event){
                                                            event.preventDefault();
                                                            var a = $(this);
                                                            var url = a.attr('href');
                                                            var id__ = a.parent().parent().data('id');
                                                            $.post('/modelo/turismo/nwprs.php',
                                                                  {accion: url, id: id__},
                                                                  function(response){
                                                                      var dialog = $('<div></div>')
                                                                                    .html(response)
                                                                                    .dialog({modal: true,
                                                                                            height: 400,
                                                                                            width: 725,
                                                                                            title: 'Servicios cargados al presupuesto Numero: '+id__});
                                                                      dialog.dialog('open');
                                                                    
                                                                  });


                      });

                      $('#prsList .confirm').click(function(event){
                                                            event.preventDefault();
                                                            var a = $(this);
                                                            var conf = a.data('confirmado');
                                                            var idp = a.data('presupuesto');
                                                            if (conf == 1){
                                                                alert('El presupuesto ya ha sido confirmado');
                                                            }
                                                            else{
                                                                if (a.data('state') == 1)
                                                                {
                                                                  if (conf == 0){
                                                                      $('#detap').remove();
                                                                      var dialog = $('<div id=\"detap\"></div>').appendTo('body');
                                                                      dialog.dialog({
                                                                                             title: 'Confirmar Presupuesto',
                                                                                             width:400,
                                                                                             height:350,
                                                                                             modal:true,
                                                                                             autoOpen: false
                                                                                    });
                                                                      dialog.load('/modelo/turismo/nwprs.php',
                                                                                            {accion: 'confprs', prs: idp},
                                                                                            function (){ 
                                                                                            });
                                                                      dialog.dialog('open');
                                                                  }
                                                                }
                                                                else{
                                                                  alert('Debe primero cargar el valor del presupuesto para confirmarlo!');
                                                                }
                                                            }
                      });

                      $('#prsList .factu').click(function(event){

                                                            event.preventDefault();
                                                            var a = $(this);
                                                            var factu = a.data('factu');
                                                            if (factu == 1){
                                                                alert('El presupuesto ya ha sido facturado');
                                                            }
                                                            else{
                                                                if (a.data('state') == 2){
                                                                    var emite = a.data('emite');
                                                                    var pres = a.data('presupuesto');
                                                                    if (emite == 1){
                                                                        $(\"#fctPre\").remove();
                                                                        var dialog = $('<div id=\'fctPre\'></div>').appendTo('body');
                                                                        dialog.dialog({
                                                                                               title: 'Cargar Datos Factura',
                                                                                               width:400,
                                                                                               height:350,
                                                                                               modal:true,
                                                                                               autoOpen: false
                                                                                      });
                                                                        dialog.load('/modelo/turismo/nwprs.php',
                                                                                              {accion: 'fctPre', prs: pres},
                                                                                              function (){ 
                                                                                              });
                                                                        dialog.dialog('open');
                                                                    }                                                                    
                                                                }
                                                                else{
                                                                      alert('Para poder ser facturado el presupuesto debe estar confirmado!');
                                                                }
                                                            }               

                      });                      


              </script>
                ";
    print $tabla.$script;
  }
  elseif($accion == 'viewService'){
    $presupuesto = find('Presupuesto', $_POST['id']);
    $tabla = "<table class='table table-zebra' id='vjeList'>
              <thead>
                <tr>
                  <th>Origen</th>
                  <th>Destino</th>
                  <th>Fecha Salida</th>
                  <th>Hora Salida</th>
                  <th>Capacidad</th>
                </tr>
              </thead>
              <tbody>";    

    foreach ($presupuesto->getViajes() as $viaje) {
      $tabla.="<tr>
                <td>".$viaje->getOrigen()." (".strtoupper($viaje->getLugarSalida()).")</td>
                <td>".$viaje->getDestino()." (".strtoupper($viaje->getLugarLlegada()).")</td>
                <td>".$viaje->getFSalida()->format('d/m/Y')."</td>
                <td>".$viaje->getHSalida()->format('H:i')."</td>
                <td>".$viaje->getPax()."</td>
              </tr>
              ";
    }
    $tabla.="</tbody>
             </table>";


    print $tabla;
  }
  elseif($accion == 'loadSrvPr'){
    $presupuesto = find('Presupuesto', $_POST['id']);
    $tabla = "<table class='table table-zebra' id='vjelst' width='100%''>
              <thead>
                <tr>
                  <th>Fecha Salida</th>                
                  <th>Origen</th>
                  <th>Destino</th>
                  <th>Hora Salida</th>
                  <th>Capacidad</th>
                  <th>Accion</th>                  
                </tr>
              </thead>
              <tbody>";    

    foreach ($presupuesto->getViajes() as $viaje) {
      if (!$viaje->getEliminado())
          $tabla.="<tr>
                    <td>".$viaje->getFSalida()->format('d/m/Y')."</td>
                    <td>".$viaje->getOrigen()." (".strtoupper($viaje->getLugarSalida()).")</td>
                    <td>".$viaje->getDestino()." (".strtoupper($viaje->getLugarLlegada()).")</td>
                    <td>".$viaje->getHSalida()->format('H:i')."</td>
                    <td>".$viaje->getPax()."</td>
                    <td><img src='../../eliminar.png' width='20' height='20' border='0' onclick='remove(".$viaje->getId().")';></td>
                  </tr>";
    }
    $tabla.="</tbody>
             </table>   
             <script>

                    function remove(id_vje){
                                            $.post('/modelo/turismo/nwprs.php',
                                                  {accion: 'rmvvje', id: id_vje},
                                                  function(response){
                                                        viajes[id_vje] = 1;
                                                  });
                                            }; 
             </script>";

    print $tabla;
  }
  elseif($accion == 'confPres'){
    try {
          $estado = find('EstadoPresupuesto', 3);
          $debito = new MovimientoDebito();
          $credito = new MovimientoCredito();    
          $presupuesto = find('Presupuesto', $_POST['id']);
          $presupuesto->setEstado($estado);
          $debito->setPresupuesto($presupuesto);
          $data = $_POST;
          $data['monto'] = $presupuesto->getMontoFinal();
          $data['desc'] = 'Presupuesto '.str_pad($presupuesto->getId(), 6, "0", STR_PAD_LEFT);
          setDataMovimiento($debito, $data);    
          $data['monto'] = $_POST['senia'];
          $data['desc'] = 'Pago a cuenta Presupuesto '.str_pad($presupuesto->getId(), 6, "0", STR_PAD_LEFT);    
          setDataMovimiento($credito, $data);   
          $credito->setDebito($debito);
          $debito->addPago($credito);
          $presupuesto->addPago($credito);
          $entityManager->persist($debito);
          $entityManager->persist($credito);
          $entityManager->flush();
          print json_encode(array('ok'=>true));
    }
    catch (Exception $e) {
                          print json_encode(array('ok'=>false, 'message' => $e->getMessage()));

                          };
  }
  elseif ($accion == 'editps') {
      $servicios = array();

      $viajes = json_decode($_POST['vjes'], true);

      $ok = true;
      foreach ($viajes as $key => $value) {
        $ok = (($ok) && ($value == 1));  
        
      }

      if ($ok){
          print json_encode(array('status' => false, 'message' => 'Debe existir al menos un servicio'));
          exit();
      }

      $presupuesto = find('Presupuesto', $_POST['idprs']); 

      $presupuesto = setCamposPresupuestos($presupuesto, $_POST);

    /*  $validator = Validation::createValidatorBuilder()
                              ->addMethodMapping('loadValidatorMetadata')
                              ->getValidator();
      $errors = $validator->validate($presupuesto);
      if (count($errors) > 0) 
      {
          $errores = "";
          foreach($errors as $error)
          {
              $errores.= $error->getMessage()."<br>";
          }
          $response = array('status' => false, 'message' => $errores);
          print json_encode($response);
          exit();
      }                              
*/

      foreach ($viajes as $key_viaje => $value) {

          $viaje = find('Viaje', $key_viaje);
          $viaje->setEliminado($value); 
          if (!$presupuesto->existeViaje($viaje)){
            $presupuesto->addViaje($viaje);
            $viaje->setPresupuesto($presupuesto);
          }
      }

      $entityManager->flush();



      print json_encode(array('status' => true, 'message' => 'Datos actualizados correctamente'));
  }
  elseif($accion == 'confprs'){
    $pres = find('Presupuesto', $_POST['prs']);

    if ($pres->getConfConOrdenCompra()){
        $orden = "<p>Nrp orden Compra<input type='text' name='ordC' class='ui-widget-content ui-corner-all'/>
                  <input type='hidden' name='typo' value='oc'/></p>";
    }
    else{
        if ($pres->getPagoAnticipado()){
          $scripting = "$('#medios').selectmenu({width: 250});";
          $options = mediosPagosOptions();
          $orden = "<p>Importe Seña <input type='text' class='ui-widget-content ui-corner-all' name='monto'/></p>
                    <p>Medio de Pago <select name='medios' id='medios'>$options</select></p>
                    <input type='hidden' name='typo' value='pa'/>";
        }
        else{
          $orden = "<p><input type='hidden' name='typo' value='ocnf'/></p>";     
        }
    }

    $form = "<form id='seniaConf'>
                <p>Fecha Confirmacion<input type='text' name='fechaC' id='fechaC' class='ui-widget-content ui-corner-all'/></p>
                $orden
                <input type='button' value='Confirmar Presupuesto' id='cnfPres'/>
                <input type='hidden' name='idprs' value='$_POST[prs]' />
                <input type='hidden' name='accion' value='onlyConf' />                
            </form>
            <script>
              $scripting
              $('#fechaC').datepicker({dateFormat:'dd/mm/yy', autoOpen:false});
              
              $('#cnfPres').button().click(function(){
                                                    var senia = $('#seniaConf').serialize();
                                                    $.post('/modelo/turismo/nwprs.php',
                                                          senia,
                                                          function(data){
                                                                        alert(data);
                                                                        console.log(data);
                                                          });
                                                  });
            </script>";
    print $form;
  }
  elseif($accion == 'fctPre'){
    $pres = find('Presupuesto', $_POST['prs']);

    $orden = "<p>Numero factura<input type='text' style='text-align:right' class='ui-widget-content ui-corner-all' id='numFact' name='numFact'/></p>
    <p>Monto Final Sin Percepcion<input type='text' readonly style='text-align:right' class='ui-widget-content ui-corner-all' id='montoF' name='montoF' value='".$pres->getMontoFinal()."'/></p>
              <p>Monto Percepcion<input type='text' style='text-align:right' class='ui-widget-content ui-corner-all' id='montoPer' name='montoPer'/></p>
              <p>Monto Final <input type='text' style='text-align:right' class='ui-widget-content ui-corner-all' id='montoFinMasPerc' name='montoFinMasPerc'/></p>";

    $form = "<form id='seniaFact'>
                <p>Fecha Factura<input type='text' name='fechaF' id='fechaF'/></p>
                $orden
                <input type='button' value='Guardar Factura' id='save'/>
                <input type='hidden' name='idprs' value='$_POST[prs]' />
                <input type='hidden' name='accion' value='factuPresu' />                
            </form>
            <script> 
              $('#fechaF').datepicker({dateFormat:'dd/mm/yy', autoOpen:false});
              $('#montoPer').keyup(function(){
                                                var ret = parseFloat($(this).val());
                                                var monto = parseFloat($('#montoF').val());
                                                var suma = ret + monto;
                                                $('#montoFinMasPerc').val(suma);
              });
              $('#save').button().click(function(){
                                                    var senia = $('#seniaFact').serialize();
                                                    alert(senia);
                                                    $.post('/modelo/turismo/nwprs.php',
                                                          senia,
                                                          function(data){
                                                                        console.log(data);
                                                          });
                                                  });
            </script>";
    print $form;
  }  
  elseif($accion == 'factuPresu'){
    $presupuesto = find('Presupuesto', $_POST['idprs']);
    try {
          $debito = new MovimientoDebito();
          $debito->setPresupuesto($presupuesto);
          $data = array();
          $data['monto'] = $_POST['montoFinMasPerc'];
          $data['desc'] = 'Factura N° '.$_POST['numFact']." (Presupuesto N°".str_pad($presupuesto->getId(), 6, "0", STR_PAD_LEFT).")";
          $data['fec'] = $_POST['fechaF'];
          $data['cli'] = $presupuesto->getCliente()->getId();        
          $presupuesto->setFacturado(true);  
          setDataMovimiento($debito, $data);    
          $entityManager->persist($debito);
          $entityManager->flush();
          print json_encode(array('ok'=>true));
    }
    catch (Exception $e) {
                          print json_encode(array('ok'=>false, 'message' => $e->getMessage()));

                          };    

  }
  elseif($accion == 'onlyConf'){
    $estado = find('EstadoPresupuesto', 2);
    $presupuesto = find('Presupuesto', $_POST['idprs']);
    $presupuesto->setConfirmado(true);
    $presupuesto->setEstado($estado);
    $debito="";

    if (!$presupuesto->getEmiteComprobante()){
        $debito = new MovimientoDebito();
        $data = array('cli' => $presupuesto->getCliente()->getId(), 
                      'fec'=> $_POST['fechaC'], 
                      'monto' => $presupuesto->getMontoFinal(), 
                      'desc'=> 'Presupuesto Nro. '.str_pad($presupuesto->getId(), 6, "0", STR_PAD_LEFT));
        setDataMovimiento($debito, $data);   
        $debito->setPresupuesto($presupuesto); 
    };

    if ($_POST['typo'] == 'oc'){////solo se confirma con la orden de compra
          $presupuesto->setNumeroOrdenCompra($_POST['ordC']);
    }
    elseif ($_POST['typo'] == 'pa'){ ////confirma con la seña 
        $medioPago = find('MedioPago', $_POST['medios']);

        $credito = new MovimientoCredito();
        $credito->setMedioPago($medioPago);
        $data = array('cli' => $presupuesto->getCliente()->getId(), 
                      'fec'=> $_POST['fechaC'], 
                      'monto' => $_POST['monto'], 
                      'desc'=> 'Pago a cuenta Presupuesto Nro. '.str_pad($presupuesto->getId(), 6, "0", STR_PAD_LEFT)); 
        setDataMovimiento($credito, $data);  
        if ($debito){ 
            $credito->setDebito($debito);
            $debito->addPago($credito);        
        }
    }

    if ($debito)
      $entityManager->persist($debito);
    if ($credito)
          $entityManager->persist($credito);
    $entityManager->flush();    
    print json_encode(array('ok'=>true));
  }    
  elseif($accion == 'cccli'){
          $cliente = find('Cliente', $_POST['cli']);
          if ($cliente)
            print $cliente->getCuit();
          else
            print "";
  }

  function setDataMovimiento($movimiento, $campos)
  {
    $cliente = find('Cliente', $campos['cli']);
    $usuario =   find('Usuario', $_SESSION['userid']);       
    $estructura =   find('Estructura', $_SESSION['structure']);       
    $fecha = DateTime::createFromFormat('d/m/Y', $campos['fec']);

    $movimiento->setFecha($fecha);        
    $movimiento->setCliente($cliente);
    $movimiento->setUsuario($usuario);
    $movimiento->setEstructura($estructura); 
    $movimiento->setImporte($campos['monto']);
    $movimiento->setDescripcion($campos['desc']);    
  }  

  function setCamposPresupuestos($pres, $campos){
      $fpedido = DateTime::createFromFormat('d/m/Y', $campos[fpedido]);
      $pres->setFechaSolicitud($fpedido);
      if ($campos['fresp']){
        $frspta = DateTime::createFromFormat('d/m/Y', $campos[fresp]);
        $pres->setFechaInforme($frspta);        
      }
      if ($campos['fconf']){
        $fconf = DateTime::createFromFormat('d/m/Y', $campos[fconf]);
        $pres->setFechaConfeccion($fconf);        
      }

      $cliente = find('Cliente', $campos['cliente']);
      $canal =   find('CanalPedido', $campos['canal']); 
      $usuario =   find('Usuario', $_SESSION['userid']);       
      $estructura =   find('Estructura', $_SESSION['structure']);            
      $pagoAnticipado = (isset($campos['pagoanti'])?true:false);
      $emiteComprobante = (isset($campos['efc'])?true:false);
      $confOrdenCompra = (isset($campos['requiereOC'])?true:false);      
      $pres->setCliente($cliente);
      $pres->setUsuario($usuario);
      $pres->setCanalPedido($canal);
      $pres->setEstructura($estructura);      
      $pres->setPagoAnticipado($pagoAnticipado);
      $pres->setEmiteComprobante($emiteComprobante);
      $pres->setObservaciones($campos['observa']);
      $pres->setMailContacto($campos['mailcontacto']);
      $pres->setTelefonoContacto($campos['telcontacto']);
      $pres->setNombreContacto($campos['nomcontacto']);  
      $pres->setPax($campos['pax']);        
      $pres->setConfConOrdenCompra($confOrdenCompra);           


      if (is_numeric($campos['precioNeto'])){
          $pres->setMontoSIva($campos['precioNeto']);
          $pres->setIva($campos['iva']);
          $pres->setMontoFinal($campos['preciofinal']);
          $estado = find('EstadoPresupuesto', 1);
      }
      else{
          $pres->setMontoSIva(null);
          $pres->setIva(null);
          $pres->setMontoFinal(null);        
          $estado = find('EstadoPresupuesto', 3);
      }
      $pres->setEstado($estado);

     /* $pres->setMontoSIva($campos['precioNeto']);
      $pres->setIva($campos['iva']);
      $pres->setMontoFinal($campos['preciofinal']);
      if ($campos['precioNeto']){
          $estado = find('EstadoPresupuesto', 1);
          $pres->setEstado($estado);
      }*/

      return $pres;    
  }


  
?>
