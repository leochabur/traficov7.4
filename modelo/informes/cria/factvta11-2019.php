<?php
error_reporting(E_ALL & ~E_NOTICE);
set_time_limit(0);

     session_start();
//error_reporting(0);      
include ('../../../modelsORM/manager.php');  
include_once ('../../../modelsORM/controller.php');     
include_once ('../../../modelsORM/call.php');     
include_once ('../../../modelsORM/src/FacturaVenta.php');     
include_once ('../../../modelsORM/src/OrdenFacturada.php');  

  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include_once ('../../../controlador/ejecutar_sql.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];



  if($accion == 'addfact'){
    try{
      $cliente = getCliente($_POST['clientes'], $_POST['str']);
  //    $factura = getFacturaVenta($cliente->getId());
      $fact = 0;
      if ($factura){
        $fact = $factura->getId();
        $status = true;
      }
      else{
        $factura = new FacturaVenta();
        $formato = 'd/m/Y';
        $desde = DateTime::createFromFormat($formato, $_POST['desde']);
        $hasta = DateTime::createFromFormat($formato, $_POST['hasta']);     
        $factura->setDesde($desde);
        $factura->setHasta($hasta);   
        $factura->setCliente($cliente);
        $factura->setDescripcion($_POST['observa']);
        $entityManager->persist($factura);        
        $entityManager->flush();
        $fact = $factura->getId();
        $status = false;        
      }
      print json_encode(array('ok' => true, 'fact' => $fact));
    }
    catch (Exception $e){
                          print json_encode(array('ok' => false, 'msge' => $e->getMessage()));
                        }
  }  
  elseif($accion == 'listfact'){
      $facturas = getFacturasPendientes($_POST['show']);
      $table = '<table border="0" align="center" width="85%" name="tabla" class="table table-zebra">
                    <thead>
                      <tr>
                          <th>Cliente</th>
                          <th>Desde</th>
                          <th>Hasta</th>
                          <th>Estado</th>
                          <th>Importe</th>
                          <th>Observacion</th>
                          <th></th>
                      </tr>
                    </thead>
                    <tbody>';
      foreach ($facturas as $factura) {
        $close = "";
        $estado = "Finalizada";
        if (!$factura->getCerrada())
        {
          $close = '<a href="#" class="close" data-id="'.$factura->getId().'" title="Cerrar Factura"><i class="fas fa-edit fa-2x"></i></a>
                    <a href="/vista/informes/cria/addordfac.php?fv='.$factura->getId().'" target="_blanck" title="Agregar ordenes trabajo"><i class="fas fa-arrow-circle-right fa-2x"></i></a>';
          $estado = "Pendiente de finalizar";
        }
        else{
          $close = '<a href="/vista/informes/cria/viewfac.php?fv='.$factura->getId().'" title="Ver Factura"><i class="fas fa-eye fa-2x"></i></a>';
        }

          $table.='<tr>
                      <td>'.$factura->getCliente().'</td>
                      <td>'.$factura->getDesde()->format('d/m/Y').'</td>
                      <td>'.$factura->getHasta()->format('d/m/Y').'</td>
                      <td>'.$estado.'</td>
                      <td align="right">$ '.number_format($factura->getMontoFactura(),2).'</td>
                      <td>'.$factura->getDescripcion().'</td>                      
                      <td>                        
                        '.$close.'
                      </td>
                   </tr>';
      }
      $table.='</tbody>
               </table>
               <script>
                        $(".close").click(function(event){
                                                          event.preventDefault();
                                                          var id = $(this).data("id");
                                                          if (confirm("Seguro cerrar la factura?"))
                                                          {
                                                            $.post("/modelo/informes/cria/factvta.php",
                                                                  {accion: "closeFact", fact: id},
                                                                  function(data){
                                                                                var response = $.parseJSON(data);
                                                                                if (response.ok)
                                                                                {
                                                                                  $("#cargar").click();
                                                                                }
                                                                                else
                                                                                  alert(response.messgae);
                                                                   });
                                                          }
                        });
               </script>';
      print $table;
  }
  elseif($accion == 'closeFact'){
    try{
          $factura = find('FacturaVenta', $_POST['fact']);
          $factura->setCerrada(true);
          $entityManager->flush();
          print json_encode(array('ok' => true ));
    }catch(Exception $e){
                            print json_encode(array('ok' => false, 'messgae' => $e->getMessage()));
                        }
  }
  elseif($accion == 'addsrv'){

try{
     $desde = dateToMysql($_POST['fecha'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $cliente=$_POST['cliente'];

     $facturaVenta = find('FacturaVenta',$_POST['factura']);

     $idsOrdenes = getAllIdsOrdenesFactura($facturaVenta);

     $ids="";
     foreach ($idsOrdenes as $id) {
       $ids.="$id[id],";
     }
     $ids.="0";
     $tiposS = "";
     if ($_POST['tipos'] == '1'){
        $tiposS = "AND (s.id_TipoServicio in (1,2,16)) AND (s.id_estructura_TipoServicio = $_SESSION[structure])";
     }
     elseif ($_POST['tipos'] == '2'){
        $tiposS = "AND (s.id_TipoServicio not in (1,2,16)) AND (s.id_estructura_TipoServicio = $_SESSION[structure])";
      }

//            LEFT JOIN tiposervicio ts ON ts.id = s.id_TipoServicio AND ts.id_estructura = s.id_estructura_TipoServicio
      $sql="select fservicio, c.nombre, 1 as cant, if(c.id is null, 0, c.id) as crono, dayofweek(fservicio) as dia, tu.id as tipo, o.id as orden, 
                   id_micro as micro, o.id_estructura
            from ordenes o
            left join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
            left join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma

            left join unidades u on u.id = o.id_micro
            left join tipounidad tu on tu.id = u.id_tipounidad and tu.id_estructura = u.id_estructura_tipounidad
            where o.id_cliente = $cliente and 
                 (concat(fservicio, ' ', o.hllegada) between concat('$desde', ' ', '05:00:00') and concat('$hasta', ' ', '23:59:00') or 
                 (s.i_v = 'v' and concat(fservicio, ' ', o.hsalida) between concat(DATE_ADD('$hasta', INTERVAL 1 day), ' ' ,'00:00:00') and concat(DATE_ADD('$hasta', INTERVAL 1 day), ' ' ,'07:00:00'))) and 
                 not borrada and not suspendida and o.id not in ($ids) $tiposS
            order by dayofweek(fservicio), c.id, tu.id";
      //die($sql);

      $facturacion = facturacionCliente($cliente); ////busca la facturacion del cliente
      if (!$facturacion){
        die('no encuentra '.$_POST['cli']);
      }
           $estructura = find('Estructura', $_SESSION['structure']);
           $conn = conexcion();

           $tarifas = $facturacion->getTarifas();  ////obtiene todas las tarifas cargadas

           $result = mysql_query($sql, $conn) or die(mysql_error($conn)."  asd ".$sql);

           $cronos = array();
          ////////////////////////////////
         /*  $cr = "";
           while ($value = mysql_fetch_array($result)){
              $crono = cronograma($value['id_estructura'], $value['crono']);
              if ($crono){
                  $cronos[$crono->getId()] = $crono;
              }
           }*/

           ///////////////////////////////////////////////////////////////
           $dias = array();
           $tipos = array();

           
           $factura = array();
           $tt = array();
           $i=0;
    }catch (Exception $e){ die("toronjeta ".$e->getMessage());}

    $auxOrdenes = array();
           while ($value = mysql_fetch_array($result)) {

               try{
                      if (!isset($dias[$value['dia']])){////sino busco ya el dia de la semana lo busca y lo almacena para evitar buscarlo en el futuro
                        $dias[$value['dia']] = diaSemana($value['dia']);
                      }

                      if (!isset($tipos[$value['tipo']])){ ////idem tipo vehiculo
                        if ($value['tipo']){                          
                          $tipos[$value['tipo']] = tipoVehiculo($estructura, $value['tipo']);
                        }
                      } 
                      
                      if (!isset($cronos[$value['crono']])){   ////idem cronogramas
                        if ($value['crono']){
                          $crono = cronograma($value['id_estructura'], $value['crono']);
                          if (!isset($crono)){
                            die('no esta seteado');
                            
                          }
                          else{
                            $cronos[$value['crono']] = $crono;
                          }
                        }
                      }
                      
                      $existe = false;
                      $sincoche = false;
                      if ($value['crono'])
                      {
                          if (!($value['micro'])){
                                if (!isset($factura['ORDEN_SIN_INTERNO'])){
                                  $factura['ORDEN_SIN_INTERNO'] = array('art'=> 'Servicios Sin coche asignado', 'cant' => $value['cant'], 'ords' => array());
                                }
                                else{
                                  $factura['ORDEN_SIN_INTERNO']['cant']+= $value['cant'];
                                }                                              
                                $factura['ORDEN_SIN_INTERNO']['ords'][] = $value['orden'];
                                $existe = true;
                                $sincoche = true;                                              
                            }                                          
                            elseif (!$value['tipo']){                                              
                                if (!isset($factura['ORDEN_SIN_TIPO_VEHICULO'])){
                                  $factura['ORDEN_SIN_TIPO_VEHICULO'] = array('art'=> 'Servicios con unidad sin asignar tipo', 'cant' => $value['cant'], 'ords' => array());
                                }
                                else{
                                  $factura['ORDEN_SIN_TIPO_VEHICULO']['cant']+= $value['cant'];
                                }
                                $factura['ORDEN_SIN_TIPO_VEHICULO']['ords'][] = $value['orden'];
                                $existe = true;
                                $sincoche = true;
                            }
                            else
                            {  
                                foreach ($tarifas as $tarifa) 
                                {  /// recorre todas las tarifas que tiene cargadas     
                                        if (($tarifa->existeDia($dias[$value['dia']])) && ($tarifa->existeCronograma($cronos[$value['crono']])))
                                        {
                                                 ////la orden tiene tipo de vehiculo asignado
                                                  $tfa = $tarifa->getTarifaTipoVehiculo($tipos[$value['tipo']]);
                                                  if ($tfa){ ///existe una tarifa para el tipo de vehiculo
                                                      $cant = $value['cant'];
                                                      if ($tfa->getArticulo()!= null){
                                                        if (isset($factura[$tarifa->getId()])){
                                                          $factura[$tarifa->getId()]['cant']+=$cant;//value['cant'];
                                                          $factura[$tarifa->getId()]['ords'][] = $value['orden'];
                                                        }
                                                        else{
                                                          $factura[$tarifa->getId()] = array('article' => $tfa->getArticulo()->getId(),'art'=> $tfa, 'cant' => $cant, 'tfa'=>$tfa, 'ords' => array(0=>$value['orden']));
                                                        }
                                                        
                                                      }
                                                      else{
                                                        if (isset($factura[$tarifa->getId()])){
                                                          $factura[$tarifa->getId()]['cant']+=$cant;
                                                        }
                                                        else{
                                                          $factura[$tarifa->getId()] = array('art'=> $tarifa->getNombre(), 'cant' => $cant, 'tfa'=>$tfa);
                                                        }                                          

                                                      }
                                                      $existe = true;
                                                  }
                                              }
                                  }     
                                /*                  
                                foreach ($tarifas as $tarifa) 
                                {  /// recorre todas las tarifas que tiene cargadas     
                                        if (($tarifa->existeDia($dias[$value['dia']])) && ($tarifa->existeCronograma($cronos[$value['crono']])))
                                        {
                                                 ////la orden tiene tipo de vehiculo asignado
                                                  $tfa = $tarifa->getTarifaTipoVehiculo($tipos[$value['tipo']]);
                                                  if ($tfa){ ///existe una tarifa para el tipo de vehiculo
                                                      $cant = $value['cant'];
                                                      if ($tfa->getArticulo()!= null){
                                                        if (isset($factura[$tfa->getArticulo()->getId()])){
                                                          $factura[$tfa->getArticulo()->getId()]['cant']+=$cant;//value['cant'];
                                                          $factura[$tfa->getArticulo()->getId()]['ords'][] = $value['orden'];
                                                        }
                                                        else{
                                                          $factura[$tfa->getArticulo()->getId()] = array('art'=> $tfa, 'cant' => $cant, 'tfa'=>$tfa, 'ords' => array(0=>$value['orden']));
                                                        }
                                                        
                                                      }
                                                      else{
                                                        if (isset($factura[$tarifa->getNombre()])){
                                                          $factura[$tarifa->getNombre()]['cant']+=$cant;
                                                        }
                                                        else{
                                                          $factura[$tarifa->getNombre()] = array('art'=> $tarifa->getNombre(), 'cant' => $cant, 'tfa'=>$tfa);
                                                        }                                          

                                                      }
                                                      $existe = true;
                                                  }
                                              }
                                  }  */  
                          }                          
                      }
                      else{
                            if (!isset($factura['SERVICIO_EVENTUAL'])){
                              $factura['SERVICIO_EVENTUAL'] = array('art'=> 'Servicios Eventuales', 'cant' => $value['cant'], 'ords' => array());
                            }
                            else{
                              $factura['SERVICIO_EVENTUAL']['cant']+= $value['cant'];
                            }
                            $factura['SERVICIO_EVENTUAL']['ords'][] = $value['orden'];        
                            $existe = true;
                            $sincoche = true;                 
                      }                      
                  /*    if ((!$existe) && !($sincoche)){ ///flag para indicar que no existe la tarifa para la orden
                          if (!isset($factura['_SIN ASIGNAR'])){
                              $factura['_SIN ASIGNAR'] = array('art'=> 'Servicios sin Asignar', 'cant' => $value['cant'], 'peaje'=>0, 'ords' => array(0=>$value['orden']));
                          }
                          else{
                              $factura['_SIN ASIGNAR']['cant']+=$value['cant'];                              
                          }                          
                          $factura['_SIN ASIGNAR']['ords'][] = $value['orden'];
                      }      */            
                }catch (Exception $e){ die("toronjeta ".$e->getMessage());}
            $auxOrdenes[] = $value['orden'];
           }
           //die(print_r($cronos));
           $tabla = "<table class='table table-zebra'>
                      <thead>
                        <tr>
                            <th>N#</th>
                            <th>Tarifa</th>
                            <th>Articulo/Detalle</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>";
        //  die("osooooooooooooooo");
          try{
           ksort($factura);
           $i = 1;
           foreach ($factura as $key => $value) {
            $tarifa = (isset($value['tfa'])?$value['tfa']->getTarifaServicio():'SIN ASIGNAR');
            $tabla.="<tr>
                        <td>$i</td>
                        <td>".$tarifa."</td>
                        <td>$value[art]</td>
                        <td>$value[cant]</td>
                        <td align='right'>$ ".(isset($value['tfa'])?round($value['tfa']->getArticulo()->getImporte(),2):0)."</td>
                        <td align='right'>$ ".number_format(($value['cant']*(isset($value['tfa'])?$value['tfa']->getArticulo()->getImporte():'0')),2,',','.')."</td>                
                        <td><a href='#' data-item='$i' data-tarifa='".$tarifa."' class='viewfac' data-id='".implode(',', $value['ords'])."'>Facturar Manual</a>
                        <a href='#' class='factall' data-article='".$value['article']."' data-tfa='".$key."' data-fact='".$_POST['factura']."' data-id='".implode(',', $value['ords'])."'>Facturar Todo</a></td>                                             
                     </tr>";
              //$tabla.="$key -> articulo: $value[art]   _   cantidad: $value[cant]<br>";
              $i++;
           }
         }catch (Exception $e){ die($e->getMessage());}
           $tabla.="</tbody>
                    </table>";
          // print $tabla;
           //exit();
           $script = "<script>
                      $('.viewfac').button().click(function(event){
                                                              event.preventDefault();
                                                              var titulo = $(this).data('tarifa');
                                                              var i = $(this).data('item');
                                                              $('#detap').remove();
                                                              var dialog = $('<div id=\"detap\"></div>').appendTo('body');
                                                              dialog.dialog({
                                                                                title: 'Item N# '+i +' - ('+titulo+')',
                                                                                width:900,
                                                                                height:400,
                                                                                modal:true,
                                                                                autoOpen: false
                                                                            });
                                                              dialog.load('/modelo/informes/cria/factvta.php',
                                                                          {accion: 'factman', ords: $(this).data('id'), fact:".$facturaVenta->getId().", fec:'$_POST[fecha]', has:'$_POST[hasta]'},
                                                                          function (){ 
                                                                                       });
                                                              dialog.dialog('open');
                                                            });
                      $('.factall').button().click(function(event){
                                                              event.preventDefault();
                                                              if (confirm('Seguro facturar todos los servicios')){
                                                                  $.post('/modelo/informes/cria/factvta.php',
                                                                              {accion: 'facall', ords: $(this).data('id'), tfa: $(this).data('tfa'), fact: $(this).data('fact'), art: $(this).data('article')},
                                                                              function (data){ 
                                                                                              var response = $.parseJSON(data);

                                                                                              if (response.ok){
                                                                                                window.location.href='/vista/informes/cria/addordfac.php?fv=".$facturaVenta->getId()."&fec=$_POST[fecha]&has=$_POST[hasta]';
                                                                                              }
                                                                                              else{
                                                                                                alert(response.messgae);
                                                                                              }
                                                                                              });
                                                              }
                                                            });                                                            
                      </script>";
           print $tabla.$script;
  }
  elseif ($accion == 'facall') {
    try{
          if (!is_numeric($_POST['art'])){
            die ('No existe un articulo asociado!');
          }
          $factura = find('FacturaVenta', $_POST['fact']);
          $ordenes = getOrdenes(explode(',',  $_POST['ords']));
          $articulo = find('ArticuloCliente', $_POST['art']);
          $ordeneFacturada = new OrdenFacturada();
          $ordeneFacturada->setCantidad(count($ordenes));
          $ordeneFacturada->setArticulo($articulo);
          $ordeneFacturada->setImporteUnitario($articulo->getImporte());
          $ordeneFacturada->setImporte(($articulo->getImporte()*count($ordenes)));
          $ordeneFacturada->setFacturaVenta($factura);
          $tarifa = find('TarifaServicio', $_POST['tfa']);
          $ordeneFacturada->setFechaAlta(new DateTime());
          $ordeneFacturada->setTarifa($tarifa);
          foreach ($ordenes as $orden) {
            $ordeneFacturada->addOrdene($orden);
          }
          $factura->addOrdenesFacturada($ordeneFacturada);
          $entityManager->persist($ordeneFacturada);
          $entityManager->flush();
          print json_encode(array('ok' => true ));
    }catch(Exception $e){
                            print json_encode(array('ok' => false, 'messgae' => $e->getMessage()));
                        }
  }
  elseif ($accion == 'detof') {
      try{
          $ordFact = find('OrdenFacturada', $_POST['of']);  

          $tabla = "<table class='table tablaItems'>
                          <thead>
                              <tr>
                                  <th>Orden #</th>
                                  <th>Nombre Servicio</th>                                  
                                  <th>Fecha</th>
                                  <th>H. Salida</th>
                                  <th>Conductor</th>
                                  <th>Interno</th>
                                  <th>Pax</th>
                              </tr>
                          </thead>
                          <tbody>";
          foreach ($ordFact->getOrdenes() as $orden) {
              $tabla.="<tr>
                          <td>".$orden->getId()."</td>
                          <td>$orden</td>                        
                          <td>".$orden->getFservicio()->format('d/m/Y')."</td>
                          <td>".$orden->getHsalida()->format('H:i')."</td>
                          <td>".$orden->getConductor1()."</td>
                          <td>".$orden->getUnidad()."</td>
                          <td>".$orden->getPasajeros()."</td>      
                      </tr>";
          }

          $tabla.="</tbody>
                  </table>";

          $tabla.='<script> 
                      $(".tablaItems").dataTable({
                                                                              "sScrollY": "800px",
                                                                              "bPaginate": false,
                                                                              "bScrollCollapse": true,
                                                                              "bJQueryUI": true,
                                                                              "oLanguage": {
                                                                                                     "sLengthMenu": "Display _MENU_ records per page",
                                                                                                     "sZeroRecords": "Sin Registros para mostrar",
                                                                                                     "sInfo": "",
                                                                                                     "sInfoEmpty": "Showing 0 to 0 of 0 records",
                                                                                                     "sInfoFiltered": "(filtered from _MAX_ total records)"}
                                                                               });
                  </script>';


          print $tabla;
        }catch (Exception $e){print $sql;}


  }  
  elseif ($accion == 'detsrv') {
      try{
          $sql = "SELECT o.nombre, date_format(fservicio, '%d/%m/%Y') as fecha, date_format(o.hsalida, '%H:%i') as salida,
                         concat(ch1.apellido, ', ',ch1.nombre) as conductor, interno, tipo, cantpax, o.id, cr.nombre as crono
                  FROM ordenes o
                  inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                  inner join cronogramas cr on cr.id = s.id_cronograma and cr.id_estructura = s.id_estructura_cronograma                  
                  LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
                  LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
                  LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                  LEFT JOIN unidades u ON (u.id = o.id_micro)
                  LEFT JOIN tipounidad tu ON (tu.id = u.id_tipounidad) and (tu.id_estructura = u.id_estructura_tipounidad)
                  WHERE o.id in ($_POST[ords])
                  ORDER BY fservicio, o.nombre, o.hsalida";    
          $result = ejecutarSQL($sql);
          $tabla = "<table class='table table-zebra'>
                          <thead>
                              <tr>
                                  <th>Orden</th>
                                  <th>Nombre Servicio</th>                                  
                                  <th>Fecha</th>
                                  <th>H. Salida</th>
                                  <th>Conductor</th>
                                  <th>Interno</th>
                                  <th>Pax</th>
                  <th></th>
                              </tr>
                          </thead>
                          <tbody>";
          while ($row = mysql_fetch_array($result)){
            $tabla.="<tr>
                        <td>$row[id]</td>
                        <td>$row[crono]</td>                        
                        <td>$row[1]</td>
                        <td>$row[2]</td>
                        <td>$row[3]</td>
                        <td>$row[4]</td>
                        <td>$row[6]</td>      
            <td><input type='checkbox'></td>
                    </tr>";
          }

          $tabla.="</tbody>
                  </table>";


          print $tabla;
        }catch (Exception $e){print $sql;}


  }  
  elseif ($accion == 'factman') {

      try{
          $factura = find('FacturaVenta', $_POST['fact']);
          // die("PRINTITR   fff".$factura);
          $options = "";
          $articulos = articulosCliente($factura->getCliente()->getId());
        }catch (Exception $e){print $sql;}
          foreach ($articulos as $art) {
            $options.="<option value='".$art->getId()."' data-mto='".$art->getImporte()."'>$art</option>";
          }
          $sql = "SELECT o.nombre, date_format(fservicio, '%d/%m/%Y') as fecha, date_format(s.hsalida, '%H:%i') as salida,                         
                         concat(ch1.apellido, ', ',ch1.nombre) as conductor, interno, tu.tipo, cantpax, o.id,  o.nombre as crono,
                         ts.tipo as tipoS, cr.nombre as nomCrono, date_format(s.hllegada, '%H:%i') as llegada, i_v
                  FROM ordenes o
                  left join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                  LEFT JOIN tiposervicio ts ON ts.id = s.id_TipoServicio AND ts.id_estructura = s.id_estructura_TipoServicio
                  left join cronogramas cr on cr.id = s.id_cronograma and cr.id_estructura = s.id_estructura_cronograma                  
                  LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
                  LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
                  LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                  LEFT JOIN unidades u ON (u.id = o.id_micro)
                  LEFT JOIN tipounidad tu ON (tu.id = u.id_tipounidad) and (tu.id_estructura = u.id_estructura_tipounidad)
                  WHERE o.id in ($_POST[ords])
                  ORDER BY fservicio, o.nombre, o.hsalida";    
          //die($sql);
          $result = ejecutarSQL($sql);
          $tabla = "<table id='tabla' class='tablesorter' width='100%'>
                          <thead>
                              <tr>
                                  <th>Nro</th>
                                  <th>Orden</th>
                                  <th>Nombre Orden</th>        
                                  <th>Nombre Servicio</th>     
                                  <th>Descripcion</th>    
                                  <th>Tipo Servicio</th>                                                              
                                  <th>Fecha</th>
                                  <th>H. Salida</th>
                                  <th>Conductor</th>
                                  <th>Interno</th>
                                  <th>Pax</th>
                                  <th>Orden</th>
                                  <th>Todos<input type='checkbox' class='allItem'></th>
                              </tr>
                          </thead>
                          <tbody>";
          $row = mysql_fetch_array($result);
          $orden = 1;
          while ($row){
            $date = $row['fecha'];
            $i = 1;
            while (($row) &&($date == $row['fecha']))
            {
                $descripcion =  ($row['i_v'] == 'i'?'Entrada '.$row['llegada']:'Salida '.$row['salida']);
                $tabla.="<tr>
                            <td>$i</td>
                            <td>$row[id]</td>
                            <td>$row[crono]</td>      
                            <td>$row[nomCrono]</td>    
                            <td>$descripcion</td>    
                            <td>$row[tipoS]</td>      
                            <td>$row[1]</td>
                            <td>$row[2]</td>
                            <td>$row[3]</td>
                            <td>$row[4]</td>
                            <td>$row[6]</td>      
                            <td>$orden</td>
                            <td><input type='checkbox' class='sn' data-id='$row[id]'></td>
                        </tr>";
                $i++;
                $row = mysql_fetch_array($result);
                $orden++;
            }
          }

          $tabla.="</tbody>
                  </table>";
          $tabla.='<fieldset class="ui-widget ui-widget-content ui-corner-all">
                     <legend class="ui-widget ui-widget-header ui-corner-all">Detalle Item</legend>
                         <table border="0" align="center" width="75%" class="table table-zebra">
                            <thead>
                              <tr>
                                  <th>Articulo</th>
                                  <th>Cantidad</th>                                  
                                  <th>Unitario</th>
                                  <th>Total</th>
                                  <th>Facturar</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                  <td>
                                        <select id="artFact">
                                              <option value="0"></option>
                                              '.$options.'
                                        </select>
                                  </td>
                                  <td><input type="text" id="cant" readonly/></td>                                  
                                  <td><input type="text" id="unit"readonly/></td>
                                  <td><input type="text" id="tot" readonly/></td>      
                                  <td><input type="button" class="fact" data-fact="'.$factura->getId().'" value="Facturar"/></td>
                              </tr>
                            </tbody>
                        </table>
                    </fieldset>';
        $script = "<script>
                          $('#tabla').tablesorter({widgets: ['zebra']});
                          $('.allItem').click(function(){
                                                          if( $(this).attr('checked')){
                                                              $('.sn').attr('checked', false);
                                                          }
                                                          else{
                                                            $('.sn').attr('checked', true);
                                                          }
                                                          $('.sn').trigger( 'click' );
                            });

                          $('select').change(function(data){

                                                        var cant = $('.sn:checked').size();
                                                        var unit = $(this).find(':selected').data('mto');
                                                        $('#cant').val(cant);
                                                        var total = unit * cant;
                                                        $('#tot').val(total);                        
                                                        $('#unit').val(unit);
                          });
                          $('.sn').click(function(){
                              var cant = $('.sn:checked').size();
                              var unit = $('#artFact').find(':selected').data('mto');
                              $('#cant').val(cant);
                              var total = unit * cant;
                              $('#tot').val(total);
                          });
                          $('.fact').button().click(function(){
                                                                var fac = $(this).data('fact');
                                                                var ids = new Array();
                                                                var art = $('#artFact').val();
                                                                $('.sn').each(function(){
                                                                      if($(this).is(':checked')){
                                                                        ids.push($(this).data('id'));
                                                                      }                                    
                                                                });
                                                                $.post('/modelo/informes/cria/factvta.php',
                                                                        {accion: 'factm', ords: ids.join(), fv: fac, ar: art, unit: $('#unit').val()},
                                                                        function(data){
                                                                                        var response = $.parseJSON(data);
                                                                                        if (response.ok){
                                                                                            window.location.href='/vista/informes/cria/addordfac.php?fv=".$factura->getId()."&fec=$_POST[fec]&has=$_POST[has]';
                                                                                         }
                                                                                         else{
                                                                                            alert(response.msge);
                                                                                         }
                                                                        });
                          });
                          


                   </script>";
          print $tabla.$script;
        
  }    
  elseif($accion == 'factm'){
    try{
          $factura = find('FacturaVenta', $_POST['fv']);
          $ordenes = getOrdenes(explode(',',  $_POST['ords']));
          $articulo = find('ArticuloCliente', $_POST['ar']);
          $ordeneFacturada = new OrdenFacturada();
          $ordeneFacturada->setCantidad(count($ordenes));
          $ordeneFacturada->setArticulo($articulo);
          $ordeneFacturada->setImporteUnitario($_POST['unit']);
          $ordeneFacturada->setImporte(($_POST['unit']*count($ordenes)));
          $ordeneFacturada->setFacturaVenta($factura);

          foreach ($ordenes as $orden) {
            $ordeneFacturada->addOrdene($orden);
          }
          $factura->addOrdenesFacturada($ordeneFacturada);
          $entityManager->persist($ordeneFacturada);
          $entityManager->flush();
          print json_encode(array('ok' => true ));
    }catch(Exception $e){
                            print json_encode(array('ok' => false , 'msge' => $e->getMessage()));
                        }          
  }
elseif($accion == 'detIt'){
  try{
         $factura = find('FacturaVenta', $_POST['fv']);         
         $articulo = find('ArticuloCliente', $_POST['art']);
         $q = $entityManager->createQuery("SELECT ofa FROM OrdenFacturada ofa WHERE ofa.facturaVenta = :factura AND ofa.articulo = :articulo");  
         $q->setParameter('factura', $factura);
         $q->setParameter('articulo', $articulo);             
         $result = $q->getResult();  
    }catch(Exception $e){
                            die ($e->getMessage());
                        }   
          $tabla = "<table class='table table-zebra'>
                    <thead>
                        <tr>
                            <th>Item Nro.</th>
                            <th>Tarifa aplicada</th>
                            <th>Articulo</th>
                            <th>Cantidad Servicios</th>           
                            <th>Unitario</th>                                                              
                            <th>Total</th>
                            <th>Fecha Carga</th>
                            <th>Ver Servicios facturados</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>";
          $i = 1;
          foreach ($result as $value) {
                $ords = "";
                foreach ($value->getOrdenes() as $orden) {
                    if ($ords){
                      $ords.=", ".$orden->getId();
                    }
                    else{
                      $ords = $orden->getId();
                    }
                
                }
                $tfa = ($value->getTarifa()?$value->getTarifa()->getNombre():"");
                $tabla.="<tr>
                            <td>$i</td>
                            <td>$tfa</td>
                            <td>".$value->getArticulo()."</td>
                            <td align='right'>".count($value->getOrdenes())."</td>      
                            <td align='right'>".$value->getImporteUnitario()."</td>      
                            <td align='right'>".(count($value->getOrdenes())*$value->getImporteUnitario())."</td>   
                            <td align='center'>".($value->getFechaAlta()?$value->getFechaAlta()->format('d/m/Y H:i'):'')."</td>   
                            <td><input type='button' value='Ver Servicios' data-ords='$ords' class='detser' data-it='$i'></td>    
                            <td><input type='button' value='Eliminar Item' class='delitem' data-fact='".$factura->getId()."' data-of='".$value->getId()."'></td>                                

                        </tr>";
                $i++;
          }
          $tabla.="</tbody>
                   </table>
                   <div id='detale'>
                   </div>";
          $script="
                    <script>
                              $('.detser').button().click(function(){
                                                              $('#detale').html('');
                                                              $.post('/modelo/informes/cria/factvta.php',
                                                                     {accion: 'detale', ords: $(this).data('ords'), it: $(this).data('it')},
                                                                     function(result){

                                                                                      $('#detale').append(result);
                                                                      });
                                });

                              $('.delitem').button().click(function(){
                                                              var btn = $(this);
                                                              if (confirm('Seguro eliminar el item?')){
                                                                $.post('/modelo/informes/cria/factvta.php',
                                                                       {accion: 'delItem', fv: btn.data('fact'), of: btn.data('of')},
                                                                       function(result){
                                                                                      var data = $.parseJSON(result);
                                                                                      if (data.status){
                                                                                        window.cambio = true;
                                                                                        $('#detaItem').dialog('close');
                                                                                      }
                                                                                      else
                                                                                        alert(data.msge);
                                                                        });
                                                              }
                                });                                
                    </script>
          ";
          print $tabla.$script;
}
elseif($accion == 'delItem'){
  try{
         $factura = find('FacturaVenta', $_POST['fv']);         
         $ordenFacturada = find('OrdenFacturada', $_POST['of']);
         $factura->removeOrdenesFacturada($ordenFacturada);
         $entityManager->remove($ordenFacturada);
         $entityManager->flush();
         print json_encode(array('status'=> true));
    }catch(Exception $e){
                          print json_encode(array('status'=> false, 'msge' => $e->getMessage()));
                        }     
}
elseif($accion == 'detale'){
          $sql = "SELECT o.nombre, date_format(fservicio, '%d/%m/%Y') as fecha, date_format(o.hsalida, '%H:%i') as salida,
                         concat(ch1.apellido, ', ',ch1.nombre) as conductor, interno, tu.tipo, cantpax, o.id, if (o.id_servicio is null, o.nombre, cr.nombre) as crono,
                         ts.tipo as tipoS
                  FROM ordenes o
                  left join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                  LEFT JOIN tiposervicio ts ON ts.id = s.id_TipoServicio AND ts.id_estructura = s.id_estructura_TipoServicio
                  left join cronogramas cr on cr.id = s.id_cronograma and cr.id_estructura = s.id_estructura_cronograma                  
                  LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
                  LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
                  LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                  LEFT JOIN unidades u ON (u.id = o.id_micro)
                  LEFT JOIN tipounidad tu ON (tu.id = u.id_tipounidad) and (tu.id_estructura = u.id_estructura_tipounidad)
                  WHERE o.id in ($_POST[ords])
                  ORDER BY fservicio, o.nombre, o.hsalida";    
        //  die($sql);
          $result = ejecutarSQL($sql);
          $tabla = "<br>
                    <span><h5>Detalle item $_POST[it]</h5></span>
                    <table class='table table-zebra'>
                          <thead>
                              <tr>
                                  <th>Nro</th>
                                  <th>Orden</th>
                                  <th>Nombre Servicio</th>           
                                  <th>Tipo Servicio</th>                                                              
                                  <th>Fecha</th>
                                  <th>H. Salida</th>
                                  <th>Conductor</th>
                                  <th>Interno</th>
                                  <th>Pax</th>
                                  <th>Orden</th>
                                  <th></th>
                              </tr>
                          </thead>
                          <tbody>";
          $row = mysql_fetch_array($result);
          $orden = 1;
          while ($row){
            $date = $row['fecha'];
            $i = 1;
            while (($row) &&($date == $row['fecha']))
            {
                $tabla.="<tr>
                            <td>$i</td>
                            <td>$row[id]</td>
                            <td>$row[crono]</td>      
                            <td>$row[tipoS]</td>      
                            <td>$row[1]</td>
                            <td>$row[2]</td>
                            <td>$row[3]</td>
                            <td>$row[4]</td>
                            <td>$row[6]</td>      
                            <td>$orden</td>
                            <td><input type='checkbox' class='sn' data-id='$row[id]'></td>
                        </tr>";
                $i++;
                $row = mysql_fetch_array($result);
                $orden++;
            }
          }

          $tabla.="</tbody>
                  </table>";  
          print $tabla;
}
elseif($accion == 'loadascr'){
  try{
         $cliente = find('Cliente', $_POST['clientes']);       
         $q = $entityManager->createQuery("SELECT fc FROM FacturacionCliente fc WHERE fc.cliente = :cliente");  
         $q->setParameter('cliente', $cliente);       
         $facturacion = $q->getOneOrNullResult(); 

         $c = $entityManager->createQuery("SELECT c FROM Cronograma c WHERE c.cliente = :cliente AND c.activo = :activo ORDER BY c.nombre");  
         $c->setParameter('cliente', $cliente);       
         $c->setParameter('activo', true);          
         $cronogramas = $c->getResult(); 

         $tabla = "<table class='table table-zebra'>
                      <thead>
                          <tr>
                              <th>Cronograma</th>
                              <th>Origen</th>
                              <th>Destino</th>           
                              <th>Tarifa Aplicada</th>                                                              
                              <th>Articulo Facturacion</th>
                          </tr>
                      </thead>
                      <tbody>";
          foreach ($cronogramas as $crono) {
              $tabla.="<tr>
                          <td>$crono</td>
                          <td>".$crono->getOrigen()."</td>
                          <td>".$crono->getDestino()."</td>
                          ";
              $tarifa = $facturacion->existeCronograma($crono);
              if ($tarifa['existe']){
                  $tabla.="<td>$tarifa[tarifa]</td>
                           <td>".$tarifa['tarifa']->getArticulosAsignados()."</td>
                           </tr>";
              }
              else{
                  $tabla.="<td></td>
                           <td></td>
                           </tr>";                
              }

          }


          print $tabla;

    }
    catch(Exception $e){
                          print json_encode(array('status'=> false, 'msge' => $e->getMessage()));
                        }     
}

?>

