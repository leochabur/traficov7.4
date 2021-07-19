<?php
     set_time_limit(0);
//error_reporting(E_ALL); 
//ini_set("display_errors", 1); 
     session_start();
include ('../../../modelsORM/controller.php');     
include_once ('../../../modelsORM/call.php');     
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include_once ('../../../controlador/ejecutar_sql.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];



  if ($accion == 'ldcli'){
     $conn = conexcion();

     $sql = "SELECT upper(razon_social) as nombre,  id
             FROM clientes c
             where id_estructura = $_POST[str]
             order by razon_social";
     $result = mysql_query($sql, $conn);

     $tabla= '<select id="clientes" name="clientes" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
              <option value="0">Todos</option>';
     while ($data = mysql_fetch_array($result)){
           $tabla.="<option value='$data[id]'>".htmlentities($data[0])."</option>";
     }
     $tabla.="
               <script type='text/javascript'>
                                $('#clientes').selectmenu({width: 350});
               </script>";
     mysql_free_result($result);
     mysql_close($conn);
     print $tabla;
  }
  elseif($accion == 'reskm'){
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $struct = '';
     $cliente=(($_POST['cli'])? "and cli.id = $_POST[cli]": "");
     $conn = conexcion();

     $sql = "select c.id as crono, id_tipounidad as tipo, sum(if(precio_peaje is null,0,precio_peaje)) as precio
            from peajesporcronogramas pxc
            inner join (select * from cronogramas where id_cliente = $_POST[cli] and id_estructura_cliente = $_SESSION[structure]) c on c.id = pxc.id_cronograma and c.id_estructura = pxc.id_estructura_cronograma
            inner join estacionespeaje ep on ep.id = pxc.id_estacion_peaje
            inner join preciopeajeunidad ppu on ppu.id_estacionpeaje = ep.id
            group by c.id, id_tipounidad";
      $result = ejecutarSQL($sql, $conn);
      $peajes = array();

      while ($row = mysql_fetch_array($result)){
        if (!isset($peajes[$row['crono']]))
          $peajes[$row['crono']] = array();
        $peajes[$row['crono']][$row['tipo']] = $row['precio'];
      }


  //    print_r($peajes);

      $sql="select fservicio, c.nombre, 1 as cant, c.id as crono, dayofweek(fservicio) as dia, tu.id as tipo, o.id as orden
            from ordenes o
            inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
            inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
            left join unidades u on u.id = o.id_micro
            left join tipounidad tu on tu.id = u.id_tipounidad and tu.id_estructura = u.id_estructura_tipounidad
            where o.id_cliente = $_POST[cli] and fservicio between '$desde' and '$hasta' and not borrada and not suspendida
            order by dayofweek(fservicio), c.id, tu.id";
      //die($sql);
        $facturacion = facturacionCliente($_POST['cli']); ////busca la facturacion del cliente
      if (!$facturacion)
          die('no encuentra '.$_POST['cli']);
      //  else
        //  die($facturacion->getCliente());
           $conn = conexcion();

           $tarifas = $facturacion->getTarifas();  ////obtiene todas las tarifas cargadas

           $result = mysql_query($sql, $conn) or die(mysql_error($conn));
           $dias = array();
           $tipos = array();
           $cronos = array();
           $factura = array();
           while ($value = mysql_fetch_array($result)) {

                if (!isset($dias[$value['dia']])){////sino busco ya el dia de la semana lo busca y lo almacena para evitar buscarlo en el futuro
                  $dias[$value['dia']] = diaSemana($value['dia']);
                }

                if (!isset($tipos[$value['tipo']])){ ////idem tipo vehiculo
                  if ($value['tipo'])
                    $tipos[$value['tipo']] = tipoVehiculo($_SESSION['structure'], $value['tipo']);
                }
                if (!isset($cronos[$value['crono']])){   ////idem cronogramas
                  $cronos[$value['crono']] = cronograma($_SESSION['structure'], $value['crono']);
                }

                foreach ($tarifas as $tarifa) {  /// recorre todas las tarifas que tiene cargadas 
                          try{
                                  if (($tarifa->getDiasSemana()->contains($dias[$value['dia']])) && ($tarifa->getCronogramas()->contains($cronos[$value['crono']]))){
                                        if (!isset($tipos[$value['tipo']])){ ////para el caso que no haya interno asignado o el interno no tenga un tip de vehiculo
                                          if ($value['tipo'])
                                            $tipos[$value['tipo']] = find('TipoUnidad', $value['tipo']);
                                          else{
                                              if (!isset($factura['TARIFA_SIN_INTERNO'])){
                                                $factura['TARIFA_SIN_INTERNO'] = array('art'=> 'Sin coche asignado', 'cant' => $value['cant'], 'ords' => array());
                                              }
                                              else{
                                                $factura['TARIFA_SIN_INTERNO']['cant']+= $value['cant'];
                                              }
                                              $factura['TARIFA_SIN_INTERNO']['ords'][] = $value['orden'];
                                          }

                                        }

                                        if ($value['tipo']){  ////tiene tipo de vehiculo asignado
                                            $tfa = $tarifa->getTarifaTipoVehiculo($tipos[$value['tipo']]);

                                            if ($tfa){ ///existe una tarifa para el tipo de vehiculo
                                                if ($tfa->getArticulo()!= null){
                                                  if (isset($factura[$tfa->getArticulo()->getId()])){
                                                    $factura[$tfa->getArticulo()->getId()]['cant']+=$value['cant'];
                                                    $factura[$tfa->getArticulo()->getId()]['peaje']+=$peajes[$value['crono']][$value['tipo']];
                                                  }
                                                  else{
                                                    $factura[$tfa->getArticulo()->getId()] = array('art'=> $tfa, 'cant' => $value['cant'], 'tfa'=>$tfa, 'ords' => array(), 'peaje'=> $peajes[$value['crono']][$value['tipo']]);
                                                  }
                                                  $factura[$tfa->getArticulo()->getId()]['ords'][] = $value['orden'];
                                                }
                                                else{
                                                  if (isset($factura[$tarifa->getNombre()])){
                                                    $factura[$tarifa->getNombre()]['cant']+=$value['cant'];
                                                    $factura[$tarifa->getNombre()]['peaje']+=$peajes[$value['crono']][$value['tipo']];
                                                  }
                                                  else{
                                                    $factura[$tarifa->getNombre()] = array('art'=> $tarifa->getNombre(), 'cant' => $value['cant'], 'tfa'=>$tfa, 'peaje'=>$peajes[$value['crono']][$value['tipo']]);
                                                  }                                          

                                                }
                                            }
                                        }
                                  }
                                  else{
                                           // die("fsafasf  $tarifa   $value[crono]   -   $value[nombre]   -  ".$dias[$value['dia']]."   -   ".$value['dia']);
                                  }
                              }catch (Exception $e){ die($e);}
                }
           }


           $tabla = "<table class='table table-zebra'>
                      <thead>
                        <tr>
                            <th>Tarifa</th>
                            <th>Articulo/Detalle</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total</th>
                            <th>Peajes</th>
                            <th>Ver</th>
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
                        <td align='right'>$ ".(isset($value['tfa'])?$value['tfa']->getImporte():0)."</td>
                        <td align='right'>$ ".number_format(($value['cant']*(isset($value['tfa'])?$value['tfa']->getImporte():'0')),2,',','.')."</td>
                        <td align='right'>$ ".number_format($value['peaje'],2,',','.')."</td>                        
                        <td><a href='#' class='viewfac' data-id='".implode(',', $value['ords'])."'>Ver</a></td>                  
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
                                                                                width:400,
                                                                                height:350,
                                                                                modal:true,
                                                                                autoOpen: false
                                                                            });
                                                              dialog.load('/modelo/informes/cria/reccliint.php',
                                                                          {accion: 'detsrv', ords: $(this).data('id')},
                                                                          function (){ 
                                                                                       });
                                                              dialog.dialog('open');
                                                            });
                      </script>";
           print $tabla.$script;


  }
  elseif ($accion == 'detsrv') {
      try{
          $sql = "SELECT o.nombre, date_format(fservicio, '%d/%m/%Y') as fecha, date_format(hsalida, '%H:%i') as salida,
                         concat(ch1.apellido, ', ',ch1.nombre) as conductor, interno, tipo, cantpax, o.id
                  FROM ordenes o
                  LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
                  LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
                  LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                  LEFT JOIN unidades u ON (u.id = o.id_micro)
                  LEFT JOIN tipounidad tu ON (tu.id = u.id_tipounidad) and (tu.id_estructura = u.id_estructura_tipounidad)
                  WHERE o.id in ($_POST[ords])
                  ORDER BY fservicio, o.nombre";    
          $result = ejecutarSQL($sql);
          $tabla = "<table class='table table-zebra'>
                          <thead>
                              <tr>
                                  <th>Orden</th>
                                  <th>Servicio</th>
                                  <th>Fecha</th>
                                  <th>H. Salida</th>
                                  <th>Conductor</th>
                                  <th>Interno</th>
                                  <th>Tipo Unidad</th>
                                  <th>Pax</th>
								  <th></th>
                              </tr>
                          </thead>
                          <tbody>";
          while ($row = mysql_fetch_array($result)){
            $tabla.="<tr>
                        <td>$row[id]</td>
                        <td>$row[0]</td>
                        <td>$row[1]</td>
                        <td>$row[2]</td>
                        <td>$row[3]</td>
                        <td>$row[4]</td>
                        <td>$row[5]</td>
                        <td>$row[6]</td>      
						<td><input type='checkbox'></td>
                    </tr>";
          }

          $tabla.="</tbody>
                  </table>";
          print $tabla;
        }catch (Exception $e){print $sql;}


  }
  elseif ($accion == 'detsrv') {

      // die (cronogramasPendientes());

        $facturacion = facturacionCliente($_POST['clientes']); ////busca la facturacion del cliente
        if (!$facturacion)
            die('no encuentra '.$_POST['clientes']);

        $options = "<option value=''></option>";
        foreach ($facturacion->getTarifas() as $tarifa) {
          $options.="<option value='".$tarifa->getId()."'>".$tarifa->getNombre()."</option>";
        }

        $tabla = "<table class='table table-zebra' id='cras'>
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Asignado a tarifa...</th>
                            <th>Cambiar a...</th>
                        </tr>
                    </thead>
                    <tbody>";

        foreach ($facturacion->getTarifas() as $tarifa) {
          foreach ($tarifa->getCronogramas() as $crono) {
            $tabla.="<tr>
                      <td>$crono</td>
                      <td>".$crono->getOrigen()."</td>
                      <td>".$crono->getDestino()."</td>
                      <td>".$tarifa->getNombre()."</td>
                      <td><select>$options</select><input type='button' value='Asignar'data-crono='".$crono->getId()."' data-tarifa='".$tarifa->getId()."'/></td>
                    </tr>";
          }
        }
        $tabla.="</tbody>
                </table>
                <script>
                          $('#cras select').selectmenu({width: 250});
                          $('#cras :button').button().click(function(){
                                                                        var bt = $(this);
                                                                        var cr = bt.data('crono');
                                                                        var tf = bt.data('tarifa');
                                                                        var nwtf = bt.siblings().val();
                                                                        $.post('/modelo/informes/cria/reccliint.php',
                                                                              {accion: 'reasig', old: tf, new: nwtf, crono: cr},
                                                                              function(data){
                                                                                            alert(data);
                                                                              });
                                                                      });
                </script>";
        print $tabla;
  }
  elseif($accion == 'saisg'){
        $facturacion = facturacionCliente($_POST['clientes']); ////busca la facturacion del cliente
        if (!$facturacion)
            die('no encuentra '.$_POST['clientes']);

        $sql = "select c.id, c.nombre, o.ciudad as ori, d.ciudad as des
                from cronogramas c
                inner join ciudades o on o.id = c.ciudades_id_origen
                inner join ciudades d on d.id = c.ciudades_id_destino
                where c.activo and c.id_cliente = $_POST[clientes] and c.id not in (select id_cronograma from fact_cronogramas_por_tarifa)
                order by nombre";

         $conn = conexcion();
         $result = mysql_query($sql, $conn); 


        $options = "<option value=''></option>";
        foreach ($facturacion->getTarifas() as $tarifa) {
          $options.="<option value='".$tarifa->getId()."'>".$tarifa->getNombre()."</option>";
        }

        $tabla = "<table class='table table-zebra' id='crnoas'>
                    <thead>
                        <tr>
                            <th>Servicio</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Asignar a...</th>
                        </tr>
                    </thead>
                    <tbody>";

        while ($row = mysql_fetch_array($result)) {
            $tabla.="<tr>
                      <td>$row[nombre]</td>
                      <td>$row[ori]</td>
                      <td>$row[des]</td>
                      <td><select>$options</select><input type='button' value='Asignar' data-crono='$row[id]'/></td>
                    </tr>";
        }
        $tabla.="</tbody>
                </table>
                <script>
                          $('#crnoas select').selectmenu({width: 250});
                          $('#crnoas :button').button().click(function(){
                                                                        var bt = $(this);
                                                                        var cr = bt.data('crono');
                                                                        var nwtf = bt.siblings().val();
                                                                        $.post('/modelo/informes/cria/reccliint.php',
                                                                              {accion: 'asignew', new: nwtf, crono: cr},
                                                                              function(data){
                                                                                            alert(data);
                                                                              });
                                                                      });
                </script>";
        print $tabla;        




  }
  elseif($accion == 'asignew'){
      $new = find('TarifaServicio', $_POST['new']);
      $crono = find('Cronograma', $_POST['crono']);
      $new->addCronograma($crono);
      $entityManager->flush();
  }  
  elseif($accion == 'reasig'){
      $old = find('TarifaServicio', $_POST['old']);
      $new = find('TarifaServicio', $_POST['new']);
      $crono = find('Cronograma', $_POST['crono']);

      $old->removeCronograma($crono);
      $new->addCronograma($crono);
      $entityManager->flush();
  }
  
?>

