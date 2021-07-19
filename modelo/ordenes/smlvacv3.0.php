<?php
session_start();
set_time_limit(0);
error_reporting(E_ALL);
//ini_set('error_reporting', 0);
    
//include ('../../controlador/bdadminv2.php');
//include ('../../controlador/ejecutar_sqlv2.php');
include('../../modelo/utils/dateutils.php');

$accion = $_POST['accion'];

if ($accion == 'sml'){
   $fecha =  dateToMysql($_POST['fecha'],'/');
   $simula = (isset($_POST['simula'])?1:0);
   $dias = $_POST['mins'];
   
  // $conn = conexcionV2(true);
  $conn = new mysqli('traficonuevo.masterbus.net', 'c0mbexpuser', 'Mb2013Exp', 'c0mbexport');
           // $mysqli = new mysqli('127.0.0.1', 'root', 'leo1979', 'master');
  mysqli_query($conn, "SET NAMES 'utf8'");

   //$simula=1;
   try{
       beginV2($conn);
       $fleteros = getConductoresFleteros($conn);
       $data = simularVacios($fecha, $_SESSION['structure'], $simula, $dias, $conn, $fleteros);
     //  $tabla = simularVaciosConductor2($fecha, $_SESSION[structure], $simula, $dias, $data[1], $data[2], $conn, $data[3], $data[4], $conds);
       print $data[0]."<br>";//.$tabla;
    //   print $tabla;
       commitV2($conn);
       mysqli_close($conn);
     }catch (Exception $e) {
                           rollbackV2($conn);
                           mysqli_close($conn);
                           print "SE PRODUJERON ERRORES  ".$e->getMessage();
                           }
}
elseif ($accion == 'vvc'){
  $conn = new mysqli('traficonuevo.masterbus.net', 'c0mbexpuser', 'Mb2013Exp', 'c0mbexport');
           // $mysqli = new mysqli('127.0.0.1', 'root', 'leo1979', 'master');
  mysqli_query($conn, "SET NAMES 'utf8'");
       $fecha =  dateToMysql($_POST['fecha'],'/');
       $data = getFechaVaciosGenerados($fecha, $_SESSION['structure'], $conn);
       mysqli_close($conn);
       print (json_encode($data));
}
elseif ($accion == 'updcnd'){
       $sql = "INSERT INTO diagramaVaciosACodnuctor (id_conductor, diagrama_sino, id_estructura) VALUES ($_POST[cnd], $_POST[sn], $_SESSION[structure]) ON DUPLICATE KEY UPDATE diagrama_sino=$_POST[sn]";
  $conn = new mysqli('127.0.0.1', 'c0mbexpuser', 'Mb2013Exp', 'c0mbexport');
           // $mysqli = new mysqli('127.0.0.1', 'root', 'leo1979', 'master');
  mysqli_query($conn, "SET NAMES 'utf8'");
       mysqli_query($conn, $sql);
       mysqli_close($conn);
       print (json_encode($data));
}

function getConductoresFleteros($conn){
          if (!$conn)
              throw new Exception("NO HAY CONEXCION");
         $sql = "SELECT id_empleado
                 FROM empleados e
                 WHERE id_empleador not in (1, 51)";
         $result = mysqli_query($conn, $sql);
         $data = array();
         while ($row = mysqli_fetch_array($result)){
               $data[] = $row[0];
         }
         return $data;
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

function getClienteVacio($conn, $str){
         $sql = "SELECT valor
                 FROM opciones o
                 where opcion = 'cliente-vacio' and id_estructura = $str";
         $result = mysqli_query($conn, $sql);
         $data = array();
         if ($row = mysqli_fetch_array($result)){
            $data[0]=$row[0];
         }
         return $data;
}

function getKmTiempo($conn, $origen, $destino, $str, $latiO, $longO, $latiD, $longD, $log = ""){    ///recupera la distancia y el tiempo de viaje desde el origen al destino
         $sql = "SELECT distancia, round(tiempo/60)
                 FROM distanciasRecorridos
                 where id_origen = $origen and id_estructura_origen = $str and id_destino = $destino and id_estructura_destino = $str";
        // die($sql);
         $result = mysqli_query($conn, $sql);
         $data = array();
         if ($row = mysqli_fetch_array($result)){
            $data[0] = $row[0];
            $data[1] = $row[1];
         }
         else{
          //    die("$origen, $destino, $str, $latiO, $longO, $latiD, $longD");
              //realiza una llamada para calcular el tiempo y la distancoia entre los dos puntos
              $data = file_get_contents("https://maps.googleapis.com/maps/api/distancematrix/json?units=metric&origins=$latiO,$longO&destinations=$latiD,$longD&key=AIzaSyCwAptX6vQb5EmgcsNeC442bFRNui1xA6A");
              $data_array = json_decode($data,true);
            //  print_r($data_array);
              $km = ($data_array['rows'][0]['elements'][0]['distance']['value']/1000);
              $tiempo = ($data_array['rows'][0]['elements'][0]['duration']['value']);
              if (!isset($km) || !isset($tiempo)){
                 $sql = "SELECT upper(ciudad) as origen, (select upper(ciudad) from ciudades where id = $destino and id_estructura = $str) as destino
                         FROM ciudades
                         where id = $origen and id_estructura = $str";
                 $result = mysqli_query($conn, $sql)or die(mysqli_error($conn)." ".$sql);
                 if ($row = mysqli_fetch_row($result)){
                      throw new Exception("No se pudo calcular la ruta para el recorrido ($log) $row[0] - $row[1] <a href='../../vista/ordenes/modkmtpo.php?des=$origen&has=$destino' title='Crear' target='_blank'><b><h2>Click Aqui Para Agregar Recorrido Manualmente</h2></b></a>");
                 }
              }
              $sql = "INSERT INTO distanciasRecorridos (id_origen, id_estructura_origen, id_destino, id_estructura_destino, distancia, tiempo)
                      VALUES ($origen, $str, $destino, $str, $km, $tiempo)";
              mysqli_query($conn, $sql);
              $data[0]=$km;
              $data[1]=$tiempo;
         }
         return $data;
}

function getDataGPS($con, $str){
}

function createOrden($str, $fservicio, $nombre, $hcitacion, $hsalida, $hllegada, $hfinserv, $km, $origen, $destino, $cliente, $afectadoACliente,
                     $chofer_1, $chofer_2, $micro, $id_ordenServicio, $conn, $simular, $id_diag, $conds, $place = 0, $line = "")
{
$id_chofer_1 = ($chofer_2?$chofer_2:'NULL');
$id_micro = ($micro?$micro:'NULL');
if (!$destino){
   if ($origen == 1) {
      if (mb_stristr($nombre, 'Ibicui')){
         $destino = 5;
      }
   }
}
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
      if (!in_array($chofer_1, $conds)){
         $id_OrdenVacio = inserti("ordenes", $campos, $values, $conn);
         $campos = "id, id_orden, id_estructura_orden, id_orden_vacio, id_estructura_orden_vacio, id_ordenvaciosgenerados";
         $valores = "$id_ordenServicio, $str, $id_OrdenVacio, $str, $id_diag";
         inserti("ordenesAsocVacios", $campos, $valores, $conn);
       }
    }
 //   commit($conn);

}catch (Exception $e) {

                      throw new Exception("Error al generar el vacio de la orden Numero: $id_ordenServicio  Lugra: $place".$e->getMessage());
                      // rollback($conn);
                      // die($e->getMessage());
                       };
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

