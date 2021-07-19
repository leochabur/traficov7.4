<?php
set_time_limit(0);
    session_start();
include ('../../controlador/bdadmin.php');
include ('../../controlador/ejecutar_sql.php');
include('../../modelo/utils/dateutils.php');

$accion = $_POST[accion];

if ($accion == 'sml'){
   $fecha =  dateToMysql($_POST['fecha'],'/');
   $simula = (isset($_POST['simula'])?1:0);
   $dias = $_POST['mins'];
   
   $conn = conexcion();
   //$simula=1;
   try{
       begin($conn);
       $data = simularVacios($fecha, $_SESSION[structure], $simula, $dias, $conn);
       $tabla = simularVaciosConductor2($fecha, $_SESSION[structure], $simula, $dias, $data[1], $data[2], $conn, $data[3], $data[4]);
       print $data[0]."<br>".$tabla;
    //   print $tabla;
       commit($conn);
       mysql_close($conn);
     }catch (Exception $e) {
                           rollback($conn);
                           mysql_close($conn);
                           print "SE PRODUJERON ERRORES  ".$e->getMessage();
                           }
}
elseif ($accion == 'vvc'){
       $conn = conexcion();
       $fecha =  dateToMysql($_POST['fecha'],'/');
       $data = getFechaVaciosGenerados($fecha, $_SESSION[structure], $conn);
       mysql_close($conn);
       print (json_encode($data));
}
elseif ($accion == 'updcnd'){
       $sql = "INSERT INTO diagramaVaciosACodnuctor (id_conductor, diagrama_sino, id_estructura) VALUES ($_POST[cnd], $_POST[sn], $_SESSION[structure]) ON DUPLICATE KEY UPDATE diagrama_sino=$_POST[sn]";
       $conn = conexcion();
       mysql_query($sql, $conn);
       mysql_close($conn);
       print (json_encode($data));
}


function dataCabecera($con, $str){     ///recupera los datos de la cabecera de los recorridos
         $sql = "SELECT c.id, c.lati, c.long, c.ciudad FROM ciudades c WHERE id_estructura = $str and esCabecera";
         $data = array();
         $result = mysql_query($sql, $con);
         if ($row = mysql_fetch_array($result)){
            $data[0]=$row[0];
            $data[1]=$row[1];
            $data[2]=$row[2];
            $data[3]=$row[3];
         }
         return $data;
}

function getClienteVacio($conn, $str){
         $sql = "SELECT valor
                 FROM opciones o
                 where opcion = 'cliente-vacio' and id_estructura = $str";
         $result = mysql_query($sql, $conn);
         $data = array();
         if ($row = mysql_fetch_array($result)){
            $data[0]=$row[0];
         }
         return $data;
}

function getKmTiempo($conn, $origen, $destino, $str, $latiO, $longO, $latiD, $longD){    ///recupera la distancia y el tiempo de viaje desde el origen al destino
         $sql = "SELECT distancia, round(tiempo/60)
                 FROM distanciasRecorridos
                 where id_origen = $origen and id_estructura_origen = $str and id_destino = $destino and id_estructura_destino = $str";
        // die($sql);
         $result = mysql_query($sql, $conn);
         $data = array();
         if ($row = mysql_fetch_array($result)){
            $data[0] = $row[0];
            $data[1] = $row[1];
         }
         else{
          //    die("$origen, $destino, $str, $latiO, $longO, $latiD, $longD");
              //realiza una llamada para calcular el tiempo y la distancoia entre los dos puntos
              $data = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=$latiO,$longO&destinations=$latiD,$longD&key=AIzaSyCwAptX6vQb5EmgcsNeC442bFRNui1xA6A");
              $data_array = json_decode($data,true);
            //  print_r($data_array);
              $km = ($data_array[rows][0][elements][0][distance][value]/1000);
              $tiempo = ($data_array[rows][0][elements][0][duration][value]);
              if (!isset($km) || !isset($tiempo)){
                 $sql = "SELECT upper(ciudad) as origen, (select upper(ciudad) from ciudades where id = $destino and id_estructura = $str) as destino
                         FROM ciudades
                         where id = $origen and id_estructura = $str";
                 $result = mysql_query($sql, $conn);
                 if ($row = mysql_fetch_array($result)){
                      throw new Exception("No se pudo calcular la ruta para el recorrido $row[0] - $row[1] <a href='../../vista/ordenes/modkmtpo.php?des=$origen&has=$destino' title='Crear' target='_blank'><b><h2>Click Aqui Para Agregar Recorrido Manualmente</h2></b></a>");
                 }
              }
              $sql = "INSERT INTO distanciasRecorridos (id_origen, id_estructura_origen, id_destino, id_estructura_destino, distancia, tiempo)
                      VALUES ($origen, $str, $destino, $str, $km, $tiempo)";
              mysql_query($sql, $conn);
              $data[0]=$km;
              $data[1]=$tiempo;
         }
         
       //  if (!$data[0] || !$data[1]){
          //  throw new Exception("No se pudo calcular la ruta para el recorrido $origen - $destino");
       //  }
         
         return $data;
}

function getDataGPS($con, $str){
}

function createOrden($str, $fservicio, $nombre, $hcitacion, $hsalida, $hllegada, $hfinserv, $km, $origen, $destino, $cliente, $afectadoACliente,
                     $chofer_1, $chofer_2, $micro, $id_ordenServicio, $conn, $simular, $id_diag)
{
$id_chofer_2 = ($chofer_2?$chofer_2:'NULL');
$id_micro = ($micro?$micro:'NULL');
$campos = "id, id_estructura, fservicio, nombre, hcitacion, hsalida, hllegada, hfinservicio, km,
           id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino,
           id_cliente, id_estructura_cliente, id_cliente_vacio, id_estructura_cliente_vacio,
           id_chofer_1, id_estructura_chofer1, finalizada,
           borrada, comentario, id_micro, vacio, id_user, fecha_accion, cantpax, suspendida, checkeada,
           id_claseservicio, id_estructuraclaseservicio, peajes,
           hllegadaplantareal, hsalidaplantareal, hfinservicioreal, hcitacionreal";
$values = "$str, '$fservicio', '$nombre', '$hcitacion', '$hsalida', '$hllegada', '$hfinserv', $km,
           $origen, $str, $destino, $str,
           $cliente, $str,  $afectadoACliente, $str,
           $chofer_1, $str, 0,
           0,'',$id_micro, 1, $_SESSION[userid], now(), 0, 0,0,
           null, null, 0,
           '$hllegada', '$hsalida', '$hfinserv', '$hcitacion'";
           
//die ("insert into ordenes ($campos) values ($values)");


try{
    if (!$simular){
       $id_OrdenVacio = insert("ordenes", $campos, $values, $conn);
       $campos = "id, id_orden, id_estructura_orden, id_orden_vacio, id_estructura_orden_vacio, id_ordenvaciosgenerados";
       $valores = "$id_ordenServicio, $str, $id_OrdenVacio, $str, $id_diag";
       insert("ordenesAsocVacios", $campos, $valores, $conn);
    }
 //   commit($conn);

}catch (Exception $e) {

                      throw new Exception("Error al generar el vacio de la orden Numero: $id_ordenServicio  ".$e->getMessage());
                      // rollback($conn);
                      // die($e->getMessage());
                       };
}

function getClientes($conn, $str){
         $sql = "select id, razon_social
                 from clientes
                 where activo and id_estructura = $str";
         $result = mysql_query($sql, $conn);
         $data = array();
         while ($row = mysql_fetch_array($result)){
               $data[$row[0]] = $row[1];
         }
         return $data;
}

function getFechaVaciosGenerados($fecha, $str, $conn){
         $sql = "SELECT o.id, apenom, fecha_creacion
                 FROM ordenesVaciosGenerados o
                 inner join usuarios u on u.id = o.id_user
                 where fecha = '$fecha' and id_estructura = $str";
         $result = mysql_query($sql, $conn);
         $data = array();
         $data[status]=false;
         if ($row = mysql_fetch_array($result)){
            $data[status]=true;
            $data[id]=$row[0];
            $data[user]= $row[1];
            $data[fecha] = $row[2];
         }
         return $data;
}

