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
   $simula=1;
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
                           print "SE PRODUCIERON ERRORES  ".$e->getMessage();
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
                 throw new Exception("No se pudo calcular la ruta para el recorrido $origen - $destino");
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
       insert("ordenesasocvacios", $campos, $valores, $conn);
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
                 where id = (select id_orden_vacio from ordenesasocvacios oa where oa.id_orden = $orden and oa.id_estructura_orden = $str)";
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


function simularVacios($fec, $st, $simular, $mcita, $conn){

$con = $conn;//conexcion();
         
$fecha = $fec;
$str = $st;
$minCitacion = $mcita;
$cabecera = dataCabecera($con, $str);   // array(id, lati, long)  representa el lugar que se indica como cabecera de recorrido
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
                              
                              if ($ultorden[destino] != $row[origen]){  ///no se encuentra en el origen, necesita crear un vacio desde el ultimo destino al orgien actual
                                 $kmtpo = getKmTiempo($con, $ultorden[destino], $row[origen], $str, $ultorden[latiD], $ultorden[longD], $row[latiO], $row[longO]);

                                 if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                     throw new Exception("CE-1.0 / No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row[id_cliente]].") - ($cabecera[3] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                 }

                                 $saleVacio = new DateTime($row[hcita]);
                                 $saleVacio->sub(new DateInterval("PT$kmtpo[1]M"));

                                 $nombre = "VACIO ($ultorden[cityD] - $row[cityO])~(".$clientes[$row[id_cliente]].")";
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

                                 //if (!in_array($ultorden[id_chofer_1], $conductores)) {
                                    createOrden($str, $saleVacio->format('Y-m-d'), $nombre,
                                                $saleVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $hcita->format('H:i:s'), $hcita->format('H:i:s'),
                                                $kmtpo[0], $ultorden[destino], $row[origen], $cliVac[0], $row[id_cliente],
                                                $ultorden[id_chofer_1], $ultorden[id_chofer_2], $row[id_micro], $row[id], $con, $simular, $id_Diag_Vacio);
                               //  }
                                 //die("guardo");
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
                                        
                                 ////////////////  calculo horas desde la ultima orden/////////////////////
                                 
                              /*   if ($second > $hscorte){ //hay corte
                                    $seconds = ($hcita->format('U') - $hllega->format('U')/60);
                                    if (($minsnormales + $seconds) > $hs8){
                                      // die ("GENERO HS DE MAS ".$hcita->format('H:i')." ".$hllega->format('H:i'));
                                    }
                                 }
                                 
                                 $second = $saleVacio->format('U') - $hFinUltOrden->format('U'); ///evalua si debe hacer un corte o no en el turno
                                 if ($saleVacio > $corte12){  ///inicia el servicio despues del corte de las 12 hs
                                    $min100+= $saleVacio->format('U') - $hcita->format('U');
                                   // die("$row[apellido]  hs: $min100 ");
                                 }
                                 else{
                                      if ($hcita > $corte12){
                                     // $second = $saleVacio->format('U') - $hFinUltOrden->format('U');///calcula el tiempo desde el inicio del servicio hasta el corte 12 hs
                                   // die("$row[apellido]  Finaliza despues del corte 12 ".$corte12->format('H:i')." - ".$hcita->format('H:i'));
                                    }
                                 }     */


                                 //////////////////////fin calculo horas //////////////////////////
                                 

                              }
                              else{
                                   $second = $hcita->format('U') - $hFinUltOrden->format('U');
                                   if ($second >  7200){
                                      $tabla.="<tr><td colspan='8'>Debe crear una orden a la cabecera</td></tr>";
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
                              }

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
                                                 $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $hcita->format('H:i:s'), $hcita->format('H:i:s'),
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

                     if ($ultorden[destino] != $cabecera[0]){ ///fianliza el turni del conductor
                        ///evaluar si el conductor no se le diagrama el servicio

                        $kmtpo = getKmTiempo($con, $cabecera[0], $ultorden[destino], $str, $cabecera[1], $cabecera[2], $ultorden[latiD], $ultorden[longD]);
                        if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                           throw new Exception("CE-3.0 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden[id_cliente]].") - ($cabecera[3] - $row[cityD]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                        }
                        $hfina = new DateTime($ultorden[hfina]);
                        $hfinaVacio = new DateTime($ultorden[hfina]);
                        
                        $hfinaVacio->add(new DateInterval("PT$kmtpo[1]M"));
                        
                        $nombre = "VACIO ($ultorden[cityD] - $cabecera[3])~(".$clientes[$ultorden[id_cliente]].")";
                        if (!in_array($ultorden[id_chofer_1], $conductores)) {
                           createOrden($str, $hfina->format('Y-m-d'), $nombre,
                                       $hfina->format('H:i:s'), $hfina->format('H:i:s'), $hfinaVacio->format('H:i:s'), $hfinaVacio->format('H:i:s'),
                                       $kmtpo[0], $ultorden[destino], $cabecera[0], $cliVac[0], $ultorden[id_cliente],
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
         } ///finaliza iteracion while
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
         while ($row){
               if ($i++%2==0)
                  $color = "#C0C0FF";
               else
                   $color = "#8080FF";
           //    if (!in_array($row[id],$auxOrden)){
                     $auxOrden[]=$row[id];
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

                     while (($row)&& ($cond == $row[id_chofer_1])){ ///mientras que este el mismo conductor
                           $hcita = new DateTime($row[hcita]);
                           $hsale = new DateTime($row[hsale]);
                           $hllega = new DateTime($row[hllega]);
                           $hfina = new DateTime($row[hfina]);
                           if ($ultorden){ ///indica que ya se ha procesado una orden para el interno dado
                              if ($ultorden[destino] != $row[origen]){

                                 $kmtpo = getKmTiempo($con, $ultorden[destino], $row[origen], $str, $ultorden[latiD], $ultorden[longD], $row[latiO], $row[longO]);
                                 if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                   throw new Exception("CE-1.1 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden[id_cliente]].") - ($cabecera[3] - $row[cityD]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                 }
                                 $saleVacio = new DateTime($row[hcita]);
                                 $saleVacio->sub(new DateInterval("PT$kmtpo[1]M"));

                                 $laCrea = "NO LA CREA";
                                 $nombre = "VACIO ($ultorden[cityD] - $row[cityO])~(".$clientes[$row[id_cliente]].")";
                                 if (!in_array($row[id], $ordenDeVacio)) {
                                    $laCrea = "SI LA CREA";
                                    $ordenDeVacio[]=$row[id];
                                    createOrden($str, $saleVacio->format('Y-m-d'), $nombre,
                                                $saleVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $hcita->format('H:i:s'), $hcita->format('H:i:s'),
                                                $kmtpo[0], $ultorden[destino], $row[origen], $cliVac[0], $row[id_cliente],
                                                $cond, $ultorden[id_chofer_2], $row[id_micro], $row[id], $con, $simular, $id_Diag_Vacio);
                                                //ultorden[id_chofer_1]
                                 }
                                 else{
                                      if (!$simular){
                                      //ultorden[id_chofer_1]
                                         actuaizarConductoresOrdenVacio($conn, $row[id], $cond, $str, "DE 5 ($row[nombre]) - $nombre");
                                      }
                                 }

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
                              else{
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
                                     if (!in_array($row[id], $ordenDeVacio)) { /// el vacio aun no se ha generado
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
                                     }
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
                                         if (!in_array($row[id], $ordenDeVacio)) {
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
                                          }
                                          $tabla.=  "<tr style='background-color:$color'>
                                                    <td>".$citaVacio->format('d/m/Y')."</td>
                                                    <td>".$citaVacio->format('H:i')."</td>
                                                    <td>".$saleVacio->format('H:i')."</td>
                                                    <td>($laCrea) $nombre</td>
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
                           if (!in_array($ultorden[id], $ordenDeVacio)) { ///todavia no se ha procesado el vacio de la orden
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
                           }

                           $tabla.=  "<tr style='background-color:$color'>
                                   <td>".$hfina->format('d/m/Y')."</td>
                                   <td>".$hfina->format('H:i')."</td>
                                   <td>".$hfina->format('H:i')."</td>
                                   <td>($laCrea) $nombre</td>
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
                                $laCrea = "NO LA CREA";
                                if (!in_array($ultorden[id], $ordenDeVacio)) {
                                   $laCrea = "SI LA CREA";
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
                                }
                                $tabla.=  "<tr style='background-color:$color'>
                                                     <td>".$hfina->format('d/m/Y')."</td>
                                                     <td>".$hfina->format('H:i')."</td>
                                                     <td>".$hfina->format('H:i')."</td>
                                                     <td>($laCrea) $nombre</td>
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



