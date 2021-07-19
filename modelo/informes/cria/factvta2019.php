<?php

//ini_set("display_errors", 1); 
     session_start();
     set_time_limit(0);
error_reporting(0);      
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
      $cliente = find('Cliente', $_POST['clientes']);
      $factura = getFacturaVenta($cliente);
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
        $entityManager->persist($factura);        
        $entityManager->flush();
        $fact = $factura->getId();
        $status = false;        
      }
      print json_encode(array('ok' => $status, 'fact' => $fact));
    }
    catch (Exception $e){die($e->getMessage());}
  }  
  elseif($accion == 'listfact'){
      $facturas = getFacturasPendientes();
      $table = '<table border="0" align="center" width="75%" name="tabla" class="table table-zebra">
                    <thead>
                      <tr>
                          <th>Cliente</th>
                          <th>Desde</th>
                          <th>Hasta</th>
                          <th>Importe</th>
                          <th></th>
                      </tr>
                    </thead>
                    <tbody>';
      foreach ($facturas as $factura) {
          $table.='<tr>
                      <td>'.$factura->getCliente().'</td>
                      <td>'.$factura->getDesde()->format('d/m/Y').'</td>
                      <td>'.$factura->getHasta()->format('d/m/Y').'</td>
                      <td>$ '.number_format($factura->getMontoFactura(),2).'</td>
                      <td><a href="/vista/informes/cria/addordfac.php?fv='.$factura->getId().'">>></a></td>
                   </tr>';
      }
      $table.='</tbody>
               </table>';
      print $table;
  }
  elseif($accion == 'addsrv'){
     $desde = dateToMysql($_POST['fecha'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $cliente=$_POST['cliente'];

     $facturaVenta = find('FacturaVenta',$_POST['factura']);

     $idsOrdenes = getAllIdsOrdenesFactura($facturaVenta);

     $ids = "";
     foreach ($idsOrdenes as $id) {
       $ids.="$id[id],";
     }
     $ids.="0";

      $sql="select fservicio, c.nombre, 1 as cant, if(c.id is null, 0, c.id) as crono, dayofweek(fservicio) as dia, if(s.id_TipoServicio = 20, 0, tu.id) as tipo, o.id as orden, 
                    hour(TIMEDIFF(o.hfinservicio, o.hcitacion)) as horas, minute(TIMEDIFF( o.hfinservicio, o.hcitacion)) as minutos
            from ordenes o
            left join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
            left join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
            left join unidades u on u.id = o.id_micro
            left join tipounidad tu on tu.id = u.id_tipounidad and tu.id_estructura = u.id_estructura_tipounidad
            where o.id_cliente = $cliente and fservicio between '$desde' and '$hasta' and not borrada and not suspendida and o.id not in ($ids)
            order by dayofweek(fservicio), c.id, tu.id";
     // die($sql);

      $facturacion = facturacionCliente($cliente); ////busca la facturacion del cliente
      if (!$facturacion){
        die('no encuentra '.$_POST['cli']);
      }

           $conn = conexcion();

           $tarifas = $facturacion->getTarifas();  ////obtiene todas las tarifas cargadas

           $result = mysql_query($sql, $conn) or die(mysql_error($conn)."  ".$sql);
           $dias = array();
           $tipos = array();
           $cronos = array();
           $factura = array();
           $tt = array();
           $i=0;
           while ($value = mysql_fetch_array($result)) {

               try{
                      if (!isset($dias[$value['dia']])){////sino busco ya el dia de la semana lo busca y lo almacena para evitar buscarlo en el futuro
                        $dias[$value['dia']] = diaSemana($value['dia']);
                      }

                      if (!isset($tipos[$value['tipo']])){ ////idem tipo vehiculo
                        if ($value['tipo'])
                          $tipos[$value['tipo']] = tipoVehiculo($_SESSION['structure'], $value['tipo']);
                      }
                      if (!isset($cronos[$value['crono']])){   ////idem cronogramas
                        if ($value['crono'])
                          $cronos[$value['crono']] = cronograma($_SESSION['structure'], $value['crono']);
                      }
                      
                      $existe = false;
                      foreach ($tarifas as $tarifa) 
                      {  /// recorre todas las tarifas que tiene cargadas     

                                  if (($tarifa->existeDia($dias[$value['dia']])) && ($tarifa->existeCronograma($cronos[$value['crono']])))
                                  {
                                        if (!isset($tipos[$value['tipo']])){ ////para el caso que no haya interno asignado o el interno no tenga un tip de vehiculo
                                          if ($value['tipo']){
                                            $tipos[$value['tipo']] = find('TipoUnidad', $value['tipo']);
                                          }
                                          else{
                                              if (!isset($factura['TARIFA_SIN_INTERNO'])){
                                                $factura['TARIFA_SIN_INTERNO'] = array('art'=> 'Sin coche asignado', 'cant' => $value['cant'], 'ords' => array());
                                              }
                                              else{
                                                $factura['TARIFA_SIN_INTERNO']['cant']+= $value['cant'];
                                              }
                                              $factura['TARIFA_SIN_INTERNO']['ords'][] = $value['orden'];
                                          }
                                          $existe = true;
                                        }

                                        if ($value['tipo']){  ////la orden tiene tipo de vehiculo asignado
                                            $tfa = $tarifa->getTarifaTipoVehiculo($tipos[$value['tipo']]);

                                            if ($tfa){ ///existe una tarifa para el tipo de vehiculo
                                                $cant = $value['cant'];
                                              /*  if ($tarifa->getCalculaXHora())
                                                {
                                                  $cant = $value['horas'];
                                                  if ($value['minutos'] > 30){
                                                    $cant++;
                                                  }
                                                }*/
                                                if ($tfa->getArticulo()!= null){
                                                  if (isset($factura[$tfa->getArticulo()->getId()])){
                                                    $factura[$tfa->getArticulo()->getId()]['cant']+=$cant;//value['cant'];
                                                    $factura[$tfa->getArticulo()->getId()]['ords'][] = $value['orden'];
                                                  }
                                                  else{
                                                    $factura[$tfa->getArticulo()->getId()] = array('art'=> $tfa, 'cant' => $cant, 'tfa'=>$tfa, 'ords' => array(0=>$value['orden']), 'peaje'=> $peajes[$value['crono']][$value['tipo']]);
                                                  }
                                                  
                                                }
                                                else{
                                                  if (isset($factura[$tarifa->getNombre()])){
                                                    $factura[$tarifa->getNombre()]['cant']+=$cant;
                                                  }
                                                  else{
                                                    $factura[$tarifa->getNombre()] = array('art'=> $tarifa->getNombre(), 'cant' => $cant, 'tfa'=>$tfa, 'peaje'=>$peajes[$value['crono']][$value['tipo']]);
                                                  }                                          

                                                }
                                                $existe = true;
                                            }
                                        }
                                  }                              
                      }
                      if (!$existe){ ///flag para indicar que no existe la tarifa para la orden
                          if (!isset($factura['_SIN ASIGNAR'])){
                              $factura['_SIN ASIGNAR'] = array('art'=> 'S/A', 'cant' => $value['cant'], 'peaje'=>0, 'ords' => array(0=>$value['orden']));
                          }
                          else{
                              $factura['_SIN ASIGNAR']['cant']+=$value['cant'];
                              $factura['_SIN ASIGNAR']['ords'][] = $value['orden'];
                          }                          
                      }                  
                }catch (Exception $e){ die("error ".$e->getMessage());}
           }

        //   die(print_r($tt));

           $tabla = "<table class='table table-zebra'>
                      <thead>
                        <tr>
                            <th>Tarifa</th>
                            <th>Articulo/Detalle</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>";
           ksort($factura);
           foreach ($factura as $key => $value) {
         //   die ($value['tfa']);
            $tabla.="<tr>
                        <td>".(isset($value['tfa'])?$value['tfa']->getTarifaServicio():'SIN ASIGNAR')."</td>
                        <td>$value[art]</td>
                        <td>$value[cant]</td>
                        <td align='right'>$ ".(isset($value['tfa'])?round($value['tfa']->getArticulo()->getImporte(),2):0)."</td>
                        <td align='right'>$ ".number_format(($value['cant']*(isset($value['tfa'])?$value['tfa']->getArticulo()->getImporte():'0')),2,',','.')."</td>                
                        <td><a href='#' class='viewfac' data-id='".implode(',', $value['ords'])."'>Facturar Manual</a>
                        <a href='#' class='factall' data-art='".$key."' data-fact='".$_POST['factura']."' data-id='".implode(',', $value['ords'])."'>Facturar Todo</a></td>                                             
                     </tr>";
              //$tabla.="$key -> articulo: $value[art]   _   cantidad: $value[cant]<br>";
           }
           $tabla.="</tbody>
                    </table>";

           $script = "<script>
                      $('.viewfac').button().click(function(event){
                                                              event.preventDefault();
                                                              $('#detap').remove();
                                                              var dialog = $('<div id=\"detap\"></div>').appendTo('body');
                                                              dialog.dialog({
                                                                                title: 'Detalle servicios',
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
                                                                              {accion: 'facall', ords: $(this).data('id'), fact: $(this).data('fact'), art: $(this).data('art')},
                                                                              function (data){ 
                                                                                              var response = $.parseJSON(data);

                                                                                              if (response.ok){
                                                                                                window.location.href='/vista/informes/cria/addordfac.php?fv=".$facturaVenta->getId()."&fec=$_POST[fecha]&has=$_POST[hasta]';
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

          foreach ($ordenes as $orden) {
            $ordeneFacturada->addOrdene($orden);
          }
          $factura->addOrdenesFacturada($ordeneFacturada);
          $entityManager->persist($ordeneFacturada);
          $entityManager->flush();
          print json_encode(array('ok' => true ));
    }catch(Exception $e){
                            print json_encode(array('ok' => false ));
                        }
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
          $options = "";
          $articulos = articulosCliente($factura->getCliente());
          foreach ($articulos as $art) {
            $options.="<option value='".$art->getId()."' data-mto='".$art->getImporte()."'>$art</option>";
          }
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
                        <td><input type='checkbox' class='sn' data-id='$row[id]'></td>
                    </tr>";
          }

          $tabla.="</tbody>
                  </table>";
          $tabla.='<fieldset class="ui-widget ui-widget-content ui-corner-all">
                     <legend class="ui-widget ui-widget-header ui-corner-all">Detalle Item</legend>
                         <table border="0" align="center" width="75%" name="tabla" class="table table-zebra">
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
                                        <select>
                                              <option value="0"></option>
                                              '.$options.'
                                        </select>
                                  </td>
                                  <td><input type="text" id="cant" readonly/></td>                                  
                                  <td><input type="text" id="unit"/></td>
                                  <td><input type="text" id="tot" readonly/></td>      
                                  <td><input type="button" class="fact" data-fact="'.$factura->getId().'" value="Facturar"/></td>
                              </tr>
                            </tbody>
                        </table>
                    </fieldset>';
        $script = "<script>
                          $('select').change(function(data){
                                                        $('#unit').val($(this).find(':selected').data('mto'));
                          });
                          $('.sn').click(function(){
                              var cant = $('input:checkbox:checked').size();
                              $('#cant').val(cant);
                              var total = cant* $('select').find(':selected').data('mto');
                              $('#tot').val(total);
                          });
                          $('.fact').button().click(function(){
                                                                var fac = $(this).data('fact');
                                                                var ids = new Array();
                                                                var art = $('select').val();
                                                                $('input[type=checkbox]').each(function(){
                                                                      if($(this).is(':checked')){
                                                                        ids.push($(this).data('id'));
                                                                      }                                    
                                                                });
                                                                $.post('/modelo/informes/cria/factvta.php',
                                                                        {accion: 'factm', ords: ids.join(), fv: fac, ar: art, unit: $('#unit').val()},
                                                                        function(data){
                                                                                        var response = $.parseJSON(data);
                                                                                        if (response.ok){
                                                                                            window.location.href='/vista/informes/cria/addordfac.php?fv=".$factura->getId()."&fec=$_POST[fec]&has=$_POST[hasta]';
                                                                                         }
                                                                        });
                          });
                          


                   </script>";
          print $tabla.$script;
        }catch (Exception $e){print $sql;}
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
?>