function getConductoresExcluidos($str, $conn){
         $sql = "SELECT id_conductor FROM diagramaVaciosACodnuctor where diagrama_sino and id_estructura = $str";
         $result = mysql_query($sql, $conn);
         $data = array();
         while ($row = mysql_fetch_array($result)){
            $data[]=$row[0];
         }
         return $data;
}

function getConductoresSecundarios($fecha, $str, $conn){
         $sql = "select id_chofer_1
                 from ordenes o
                 where fservicio = '$fecha' and o.id_estructura = $str and not vacio and not borrada and not suspendida and id_chofer_2 is not null and id_chofer_1 is not null
                 union all
                 select id_chofer_2
                 from ordenes o
                 where fservicio = '$fecha' and o.id_estructura = $str and not vacio and not borrada and not suspendida and id_chofer_2 is not null";
         $result = mysql_query($sql, $conn);
         $data = array();
         while ($row = mysql_fetch_array($result)){
            $data[]=$row[0];
         }
         return $data;
}

function actuaizarConductoresOrdenVacio($conn, $orden, $conductor, $str, $var = ""){
try{

         $sql = "SELECT o.id_chofer_2, o.id_chofer_1, o.id
                 from ordenes o
                 where id = (select id_orden_vacio from ordenesAsocVacios oa where oa.id_orden = $orden and oa.id_estructura_orden = $str)";
        // throw new Exception($sql);
         $result = mysql_query($sql, $conn);
         if ($row = mysql_fetch_array($result)){
            if ($row[id_chofer_1]){
               if ($row[id_chofer_1] != $conductor){
                  $sql = "UPDATE ordenes SET id_chofer_2 = $conductor WHERE id = $row[id]";
               }
            }
            else{
                 if ($row[id_chofer_2] != $conductor){
                     $sql = "UPDATE ordenes SET id_chofer_1 = $conductor WHERE id = $row[id]";
                 }
            }
            if (($conductor == 1253) &&($orden == 2954090)){
              // throw new Exception("INTENRA ACTUALIZAR $sql - $var - $orden");
            }
            mysql_query($sql, $conn);
         }
}catch (Exception $e) {
                      throw new Exception("Error al generar el vacio de la orden Numero".$e->getMessage());
                       };
}

function imprimir($fecha, $cita, $sale, $llega, $nombre, $conductor, $cliente, $clientes = array(), $color, $hfina = 0){
         $data  =  "<tr style='background-color:$color'>
                        <td>".$fecha->format('d/m/Y')."</td>
                        <td>".$cita->format('H:i')."</td>
                        <td>".$sale->format('H:i')."</td>
                        <td>$nombre</td>
                        <td></td>
                        <td>".$clientes[$cliente]."</td>
                        <td>".$llega->format('H:i')."</td>
                        <td>".($hfina?$hfina->format('H:i'):$llega->format('H:i'))."</td>
                    </tr>";
         return $data;
}