function getFechaVaciosGenerados($fecha, $str, $conn){
         $sql = "SELECT o.id, apenom, fecha_creacion
                 FROM ordenesVaciosGenerados o
                 inner join usuarios u on u.id = o.id_user
                 where fecha = '$fecha' and id_estructura = $str";
         $result = mysqli_query($conn, $sql);
         $data = array();
         $data['status']=false;
         if ($row = mysqli_fetch_array($result)){
            $data['status']=true;
            $data['id']=$row[0];
            $data['user']= $row[1];
            $data['fecha'] = $row[2];
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

function getConductoresSecundarios($fecha, $str, $conn){
         $sql = "select id_chofer_1
                 from ordenes o
                 where fservicio = '$fecha' and o.id_estructura = $str and not vacio and not borrada and not suspendida and id_chofer_1 is not null and id_chofer_1 is not null
                 union all
                 select id_chofer_1
                 from ordenes o
                 where fservicio = '$fecha' and o.id_estructura = $str and not vacio and not borrada and not suspendida and id_chofer_1 is not null";
         $result = mysqli_query($conn,$sql);
         $data = array();
         while ($row = mysqli_fetch_array($result)){
            $data[]=$row[0];
         }
         return $data;
}

function actuaizarConductoresOrdenVacio($conn, $orden, $conductor, $str, $var = ""){
try{

         $sql = "SELECT o.id_chofer_2, o.id_chofer_1, o.id
                 from ordenes o
                 where id = (select id_orden_vacio from ordenesAsocVacios oa where oa.id_orden = $orden and oa.id_estructura_orden = $str)";
         $result = mysqli_query($conn, $sql);
         if ($row = mysqli_fetch_array($result)){
            if ($row['id_chofer_1']){
               if ($row['id_chofer_1'] != $conductor){
                  $sql = "UPDATE ordenes SET id_chofer_2 = $conductor WHERE id = $row[id]";
               }
            }
            else{
                 if ($row[id_chofer_2] != $conductor){
                     $sql = "UPDATE ordenes SET id_chofer_1 = $conductor WHERE id = $row[id]";
                 }
            }
            mysqli_query($conn, $sql);
            return true;
         }
         else{
               return false;
                
         }
}catch (Exception $e) {
                      throw new Exception("Error al generar el vacio de la orden Numero".$e->getMessage());
                       };
}

function getOrdenesTurismo($str, $fecha, $conn){

      $sql = "SELECT o.id
              FROM ordenes_turismo ot
              inner join ordenes o on o.id = ot.id_orden and o.id_estructura = ot.id_estructura_orden
              where fservicio = '$fecha' and o.id_estructura = $str";
       $result = mysqli_query($conn,$sql);
       $data = array();
       while ($row = mysqli_fetch_array($result)){
          $data[]=$row[0];
       }
       return $data;              

}

function imprimir($cita, $sale, $llega, $hfina, $nombre, $conductor, $cliente, $clientes = array(), $color,  $linea = "0", $cliVac = ""){
        if (is_string($llega) || is_integer($hfina)){
            throw new Exception("Nombre orden:  ".$nombre." // Linea:  $linea");
        }
        $data  =  "<tr style='background-color:$color'>
                        <td>".$cita->format('d/m/Y')."</td>
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


/*function getSQLConductore($fecha, $str){
  $sql = "SELECT o.id, o.id_cliente, o.id_chofer_1,
                        concat(fservicio,' ',hcitacion) as hcita,
                        concat(if(hsalida < hcitacion, DATE_ADD(fservicio, INTERVAL 1 DAY), fservicio),' ',hsalida) as hsale,
                        concat(if(hllegada < hsalida, DATE_ADD(fservicio, INTERVAL 1 DAY), fservicio),' ',hllegada) as hllega,
                        concat(if(hllegada < hsalida, DATE_ADD(fservicio, INTERVAL 1 DAY), fservicio),' ',hfinservicio) as hfina,
                        o.nombre, id_cliente,
                        origen.id as origen, origen.lati as latiO, origen.long as longO, destino.id as destino, destino.lati as latiD, destino.long as longD,
                        origen.ciudad as cityO, destino.ciudad as cityD, e1.apellido, e1.nombre as nom, id_chofer_1, id_micro,
                        cico1.id as idCityC1, cico1.ciudad as cityC1, cico1.lati as latCico1, cico1.long as longCico1
          FROM (
                select id_chofer_1, id, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, fservicio, hsalida, hcitacion, hllegada, nombre, id_micro, hfinservicio
                from ordenes o
                where fservicio = '$fecha' and o.id_estructura = $str and not vacio and not borrada and not suspendida and id_chofer_1 is not null and id_chofer_1 is not null
                union all
                select id_chofer_1, id, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, fservicio, hsalida, hcitacion, hllegada, nombre, id_micro, hfinservicio
                from ordenes o
                where fservicio = '$fecha' and o.id_estructura = $str and not vacio and not borrada and not suspendida and id_chofer_1 is not null and id_chofer_1 is not null
                union all
                select id_chofer_1, id, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, fservicio, hsalida, hcitacion, hllegada, nombre, id_micro, hfinservicio
                from ordenes o
                where fservicio = '$fecha' and o.id_estructura = $str and not vacio and not borrada and not suspendida and id_chofer_1 is not null and id_chofer_1 is null
                union all
                select id_chofer_1, id, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, fservicio, hsalida, hcitacion, hllegada, nombre, id_micro, hfinservicio
                from ordenes o
                where fservicio = '$fecha' and o.id_estructura = $str and not vacio and not borrada and not suspendida and id_chofer_1 is null and id_chofer_1 is not null
              ) o
         INNER JOIN empleados e1 on e1.id_empleado = o.id_chofer_1
         LEFT JOIN ciudades origen ON origen.id = o.id_ciudad_origen and origen.id_estructura = o.id_estructura_ciudad_origen
         LEFT JOIN ciudades destino ON destino.id = o.id_ciudad_destino and destino.id_estructura = o.id_estructura_ciudad_destino
         LEFT JOIN ciudades cico1 on cico1.id = e1.id_ciudad
         ORDER BY e1.apellido, e1.id_empleado, hsalida";
  return $sql;
}*/

function getSQLConductore($fecha, $str){
  $sql = "SELECT o.id, o.id_cliente, o.id_chofer_1,
                        concat(fservicio,' ',hcitacion) as hcita,
                        concat(if(hsalida < hcitacion, DATE_ADD(fservicio, INTERVAL 1 DAY), fservicio),' ',hsalida) as hsale,
                        concat(if(hllegada < hsalida, DATE_ADD(fservicio, INTERVAL 1 DAY), fservicio),' ',hllegada) as hllega,
                        concat(if(hllegada < hsalida, DATE_ADD(fservicio, INTERVAL 1 DAY), fservicio),' ',hfinservicio) as hfina,
                        o.nombre, id_cliente,
                        origen.id as origen, origen.lati as latiO, origen.long as longO, destino.id as destino, destino.lati as latiD, destino.long as longD,
                        origen.ciudad as cityO, destino.ciudad as cityD, e1.apellido, e1.nombre as nom, id_chofer_1, id_micro,
                        cico1.id as idCityC1, cico1.ciudad as cityC1, cico1.lati as latCico1, cico1.long as longCico1
          FROM (
                select id_chofer_1, id, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, fservicio, hsalida, hcitacion, hllegada, nombre, id_micro, hfinservicio
                from ordenes o
                where fservicio = '$fecha' and o.id_estructura = $str and not vacio and not borrada and not suspendida and id_chofer_1 is not null and id_chofer_2 is not null
                union all
                select id_chofer_2, id, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, fservicio, hsalida, hcitacion, hllegada, nombre, id_micro, hfinservicio
                from ordenes o
                where fservicio = '$fecha' and o.id_estructura = $str and not vacio and not borrada and not suspendida and id_chofer_1 is not null and id_chofer_2 is not null
                union all
                select id_chofer_1, id, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, fservicio, hsalida, hcitacion, hllegada, nombre, id_micro, hfinservicio
                from ordenes o
                where fservicio = '$fecha' and o.id_estructura = $str and not vacio and not borrada and not suspendida and id_chofer_1 is not null and id_chofer_2 is null
                union all
                select id_chofer_2, id, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, fservicio, hsalida, hcitacion, hllegada, nombre, id_micro, hfinservicio
                from ordenes o
                where fservicio = '$fecha' and o.id_estructura = $str and not vacio and not borrada and not suspendida and id_chofer_1 is null and id_chofer_2 is not null
              ) o
         INNER JOIN empleados e1 on e1.id_empleado = o.id_chofer_1
         LEFT JOIN ciudades origen ON origen.id = o.id_ciudad_origen and origen.id_estructura = o.id_estructura_ciudad_origen
         LEFT JOIN ciudades destino ON destino.id = o.id_ciudad_destino and destino.id_estructura = o.id_estructura_ciudad_destino
         LEFT JOIN ciudades cico1 on cico1.id = e1.id_ciudad
         ORDER BY e1.apellido, e1.id_empleado, hsalida";
  return $sql;
}

function encabezadoTablaConductor($nombre, $apellido, $ciudad, $color){
  $tabla = "<tr style='background-color:$color'><td colspan='8'>CONDUCTOR $apellido, $nombre   ($ciudad )</td></tr>
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
  return $tabla;
}


function simularVacios($fec, $st, $simular, $mcita, $conn, $conds, $amplitud = 3600, $amplitudCondFijo = 3600){
//throw new Exception(getSQLConductore($fec, $st));


$fecha = $fec;
$str = $st;
$minCitacion = $mcita;
$cabecera = dataCabecera($conn, $str);   // array(id, lati, long, ciudad)  representa el lugar que se indica como cabecera de recorrido
$cliVac =  getClienteVacio($conn, $str);
$clientes = getClientes($conn, $str);


       
$result = mysqli_query($conn, getSQLConductore($fec, $st));
$row = mysqli_fetch_array($result);
$ultcoche="";
$ultcondu="";
$ultorden="";
$tabla="";
$ordenYaProcesada = array();
$id_Diag_Vacio;
	
	
	
try{
    $fleteros = $conds;
    $auxCond = getConductoresFleteros($fecha, $str, $conn);
    $auxOrden = array();
    $ordenesProcesadas = array();
	  $color = "";
    $conductores = getConductoresExcluidos($str, $conn);
    $ordTur = getOrdenesTurismo($str, $fecha, $conn);
    $i=0;
    $id_Diag_Vacio = "";
    $conductor = "";
    if (!$simular){
        $id_Diag_Vacio = inserti("ordenesVaciosGenerados", "id, fecha, id_user, fecha_creacion, id_estructura", "'$fecha', $_SESSION[userid], now(), $_SESSION[structure]", $conn);
    }
    $tabla = "<style>
                    .dge {
                            font-family: serif;
                          }
              </style>
              <table border='1' class='dge' width='100%'>";

         while ($row){
               $class = ($i%2)?'#ffffff':'#aaaaaa'; 
               $i++;
               if ((!in_array($row['id_chofer_1'], $fleteros)) && (!in_array($row['id'], $ordTur))) /////si el conductor no es un fletero o la orden no es de turismo comienza a diagramar los vacios
               {
                     $cond = $row['id_chofer_1'];
                     $tabla.= encabezadoTablaConductor($row['apellido'], $row['nom'], $row['cityC1'], $class); ////imprime el encabezado para el codnuctore actual
						
                     while (($row)&& ($cond == $row['id_chofer_1'])){ ///mientras que este el mismo conductor
                         $hcita = '';
                         $hsale = '';
                         $hllega = '';
                         $hfina = '';
                         try{
                             $hcita = new DateTime($row['hcita']);
                             $hsale = new DateTime($row['hsale']);
                             $hllega = new DateTime($row['hllega']);
                             $hfina = new DateTime($row['hfina']);
                         }catch (Exception $e) {throw new Exception($row['id']);}                         
                         if ($ultorden) ///indica que ya se ha procesado al menos una orden para el conductor dado
                         {                            
                              $hFinUltOrden = new DateTime($ultorden['hfina']); /// horario en el que finaliza la ultima orden
                              $second = $hcita->format('U') - $hFinUltOrden->format('U'); ///calcula el tiempo entre entre el fin del servicio actual y el inicio del proximo
                              $generarOrdenLugarResidenciaAOrigen = false;
                              $generarOrdenDestinoAOrigen = false;
                              $generarOrdenCabeceraAOrigen = false;
                              if (in_array($ultorden['id_chofer_1'], $conductores)){ ///el conductor se queda con el coche  ##A##                              
                                  if ($ultorden['idCityC1'] == $ultorden['destino']){ ///termino el servicio donde vive el conductor ##B##
                                      ////Debe generar una orden desde la ciudad de residencia del conductor al lugar de origen de la orden actual
                                      $generarOrdenLugarResidenciaAOrigen = true;
                                  }
                                  else{///termino en un lugar distino al de donde vive - Debe calcular si el tiempo le da para ir a la casa
                                      
                                      //calcula los datos del viaje desde el destino al lugar de residencia del conductor
                                      $kmtpoDestResidencia = getKmTiempo($conn, $ultorden['destino'], $ultorden['idCityC1'], $str, $ultorden['latiD'], $ultorden['longD'], $ultorden['latCico1'], $ultorden['longCico1'], 1); 
                                      if (!($kmtpoDestResidencia[1] >= 0)  ||  !($kmtpoDestResidencia[0] >= 0)){
                                          throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden['id_cliente']].") - ($ultorden[cityD] - $ultorden[cityC1]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                      }                                      
                                      //calcula los datos del viaje desde el lugar de residencia del conductor al lugar de origen de la orden
                                      $kmtpoResidenciaOrigen = getKmTiempo($conn, $row['idCityC1'], $row['origen'], $str, $row['latCico1'], $row['longCico1'], $row['latiO'], $row['longO'],2);//calcula la distancia desde la cabecera a la salida del recorrido
                                      if (!($kmtpoResidenciaOrigen[1] >= 0)  ||  !($kmtpoResidenciaOrigen[0] >= 0)){
                                          throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row['id_cliente']].") - ($row[cityC1] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                      }                                      
                                      $tiempoViajeLlendoALaCasa = $kmtpoDestResidencia[1]+$kmtpoResidenciaOrigen[1];
                                      if (($second - $tiempoViajeLlendoALaCasa) > $amplitudCondFijo){ ///el tiempo le da para ir al lugar de residencia del conductor??????
                                          ////Tiene tiempo de ir al lugar de residencia
                                          ////debe generar una orden del destino de la orden anterior al lugar de residencia del conductor       
                                          $citaVacio = clone $hFinUltOrden;
                                          $llegaVacio = clone $hFinUltOrden;
                                          try{
                                              $llegaVacio->add(new DateInterval("PT$kmtpoDestResidencia[1]M"));
                                              if (!$llegaVacio)
                                                  throw new Exception("ERROR LINEA 485");
                                          }
                                          catch (Exception $e) {  throw new Exception("ERROR LINEA 487");}
                                          $nombre = "VACIO ($ultorden[cityD] - $ultorden[cityC1])~(".$clientes[$ultorden['id_cliente']].")  -- second: $second   -- tpo. vta $tiempoViajeLlendoALaCasa - $ultorden[destino] $ultorden[idCityC1]  ////  $row[idCityC1], $row[origen]";
                                          if (in_array($ultorden['id'], $ordenesProcesadas)) // ya ha procesado la orden, solo debe actualizar el segundo condutor de la orden
                                          {
                                              actuaizarConductoresOrdenVacio($conn, $ultorden['id'], $ultorden['id_chofer_1'], $str);
                                          }
                                          else{                                              
                                              //////genera un vacio desde el destino de la ultima orden al lugar de residencia del conductor
                                              createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                  $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                  $kmtpo[0], $ultorden['destino'], $ultorden['idCityC1'], $cliVac[0], $ultorden['id_cliente'],
                                                  $ultorden['id_chofer_1'], $ultorden['id_chofer_1'], $ultorden['id_micro'], $ultorden['id'], $conn, $simular, $id_Diag_Vacio, $conds, 1);              
                                              if ($row['idCityC1'] == $row['origen']){///se encuentra donde incia el servicio
                                                  $generarOrdenLugarResidenciaAOrigen = true;
                                              }                                              
                                          } 
                                          //// imprime el servicio desde el ultimo destino al lugar de residencia del conductor
                                          $tabla.= imprimir($citaVacio, $citaVacio, $llegaVacio, $llegaVacio, $nombre, $conductor, $ultorden['id_cliente'], $clientes, $color, 506); 
                                      }
                                      else{
                                          //no tiene tiempo de ir al lugar de residencia
                                          $generarOrdenDestinoAOrigen = true; ///flag para indicar que debe generar una orden del destino ultimo al origen actual
                                      }
                                  }                                  
                              }
                              else{////El conductor no se queda con el coche
                                  $nombre = "";
                                  if (($ultorden['destino'] == $cabecera[0])){//termino en la cabecera
                                      //SI
                                      if ($row['origen'] != $cabecera[0]){
                                          //calcula la distancia desde la cabecera a la salida del recorrido
                                          $kmtpoCabeceraOrigen = getKmTiempo($conn, $cabecera[0], $row['origen'], $str, $cabecera[1], $cabecera[2], $row['latiO'], $row['longO'],3);
                                          if (!($kmtpoCabeceraOrigen[1] >= 0)  ||  !($kmtpoCabeceraOrigen[0] >= 0)){
                                              throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row['id_cliente']].") - ($cabecera[3] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                          }                                      
                                          $llegaVacio = clone $hcita;
                                          $saleVacio = clone $hcita;
                                          try{
                                              $saleVacio->sub(new DateInterval("PT$kmtpoCabeceraOrigen[1]M"));
                                          } catch (Exception $e){throw new Exception("ERROR LINEA 525");}
                                          $nombre = "VACIO ($cabecera[3] - $row[cityO])~(".$clientes[$row['id_cliente']].")";
                                          if (in_array($row['id'], $ordenesProcesadas)){ // ya ha procesado la orden, solo debe actualizar el segundo condutor de la orden                                     
                                              actuaizarConductoresOrdenVacio($conn, $row['id'], $row['id_chofer_1'], $str);
                                             // $tabla.= imprimir($citaVacio, $citaVacio, $llegaVacio, $llegaVacio, $nombre, $conductor, $row['id_cliente'], $clientes, $color, 521);                                          
                                         }
                                         else{
                                             //Debe generar un vacio desde la cabecera al origen del servicio
                                             createOrden($str, $saleVacio->format('Y-m-d'), $nombre,
                                                 $saleVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                 $kmtpo[0], $cabecera[0], $row['origen'], $cliVac[0], $row['id_cliente'],
                                                 $row['id_chofer_1'], $row['id_chofer_1'], $row['id_micro'], $row['id'], $conn, $simular, $id_Diag_Vacio, $conds, 2);
                                         }
                                         $tabla.= imprimir($saleVacio, $saleVacio, $llegaVacio, $llegaVacio, $nombre, $conductor, $row['id_cliente'], $clientes, $color, 540, $cliVac[0]);
                                      }
                                  }
                                  else{///no termino en la cabecera
                                      ///debe calcular los tiempos para evaluar si puede ir hasta la cabecera o no
                                      
                                      ///calcula distancia del ultimo destino a la cabecera
                                      $kmtpoDestinoCabecera = getKmTiempo($conn, $ultorden['destino'], $cabecera[0], $str, $ultorden['latiD'], $ultorden['longD'], $cabecera[1], $cabecera[2],4); 
                                      if (!($kmtpoDestinoCabecera[1] >= 0)  ||  !($kmtpoDestinoCabecera[0] >= 0)){
                                          throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden['id_cliente']].") - ($ultorden[cityD] - $cabecera[3]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                      }
                                      ///calcula distancia entre la cabecera y el origne
                                      $kmtpoCabeceraOrigen = getKmTiempo($conn, $cabecera[0], $row['origen'], $str, $cabecera[1], $cabecera[2], $row['latiO'], $row['longO'],5);
                                      if (!($kmtpoCabeceraOrigen[1] >= 0)  ||  !($kmtpoCabeceraOrigen[0] >= 0)){
                                          throw new Exception("CE-2.0 No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row['id_cliente']].") - ($cabecera[3] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                      }
                                      $tiempoViajeLlendoACabecera = $kmtpoDestinoCabecera[1]+$kmtpoCabeceraOrigen[1];
                                      if (($second - $tiempoViajeLlendoACabecera) > $amplitud){///tiene timpo de ir a la cabecera
                                          //SI tiene tiempo de ir a la cabecera  -  Generea un vacio desde donde finalizo a la cabecera
                                          $citaVacio = clone $hFinUltOrden;
                                          $llegaVacio = clone $hFinUltOrden;
                                          try{
                                              $llegaVacio->add(new DateInterval("PT$kmtpoDestinoCabecera[1]M"));
                                              if (!$llegaVacio)
                                                  throw new Exception("ERROR LINEA 561");
                                          }
                                          catch (Exception $e) {  throw new Exception("ERROR LINEA 563");}
                                          $nombre = "VACIO ($ultorden[cityD] - $cabecera[3]) ~(".$clientes[$ultorden['id_cliente']].")"; 
                                          if (in_array($ultorden['id'], $ordenesProcesadas)){ // ya ha procesado la orden, solo debe actualizar el segundo condutor de la orden
                                              actuaizarConductoresOrdenVacio($conn, $ultorden['id'], $ultorden['id_chofer_1'], $str);
                                              $tabla.= imprimir($citaVacio, $citaVacio, $llegaVacio, $llegaVacio, $nombre, $conductor, $row['id_cliente'], $clientes, $color, 564, $cliVac[0]);
                                          }
                                          else{                                                                                                          
                                              //////genera un vacio desde el destino de la ultima orden a la cabecera
                                              createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                  $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                  $kmtpo[0], $ultorden['destino'], $cabecera[0], $cliVac[0], $ultorden['id_cliente'],
                                                  $ultorden['id_chofer_1'], $ultorden['id_chofer_1'], $ultorden['id_micro'], $ultorden['id'], $conn, $simular, $id_Diag_Vacio, $conds, 1);
                                              $tabla.= imprimir($citaVacio, $citaVacio, $llegaVacio, $llegaVacio, $nombre, $conductor, $cliVac[0], $clientes, $color, 579, $cliVac[0]);
                                              if ($row['origen'] != $cabecera[0]){
                                                  $generarOrdenCabeceraAOrigen = true;
                                              }
                                          }
                                      }
                                      else{///No tiene tiempo de ir a la cabecera
                                              $generarOrdenDestinoAOrigen = true;                                                                                    
                                      }
                                  }
                                  
                              }
                              if (($generarOrdenDestinoAOrigen) && ($ultorden['destino'] != $row['origen'])){///debe generar una orden desde el destino ultimo al origen actual
                                  $nombre = "VACIO ($ultorden[cityD] - $row[cityO])~(".$clientes[$row['id_cliente']].")";
                                  if (in_array($row['id'], $ordenesProcesadas)) // ya ha procesado la orden, solo debe actualizar el segundo condutor de la orden
                                  {
                                      actuaizarConductoresOrdenVacio($conn, $row['id'], $row['id_chofer_1'], $str);                                     
                                  }
                                  else
                                  {
                                      $kmtpo = getKmTiempo($conn, $ultorden['destino'], $row['origen'], $str, $ultorden['latiD'], $ultorden['longD'], $row['latiO'], $row['latiO'],6);
                                      if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                          throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row['id_cliente']].") - ($ultorden[cityD] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                      }
                                      $saleVacio = clone $hFinUltOrden;
                                      $llegaVacio = clone $saleVacio;
                                      try{
                                          $llegaVacio->add(new DateInterval("PT$kmtpo[1]M"));
                                      }
                                      catch (Exception $e) {  throw new Exception("ERROR LINEA 599");}
                                      createOrden($str, $saleVacio->format('Y-m-d'), $nombre,
                                          $saleVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                          $kmtpo[0], $ultorden['destino'], $row['origen'], $cliVac[0], $row['id_cliente'],
                                          $row['id_chofer_1'], $row['id_chofer_1'], $row['id_micro'], $row['id'], $conn, $simular, $id_Diag_Vacio, $conds, 3);                                      
                                  }
                                  $tabla.= imprimir($saleVacio, $saleVacio, $llegaVacio, $llegaVacio, $nombre, $conductor, $row['id_cliente'], $clientes, $color, 614, $cliVac[0]);
                              }
                              if (($generarOrdenLugarResidenciaAOrigen) && ($row['idCityC1'] != $row['origen'])){//genera una orden desde el lugar de residencia del conductor al origen del servicio
                                  $nombre = "VACIO ($row[cityC1] - $row[cityO])~(".$clientes[$row['id_cliente']].") --5";
                                  if (in_array($row['id'], $ordenesProcesadas)) // ya ha procesado la orden, solo debe actualizar el segundo condutor de la orden
                                  {
                                      actuaizarConductoresOrdenVacio($conn, $row['id'], $row['id_chofer_1'], $str);
                                  }
                                  else{
                                      
                                      $kmtpo = getKmTiempo($conn, $row['idCityC1'], $row['origen'], $str, $row['latCico1'], $row['longCico1'], $row['latiO'], $row['longO'],7);//calcula la distancia desde la cabecera a la salida del recorrido
                                      if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                          throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row['id_cliente']].") - ($row[cityC1] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                      }                                      
                                      $llegaVacio = clone $hcita;
                                      $saleVacio = clone $hcita;
                                      try{
                                          $saleVacio->sub(new DateInterval("PT$kmtpo[1]M"));
                                      }catch (Exception $e) {  throw new Exception("ERROR LINEA 438");}
                                      createOrden($str, $saleVacio->format('Y-m-d'), $nombre,
                                          $saleVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                          $kmtpo[0], $row['idCityC1'], $row['origen'], $cliVac[0], $row['id_cliente'],
                                          $row['id_chofer_1'], $row['id_chofer_1'], $row['id_micro'], $row['id'], $conn, $simular, $id_Diag_Vacio, $conds, 2);
                                  }
                                  $tabla.= imprimir($saleVacio, $saleVacio, $llegaVacio, $llegaVacio,$nombre, $conductor, $row['id_cliente'], $clientes, $color, 638, $cliVac[0]);
                              }
                              $tabla.= imprimir($hcita, $hsale, $hllega,  $hfina, $row['nombre'], $conductor, $row['id_cliente'], $clientes, $color, 640, $cliVac[0]);
                           }
                           else	////es la primer orden que se procesa para el conducotr
						   {        
						       if (in_array($row['id_chofer_1'], $conductores)) ///el conductor se queda con el coche
						       {
						           ///hay que verificar si el conductor vive donde sale el servicio, de ser asi no debe generar ningun vacio
						           if ($row['idCityC1'] != $row['origen'])
						           {
						               //recupera distancia y tiempo de viaje desde la ciudad donde reside el conductor y el inicio del recorrido
						               $kmtpo = getKmTiempo($conn, $row['idCityC1'], $row['origen'], $str, $row['latCico1'], $row['longCico1'], $row['latiO'], $row['longO'],8);
						               if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0))
						               {//no pudo recuperar los parametros, genera una excepcion
						                   throw new Exception("CE-4.0 / No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row[id_cliente]].") - ($cabecera[3] - $row[cityD]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
						               }						               
						               $citaVacio = clone $hcita;
						               $saleVacio = clone $hcita;
						               ///para el inicio del recorrido debe citarlo un timpo antes en general 30 minutos
						               $delayCitacion = $kmtpo[1]+$minCitacion;
						               try{
						                   $citaVacio->sub(new DateInterval("PT".$delayCitacion."M"));
						                   $saleVacio->sub(new DateInterval("PT$kmtpo[1]M"));
						               } catch (Exception $e){throw new Exception("ERROR LINEA 573"); }
						               $nombre = "VACIO ($row[cityC1] - $row[cityO])~(".$clientes[$row['id_cliente']].")";
						               if (in_array($row['id'], $ordenesProcesadas)) // ya ha procesado la orden, solo debe actualizar el segundo condutor de la orden
						               {
						                   actuaizarConductoresOrdenVacio($conn, $row['id'], $row['id_chofer_1'], $str);
						               }
						               else{
						                   createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
						                       $citaVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $hcita->format('H:i:s'), $hcita->format('H:i:s'),
						                       $kmtpo[0], $row['idCityC1'], $row['origen'], $cliVac[0], $row['id_cliente'],
						                       $row['id_chofer_1'], $row['id_chofer_1'], $row['id_micro'], $row['id'], $conn, $simular, $id_Diag_Vacio, $conds,5);
						               }
						               $tabla.= imprimir($citaVacio, $saleVacio, $hcita, $hcita, $nombre, $conductor, $cliVac[0], $clientes, $color, 674, $cliVac[0]);
						           }
						       }
						       else{///el conductor no se queda con el coche
						           if ($row['origen'] != $cabecera[0]) ///el servicio no sale de la cabecera
						           {						               
						                   //calcula los tiempos y km desde la cabecera al lugar de salida del servicio
						                   $kmtpo = getKmTiempo($conn, $cabecera[0], $row['origen'], $str, $cabecera[1], $cabecera[2], $row['latiO'], $row['longO'],9);
						                   if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0))
						                   {
						                       throw new Exception("CE-2.0 No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row['id_cliente']].") - ($cabecera[3] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
						                   }
						                   $citaVacio = clone $hcita;
						                   $saleVacio = clone $hcita;
						                   ///para el inicio del recorrido debe citarle un timpo antes en general 30 minutos
						                   $delayCitacion = $kmtpo[1]+$minCitacion;
						                   try{
						                       $citaVacio->sub(new DateInterval("PT".$delayCitacion."M"));
						                       $saleVacio->sub(new DateInterval("PT$kmtpo[1]M"));
						                   } catch (Exception $e)
						                   {
						                       throw new Exception("ERROR LINEA 600");
						                   }
						                   $nombre = "VACIO ($cabecera[3] - $row[cityO])~(".$clientes[$row['id_cliente']].")";
						                   if (in_array($row['id'], $ordenesProcesadas)) // ya ha procesado la orden, solo debe actualizar el segundo condutor de la orden
						                   {
						                       actuaizarConductoresOrdenVacio($conn, $row['id'], $row['id_chofer_1'], $str);
						                   }
						                   else
						                   {
						                       createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
						                           $citaVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $hcita->format('H:i:s'), $hcita->format('H:i:s'),
						                           $kmtpo[0], $cabecera[0], $row['origen'], $cliVac[0], $row['id_cliente'],
						                           $row['id_chofer_1'], $row['id_chofer_1'], $row['id_micro'], $row['id'], $conn, $simular, $id_Diag_Vacio, $conds,4);						                       
						                   }						                   
						                   $tabla.= imprimir($citaVacio, $saleVacio, $hcita, $hcita, $nombre, $conductor, $cliVac[0], $clientes, $color,709, $cliVac[0]);						               
						           }
						       }
						       $tabla.= imprimir($hcita, $hsale, $hllega, $hfina, $row['nombre'], $conductor, $row['id_cliente'], $clientes, $color, 712, $row['id_cliente']);
                           }
                           $ultorden = $row;
                           $row = mysqli_fetch_array($result);
                           if ($ultorden['id_chofer_1'] == $row['id_chofer_1'])
                              $ordenesProcesadas[] = $ultorden['id']; 
                     }
					           ///fianliza el turno del conductor 
                     if ($ultorden['destino'] != $cabecera[0]) ////si el servicio no termina en la cabecera debe evaluar a donde genera el vacio
					           { 

                       // if (in_array($row['id_chofer_1'], $conductores)) ///el conductor se queda con el coche
                        $kmtpo = getKmTiempo($conn, $cabecera[0], $ultorden['destino'], $str, $cabecera[1], $cabecera[2], $ultorden['latiD'], $ultorden['longD'],10);
                        if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                           throw new Exception("CE-3.0 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden['id_cliente']].") - ($cabecera[3] - $row[cityD]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                        }
                        $hfina = new DateTime($ultorden['hfina']);
                        
                      /*  if ($hfina->format('H') == 0){
                           $hfina->add(new DateInterval("P1D"));
                        }*/
                        $hfinaVacio = new DateTime($ultorden['hfina']);
                        try{
                            $hfinaVacio->add(new DateInterval("PT$kmtpo[1]M"));
                         } catch (Exception $e){  
                                                  throw new Exception("ERROR LINEA 637 - $kmtpo[1]");
                                               }
                        $nombre = "VACIO ($ultorden[cityD] - $cabecera[3])~(".$clientes[$ultorden['id_cliente']].")";
                        if (in_array($ultorden['id'], $ordenesProcesadas)) // ya ha procesado la orden, solo debe actualizar el segundo condutor de la orden
                        {
                            $ok = actuaizarConductoresOrdenVacio($conn, $ultorden['id'], $ultorden['id_chofer_1'], $str);
                            if (!$ok)
                            {
                               createOrden($str, $hfina->format('Y-m-d'), $nombre,
                                           $hfina->format('H:i:s'), $hfina->format('H:i:s'), $hfinaVacio->format('H:i:s'), $hfinaVacio->format('H:i:s'),
                                           $kmtpo[0], $ultorden['destino'], $cabecera[0], $cliVac[0], $ultorden['id_cliente'],
                                           $ultorden['id_chofer_1'], $ultorden['id_chofer_1'], $ultorden['id_micro'], $ultorden['id'], $conn, $simular, $id_Diag_Vacio, $conds,6);
                               //$nombre.="  INTENTA ACTUALIZAR";
                            }
                            $tabla.= imprimir($hfina, $hfina, $hfinaVacio, $hfinaVacio, $nombre, $conductor, $cliVac[0], $clientes, $color, 751, $cliVac[0]);
                        }
                        else ///la orden aun no ha sido procesada  (ELSE)
                        {
                            $ordenesProcesadas[] = $ultorden['id']; ///almacena la orden como procesada
                            if (!in_array($ultorden['id_chofer_1'], $conductores))  
                            {
                               createOrden($str, $hfina->format('Y-m-d'), $nombre,
                                           $hfina->format('H:i:s'), $hfina->format('H:i:s'), $hfinaVacio->format('H:i:s'), $hfinaVacio->format('H:i:s'),
                                           $kmtpo[0], $ultorden['destino'], $cabecera[0], $cliVac[0], $ultorden['id_cliente'],
                                           $ultorden['id_chofer_1'], $ultorden['id_chofer_1'], $ultorden['id_micro'], $ultorden['id'], $conn, $simular, $id_Diag_Vacio, $conds,6);
                                           
                               $tabla.= imprimir($hfina, $hfina, $hfinaVacio, $hfinaVacio, $nombre, $conductor, $cliVac[0], $clientes, $color, 763, $cliVac[0]);
                            }
                            else
                            {
                                 if ($ultorden['destino'] != $ultorden['idCityC1']){
                                    $nombre = "VACIO ($ultorden[cityD] - $ultorden[cityC1])~(".$clientes[$ultorden['id_cliente']].")";
                                    $kmtpo = getKmTiempo($conn, $ultorden['destino'], $row['idCityC1'], $str, $ultorden['latiD'], $ultorden['longD'], $row['latCico1'], $row['longCico1'],11);
                                    if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                       throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden['id_cliente']].") - ($cabecera[3] - $row[cityD]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                    }
                                    createOrden($str, $hfina->format('Y-m-d'), $nombre,
                                                $hfina->format('H:i:s'), $hfina->format('H:i:s'), $hfinaVacio->format('H:i:s'), $hfinaVacio->format('H:i:s'),
                                                $kmtpo[0], $ultorden['destino'], $row['idCityC1'], $cliVac[0], $ultorden['id_cliente'],
                                                $ultorden['id_chofer_1'], $ultorden['id_chofer_1'], $ultorden['id_micro'], $ultorden['id'], $conn, $simular, $id_Diag_Vacio, $conds,7);
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
                     }

                     $tabla.="<tr><td colspan='8'><hr></td></tr>";
                     
               $ultorden="";
               }
               else{
                    $auxOrden[] = $row['id'];
                    $row = mysqli_fetch_array($result);
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

function getKmInterCabecera($conn, $cabecera, $row, $clientes, $ultorden, $str){
    $kmcabor = getKmTiempo($conn, $cabecera[0], $row['origen'], $str, $cabecera[1], $cabecera[2], $row['latiO'], $row['longO'],13);
    //calcula la distancia desde la cabecera a la salida del recorrido
    if (!($kmcabor[1] >= 0)  ||  !($kmcabor[0] >= 0)){
        throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row['id_cliente']].") - ($cabecera[3] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
    }
    
    $kmdestcab = getKmTiempo($conn, $ultorden['destino'], $cabecera[0], $str, $ultorden['latiD'], $ultorden['longD'], $cabecera[1], $cabecera[2],14);
    ///calcula distancia del ultimo destino a la cabecera
    if (!($kmdestcab[1] >= 0)  ||  !($kmdestcab[0] >= 0)){
        throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden['id_cliente']].") - ($ultorden[cityD] - $cabecera[3]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
    }
    return $kmcabor[1]+$kmdestcab[1];
}

