<?php
session_start();
set_time_limit(0);
error_reporting(0);
//ini_set('error_reporting', E_ALL);
    
include ('../../controlador/bdadmin.php');
//include ('../../controlador/ejecutar_sqlv2.php');
include('../../modelo/utils/dateutils.php');

$accion = $_POST['accion'];

if ($accion == 'vvc'){
   $fecha =  dateToMysql($_POST['fecha'],'/');

   
   $conn = conexcion(true);
   $conn = new mysqli('127.0.0.1', 'c0mbexpuser', 'Mb2013Exp', 'c0mbexport');
 //  $conn = new mysqli('127.0.0.1', 'root', 'leo1979', 'c0mbexport');
   mysqli_query($conn, "SET NAMES 'utf8'");
   try{

       $data = simularVacios($fecha, $_SESSION['structure'], $conn, isset($_POST['finalizada']));
       print $data;
     }catch (Exception $e) {
                           print "SE PRODUJERON ERRORES  ".$e->getMessage();
                           }
}

function getClientes($conn, $str){
         $sql = "select id, razon_social
                 from clientes
                 where activo and id_estructura = $str";
         $result = mysqli_query($conn, $sql);
         $data = array();
         while ($row = mysqli_fetch_array($result)){
               $data[$row[0]] = $row[1];
         }
         return $data;
}



function getConductoresExcluidos($str, $conn){
         $sql = "SELECT id_conductor FROM diagramaVaciosACodnuctor where diagrama_sino and id_estructura = $str";
         $result = mysqli_query($conn, $sql);
         $data = array();
         while ($row = mysqli_fetch_array($result)){
            $data[]=$row[0];
         }
         return $data;
}




function imprimir($fecha, $cita, $sale, $llega, $nombre, $conductor, $cliente, $clientes = array(), $color, $hfina = 0){
         $data  =  "<tr style='background-color:$color'>
                        <td>".$fecha->format('d/m/Y')."</td>
                        <td>".$cita->format('H:i')."</td>
                        <td>".$sale->format('H:i')."</td>
                        <td>$nombre</td>
                        <td></td>
                        <td>".$clientes[$cliente]."</td>
                        <td>".($llega?$llega->format('H:i'):'SIN HORA')."</td>
                        <td>".($hfina?$hfina->format('H:i'):($llega?$llega->format('H:i'):'SIN HORA'))."</td>
                    </tr>";
         return $data;
}




function encabezadoTablaConductor($nombre, $apellido, $color){
  $tabla = "<tr style='background-color:$color'><td colspan='12'>CONDUCTOR $apellido, $nombre</td></tr>
              <tr style='background-color:$color'>
                  <td></td>
                  <td>N# Orden</td>
                  <td>Fecha Servicio</td>
                  <td>H. Citacion</td>
                  <td>H. Salida</td>
                  <td>Servicio</td>
                  <td>Origen-Destino</td>
                  <td>Interno</td>
                  <td>Cliente</td>
                  <td>H. Llegada</td>
                  <td>H. Fin Serv.</td>
                  <td>Observacion</td>
              </tr>";
  return $tabla;
}

function dataCabecera($con, $str){     ///recupera los datos de la cabecera de los recorridos
         $sql = "SELECT c.id, c.lati, c.long, c.ciudad FROM ciudades c WHERE id_estructura = $str and esCabecera";
         $data = array();
         $result = mysqli_query($con, $sql);
         if ($row = mysqli_fetch_array($result)){
            $data[0]=$row[0];
            $data[1]=$row[1];
            $data[2]=$row[2];
            $data[3]=$row[3];
         }
         return $data;
}