function simularVacios($fec, $st, $simular, $mcita, $conn){

$con = $conn;//conexcion();
         
$fecha = $fec;
$str = $st;
$minCitacion = $mcita;
$cabecera = dataCabecera($con, $str);   // array(id, lati, long, ciudad)  representa el lugar que se indica como cabecera de recorrido
$cliVac =  getClienteVacio($con, $str);
$clientes = getClientes($con, $str);

$sql = "select o.id, o.id_cliente, o.id_chofer_1, o.id_chofer_2,
                        concat(fservicio,' ',hcitacion) as hcita,
                        concat(fservicio,' ',hsalida) as hsale,
                        concat(fservicio,' ',hllegada) as hllega,
                        concat(fservicio,' ',hfinservicio) as hfina,
                        o.nombre, id_cliente,
                        origen.id as origen, origen.lati as latiO, origen.long as longO, destino.id as destino, destino.lati as latiD, destino.long as longD,
                        origen.ciudad as cityO, destino.ciudad as cityD, e1.apellido, e1.nombre as nom, id_chofer_1, id_chofer_2, id_micro,
                        cico1.id as idCityC1, cico1.ciudad as cityC1, cico2.id as idCityC2, cico2.ciudad as cityC2, cico1.lati as latCico1, cico1.long as longCico1
                 from ordenes o
                 inner join empleados e1 on e1.id_empleado = o.id_chofer_1
                 left join empleados e2 on e2.id_empleado = o.id_chofer_2
                 left join ciudades origen ON origen.id = o.id_ciudad_origen and origen.id_estructura = o.id_estructura_ciudad_origen
                 left join ciudades destino ON destino.id = o.id_ciudad_destino and destino.id_estructura = o.id_estructura_ciudad_destino
                 left join ciudades cico1 on cico1.id = e1.id_ciudad
                 left join ciudades cico2 on cico2.id = e2.id_ciudad
                 where fservicio = '$fecha' and o.id_estructura = $str and not vacio and not borrada and not suspendida
                 order by e1.apellido, e1.id_empleado, hsalida";
// throw new Exception($sql);
       //   die($sql);
       /*and id_chofer_1 not in (select id_chofer_1
                                           from ordenes
                                           where fservicio = '$fecha' and id_estructura = $str and not borrada and not suspendida and id_chofer_2 is not null and id_chofer_1 is not null
                                           union
                                           select id_chofer_2
                                           from ordenes
                                           where fservicio = '$fecha' and id_estructura = $str and not borrada and not suspendida and id_chofer_2 is not null and id_chofer_1 is not null)*/
       
$result = mysql_query($sql, $con);
$row = mysql_fetch_array($result);
$ultcoche="";
$ultcondu="";
$ultorden="";
$tabla="";
$ordenYaProcesada = array();
$id_Diag_Vacio;

$hs8 = 28800;
$hs12 = 43200;
$hscorte = 7200;

try{
    $auxCond = getConductoresSecundarios($fecha, $str, $con);
    $auxOrden = array();
   // begin($con);
    $conductores = getConductoresExcluidos($str, $con);
    $i=0;
         if (!$simular){
            $id_Diag_Vacio = insert("ordenesVaciosGenerados", "id, fecha, id_user, fecha_creacion, id_estructura", "'$fecha', $_SESSION[userid], now(), $_SESSION[structure]", $con);
         }
         $tabla = "<style>.dge {
  font-family: serif;
}</style><table border='1' class='dge' width='100%'>";

         while ($row){
               if ($i++%2==0)
                  $color = "#C0C0FF";
               else
                   $color = "#8080FF";
               if (!in_array($row[id_chofer_1], $auxCond)){
                     $cond = $row[id_chofer_1];
                     $tabla.= "<tr style='background-color:$color'><td colspan='8'>CONDUCTOR $row[apellido], $row[nom]   ($row[cityC1])</td></tr>
                            <tr style='background-color:$color'>
                                <td>Fecha Servicio</td>
                                <td>H. Citacion</td>
                                <td>H. Salida</td>
                                <td>Servicio</td>
                                <td>Interno</td>
                                <td>Cliente</td>
                                <td>H. Llegada</td>
                                <td>H. Fin Serv.</td>
                            </tr>";
                     $min50 = 0;
                     $min100 = 0;
                     $minnormales = 0;
                     $cortes=0;  ///indica cuantos cortes se realizan en el turno del conductor

                     while (($row)&& ($cond == $row[id_chofer_1])){ ///mientras que este el mismo conductor
                           $hcita = new DateTime($row[hcita]);
                           $hsale = new DateTime($row[hsale]);
                           $hllega = new DateTime($row[hllega]);
                           $hfina = new DateTime($row[hfina]);

                           if ($ultorden){ ///indica que ya se ha procesado una orden para el interno dado
                           
                              $hFinUltOrden = new DateTime($ultorden[hfina]); /// horario en el que finaliza la ultima orden
                              
                              if ($ultorden[destino] != $row[origen]){  ///finaliza en un lugar distinto al de donde inicia la proxima orden
                                 $second = $hcita->format('U') - $hFinUltOrden->format('U'); ///calcula el tiempo entre servicios para ver si debe generar un vacio a la cabecera
                                 if ($second >  7200){ ///si tiene mas de dos horas de espera genera un vacio a la cabecera
                                    if ($ultorden[destino] != $cabecera[0]){
                                       $nombre = "VACIO ($ultorden[cityD] - $cabecera[3])~(".$clientes[$ultorden[id_cliente]].")";
                                       $kmtpo = getKmTiempo($con, $ultorden[destino], $cabecera[0], $str, $ultorden[latiD], $ultorden[longD], $cabecera[1], $cabecera[2]); ///calcula distancia del ultimo destino a la cabecera
                                       if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                          throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden[id_cliente]].") - ($ultorden[cityD] - $cabecera[3]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                       }
                                       $citaVacio = clone $hFinUltOrden;
                                       $llegaVacio = clone $hFinUltOrden;
                                       $llegaVacio->add(new DateInterval("PT$kmtpo[1]M"));
                                       //////genera un vacio desde el destino de la ultima orden a la cabecera
                                       createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                   $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                   $kmtpo[0], $ultorden[destino], $cabecera[0], $cliVac[0], $ultorden[id_cliente],
                                                   $ultorden[id_chofer_1], $ultorden[id_chofer_2], $ultorden[id_micro], $ultorden[id], $con, $simular, $id_Diag_Vacio);
                                       $tabla.= imprimir($citaVacio, $citaVacio, $citaVacio, $llegaVacio, $nombre, $conductor, $ultorden[id_cliente], $clientes, $color);
                                     }
                                    ////una vez que esta en la cabecera debe evaluar si el servicio sale desde ahi o no
                                    if ($row[origen] != $cabecera[0]){ ///no sale de la cabecera
                                       $kmtpo = getKmTiempo($con, $cabecera[0], $row[origen], $str, $cabecera[1], $cabecera[2], $row[latiO], $row[longO]);//calcula la distancia desde la cabecera a la salida del recorrido
                                       if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                          throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row[id_cliente]].") - ($cabecera[3] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                       }
                                       $nombre = "VACIO ($cabecera[3] - $row[cityO])~(".$clientes[$row[id_cliente]].")";
                                       $llegaVacio = clone $hcita;
                                       $saleVacio = clone $hcita;
                                       $saleVacio->sub(new DateInterval("PT$kmtpo[1]M"));
                                       createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                  $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                  $kmtpo[0], $cabecera[0], $row[origen], $cliVac[0], $row[id_cliente],
                                                  $row[id_chofer_1], $row[id_chofer_2], $row[id_micro], $row[id], $con, $simular, $id_Diag_Vacio);
                                       $tabla.= imprimir($saleVacio, $saleVacio, $saleVacio, $llegaVacio, $nombre, $conductor, $row[id_cliente], $clientes, $color);
                                    }
                                 }
                                 else{ ///no se encuentra en el origen de la orden pero tiene una espera menor a 2 hs. debe crear un vacio del ultimo destino al actual origen
                                       $kmtpo = getKmTiempo($con, $ultorden[destino], $row[origen], $str, $ultorden[latiD], $ultorden[longD], $row[latiO], $row[latiO]);
                                       if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                          throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row[id_cliente]].") - ($ultorden[cityD] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                       }
                                       $nombre = "VACIO ($ultorden[cityD] - $row[cityO])~(".$clientes[$row[id_cliente]].")";
                                       $saleVacio = clone $hFinUltOrden;
                                       $llegaVacio = clone $saleVacio;
                                       $llegaVacio->add(new DateInterval("PT$kmtpo[1]M"));
                                       createOrden($str, $saleVacio->format('Y-m-d'), $nombre,
                                                  $saleVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                  $kmtpo[0], $ultorden[destino], $row[origen], $cliVac[0], $row[id_cliente],
                                                  $row[id_chofer_1], $row[id_chofer_2], $row[id_micro], $row[id], $con, $simular, $id_Diag_Vacio);
                                       $tabla.= imprimir($saleVacio, $saleVacio, $saleVacio, $llegaVacio, $nombre, $conductor, $row[id_cliente], $clientes, $color);
                                 }
                              }
                              $tabla.= imprimir($hcita, $hcita, $hcita, $hfina, $row[nombre], $conductor, $row[id_cliente], $clientes, $color);
                              
                              
                            /*
                              if ($ultorden[destino] != $row[origen]){  ///no se encuentra en el origen, necesita crear un vacio desde el ultimo destino al orgien actual

                                 $second = $hcita->format('U') - $hFinUltOrden->format('U');
                                 if ($second >  7200){ //// tiene un corte de mas de dos horas
                                    if ($ultorden[destino] != $cabecera[0]){//// no se encuentra en la cabecera, debe crear un vacio desde el destino de la utima orden a la cabecera
                                       $kmtpo = getKmTiempo($con, $ultorden[destino], $cabecera[0], $str, $ultorden[latiD], $ultorden[longD], $cabecera[1], $cabecera[2]);

                                       $llegada = clone $hFinUltOrden;
                                       $llegada->add(new DateInterval("PT$kmtpo[1]M"));

                                       $tabla.="<tr><td colspan='8'>Crea un servicio saliendo a las".$hFinUltOrden->format('H:i:s')." llegada ".$llegada->format('H:i:s')."</td></tr>";
                                    }


                                 }
                                 else{
                                      $kmtpo = getKmTiempo($con, $ultorden[destino], $row[origen], $str, $ultorden[latiD], $ultorden[longD], $row[latiO], $row[longO]);

                                      if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                         throw new Exception("CE-1.0 / No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row[id_cliente]].") - ($cabecera[3] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                      }


                                      $saleVacio = new DateTime($row[hcita]);
                                      $saleVacio->sub(new DateInterval("PT$kmtpo[1]M"));

                                      $nombre = "c. VACIO ($ultorden[cityD] - $row[cityO])~(".$clientes[$row[id_cliente]].")";
                                      $tabla.=  "<tr style='background-color:$color'>
                                                     <td>".$saleVacio->format('d/m/Y')."</td>
                                                     <td>".$saleVacio->format('H:i')."</td>
                                                     <td>".$saleVacio->format('H:i')."</td>
                                                     <td>$nombre</td>
                                                     <td></td>
                                                     <td>".$clientes[$cliVac[0]]."</td>
                                                     <td>".$hcita->format('H:i')." </td>
                                                     <td>".$hcita->format('H:i')."</td>
                                                     </tr>";
                                      createOrden($str, $saleVacio->format('Y-m-d'), $nombre,
                                                  $saleVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $hcita->format('H:i:s'), $hcita->format('H:i:s'),
                                                  $kmtpo[0], $ultorden[destino], $row[origen], $cliVac[0], $row[id_cliente],
                                                  $ultorden[id_chofer_1], $ultorden[id_chofer_2], $row[id_micro], $row[id], $con, $simular, $id_Diag_Vacio);
                                      $tabla.=  "<tr style='background-color:$color'>
                                                 <td>".$hcita->format('d/m/Y')."</td>
                                                 <td>".$hcita->format('H:i')."</td>
                                                 <td>".$hsale->format('H:i')."</td>
                                                 <td>$row[nombre] </td>
                                                 <td></td>
                                                 <td>".$clientes[$row[id_cliente]]."</td>
                                                 <td>".$hsale->format('H:i')."</td>
                                                 <td>".$hllega->format('H:i')."</td>
                                                 </tr>";
                                 }
                              }
                              else{
                                   $second = $hcita->format('U') - $hFinUltOrden->format('U');
                                   if ($second >  7200){      ////hay un corte de mas de dos horas
                                      if ($ultorden[destino] != $cabecera[0]){  ///el conductor no se encunetra en la cabecera
                                         if (in_array($ultorden[id_chofer_1], $conductores)){  ////al conductor no se le debe diagramar el vacio
                                           // $tabla.="<tr><td colspan='8'>NO crear una orden a la cabecera CONDUCTOR EXCLUIDO</td></tr>";
                                         }
                                         else{////hay un corte, el conductor no se encuentra en la cabecera y ademas el mismo no se encuntra marcdao para no generar vacios
                                              if ($row[origen] != $ultorden[destino]){ ////sale del mismo lugar a donde llego
                                              
                                              $kmtpo = getKmTiempo($con, $ultorden[destino], $cabecera[0], $str, $ultorden[latiD], $ultorden[longD], $cabecera[1], $cabecera[2]);

                                              $salida = new DateTime($ultorden[hfina]);

                                              $llegada = clone $salida;
                                              $llegada->add(new DateInterval("PT$kmtpo[1]M"));
                                              
                                              $nombre = "1. VACIO ($ultorden[cityD] - $cabecera[3])~(".$clientes[$ultorden[id_cliente]].")";
                                              
                                              createOrden($str, $salida->format('Y-m-d'), $nombre,
                                                          $salida->format('H:i:s'), $salida->format('H:i:s'),
                                                          $llegada->format('H:i:s'), $llegada->format('H:i:s'),
                                                          $kmtpo[0], $ultorden[destino], $cabecera[0], $cliVac[0], $ultorden[id_cliente],
                                                          $ultorden[id_chofer_1], $ultorden[id_chofer_2], $ultorden[id_micro], $ultorden[id], $con, $simular, $id_Diag_Vacio);
                                              
                                              $tabla.= "<tr style='background-color:$color'>
                                                                <td>".$salida->format('d/m/Y')."</td>
                                                                <td>".$salida->format('H:i')."</td>
                                                                <td>".$salida->format('H:i')."</td>
                                                                <td>$nombre</td>
                                                                <td></td>
                                                                <td>".$clientes[$cliVac[0]]."</td>
                                                                <td>".$llegada->format('H:i')." </td>
                                                                <td>".$llegada->format('H:i')."</td>
                                                            </tr>";
                                              
                                              if ($row[origen] != $cabecera[0]){  ///comprueba si para la siguente vuelta sale de la cabecera o no, en caso negativo crea el vacio de cabecera al orgien
                                                  $kmtpo = getKmTiempo($con, $cabecera[0], $row[origen], $str, $cabecera[1], $cabecera[2], $row[latiO], $row[longO]);

                                                  $llegada = new DateTime($row[hcita]);

                                                  $salida = clone $llegada;
                                                  $salida->sub(new DateInterval("PT$kmtpo[1]M"));
                                                  
                                                  $cita = clone $salida;
                                                  $cita->sub(new DateInterval("PT".$minCitacion."M"));

                                                  $nombre = "2. VACIO ($cabecera[3] - $row[cityO])~(".$clientes[$row[id_cliente]].")";

                                                  createOrden($str, $salida->format('Y-m-d'), $nombre,
                                                          $cita->format('H:i:s'), $salida->format('H:i:s'),
                                                          $llegada->format('H:i:s'), $llegada->format('H:i:s'),
                                                          $kmtpo[0], $cabecera[0], $row[origen], $cliVac[0], $row[id_cliente],
                                                          $row[id_chofer_1], $row[id_chofer_2], $row[id_micro], $row[id], $con, $simular, $id_Diag_Vacio);
                                                  $tabla.= "<tr style='background-color:$color'>
                                                                <td>".$cita->format('d/m/Y')."</td>
                                                                <td>".$cita->format('H:i')."</td>
                                                                <td>".$salida->format('H:i')."</td>
                                                                <td>$nombre</td>
                                                                <td></td>
                                                                <td>".$clientes[$cliVac[0]]."</td>
                                                                <td>".$llegada->format('H:i')." </td>
                                                                <td>".$llegada->format('H:i')."</td>
                                                            </tr>";
                                              }
                                         }
                                         }
                                      }
                                      else{
                                         //  $tabla.="<tr><td colspan='8'>NOOOO crear una orden a la cabecera</td></tr>";
                                      }
                                   }

                                   $tabla.=  "<tr style='background-color:$color'>
                                              <td>".$hcita->format('d/m/Y')."</td>
                                               <td>".$hcita->format('H:i')."</td>
                                               <td>".$hsale->format('H:i')."</td>
                                               <td>$row[nombre]</td>
                                               <td></td>
                                              <td>".$clientes[$row[id_cliente]]."</td>
                                              <td>".$hllega->format('H:i')." </td>
                                              <td>".$hfina->format('H:i')."</td>
                                          </tr>";
                              } */

                           }
                           else{

                                $inicio = 'Inicia Recorrido';
                                if ($row[origen] != $cabecera[0]){  ///si es la primer orden que procesa para el interno y no esta en la cabecera
                                  ////recupera la distancia y el tiempo de viaje entre la cabecera y el inicio del recorrido
                                  $kmtpo = getKmTiempo($con, $cabecera[0], $row[origen], $str, $cabecera[1], $cabecera[2], $row[latiO], $row[longO]);

                                  
                                  ////al horario de salida del servicio le resta la duracion del viaje para crear el servicio vacio con el horario de salida correspondiente                               //  $hsalida->sub(new DateInterval("PT$kmtpo[1]M"));
                                  $citaVacio = new DateTime($row[hcita]);
                                  $saleVacio = new DateTime($row[hcita]);
                                  
                                  $delayCitacion = $kmtpo[1]+$minCitacion;
                                //  die("ewe ".$delayCitacion);
                                  
                                  if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                     throw new Exception("CE-2.0 No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row[id_cliente]].") - ($cabecera[3] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                  }
                                  
                                  $citaVacio->sub(new DateInterval("PT".$delayCitacion."M"));
                                  $saleVacio->sub(new DateInterval("PT$kmtpo[1]M"));
                                  $nombre = "VACIO ($cabecera[3] - $row[cityO])~(".$clientes[$row[id_cliente]].")";
                                  if (!in_array($row[id_chofer_1], $conductores)) {
                                     createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                 $citaVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $hcita->format('H:i:s'), $hcita->format('H:i:s'),
                                                 $kmtpo[0], $cabecera[0], $row[origen], $cliVac[0], $row[id_cliente],
                                                 $row[id_chofer_1], $row[id_chofer_2], $row[id_micro], $row[id], $con, $simular, $id_Diag_Vacio);
                                     $tabla.=  "<tr style='background-color:$color'>
                                                    <td>".$citaVacio->format('d/m/Y')."</td>
                                                    <td>".$citaVacio->format('H:i')."</td>
                                                    <td>".$saleVacio->format('H:i')."</td>
                                                    <td>$nombre</td>
                                                    <td></td>
                                                    <td>".$clientes[$cliVac[0]]."</td>
                                                    <td>".$hcita->format('H:i')."</td>
                                                    <td>".$hcita->format('H:i')."</td>
                                                </tr>";
                                  }
                                  else{
                                       if ($row[origen] != $row[idCityC1]){
                                          $citaVacio = new DateTime($row[hcita]);
                                          $saleVacio = new DateTime($row[hcita]);
                                          $kmtpo = getKmTiempo($con, $row[idCityC1], $row[origen], $str, $row[latCico1], $row[longCico1], $row[latiO], $row[longO]);
                                          if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                             throw new Exception("CE-4.0 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden[id_cliente]].") - ($cabecera[3] - $row[cityD]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                          }
                                          $citaVacio->sub(new DateInterval("PT".$delayCitacion."M"));
                                          $saleVacio->sub(new DateInterval("PT$kmtpo[1]M"));
                                          $nombre = "VACIO ($row[cityC1] - $row[cityO])~(".$clientes[$row[id_cliente]].")";
                                          
                                          createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                      $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $hcita->format('H:i:s'), $hcita->format('H:i:s'),
                                                      $kmtpo[0], $row[idCityC1], $row[origen], $cliVac[0], $row[id_cliente],
                                                      $row[id_chofer_1], $row[id_chofer_2], $row[id_micro], $row[id], $con, $simular, $id_Diag_Vacio);
                                          $tabla.=  "<tr style='background-color:$color'>
                                                    <td>".$citaVacio->format('d/m/Y')."</td>
                                                    <td>".$citaVacio->format('H:i')."</td>
                                                    <td>".$saleVacio->format('H:i')."</td>
                                                    <td>$nombre</td>
                                                    <td></td>
                                                    <td>".$clientes[$cliVac[0]]."</td>
                                                    <td>".$hcita->format('H:i')."</td>
                                                    <td>".$hcita->format('H:i')."</td>
                                                    </tr>";
                                       }
                                  }
                                  $inicio = '';
                                }
                                ////calcula la antidad de tiempo para la orden inicial ////////////////
                                ////setea el inicio del turno y el corte a las 12 hs
                         /*       $hInicioTurno = clone $citaVacio;
                                $corte12 = clone $hInicioTurno;
                                $corte12->add(new DateInterval("PT12H"));
                                ////fin seteo variables calculo horas
                                $second = $corte12->format('U') - $hfina->format('U');
                                if ($second < $hs8){
                                   $minnormales = ($second/60);
                                }
                                elseif($second < $hs12){
                                   $minnormales = $hs8;
                                   $min50 = (($second-$hs8)/60);
                                   
                                }
                                else{
                                     $minnormales = $hs8;
                                     $min50 = (($hs12-$hs8)/60);
                                     $min100 = (($second-$second)/60);
                                }       */
                                ////////////fin caculo hs orden inicio/////////////////////////
                                
                                $tabla.=  "<tr style='background-color:$color'>
                                           <td>".$hcita->format('d/m/Y')."</td>
                                           <td>".$hcita->format('H:i')."</td>
                                           <td>".$hsale->format('H:i')."</td>
                                           <td>$row[nombre]</td>
                                           <td></td>
                                           <td>".$clientes[$row[id_cliente]]."</td>
                                           <td>".$hllega->format('H:i')." </td>
                                           <td>".$hfina->format('H:i')."</td>
                                       </tr>";
                           }
                           $ultorden = $row;
                           $row = mysql_fetch_array($result);

                     }

                     if ($ultorden[destino] != $cabecera[0]){ ///fianliza el turno del conductor
                        ///evaluar si el conductor no se le diagrama el servicio

                        $kmtpo = getKmTiempo($con, $cabecera[0], $ultorden[destino], $str, $cabecera[1], $cabecera[2], $ultorden[latiD], $ultorden[longD]);
                        if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                           throw new Exception("CE-3.0 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden[id_cliente]].") - ($cabecera[3] - $row[cityD]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                        }
                        $hfina = new DateTime($ultorden[hfina]);
                        
                        if ($hfina->format('H') == '00'){
                           $hfina->add(new DateInterval("P1D"));
                        }
                        $hfinaVacio = new DateTime($ultorden[hfina]);
                        $hfinaVacio->add(new DateInterval("PT$kmtpo[1]M"));
                        
                        $nombre = "VACIO ($ultorden[cityD] - $cabecera[3])~(".$clientes[$ultorden[id_cliente]].")";
                        if (!in_array($ultorden[id_chofer_1], $conductores)) {
                           createOrden($str, $hfina->format('Y-m-d'), $nombre,
                                       $hfina->format('H:i:s'), $hfina->format('H:i:s'), $hfinaVacio->format('H:i:s'), $hfinaVacio->format('H:i:s'),
                                       $kmtpo[0], $ultorden[destino], $cabecera[0], $cliVac[0], $ultorden[id_cliente],
                                       $ultorden[id_chofer_1], $ultorden[id_chofer_2], $ultorden[id_micro], $ultorden[id], $con, $simular, $id_Diag_Vacio);
                                       
                           $tabla.= imprimir($hfina, $hfina, $hfina, $hfinaVacio, $nombre, $conductor, $cliVac[0], $clientes, $color);

                      /*  $tabla.=  "<tr style='background-color:$color'>
                                   <td>".$hfina->format('d/m/Y')."</td>
                                   <td>".$hfina->format('H:i')."</td>
                                   <td>".$hfina->format('H:i')."</td>
                                   <td>$nombre</td>
                                   <td></td>
                                   <td>".$clientes[$cliVac[0]]."</td>
                                   <td>".$hfinaVacio->format('H:i')." </td>
                                   <td>".$hfinaVacio->format('H:i')."</td>
                               </tr>";  */
                        }
                        else{
                             if ($ultorden[destino] != $ultorden[idCityC1]){
                                $nombre = "VACIO ($ultorden[cityD] - $ultorden[cityC1])~(".$clientes[$ultorden[id_cliente]].")";
                                $kmtpo = getKmTiempo($con, $ultorden[destino], $row[idCityC1], $str, $ultorden[latiD], $ultorden[longD], $row[latCico1], $row[longCico1]);
                                if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                   throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden[id_cliente]].") - ($cabecera[3] - $row[cityD]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                }
                                createOrden($str, $hfina->format('Y-m-d'), $nombre,
                                            $hfina->format('H:i:s'), $hfina->format('H:i:s'), $hfinaVacio->format('H:i:s'), $hfinaVacio->format('H:i:s'),
                                            $kmtpo[0], $ultorden[destino], $row[idCityC1], $cliVac[0], $ultorden[id_cliente],
                                            $ultorden[id_chofer_1], $ultorden[id_chofer_2], $ultorden[id_micro], $ultorden[id], $con, $simular, $id_Diag_Vacio);
                                $tabla.=  "<tr style='background-color:$color'>
                                                     <td>".$hfina->format('d/m/Y')."</td>
                                                     <td>".$hfina->format('H:i')."</td>
                                                     <td>".$hfina->format('H:i')."</td>
                                                     <td>$nombre</td>
                                                      <td></td>
                                                      <td>".$clientes[$cliVac[0]]."</td>
                                                      <td>".$hfinaVacio->format('H:i')." </td>
                                                      <td>".$hfinaVacio->format('H:i')."</td>
                                           </tr>";
                             }
                        }
                     }

                     $tabla.=  "<tr><td colspan='8'><hr></td></tr>";
                     
               $ultorden="";
               }
               else{
                    $auxOrden[] = $row[id];
                    $row = mysql_fetch_array($result);
               }
         }
          $tabla.="<tr><td colspan='8'><hr></td></tr>"; ///finaliza iteracion while
      //   commit($con);
   }catch (Exception $e) {
                        // rollback($con);
                        // die($e->getMessage());
                         throw new Exception($e->getMessage());
                       };
   //mysql_close($con);
   return array($tabla, $auxCond, $auxOrden, $id_Diag_Vacio, $i);
}