function imprimirOrden($fecha, $hcita, $hsale, $nombre, $cliente, $hllega, $hfina)
{
    $color="";
   $tabla=  "<tr style='background-color:$color'>
                  <td>".$fecha->format('d/m/Y')."</td>
                  <td>".$hcita->format('H:i')."</td>
                  <td>".$hsale->format('H:i')."</td>
                  <td>$nombre</td>
                  <td></td>
                  <td>".$cliente."</td>
                  <td>".$hllega->format('H:i')."</td>
                  <td>".$hfina->format('H:i')."</td>
              </tr>";   
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


function inserti($tabla, $campos, $valores, $conn=0){
         $prox = proxi($conn, $tabla);
         $sql = "INSERT INTO $tabla ($campos) VALUES ($prox, $valores)"; 
         $result = mysqli_query($conn, $sql);
         $ok=0;
         if (!mysqli_errno($conn))
         {
            $ok = mysqli_insert_id($conn);
            return $ok;
         }
         else{
              $ok = 0;
              $err= mysqli_error($conn);
              throw new Exception("$err - $sql");
         }
}

function proxi($conn, $table){
         $sql = "select AUTO_INCREMENT as prox from information_schema.TABLES where TABLE_SCHEMA='".getBDV2()."' and TABLE_NAME='$table'";
         $result = mysqli_query($conn, $sql) or die(mysqli_error($conn)." - ".$sql);
         $data = mysqli_fetch_array($result);
         return $data[0];
}    

 function getBDV2(){
        return 'c0mbexport';
       // return 'master';
 }

   function beginV2($conn){
           $sql = "SET AUTOCOMMIT=0";
           $resultado = mysqli_query($conn, $sql);
           $sql = "BEGIN";
           mysqli_query($conn, $sql);
   }   

   function commitV2($conn){
            $sql = "COMMIT";
            mysqli_query($conn, $sql);
   }   


   function rollbackV2($conn){
            $sql = "ROLLBACK";
            mysqli_query($conn, $sql);
   }   

   
   /*
    * if ($ultorden['destino'] != $row['origen']) ///la ultima orden finalizo en un lugar distinto al de donde sale la orden actual    
							  {                   

							      
                                 //deberia evaluar el tiempo de $second con el tiempo que le demora llegar desde donde finalizo a donde debe arrrancar 
                                 if ($second >  18000) ///si tiene mas de dos horas de espera genera un vacio a la cabecera
                                 { 
                                      if ($ultorden['destino'] != $cabecera[0]) ///antes de generar el vacio verifica que no se encunetre en la cabecera
                                      {
										  
                                          /////aca deberia verificar si el conductor se queda con el coche
                                          if (in_array($ultorden['id'], $ordenesProcesadas)) // ya ha procesado la orden, solo debe actualizar el segundo condutor de la orden
                                          {
                                              actuaizarConductoresOrdenVacio($conn, $ultorden['id'], $ultorden['id_chofer_1'], $str);
                                              $tabla.= imprimir($citaVacio, $citaVacio, $citaVacio, $llegaVacio, $nombre, $conductor, $ultorden['id_cliente'], $clientes, $color);
						
                                          }
                                          else
                                          {   /////tiene mas de dos horas de espera y no se encuentra en la cabecera, debe verificar si el conductor se queda con el coche
											   
                                               if (!in_array($ultorden['id_chofer_1'], $conductores)) ///el conductor se no queda con el coche
                                               {
												   
                                                   $nombre = "VACIO ($ultorden[cityD] - $cabecera[3]) ~(".$clientes[$ultorden['id_cliente']].")";
                                                   $kmtpo = getKmTiempo($conn, $ultorden['destino'], $cabecera[0], $str, $ultorden['latiD'], $ultorden['longD'], $cabecera[1], $cabecera[2]); 
												   ///calcula distancia del ultimo destino a la cabecera
                                                   if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                                      throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden['id_cliente']].") - ($ultorden[cityD] - $cabecera[3]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                                   }
                                                   $citaVacio = clone $hFinUltOrden;
                                                   $llegaVacio = clone $hFinUltOrden;
                                                   try{
                              													$llegaVacio->add(new DateInterval("PT$kmtpo[1]M"));
                              													if (!$llegaVacio)
                              														throw new Exception("ERROR LINEA 424");
                                                   } 
                                                 catch (Exception $e) {  throw new Exception("ERROR LINEA 412");}
                                                   //////genera un vacio desde el destino de la ultima orden a la cabecera
                                                   createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                               $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                               $kmtpo[0], $ultorden['destino'], $cabecera[0], $cliVac[0], $ultorden['id_cliente'],
                                                               $ultorden['id_chofer_1'], $ultorden['id_chofer_1'], $ultorden['id_micro'], $ultorden['id'], $conn, $simular, $id_Diag_Vacio, $conds, 1);
                                                   $tabla.= imprimir($citaVacio, $citaVacio, $citaVacio, $llegaVacio, $nombre, $conductor, $ultorden['id_cliente'], $clientes, $color);
                                               }
                                               else{  ///el conductor se queda con el coche, debe crear un vacio desde donde finalizo el servicio a la ciudad donde reside el conductor
                                                      /////deberia validar si le da el tiempo para ir y venir??????
				
                                                     if ($ultorden['idCityC1'] != $ultorden['destino']) ///verifica que no haya terminado en la ciudad donde reside el conductor
                                                     {
                                                           $nombre = "VACIO ($ultorden[cityD] - $ultorden[cityC1])~(".$clientes[$ultorden['id_cliente']].")";
                                                           $kmtpo = getKmTiempo($conn, $ultorden['destino'], $ultorden['idCityC1'], $str, $ultorden['latiD'], $ultorden['longD'], $ultorden['latCico1'], $ultorden['longCico1']); ///calcula distancia del ultimo destino a la cabecera
                                                           if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                                              throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden['id_cliente']].") - ($ultorden[cityD] - $ultorden[cityC1]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                                           }
                                                           $citaVacio = clone $hFinUltOrden;
                                                           $llegaVacio = clone $hFinUltOrden;
                                                           try{
                                                               $llegaVacio->add(new DateInterval("PT$kmtpo[1]M"));
                                														   if (!$llegaVacio)
                                														      throw new Exception("ERROR LINEA 424");
                                                           } 
                                                         catch (Exception $e) {  throw new Exception("ERROR LINEA 412");}
                                                           //////genera un vacio desde el destino de la ultima orden a la cabecera
                                                           createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                                       $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                                       $kmtpo[0], $ultorden['destino'], $ultorden['idCityC1'], $cliVac[0], $ultorden['id_cliente'],
                                                                       $ultorden['id_chofer_1'], $ultorden['id_chofer_1'], $ultorden['id_micro'], $ultorden['id'], $conn, $simular, $id_Diag_Vacio, $conds, 1);
                                                           $tabla.= imprimir($citaVacio, $citaVacio, $citaVacio, $llegaVacio, $nombre, $conductor, $ultorden['id_cliente'], $clientes, $color);   
                                                     }                                           

                                               }
                                          }
                                      }
									  
                                      ////una vez que esta en la cabecera debe evaluar si el servicio sale desde ahi o no
                                      if ($row['origen'] != $cabecera[0])
                                      { ///no sale de la cabecera
                                          if (in_array($row['id'], $ordenesProcesadas)) // ya ha procesado la orden, solo debe actualizar el segundo condutor de la orden
                                          {
                                              actuaizarConductoresOrdenVacio($conn, $ultorden['id'], $ultorden['id_chofer_1'], $str);
                                              $nombre = "VACIO ($row[cityC1] - $row[cityO])~(".$clientes[$row['id_cliente']].")";
                                              $tabla.= imprimir($saleVacio, $saleVacio, $saleVacio, $llegaVacio, $nombre, $conductor, $row['id_cliente'], $clientes, $color);
                                          }
                                          else
                                          {
                                            // $ordenesProcesadas[] = $row['id'];  
                                             if (!in_array($ultorden['id_chofer_1'], $conductores))///// el conductor se queda con el coche
                                             {  ////debe generar un vacio desde el lugar de residencia del conductor al lugar de salkida de la orden actual
                                                  if ($row['idCityC1'] != $row['origen'])  ///sale desde un lugar distinot a donde reside el conductor
                                                  {
                                                      $kmtpo = getKmTiempo($conn, $row['idCityC1'], $row['origen'], $str, $row['latCico1'], $row['longCico1'], $row['latiO'], $row['longO']);//calcula la distancia desde la cabecera a la salida del recorrido
                                                      if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                                         throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row['id_cliente']].") - ($row[cityC1] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                                      }
                                                      $nombre = "VACIO ($row[cityC1] - $row[cityO])~(".$clientes[$row['id_cliente']].")";
                                                      $llegaVacio = clone $hcita;
                                                      $saleVacio = clone $hcita;
                                                      try
                                                      {
                                                         $saleVacio->sub(new DateInterval("PT$kmtpo[1]M"));
                                                      } 
                                                      catch (Exception $e) {  throw new Exception("ERROR LINEA 438");}
                                                      createOrden($str, $saleVacio->format('Y-m-d'), $nombre,
                                                                 $saleVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                                 $kmtpo[0], $row['idCityC1'], $row['origen'], $cliVac[0], $row['id_cliente'],
                                                                 $row['id_chofer_1'], $row['id_chofer_1'], $row['id_micro'], $row['id'], $conn, $simular, $id_Diag_Vacio, $conds, 2);
                                                      $tabla.= imprimir($saleVacio, $saleVacio, $saleVacio, $llegaVacio, $nombre, $conductor, $row['id_cliente'], $clientes, $color);
                                                  }
                                              }
                                              else{///el conductor no se queda con el coche, debe generar un vacio desde la cabecera al lugar de salida
                                                  $kmtpo = getKmTiempo($conn, $cabecera[0], $row['origen'], $str, $cabecera[1], $cabecera[2], $row['latiO'], $row['longO']);//calcula la distancia desde la cabecera a la salida del recorrido
                                                  if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                                                     throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row['id_cliente']].") - ($cabecera[3] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                                  }
                                                  $nombre = "VACIO ($cabecera[3] - $row[cityO])~(".$clientes[$row['id_cliente']].")";
                                                  $llegaVacio = clone $hcita;
                                                  $saleVacio = clone $hcita;
                                                  try
                                                  {
                                                     $saleVacio->sub(new DateInterval("PT$kmtpo[1]M"));
                                                  } 
                                                  catch (Exception $e) {  throw new Exception("ERROR LINEA 438");}
                                                  createOrden($str, $saleVacio->format('Y-m-d'), $nombre,
                                                             $saleVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                             $kmtpo[0], $cabecera[0], $row['origen'], $cliVac[0], $row['id_cliente'],
                                                             $row['id_chofer_1'], $row['id_chofer_1'], $row['id_micro'], $row['id'], $conn, $simular, $id_Diag_Vacio, $conds, 2);
                                                  $tabla.= imprimir($saleVacio, $saleVacio, $saleVacio, $llegaVacio, $nombre, $conductor, $row['id_cliente'], $clientes, $color);                                                
                                              }
                                          }
                                      }
                                 }
                                 else//////la ultima orden finalizo en un lugar distinto al de donde sale la orden actual, pero tiene una espera menor a 2 hs. debe crear un vacio del ultimo destino al actual origen
                                 { 
              						if (in_array($row['id'], $ordenesProcesadas)) // ya ha procesado la orden, solo debe actualizar el segundo condutor de la orden
              						{
              							  actuaizarConductoresOrdenVacio($conn, $ultorden['id'], $ultorden['id_chofer_1'], $str);
                                          $nombre = "VACIO ($ultorden[cityD] - $row[cityO])~(".$clientes[$row['id_cliente']].")";
                                          $tabla.= imprimir($saleVacio, $saleVacio, $saleVacio, $llegaVacio, $nombre, $conductor, $row['id_cliente'], $clientes, $color);
              						}
              						else
              						{
                                        // $ordenesProcesadas[] = $row['id']; 
                											   $kmtpo = getKmTiempo($conn, $ultorden['destino'], $row['origen'], $str, $ultorden['latiD'], $ultorden['longD'], $row['latiO'], $row['latiO']);
                											   if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0)){
                												  throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row['id_cliente']].") - ($ultorden[cityD] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                											   }
                											   $nombre = "VACIO ($ultorden[cityD] - $row[cityO])~(".$clientes[$row['id_cliente']].")";
                											   $saleVacio = clone $hFinUltOrden;
                											   $llegaVacio = clone $saleVacio;
                                         try{
                											   $llegaVacio->add(new DateInterval("PT$kmtpo[1]M"));
                                         } 
                                             catch (Exception $e) {  throw new Exception("ERROR LINEA 465");}
                											   createOrden($str, $saleVacio->format('Y-m-d'), $nombre,
                      														  $saleVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                      														  $kmtpo[0], $ultorden['destino'], $row['origen'], $cliVac[0], $row['id_cliente'],
                      														  $row['id_chofer_1'], $row['id_chofer_1'], $row['id_micro'], $row['id'], $conn, $simular, $id_Diag_Vacio, $conds, 3);
                											   $tabla.= imprimir($saleVacio, $saleVacio, $saleVacio, $llegaVacio, $nombre, $conductor, $row['id_cliente'], $clientes, $color);
              										  }
                                 }
            -FIN ELSE 15- }
                              elseif ($ultorden['destino'] == $row['origen'])
							  {  ///el ultimo servicio termino donde arranca el actual
                            
                                $second = $hcita->format('U') - $hFinUltOrden->format('U'); ///calcula el tiempo entre servicios para ver si debe generar un vacio a la cabecera
                                if ($second >  10800) //// hay una espera mayor a tres horas
                                {
                                    if ($cabecera[0] != $ultorden['destino'])//no se encuentra en la cabecera  
                                    {
                  										if (in_array($row['id_chofer_1'], $conductores)) ///el conductor se queda con el coche
                  										{
                  											if ($row['idCityC1'] != $row['origen']) //el conductor se queda con el coche, el servicio no sale de donde vive el conductor, debe generar un vacio hasta el lugar donde vive el conductor y la vuelta correspondiente
                  											{
                  												 //////genera un vacio desde el destino de la ultima orden al lugar de residencia del conductor
                  												 $nombre = "VACIO ($ultorden[cityD] - $row[cityC1])~(".$clientes[$ultorden['id_cliente']].")";
                  												 $kmtpo = getKmTiempo($conn, $ultorden['destino'], $row['idCityC1'], $str, $ultorden['latiD'], $ultorden['longD'], $row['latCico1'], $row['longCico1']); ///calcula distancia del ultimo destino a la cabecera
                  												 if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0))
                  												 {
                  													throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden['id_cliente']].") - ($ultorden[cityD] - $row[cityC1]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                  												 }

																 if (in_array($ultorden['id'], $ordenesProcesadas)) // ya ha procesado la orden, solo debe actualizar el segundo condutor de la orden
																 {
																	actuaizarConductoresOrdenVacio($conn, $ultorden['id'], $ultorden['id_chofer_1'], $str);
																	$tabla.= imprimir($citaVacio, $citaVacio, $citaVacio, $llegaVacio, $nombre, $conductor, $ultorden['id_cliente'], $clientes, $color);
																 }
                  												 else
                  												 {
																	//	$ordenesProcesadas[] = $row['id'];  
                    													 //////servicio destino ultima orden ciudad del conductor//////
                    													 $citaVacio = clone $hFinUltOrden;
                    													 $llegaVacio = clone $hFinUltOrden;
                    													 try{
                    														$llegaVacio->add(new DateInterval("PT$kmtpo[1]M"));
                    													 } 
                    													 catch (Exception $e) {  throw new Exception("ERROR LINEA 509");}
                    													 //////genera un vacio desde el destino de la ultima orden a la ciudad donde reside el conductor
                    													 createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                    																 $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                    																 $kmtpo[0], $ultorden['destino'], $cabecera[0], $cliVac[0], $ultorden['id_cliente'],
                    																 $ultorden['id_chofer_1'], $ultorden['id_chofer_1'], $ultorden['id_micro'], $ultorden['id'], $conn, $simular, $id_Diag_Vacio, $conds, 1);
                    													 $tabla.= imprimir($citaVacio, $citaVacio, $citaVacio, $llegaVacio, $nombre." ONE", $conductor, $ultorden['id_cliente'], $clientes, $color);
                    													 ///////////////////////////////////////////////////
                    													 ////////////////servicio ciudad del conductor origen de la orden actual////////////
                    													 $citaVacio = clone $hcita;
                    													 $llegaVacio = clone $hcita;
                    													 try{
                    														$citaVacio->sub(new DateInterval("PT$kmtpo[1]M"));
                    													 } 
                    													 catch (Exception $e) {  throw new Exception("ERROR LINEA 509");}
                    													 //////genera un vacio desde el destino de la ultima orden a la ciudad donde reside el conductor
                    													 createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                    																 $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                    																 $kmtpo[0], $ultorden['destino'], $cabecera[0], $cliVac[0], $ultorden['id_cliente'],
                    																 $ultorden['id_chofer_1'], $ultorden['id_chofer_1'], $ultorden['id_micro'], $ultorden['id'], $conn, $simular, $id_Diag_Vacio, $conds, 1);
                    													 $tabla.= imprimir($citaVacio, $citaVacio, $citaVacio, $llegaVacio, $nombre."TWO", $conductor, $ultorden['id_cliente'], $clientes, $color);
                    													 //////////////////////////////////
                    												 }
                    											}
										                  }
                               //     1  if (!in_array($ultorden[id_chofer_1], $conductores))
                                  //    {/////FIN if espera mas de 3 horas conductor no se queda con el coche
                                             
                                           if (in_array($ultorden['id'], $ordenesProcesadas)) // ya ha procesado la orden, solo debe actualizar el segundo condutor de la orden
                                           {
                                              actuaizarConductoresOrdenVacio($conn, $ultorden['id'], $ultorden['id_chofer_1'], $str);
                                              $nombre = "VACIO ACATA ($ultorden[cityD] - $cabecera[3])~(".$clientes[$ultorden[id_cliente]].")";
                                              $tabla.= imprimir($citaVacio, $citaVacio, $citaVacio, $llegaVacio, $nombre, $conductor, $ultorden[id_cliente], $clientes, $color);
                                           }
                                           else{
                                                 //////genera un vacio desde el destino de la ultima orden a la cabecera
                    												 if (!in_array($ultorden['id_chofer_1'], $conductores))
                                             {
                                                 $nombre = "VACIO ($ultorden[cityD] - $cabecera[3])~(".$clientes[$ultorden['id_cliente']].")";
                                                 $kmtpo = getKmTiempo($conn, $ultorden['destino'], $cabecera[0], $str, $ultorden['latiD'], $ultorden['longD'], $cabecera[1], $cabecera[2]); ///calcula distancia del ultimo destino a la cabecera
                                                 if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0))
                                                 {
                                                    throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $ultorden[nombre] (".$clientes[$ultorden['id_cliente']].") - ($ultorden[cityD] - $cabecera[3]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                                 }
                                                 $citaVacio = clone $hFinUltOrden;
                                                 $llegaVacio = clone $hFinUltOrden;
                                                 try{
                          											     $llegaVacio->add(new DateInterval("PT$kmtpo[1]M"));
                                                  } 
                                                  catch (Exception $e) {  throw new Exception("ERROR LINEA 493");}
                                                 //////genera un vacio desde el destino de la ultima orden a la cabecera
                                                 createOrden($str, $citaVacio->format('Y-m-d'), $nombre,
                                                             $citaVacio->format('H:i:s'), $citaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                             $kmtpo[0], $ultorden['destino'], $cabecera[0], $cliVac[0], $ultorden['id_cliente'],
                                                             $ultorden['id_chofer_1'], $ultorden['id_chofer_1'], $ultorden['id_micro'], $ultorden['id'], $conn, $simular, $id_Diag_Vacio, $conds, 1);
                                                 $tabla.= imprimir($citaVacio, $citaVacio, $citaVacio, $llegaVacio, $nombre, $conductor, $ultorden['id_cliente'], $clientes, $color);
                                                 /////fin generar vacio ultimo destino cabecera

                                                 ///////genera un vacio desde la cabecera al lugar de salida de la orden actual
                                                 $kmtpo = getKmTiempo($conn, $cabecera[0], $row['origen'], $str, $cabecera[1], $cabecera[2], $row['latiO'], $row['longO']);//calcula la distancia desde la cabecera a la salida del recorrido
                                                 if (!($kmtpo[1] >= 0)  ||  !($kmtpo[0] >= 0))
                                                 {
                                                    throw new Exception("CE-5.0 / No se pudo calcular la ruta para el recorrido $row[nombre] (".$clientes[$row['id_cliente']].") - ($cabecera[3] - $row[cityO]) - Verifique que las dos ciudades tengan cargada su Latitud y su Longitud");
                                                 }
                                                 $nombre = "VACIO ($cabecera[3] - $row[cityO])~(".$clientes[$row['id_cliente']].")";
                                                 $llegaVacio = clone $hcita;
                                                 $saleVacio = clone $hcita;
                                                 try
                                                 {
                                                    $saleVacio->sub(new DateInterval("PT$kmtpo[1]M"));
                                                 } catch (Exception $e) 
                                                                       {  
                                                                          throw new Exception("ERROR LINEA 518");
                                                                       }
                                                 createOrden($str, $saleVacio->format('Y-m-d'), $nombre,
                                                            $saleVacio->format('H:i:s'), $saleVacio->format('H:i:s'), $llegaVacio->format('H:i:s'), $llegaVacio->format('H:i:s'),
                                                            $kmtpo[0], $cabecera[0], $row['origen'], $cliVac[0], $row['id_cliente'],
                                                            $row['id_chofer_1'], $row['id_chofer_1'], $row['id_micro'], $row['id'], $conn, $simular, $id_Diag_Vacio, $conds, 2);
                                                 $tabla.= imprimir($saleVacio, $saleVacio, $saleVacio, $llegaVacio, $nombre, $conductor, $row['id_cliente'], $clientes, $color);
												                      }
                                                //////////////fin generacio 
                                          // }
                                    //  } /////FIN if espera mas de 3 horas conductor no se queda con el coche
									}
                                }

                              }
    */