function simularVacios($fec, $st, $conn, $verificaFinalizada){

         $exc = getConductoresExcluidos($st, $conn);

         $cabecera = dataCabecera($conn, $st);
       //  die(getSQLConductore($fec, $st));
         $result = mysqli_query($conn, getSQLConductore($fec, $st)) or die(mysqli_error($conn));
         $row = mysqli_fetch_array($result);

         $tabla= "<table class='table table-zebra'>";
         $ordenImpresas = array();
         while ($row){
                     $encabeza = encabezadoTablaConductor($row['nom'], $row['apellido'], '');
                     $cuerpo = "";
                     $cond = $row['id_chofer_1'];
                     $ultorden="";
                     while (($row)&& ($cond == $row['id_chofer_1']))
                     {
                         $observacion = "";
                        if (!$row['id_micro']){
                                $observacion.= 'Orden sin interno asignado / ';
                        }
                        if ($verificaFinalizada)
                        {                        
                            if (!$row['finalizada']){
                                    $observacion.= 'Orden no finalizada / ';
                            }
                         }
                         if ($ultorden) ///indica que ya se ha procesado una orden para el conductor dado
                         {         
                             $solapada = false;
                             $resu = verificarHorarioUltOrden($row, $ultorden);            
                              if (!$resu['result'])
                              {
                                  $observacion.= $resu['msge'];
                                  $solapada = true;
                              }

                              if ($ultorden['destino'] != $row['origen'])
                              {
                                  $observacion.= 'Vacio inexistente desde el destino de la orden anterior y el origen de la orden actual / ';
                                  $solapada = true;
                              }

                              if (($ultorden['id_micro'] && $row['id_micro']) && ($ultorden['id_micro'] != $row['id_micro']))
                              {
                                  if (in_array($ultorden['id_chofer_1'], $exc))
                                  {
                                      if (!(($ultorden['destino'] == $ultorden['idCityC1']) || ($ultorden['destino'] == $cabecera[0])))
                                      {
                                          $observacion.='Diagramacion de interno inconcistente con la orden anterior / ';
                                          $solapada = true;
                                      }
                                  }
                                  else{
                                      if (!($ultorden['destino'] == $cabecera[0]))
                                        {
                                            $observacion.='Diagramacion de interno inconcistente con la orden anterior / ';
                                            $solapada = true;
                                        }
                                  }
                                  
                              }

                              if ($observacion)
                              {
                                  if ($solapada)
                                  {
                                    if (!in_array($ultorden['id'], $ordenImpresas))
                                    {
                                        $cuerpo.= imprimirOrden($ultorden, '');
                                        $ordenImpresas[] = $ultorden['id'];
                                    }
                                  }

                                  if (!in_array($row['id'], $ordenImpresas))
                                  {
                                      $cuerpo.= imprimirOrden($row, $observacion);
                                      $ordenImpresas[] = $row['id'];
                                  }
                              }       

                              

                             
                         }
                         else	////es la primer orden que se procesa para el conducotr
						             {    
                              $posActual = (in_array($row['id_chofer_1'], $exc)?$row['idCityC1']:$cabecera[0]);
                              if ($posActual != $row['origen']) 
                              {
                                 $observacion.= 'Vacio inexistente hasta el lugar de salida del servicio  / ';
                              }                                                      

                              $resu = verificarHorario($row);
                              if (!$resu['result']){
                                $observacion.= $resu['msge'];
                              }   
                              if ($observacion){
                                if (!in_array($row['id'], $ordenImpresas))
                                {
                                    $cuerpo.= imprimirOrden($row, $observacion);
                                    $ordenImpresas[] = $row['id'];
                                }
                              }                           
                          }
                        
                         
                         $ultorden = $row;
                         $row = mysqli_fetch_array($result);
                     }
                     if ($cuerpo){
                      $tabla.=$encabeza.$cuerpo."<tr><td colspan='12'></td></tr>";;
                     }                     
                                        
         }
          $tabla.="
                    </table>
                    <script>
                    $('.modord').click(function(){
                                                  var id_orden = $(this).data('id');
                                                  var dialog = $('<div style=\"display:none\" id=\"dialog\" class=\"loading\" align=\"center\"></div>').appendTo('body');
                                                  dialog.dialog({
                                                                                   close: function(event, ui) {dialog.remove();},
                                                                                   title: 'Modificar orden',
                                                                                   width:850,
                                                                                   height:600,
                                                                                   modal:true,
                                                                                         show: {
                                                                                                effect: 'blind',
                                                                                                duration: 300
                                                                                         },
                                                                                         hide: {
                                                                                               effect: 'blind',
                                                                                               duration: 300
                                                                                               }
                                                                });
                                                    dialog.load('/vista/ordenes/modord.php',{orden:id_orden},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});

                                                                    });
                    </script>"; ///finaliza iteracion while

   return $tabla;
}

function verificarHorario($row)
{
    $hcita = new DateTime($row['hcita']);
    $hsale = new DateTime($row['hsale']);
    $hllega = new DateTime($row['hllega']);
    $hfina = new DateTime($row['hfina']);
    $result = array('result' => true, 'msge' => '');
    if ($hcita > $hsale)
    {
        $result['msge'].= 'El servicio esta citado despues del horario de salida / ';
        $result['result'] = false;
    }
    if ($hsale > $hllega)
    {
        $result['msge'].= 'El horario de llegada del servicio es anterior al horario de salida / ';
        $result['result'] = false;
    }
    if ($hllega > $hfina)
    {
        $result['msge'].= 'El horario de llegada del servicio es posterior al horario de finalizacion / ';
        $result['result'] = false;
    }
    return $result;
}