function simularVaciosConductor2($fec, $st, $simular, $mcita, $cnds, $ordenes, $conn, $id_Diag_Vacio, $i){

$con = $conn;

$fecha = $fec;
$str = $st;
$minCitacion = $mcita;
$cabecera = dataCabecera($con, $str);   // array(id, lati, long)  representa el lugar que se indica como cabecera de recorrido
$cliVac =  getClienteVacio($con, $str);
$clientes = getClientes($con, $str);

$ordenes = implode(',', $ordenes);

$sql_filtro = "select if(id_chofer_1 is null, 0, id_chofer_1) as chofer
               from ordenes
               where fservicio = '$fecha' and id_estructura = $str and not borrada and not suspendida and id_chofer_2 is not null
               union
               select id_chofer_2
               from ordenes
               where fservicio = '$fecha' and id_estructura = $str and not borrada and not suspendida and id_chofer_2 is not null";

$result = ejecutarSQL($sql_filtro, $conn);

$filtro="";
while ($row = mysql_fetch_array($result)){
      if (!$filtro){
         $filtro = $row[0];
      }
      else{
           $filtro.=",$row[0]";
      }
}

$sql_ordenes = "select *
                from (
                select id, id_micro, fservicio, id_chofer_1, id_chofer_2, hcitacion, hsalida, hllegada, hfinservicio, nombre, id_cliente, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino
                from ordenes
                where fservicio = '$fecha' and id_estructura = $str and not borrada and not suspendida
                union all
                select id, id_micro, fservicio, id_chofer_2, id_chofer_1, hcitacion, hsalida, hllegada, hfinservicio, nombre, id_cliente, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino
                from ordenes
                where fservicio = '$fecha' and id_estructura = $str and not borrada and not suspendida ) o
                where o.id_chofer_1 in ($filtro)";

$sql = "select o.id, o.id_cliente, o.id_chofer_1, id_chofer_2,
                        concat(fservicio,' ',hcitacion) as hcita,
                        concat(fservicio,' ',hsalida) as hsale,
                        concat(fservicio,' ',hllegada) as hllega,
                        concat(fservicio,' ',hfinservicio) as hfina,
                        o.nombre, id_cliente,
                        origen.id as origen, origen.lati as latiO, origen.long as longO, destino.id as destino, destino.lati as latiD, destino.long as longD,
                        origen.ciudad as cityO, destino.ciudad as cityD, e1.apellido, e1.nombre as nom, id_chofer_1, id_chofer_2, id_micro,
                        cico1.id as idCityC1, cico1.ciudad as cityC1, cico1.lati as latCico1, cico1.long as longCico1,
                        id_chofer_1 as fercho, hcitacion as cita
                 from ($sql_ordenes) o
                 inner join empleados e1 on e1.id_empleado = o.id_chofer_1
                 left join ciudades origen ON origen.id = o.id_ciudad_origen and origen.id_estructura = o.id_estructura_ciudad_origen
                 left join ciudades destino ON destino.id = o.id_ciudad_destino and destino.id_estructura = o.id_estructura_ciudad_destino
                 left join ciudades cico1 on cico1.id = e1.id_ciudad
                 order by id_chofer_1, hcita";
 //throw new Exception("DIIIII ".$sql);
        //  die($sql);
$result = mysql_query($sql, $con);
$row = mysql_fetch_array($result);
$ultcoche="";
$ultcondu="";
$ultorden="";
$tabla="";
try{
  //  $auxCond = getConductoresSecundarios($fecha, $str, $con);
    $auxOrden = array();
   // begin($con);
    $ordenDeVacio = array();
    $conductores = getConductoresExcluidos($str, $con); // representa los conductores que no se les debe diagramar vacion de ingreso y/o egreso
    
       //  if (!$simular){
       //     $id_Diag_Vacio = insert("ordenesVaciosGenerados", "id, fecha, id_user, fecha_creacion, id_estructura", "'$fecha', $_SESSION[userid], now(), $_SESSION[structure]", $con);
       //  }
         $tabla = "<table border='1' class='dge' width='100%'>";
         $entro=false;
         while ($row){
               if ($i++%2==0)
                  $color = "#C0C0FF";
               else
                   $color = "#8080FF";
           //    if (!in_array($row[id],$auxOrden)){
                     $auxOrden[]=$row[id];
                     $cond = $row[id_chofer_1];
                     $tabla.= "<tr style='background-color:$color'><td colspan='8'>CONDUCTOR $row[apellido], $row[nom]   ($row[cityC1]) $cond</td></tr>
                            <tr style='background-color:$color'>
                                <td>Fecha Servicio</td>
                                <td>H. Citacion</td>
                                <td>H. Salida</td>
                                <td>Servicio</td>
                                <td>Interno</td>
                                <td>Cliente</td>
                                <td>H. Llegada</td>
                                <td>H. Fin Serv.</td>
                            </tr>";

                     while (($row)&& ($cond == $row[id_chofer_1])){ ///mientras que este el mismo conductor
                           $hcita = new DateTime($row[hcita]);
                           $hsale = new DateTime($row[hsale]);
                           $hllega = new DateTime($row[hllega]);
                           $hfina = new DateTime($row[hfina]);

                           if ($ultorden){ ///indica que ya se ha procesado una orden para el interno dado

                              $hFinUltOrden = new DateTime($ultorden[hfina]);
                              if ($ultorden[destino] != $row[origen]){//no finaliza en el origen de la proxima orden, debe evaluar los vacios
                              
                                 $second = $hcita->format('U') - $hFinUltOrden->format('U'); ///calcula el tiempo entre servicios para ver si debe generar un vacio a la cabecera
                                 if ($second >  7200){ ///si tiene mas de dos horas de espera genera un vacio a la cabecera

                                    if ($ultorden[destino] != $cabecera[0]){ //sino se encuentra en la cabecera, crea un vacio desde el destino de la ultima ordena  la cabecera

                                       $kmtpo = getKmTiempo($con, $ultorden[destino], $cabecera[0], $str, $ultorden[latiD], $ultorden[longD], $cabecera[1], $cabecera[2]);
                                       if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                          throw new Exception("CE-1.1 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden[id_cliente]].") - ($ultorden[cityD] - $cabecera[3]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                       }

                                       $citaVacio = new DateTime($ultorden[hfina]);
                                       $llegaVacio = clone $citaVacio;
                                       $llegaVacio->add(new DateInterval("PT$kmtpo[1]M"));
                                       $nombre = "VACIO ($ultorden[cityD] - $cabecera[3])~(".$clientes[$ultorden[id_cliente]].")";

                                       if (creaOrden($ultorden[id], $ordenDeVacio, 1)){
                                          createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                      $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                      $kmtpo[0], $ultorden[destino], $cabecera[0], $cliVac[0], $ultorden[id_cliente],
                                                      $cond, $ultorden[id_chofer_2], $ultorden[id_micro], $ultorden[id], $con, $simular, $id_Diag_Vacio);
                                       }
                                       else{
                                            if (!$simular){
                                               actuaizarConductoresOrdenVacio($conn, $ultorden[id], $cond, $str, "DE 5 ($row[nombre]) - $nombre");
                                            }
                                       }

                                       /*
                                       if (!in_array($ultorden[id], $ordenDeVacio)) {
                                           $tabla.="<tr><td colspan='8'>Cuando la guarda".implode(',', $ordenDeVacio)."ultorden: ".$ultorden[id]."  actual: ".$row[id]."</td></tr>";
                                          $ordenDeVacio[]=$ultorden[id];
                                          createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                      $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                      $kmtpo[0], $ultorden[destino], $cabecera[0], $cliVac[0], $ultorden[id_cliente],
                                                      $cond, $ultorden[id_chofer_2], $ultorden[id_micro], $ultorden[id], $con, $simular, $id_Diag_Vacio);
                                       }
                                       else{
                                            if($cond == 1262){

                                                   $tabla.="<tr><td colspan='8'> Mansilla NO crea $nombre orden: $ultorden[id]</td></tr>";
                                            }
                                            if (!$simular){
                                               actuaizarConductoresOrdenVacio($conn, $ultorden[id], $cond, $str, "DE 5 ($row[nombre]) - $nombre");
                                            }
                                       }  */
                                       $tabla.= imprimir($citaVacio, $citaVacio, $citaVacio, $llegaVacio, $nombre, $conductor, $cliVac[0], $clientes, $color, $llegaVacio);
                                    }
                                    ////una vez que esta en la cabecera debe evaluar si el servicio sale desde ahi o no
                                    if ($row[origen] != $cabecera[0]){ ///no sale de la cabecera
                                       $kmtpo = getKmTiempo($con, $cabecera[0], $row[origen], $str, $cabecera[1], $cabecera[2], $row[latiO], $row[longO]);//calcula la distancia desde la cabecera a la salida del recorrido
                                       if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                          throw new Exception("CE-1.1 / No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row[id_cliente]].") - ($cabecera[3] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                       }
                                       $nombre = "VACIO ($cabecera[3] - $row[cityO])~(".$clientes[$row[id_cliente]].")";
                                       $llegaVacio = clone $hcita;
                                       $saleVacio = clone $hcita;
                                       $saleVacio->sub(new DateInterval("PT$kmtpo[1]M"));

                                       if (creaOrden($row[id], $ordenDeVacio, 2)){
                                          createOrden($str, $saleVacio->format('Y-m-d'), $nombre,
                                                      $saleVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                      $kmtpo[0], $cabecera[0], $row[origen], $cliVac[0], $row[id_cliente],
                                                      $row[id_chofer_1], $row[id_chofer_2], $row[id_micro], $row[id], $con, $simular, $id_Diag_Vacio);
                                       }
                                       else{
                                            if (!$simular){
                                               actuaizarConductoresOrdenVacio($conn, $row[id], $cond, $str, "DE 5 ($row[nombre]) - $nombre");
                                            }
                                       }



                                   /*    if (!in_array($row[id], $ordenDeVacio)) {
                                          $tabla.="<tr><td colspan='8'>Vacio cabecera servicio ".implode(',', $ordenDeVacio)."ultorden: ".$ultorden[id]."  actual: ".$row[id]."</td></tr>";
                                          $ordenDeVacio[]=$row[id];
                                          createOrden($str, $saleVacio->format('Y-m-d'), $nombre,
                                                      $saleVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                      $kmtpo[0], $cabecera[0], $row[origen], $cliVac[0], $row[id_cliente],
                                                      $row[id_chofer_1], $row[id_chofer_2], $row[id_micro], $row[id], $con, $simular, $id_Diag_Vacio);
                                       }
                                       else{
                                            if (!$simular){
                                               actuaizarConductoresOrdenVacio($conn, $row[id], $cond, $str, "DE 5 ($row[nombre]) - $nombre");
                                            }
                                       }*/
                                       $tabla.= imprimir($saleVacio, $saleVacio, $saleVacio, $llegaVacio, $nombre, $conductor, $row[id_cliente], $clientes, $color);
                                    }
                                    
                                 }
                                 else{
                                      $kmtpo = getKmTiempo($con, $ultorden[destino], $row[origen], $str, $ultorden[latiD], $ultorden[longD], $row[latiO], $row[longO]);
                                      if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                         throw new Exception("CE-1.1 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden[id_cliente]].") - ($cabecera[3] - $row[cityD]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                      }
                                      $citaVacio =  clone $hFinUltOrden;
                                      $llegaVacio = clone $citaVacio;
                                      $llegaVacio->add(new DateInterval("PT$kmtpo[1]M"));
                                      $nombre = "VACIO ($ultorden[cityD] - $row[cityO])~(".$clientes[$row[id_cliente]].")";
                                      
                                       if (creaOrden($row[id], $ordenDeVacio, 3)){
                                          createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                     $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                     $kmtpo[0], $ultorden[destino], $row[origen], $cliVac[0], $row[id_cliente],
                                                     $cond, $row[id_chofer_2], $row[id_micro], $row[id], $con, $simular, $id_Diag_Vacio);
                                       }
                                       else{
                                            if (!$simular){
                                               actuaizarConductoresOrdenVacio($conn, $row[id], $cond, $str, "DE 5 ($row[nombre]) - $nombre");
                                            }
                                       }
                                      
                                      
                                     /* if (!in_array($row[id], $ordenDeVacio)) {

                                         $ordenDeVacio[]=$row[id];
                                         createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                     $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                     $kmtpo[0], $ultorden[destino], $row[origen], $cliVac[0], $row[id_cliente],
                                                     $cond, $row[id_chofer_2], $row[id_micro], $row[id], $con, $simular, $id_Diag_Vacio);
                                      }
                                      else{
                                           if (!$simular){
                                              actuaizarConductoresOrdenVacio($conn, $row[id], $cond, $str, "DE 5 ($row[nombre]) - $nombre");
                                           }
                                      } */
                                      $tabla.= imprimir($citaVacio, $citaVacio, $citaVacio, $llegaVacio, $nombre, $conductor, $cliVac[0], $clientes, $color);
                                 }
                                 $tabla.= imprimir($hcita, $hcita, $hsale, $hllega, "$row[nombre]", $conductor, $row[id_cliente], $clientes, $color, $hfina);

                              }
                              else{
                                   $tabla.= imprimir($hcita, $hcita, $hsale, $hllega, $row[nombre], $conductor, $row[id_cliente], $clientes, $color, $hfina);
                              }
                           }
                           else{
                                $inicio = 'Inicia Recorrido';
                                if ($row[origen] != $cabecera[0]){  ///si es la primer orden que procesa para el interno y no esta en la cabecera
                                  ////recupera la distancia y el tiempo de viaje entre la cabecera y el inicio del recorrido
                                  $kmtpo = getKmTiempo($con, $cabecera[0], $row[origen], $str, $cabecera[1], $cabecera[2], $row[latiO], $row[longO]);
                                  if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                     throw new Exception("CE-2.1 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden[id_cliente]].") - ($cabecera[3] - $row[cityD]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                  }

                                  ////al horario de salida del servicio le resta la duracion del viaje para crear el servicio vacio con el horario de salida correspondiente                               //  $hsalida->sub(new DateInterval("PT$kmtpo[1]M"));
                                  $citaVacio = new DateTime($row[hcita]);
                                  $saleVacio = new DateTime($row[hcita]);

                                  $delayCitacion = $kmtpo[1]+$minCitacion;
                                //  die("ewe ".$delayCitacion);

                                  if ($kmtpo[1] == 0){
                                     die ("no existen km para el recorrido $cabecera[0] - $row[origen]");
                                  }

                                  $citaVacio->sub(new DateInterval("PT".$delayCitacion."M"));
                                  $saleVacio->sub(new DateInterval("PT$kmtpo[1]M"));
                                  $nombre = "VACIO ($cabecera[3] - $row[cityO])~(".$clientes[$row[id_cliente]].")";
                                  if (!in_array($row[id_chofer_1], $conductores)){ ///// el conductor no esta dentro de los que se quedan con la unidad
                                  

                                       if (creaOrden($row[id], $ordenDeVacio, 4)){
                                          createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                    $citaVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $hcita->format('H:i:s'), $hcita->format('H:i:s'),
                                                    $kmtpo[0], $cabecera[0], $row[origen], $cliVac[0], $row[id_cliente],
                                                    $row[id_chofer_1], $row[id_chofer_2], $row[id_micro], $row[id], $con, $simular, $id_Diag_Vacio);
                                       }
                                       else{
                                            if (!$simular){
                                                actuaizarConductoresOrdenVacio($conn, $row[id], $row[id_chofer_1], $str, "DE 4");
                                            }
                                       }
                                  
                                  
                                  /*   if (!in_array($row[id], $ordenDeVacio)) { /// el vacio aun no se ha generado
                                        $ordenDeVacio[]=$row[id];
                                        createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                    $citaVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $hcita->format('H:i:s'), $hcita->format('H:i:s'),
                                                    $kmtpo[0], $cabecera[0], $row[origen], $cliVac[0], $row[id_cliente],
                                                    $row[id_chofer_1], $row[id_chofer_2], $row[id_micro], $row[id], $con, $simular, $id_Diag_Vacio);
                                     }
                                     else{
                                          if (!$simular){
                                             actuaizarConductoresOrdenVacio($conn, $row[id], $row[id_chofer_1], $str, "DE 4");
                                          }
                                     }*/
                                     $tabla.=  "<tr style='background-color:$color'>
                                                    <td>".$citaVacio->format('d/m/Y')."</td>
                                                    <td>".$citaVacio->format('H:i')."</td>
                                                    <td>".$saleVacio->format('H:i')."</td>
                                                    <td>$nombre</td>
                                                    <td></td>
                                                    <td>".$clientes[$cliVac[0]]."</td>
                                                    <td>".$hcita->format('H:i')." </td>
                                                    <td>".$hcita->format('H:i')."</td>
                                                </tr>";
                                  }
                                  else{
                                       if ($row[origen] != $row[idCityC1]){
                                          $citaVacio = new DateTime($row[hcita]);
                                          $saleVacio = new DateTime($row[hcita]);
                                          $kmtpo = getKmTiempo($con, $row[idCityC1], $row[origen], $str, $row[latCico1], $row[longCico1], $row[latiO], $row[longO]);
                                          if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                             throw new Exception("CE-3.1 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden[id_cliente]].") - ($cabecera[3] - $row[cityD]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                          }
                                          $citaVacio->sub(new DateInterval("PT".$delayCitacion."M"));
                                          $saleVacio->sub(new DateInterval("PT$kmtpo[1]M"));
                                          //$nombre = "VACIO ($row[cityC1] - $row[cityO])~(".$clientes[$row[id_cliente]].")";
                                         // die ($nombre);
                                       //  $laCrea = "NO LA CREA";
                                       if (creaOrden($row[id], $ordenDeVacio, 5)){
                                          createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                      $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $hcita->format('H:i:s'), $hcita->format('H:i:s'),
                                                      $kmtpo[0], $row[idCityC1], $row[origen], $cliVac[0], $row[id_cliente],
                                                      $row[id_chofer_1], $row[id_chofer_2], $row[id_micro], $row[id], $con, $simular, $id_Diag_Vacio);
                                       }
                                       else{
                                            if (!$simular){
                                                actuaizarConductoresOrdenVacio($conn, $row[id], $row[id_chofer_1], $str, "DE 3");
                                            }
                                       }
                                      /*   if (!in_array($row[id], $ordenDeVacio)) {
                                          //  $laCrea = "SI LA CREA";
                                            $ordenDeVacio[]=$row[id];
                                            createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                      $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $hcita->format('H:i:s'), $hcita->format('H:i:s'),
                                                      $kmtpo[0], $row[idCityC1], $row[origen], $cliVac[0], $row[id_cliente],
                                                      $row[id_chofer_1], $row[id_chofer_2], $row[id_micro], $row[id], $con, $simular, $id_Diag_Vacio);
                                          }
                                          else{
                                               if (!$simular){
                                                  actuaizarConductoresOrdenVacio($conn, $row[id], $row[id_chofer_1], $str, "DE 3");
                                               }
                                          }*/
                                          $tabla.=  "<tr style='background-color:$color'>
                                                    <td>".$citaVacio->format('d/m/Y')."</td>
                                                    <td>".$citaVacio->format('H:i')."</td>
                                                    <td>".$saleVacio->format('H:i')."</td>
                                                    <td>$nombre</td>
                                                    <td></td>
                                                    <td>".$clientes[$cliVac[0]]."</td>
                                                    <td>".$hcita->format('H:i')."</td>
                                                    <td>".$hcita->format('H:i')."</td>
                                                    </tr>";
                                       }
                                  }

                                  $inicio = '';
                                //  die();

                                }
                                $tabla.=  "<tr style='background-color:$color'>
                                           <td>".$hcita->format('d/m/Y')."</td>
                                           <td>".$hcita->format('H:i')."</td>
                                           <td>".$hsale->format('H:i')."</td>
                                           <td>$row[nombre]</td>
                                           <td></td>
                                           <td>".$clientes[$row[id_cliente]]."</td>
                                           <td>".$hllega->format('H:i')." </td>
                                           <td>".$hfina->format('H:i')."</td>
                                       </tr>";
                           } ////fin inicio recorrido
                           $ultorden = $row;
                           $row = mysql_fetch_array($result);
                     } ///while cond
                     if ($ultorden[destino] != $cabecera[0]){  /////ultima orden del servicio
                        ///evaluar si el conductor no se le diagrama el servicio

                        $var="";

                        $kmtpo = getKmTiempo($con, $cabecera[0], $ultorden[destino], $str, $cabecera[1], $cabecera[2], $ultorden[latiD], $ultorden[longD]);
                        if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                           throw new Exception("CE-4.1 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden[id_cliente]].") - ($cabecera[3] - $row[cityD]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                        }
                        $hfina = new DateTime($ultorden[hfina]);
                        $hfinaVacio = new DateTime($ultorden[hfina]);

                        $hfinaVacio->add(new DateInterval("PT$kmtpo[1]M"));

                        $nombre = "VACIO ($ultorden[cityD] - $cabecera[3])~(".$clientes[$ultorden[id_cliente]].")";
                        if (!in_array($ultorden[id_chofer_1], $conductores)) { ///al conductor no se le diagrama el vacio de regreso
                        
                        
                        if (creaOrden($row[id], $ordenDeVacio, 6)){
                           createOrden($str, $hfina->format('Y-m-d'), $nombre,
                                       $hfina->format('H:i:s'), $hfina->format('H:i:s'), $hfinaVacio->format('H:i:s'), $hfinaVacio->format('H:i:s'),
                                       $kmtpo[0], $ultorden[destino], $cabecera[0], $cliVac[0], $ultorden[id_cliente],
                                       $ultorden[id_chofer_1], $ultorden[id_chofer_2], $ultorden[id_micro], $ultorden[id], $con, $simular, $id_Diag_Vacio);
                        }
                        else{
                             if (!$simular){
                                                actuaizarConductoresOrdenVacio($conn, $ultorden[id], $ultorden[id_chofer_1], $str, "DE 1");
                                            }
                        }
                                       
                         /*  if (!in_array($ultorden[id], $ordenDeVacio)) { ///todavia no se ha procesado el vacio de la orden
                              $ordenDeVacio[]=$ultorden[id];
                              createOrden($str, $hfina->format('Y-m-d'), $nombre,
                                       $hfina->format('H:i:s'), $hfina->format('H:i:s'), $hfinaVacio->format('H:i:s'), $hfinaVacio->format('H:i:s'),
                                       $kmtpo[0], $ultorden[destino], $cabecera[0], $cliVac[0], $ultorden[id_cliente],
                                       $ultorden[id_chofer_1], $ultorden[id_chofer_2], $ultorden[id_micro], $ultorden[id], $con, $simular, $id_Diag_Vacio);
                           }
                           else{
                                if (!$simular){
                                   actuaizarConductoresOrdenVacio($conn, $ultorden[id], $ultorden[id_chofer_1], $str, "DE 1");
                                }
                           }*/

                           $tabla.=  "<tr style='background-color:$color'>
                                   <td>".$hfina->format('d/m/Y')."</td>
                                   <td>".$hfina->format('H:i')."</td>
                                   <td>".$hfina->format('H:i')."</td>
                                   <td>$nombre</td>
                                   <td></td>
                                   <td>".$clientes[$cliVac[0]]."</td>
                                   <td>".$hfinaVacio->format('H:i')." </td>
                                   <td>".$hfinaVacio->format('H:i')."</td>
                               </tr>";
                        }
                        else{
                             $var="NO DIAGRAMA";
                             if ($ultorden[destino] != $ultorden[idCityC1]){
                                $var.="  OTRARARARRA";
                                $nombre = "VACIO ($ultorden[cityD] - $ultorden[cityC1])~(".$clientes[$ultorden[id_cliente]].")";
                                $kmtpo = getKmTiempo($con, $ultorden[destino], $row[idCityC1], $str, $ultorden[latiD], $ultorden[longD], $row[latCico1], $row[longCico1]);
                                if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                   throw new Exception("CE-5.1 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden[id_cliente]].") - ($cabecera[3] - $row[cityD]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                }
                                $laCrea = "";
                                
                                if (creaOrden($row[id], $ordenDeVacio, 7)){
                                   createOrden($str, $hfina->format('Y-m-d'), $nombre,
                                               $hfina->format('H:i:s'), $hfina->format('H:i:s'), $hfinaVacio->format('H:i:s'), $hfinaVacio->format('H:i:s'),
                                               $kmtpo[0], $ultorden[destino], $row[idCityC1], $cliVac[0], $ultorden[id_cliente],
                                               $ultorden[id_chofer_1], $ultorden[id_chofer_2], $ultorden[id_micro], $ultorden[id], $con, $simular, $id_Diag_Vacio);
                                }
                                else{
                                     if (!$simular){
                                                actuaizarConductoresOrdenVacio($conn, $ultorden[id], $ultorden[id_chofer_1], $str, "DE 2");
                                     }
                                }
                                
                                /*
                                if (!in_array($ultorden[id], $ordenDeVacio)) {
                                   $laCrea = "";
                                   $ordenDeVacio[]=$ultorden[id];
                                   createOrden($str, $hfina->format('Y-m-d'), $nombre,
                                               $hfina->format('H:i:s'), $hfina->format('H:i:s'), $hfinaVacio->format('H:i:s'), $hfinaVacio->format('H:i:s'),
                                               $kmtpo[0], $ultorden[destino], $row[idCityC1], $cliVac[0], $ultorden[id_cliente],
                                               $ultorden[id_chofer_1], $ultorden[id_chofer_2], $ultorden[id_micro], $ultorden[id], $con, $simular, $id_Diag_Vacio);
                                }
                                else{
                                     if (!$simular){
                                        actuaizarConductoresOrdenVacio($conn, $ultorden[id], $ultorden[id_chofer_1], $str, "DE 2");
                                     }
                                }*/
                                $tabla.=  "<tr style='background-color:$color'>
                                                     <td>".$hfina->format('d/m/Y')."</td>
                                                     <td>".$hfina->format('H:i')."</td>
                                                     <td>".$hfina->format('H:i')."</td>
                                                     <td>$nombre</td>
                                                      <td></td>
                                                      <td>".$clientes[$cliVac[0]]."</td>
                                                      <td>".$hfinaVacio->format('H:i')." </td>
                                                      <td>".$hfinaVacio->format('H:i')."</td>
                                           </tr>";
                             }
                        }
                        //$tabla.= "<tr><td colspan='8'>INTENTANDO DAR CIERRE AL CONDUCTOR $var $ultorden[apellido]  ($ultorden[destino] - $cabecera[0])</td></tr>";
                     }
                     $tabla.=  "<tr><td colspan='8'><hr></td></tr>";
               $ultorden="";
         } ///finaliza iteracion while
       //  commit($con);
   }catch (Exception $e) {
                        // rollback($con);
                       //  die($e->getMessage());
                        throw new Exception($e->getMessage());
                       };
  // mysql_close($con);
   return $tabla;
}

function creaOrden($idOrden, &$ordenVacio, $orden){
         $crear = true;
         if (array_key_exists($idOrden, $ordenVacio)){  ///si ya proceso la orden debe fijarse en que etapa se encuentra
            if(array_key_exists($orden, $ordenVacio[$idOrden])){ // ya proceso el vacio en esta etapa...debe actualizar la orden solamente
                 $crear = false;
            }
            else{
                 $ordenVacio[$idOrden][$orden] = true;
            }
         }
         else{ ///aun no ha procesado la orden
               $ordenVacio[$idOrden] = array();
               $ordenVacio[$idOrden][$orden] = true;
         }
         return $crear;
}



