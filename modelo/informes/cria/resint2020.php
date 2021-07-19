<?php

//ini_set("display_errors", 1); 
     session_start();
     set_time_limit(0);
//error_reporting(E_ALL);      
include ('../../../modelsORM/controller.php');     
include_once ('../../../modelsORM/call.php');     
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include_once ('../../../controlador/ejecutar_sql.php');
  include ('../../../modelo/utils/dateutils.php');




if ($_POST['accion'] == 'resint') {

    $desde = DateTime::createFromFormat('d/m/Y', $_POST['desde']);
    $hasta = DateTime::createFromFormat('d/m/Y', $_POST['hasta']);

    try{
        $resumen = getResumenVentaPorInterno($desde, $hasta, 0);
    }catch (Exception $e){ die($e->getMessage());}

    if (isset($_POST['res']))
    {
        $tabla = "<table class='table table-zebra' id='cras'>
                        <thead>
                            <tr>
                                <th>Interno</th>
                                <th>Dominio</th>
                                <th>Año</th>
                                <th>Titular</th>
                                <th>Tipo Vehiculo</th>
                                <th>Cantidad Clientes</th>
                                <th>Cantidad Servicios</th>
                                <th>$ Total</th>
                                <th>Km Servicio</th>
                                <th>Km Vacio</th>
                                <th>Hs</th>
                                <th>Peajes</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>";

        $detalle = array();
        $total = $cantSrv = $km = 0;
        $totHs = 0;
        $totMin = 0;
        foreach ($resumen as $res){
               
                $citacion = DateTime::createFromFormat('Y-m-d H:i:s', $res['fecha']->format('Y-m-d')." ".$res['citacion']->format('H:i:s'));
                $fin = DateTime::createFromFormat('Y-m-d H:i:s', $res['fecha']->format('Y-m-d')." ".$res['fin']->format('H:i:s'));

                if ($fin < $citacion)
                {
                    $fin->add(new DateInterval('P1D'));
                }
                $interval = $citacion->diff($fin);
                


                $total+= $res['unitario'];
                $cantSrv+=$res['cant'];
                $dominio = $res['dominio'];
                $km+= $res['km'];
                if ($res['nuevoDominio'])
                  $dominio = $res['nuevoDominio'];
                if (!array_key_exists($res['interno'], $detalle))
                {
                   // $detalle[$res['interno']] = array();
             /*   }

                if (!array_key_exists($res['razonSocial'], $detalle[$res['interno']]))
                {*/
                  $detalle[$res['interno']] = array('clis' => array(), 'tipo' => $res['tipo'], 'anio' => $res['anio'], 'titular' => $res['titular'], 'dominio' => $dominio, 'km' => 0, 'pesos' => 0, 'cant' => 0, 'hs' => 0, 'min' => 0, 'ordenes' => array());
                }
                $detalle[$res['interno']]['clis'][]=$res['cli'];
                $detalle[$res['interno']]['km']+=$res['km'];
                $detalle[$res['interno']]['pesos']+=$res['unitario'];
                $detalle[$res['interno']]['cant']+= $res['cant'];
                $detalle[$res['interno']]['hs']+=$interval->format('%h');
                $detalle[$res['interno']]['min']+= $interval->format('%i');
                $detalle[$res['interno']]['ordenes'][]= $res['orden'];

        }

        //foreach ($detalle as $clave => $valor) {
            $conn = conexcion();
            $totKmVac = $totPeajes = 0;
            foreach ($detalle as $k => $v) {
                $ordenes = implode(',', $v['ordenes']);

                $sql = "select sum(km) as km
                        from ordenes o
                        inner join ordenesAsocVacios oav on o.id = oav.id_orden_vacio AND o.id_estructura = oav.id_estructura_orden_vacio
                        WHERE  id_orden in ($ordenes) AND id_estructura_orden = $_POST[str] AND not suspendida and not borrada";
                $result = mysql_query($sql, $conn) or die($sql);
                $kmVac = "";
                if ($row = mysql_fetch_array($result))
                {
                    $kmVac = $row['km'];
                    $totKmVac+=$row['km'];
                }

                $sqlPeajes = "select sum(precio_peaje) as precio
                            from ordenes o
                            inner JOIN unidades m ON (m.id = o.id_micro)
                            inner join tipounidad tu on tu.id = m.id_tipounidad
                            inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                            inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                            inner join peajesporcronogramas pxc on pxc.id_cronograma = c.id and pxc.id_estructura_cronograma = c.id_estructura
                            inner join estacionespeaje ep on ep.id = pxc.id_estacion_peaje
                            inner join preciopeajeunidad ppu on ppu.id_estacionpeaje = ep.id and ppu.id_tipounidad = tu.id AND ppu.id_estructura_tipounidad = tu.id_estructura
                            where o.id in ($ordenes)";
                $result = mysql_query($sqlPeajes, $conn) or die($sqlPeajes);
                $peajes = "";
                if ($row = mysql_fetch_array($result))
                {
                    $peajes = $row['precio'];
                    $totPeajes+=$row['precio'];
                }

                $hs = $v['hs'] + intdiv($v['min'],60);    
                $min = ($v['min'] % 60);
                $time = ($hs < 10?"0$hs":$hs).":".($min < 10?"0$min":$min);
                $tabla.="<tr>
                            <td>".$k."</td>
                            <td>".$v['dominio']."</td>
                            <td>".$v['anio']."</td>
                            <td>".$v['titular']."</td>
                            <td>".$v['tipo']."</td>
                            <td align='right'>".count(array_unique($v['clis']))."</td>
                            <td align='right'>".$v['cant']."</td>
                            <td align='right'>$ ".number_format($v['pesos'],2,',','.')."</td>
                            <td align='right'>".$v['km']."</td>
                            <td align='right'>".$kmVac."</td>
                            <td align='right'>$time</td>
                            <td align='right'>$ ".number_format($peajes,2,',','.')."</td>
                            <td>
                              <a href='#' class='view' data-int='$k' data-ords='$ordenes'><i class='fas fa-arrow-circle-right fa-2x'></i></a>
                            </td>
                          </tr>";
            }
      //  }
            $tabla.="<tr>
                        <td colspan='6'>TOTAL</td>
                        <td align='right'>$cantSrv</td>
                        <td align='right'>$ ".number_format($total,2,',','.')."</td>
                        <td align='right'>$km</td>
                        <td align='right'>$totKmVac</td>
                        <td align='right'></td>
                        <td align='right'>$ ".number_format($totPeajes,2,',','.')."</td>
                        <td align='right'></td>
                    </tr>
                    </tbody>
                    </table>
                    <div id='dialog-form'>
                      <div id='bro'>
                      </div>

                    </div>
                    <script>
                              $('.view').click(function(){
                                                          var bt = $(this);

                                                          $('#detap').remove();
                                                          var dialog = $('<div id=\"detap\"></div>').appendTo('body');
                                                          dialog.dialog({
                                                                                    title: 'Interno N# '+bt.data('int'),
                                                                                    width:900,
                                                                                    height:400,
                                                                                    modal:true,
                                                                                    autoOpen: false
                                                                        });
                                                          dialog.load('/modelo/informes/cria/resint.php',
                                                                      {accion: 'detalle',  ordenes: bt.data('ords')},
                                                                      function (){ 
                                                                      });
                                                          dialog.dialog('open');
                                                          });
                    </script>";
        print $tabla;
    }
    else
    {
        $conn = conexcion(true);
        $sql = "select oav.id_orden,  km
                from ordenesAsocVacios oav
                inner join ordenes o on o.id = oav.id_orden_vacio AND o.id_estructura = oav.id_estructura_orden_vacio
                WHERE  fservicio between '".$desde->format('Y-m-d')."' AND '".$hasta->format('Y-m-d')."' AND not suspendida and not borrada";
        $result = mysqli_query($conn, $sql);
        $vacios = array();
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $vacios[$row['id_orden']] = $row['km'];
        }

       // die(print_r($vacios));
        $sql = "select o.id as id_orden, sum(precio_peaje) as precio
                    from ordenes o
                    inner JOIN unidades m ON (m.id = o.id_micro)
                    inner join tipounidad tu on tu.id = m.id_tipounidad
                    inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                    inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                    inner join peajesporcronogramas pxc on pxc.id_cronograma = c.id and pxc.id_estructura_cronograma = c.id_estructura
                    inner join estacionespeaje ep on ep.id = pxc.id_estacion_peaje
                    inner join preciopeajeunidad ppu on ppu.id_estacionpeaje = ep.id and ppu.id_tipounidad = tu.id AND ppu.id_estructura_tipounidad = tu.id_estructura
                    where o.fservicio between '".$desde->format('Y-m-d')."' AND '".$hasta->format('Y-m-d')."'
                    GROUP BY o.id";
        $result = mysqli_query($conn, $sql);
        $peajes = array();
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $peajes[$row['id_orden']] = $row['precio'];
        }

        //die(print_r($peajes));
        $tabla = "<table class='table table-zebra' id='cras'>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Interno</th>
                                <th>Dominio</th>
                                <th>Año</th>
                                <th>Titular</th>
                                <th>Tipo Vehiculo</th>
                                <th>Cliente</th>
                                <th>$</th>
                                <th>Km Servicio</th>
                                <th>Km Vacio</th>
                                <th>H. Citacion</th>
                                <th>H. Fin</th>
                                <th>Hs</th>
                                <th>Peajes</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>";

        $detalle = array();
        foreach ($resumen as $res)
        {               
                $citacion = DateTime::createFromFormat('Y-m-d H:i:s', $res['fecha']->format('Y-m-d')." ".$res['citacion']->format('H:i:s'));
                $fin = DateTime::createFromFormat('Y-m-d H:i:s', $res['fecha']->format('Y-m-d')." ".$res['fin']->format('H:i:s'));
                if ($fin < $citacion)
                {
                    $fin->add(new DateInterval('P1D'));
                }
                $interval = $citacion->diff($fin);
                $hs = ($interval->format('%h') + intdiv($interval->format('%i') , 60));    
                $min = ($interval->format('%i') % 60);
                $time = ($hs < 10?"0$hs":$hs).":".($min < 10?"0$min":$min);

                $dominio = $res['dominio'];
                if ($res['nuevoDominio'])
                  $dominio = $res['nuevoDominio'];

                $peaje = $vacio = '';
                if (array_key_exists($res['orden'], $vacios))
                    $vacio = $vacios[$res['orden']];
                if (array_key_exists($res['orden'], $peajes))
                    $peaje = $peajes[$res['orden']];
                $tabla.= "<tr>
                            <td>".$citacion->format('d/m/Y')."</td>
                            <td>$res[interno]</td>
                            <td>$dominio</td>
                            <td>$res[anio]</td>
                            <td>$res[titular]</td>
                            <td>$res[tipo]</td>
                            <td>$res[razonSocial]</td>
                            <td>$res[unitario]</td>
                            <td>$res[km]</td>
                            <td>".$vacio."</td>
                            <td>".$citacion->format('H:i')."</td>
                            <td>".$fin->format('H:i')."</td>
                            <td>$time</td>
                            <td>".$peaje."</td>
                        </tr>";
        }
        $tabla.="</tbody>
                    </table>";
        print $tabla;
    }
  }
  elseif ($_POST['accion'] == 'clis') {
    print strtoupper(clientesOptions($_POST['str']));
  }
  elseif ($_POST['accion'] == 'detalle') {

    $ordenes = getOrdenes(explode(',',$_POST['ordenes']));
    $tabla = "<table class='table table-zebra'>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Cliente</th>
                            <th>Servicio</th>
                            <th>Citacion</th>
                            <th>Finalizacion</th>
                            <th>Km</th>
                            <th>Hs</th>
                        </tr>
                    </thead>
                    <tbody>";

    foreach ($ordenes as $ord){
           
            $citacion = DateTime::createFromFormat('Y-m-d H:i:s', $ord->getFservicio()->format('Y-m-d')." ".$ord->getHcitacionReal()->format('H:i:s'));
            $fin = DateTime::createFromFormat('Y-m-d H:i:s', $ord->getFservicio()->format('Y-m-d')." ".$ord->getHfinservicioReal()->format('H:i:s'));

            if ($fin < $citacion)
            {
                $fin->add(new DateInterval('P1D'));
            }
            $interval = $citacion->diff($fin);
            $hs = ($interval->format('%h') + intdiv($interval->format('%i') , 60));    
            $min = ($interval->format('%i') % 60);
            $time = ($hs < 10?"0$hs":$hs).":".($min < 10?"0$min":$min);
            $tabla.="<tr>
                        <td>".$ord->getFservicio()->format('d/m/Y')."</td>
                        <td>".$ord->getCliente()."</td>
                        <td>".$ord->getNombre()."</td>
                        <td>".$ord->getHcitacionReal()->format('H:i')."</td>
                        <td>".$ord->getHfinservicioReal()->format('H:i')."</td>
                        <td>".$ord->getKm()."</td>
                        <td>".$time."</td>
                      </tr>";
            
  }
  $tabla.="</tbody>
            </table>";
  print $tabla;
}

  
  
?>