function verificarHorarioUltOrden($row, $last)
{
  try{
    $hcita = new DateTime($row['hcita']);

    $hfina = new DateTime($last['hfina']);

    $result = array('result' => true, 'msge' => '');

    if ($hcita < $hfina)
    {
        $result['msge'].= 'El horario de finalizacion del ultimo servicio es posterior al horario de citacion actual / ';
        $result['result'] = false;
    }
    return $result;
  }
  catch(Exception $e){ throw $e;}
}

function imprimirOrden($row, $observa)
{
  try{
    $hcita = new DateTime($row['hcita']);
    $hsale = new DateTime($row['hsale']);
    $hllega = new DateTime($row['hllega']);
    $hfina = new DateTime($row['hfina']);
  }
  catch(Exception $e){ throw $e;}
    $color="";
    $tabla=  "<tr>
                  <td><a data-id='$row[id]' class='modord' href='#'>".'<i class="far fa-edit fa-2x">'."</i></a></td>
                  <td>$row[id]</td>
                  <td>".$hcita->format('d/m/Y')."</td>
                  <td>".$hcita->format('H:i')."</td>
                  <td>".$hsale->format('H:i')."</td>
                  <td>$row[nombre]</td>
                  <td>$row[cityO] - $row[cityD]</td>
                  <td>$row[interno]</td>
                  <td>".$cliente."</td>
                  <td>".$hllega->format('H:i')."</td>
                  <td>".$hfina->format('H:i')."</td>
                  <td>$observa</td>
              </tr>";   
    return $tabla;  
}


function getSQLConductore($fecha, $str){
  $sql = "SELECT o.id, o.id_cliente, o.id_chofer_1,
                        concat(fservicio,' ',hcitacion) as hcita,
                        concat(if(hsalida < hcitacion, DATE_ADD(fservicio, INTERVAL 1 DAY), fservicio),' ',hsalida) as hsale,
                        concat(if(hllegada < hsalida, DATE_ADD(fservicio, INTERVAL 1 DAY), fservicio),' ',hllegada) as hllega,
                        concat(if(hllegada < hsalida, DATE_ADD(fservicio, INTERVAL 1 DAY), fservicio),' ',hfinservicio) as hfina,
                        o.nombre, id_cliente,
                        origen.id as origen, destino.id as destino,
                        origen.ciudad as cityO, destino.ciudad as cityD, e1.apellido, e1.nombre as nom, id_chofer_1, id_micro, interno,
                        cico1.id as idCityC1, cico1.ciudad as cityC1, finalizada
          FROM (
                select id_chofer_1, id, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, fservicio, 
                       hllegadaplantareal as hllegada, hsalidaplantareal as hsalida, hfinservicioreal as hfinservicio, hcitacionreal as hcitacion, nombre, id_micro, finalizada
                from ordenes o
                where fservicio = '$fecha' and o.id_estructura = $str and not borrada and not suspendida and id_chofer_1 is not null and id_chofer_2 is not null
                union all
                select id_chofer_2, id, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, fservicio, 
                       hllegadaplantareal as hllegada, hsalidaplantareal as hsalida, hfinservicioreal as hfinservicio, hcitacionreal as hcitacion, nombre, id_micro, finalizada
                from ordenes o
                where fservicio = '$fecha' and o.id_estructura = $str and not borrada and not suspendida and id_chofer_1 is not null and id_chofer_2 is not null
                union all
                select id_chofer_1, id, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, fservicio, 
                       hllegadaplantareal as hllegada, hsalidaplantareal as hsalida, hfinservicioreal as hfinservicio, hcitacionreal as hcitacion, nombre, id_micro, finalizada
                from ordenes o
                where fservicio = '$fecha' and o.id_estructura = $str and not borrada and not suspendida and id_chofer_1 is not null and id_chofer_2 is null
                union all
                select id_chofer_2, id, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, fservicio, 
                      hllegadaplantareal as hllegada, hsalidaplantareal as hsalida, hfinservicioreal as hfinservicio, hcitacionreal as hcitacion, nombre, id_micro, finalizada
                from ordenes o
                where fservicio = '$fecha' and o.id_estructura = $str and not borrada and not suspendida and id_chofer_1 is null and id_chofer_2 is not null
              ) o
         INNER JOIN (select * from empleados) e1 on e1.id_empleado = o.id_chofer_1
         LEFT JOIN ciudades origen ON origen.id = o.id_ciudad_origen and origen.id_estructura = o.id_estructura_ciudad_origen
         LEFT JOIN ciudades destino ON destino.id = o.id_ciudad_destino and destino.id_estructura = o.id_estructura_ciudad_destino
         LEFT JOIN ciudades cico1 on cico1.id = e1.id_ciudad
         LEFT JOIN unidades u ON u.id = id_micro
         ORDER BY e1.apellido, e1.id_empleado, hcita";
  return $sql;
}



