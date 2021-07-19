<?
  session_start();
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/bdadmin.php');
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];
  if ($accion == 'fsrv'){ //codigo para filtrar los servicios
     if ($_POST['shw'] == 'filter'){ //se debe aplicar el filtro
              $estructura = STRUCTURED;
              $hdesde = $_POST['dsd']?$_POST['dsd']:'00:00';
              $hhasta = $_POST['hst']?$_POST['hst']:'24:00';
              $origen = $_POST['org']?"(id = $_POST[org]) and":"";
              $destin = $_POST['dst']?"(id = $_POST[dst]) and":"";
              $client = $_POST['cli']?"(id = $_POST[cli]) and":"";
              $cronos = $_POST['ncron']?"(nombre like '%$_POST[ncron]%') and":"";
              $sql = "SELECT s.id, date_format(s.hcitacion, '%H:%i') as citacion, date_format(s.hsalida, '%H:%i') as salida, upper(de.ciudad) as desde, upper(ha.ciudad) as hasta, upper(cl.razon_social) as cliente, upper(c.nombre) as crono
                         FROM (SELECT * FROM cronogramas WHERE $cronos (id_estructura = $_SESSION[structure]) ) c
                         inner join (SELECT * FROM ciudades WHERE  $origen (id_estructura = $_SESSION[structure])) de on (de.id = c.ciudades_id_origen) and (de.id_estructura = c.ciudades_id_estructura_origen)
                         inner join (SELECT * FROM ciudades WHERE  $destin (id_estructura = $_SESSION[structure])) ha on (ha.id = c.ciudades_id_destino) and (ha.id_estructura = c.ciudades_id_estructura_destino)
                         inner join (SELECT * FROM clientes WHERE  $client (id_estructura = $_SESSION[structure])) cl on (cl.id = c.id_cliente) and (cl.id_estructura = c.id_estructura_cliente)
                         inner join (SELECT * FROM servicios WHERE (hcitacion between '$hdesde' and '$hhasta') and (id_estructura = $_SESSION[structure])) s on (c.id = s.id_cronograma) and (c.id_estructura = s.id_estructura_cronograma)
                         WHERE (c.activo) and (s.activo) and (cl.activo)
                         order by s.hcitacion";
     }
     elseif($_POST['shw'] == 'all'){
              $sql="SELECT s.id, date_format(s.hcitacion, '%H:%i') as citacion, date_format(s.hsalida, '%H:%i') as salida, upper(de.ciudad) as desde, upper(ha.ciudad) as hasta, upper(cl.razon_social) as cliente, upper(c.nombre) as crono
                    FROM cronogramas c
                    inner join ciudades de on (de.id = c.ciudades_id_origen) and (de.id_estructura = c.ciudades_id_estructura_origen)
                    inner join ciudades ha on (ha.id = c.ciudades_id_destino) and (ha.id_estructura = c.ciudades_id_estructura_destino)
                    inner join clientes cl on (cl.id = c.id_cliente) and (cl.id_estructura = c.id_estructura_cliente)
                    inner join servicios  s on (c.id = s.id_cronograma) and (c.id_estructura = s.id_estructura_cronograma)
                    WHERE (c.id_estructura = $_SESSION[structure]) and (c.activo) and (s.activo) and (cl.activo)
                    order by s.hcitacion, cl.razon_social";
     }

              $conn = conexcion();
              $result = mysql_query($sql, $conn) or die ($sql);
              $tabla ='<input type="checkbox" id="all-empty" name="allempty" onClick="checkTodos(this)">Todos/Ninguno
                       <table id="tablita" align="center" class="tablesorter" border="0" width="85%">
                              <thead>
                                     <tr>
                                         <th>Si/No</th>
                                         <th>H. Citacion</th>
                                         <th>H. Salida</th>
                                         <th>Cliente</th>
                                         <th>Servicio</th>
                                         <th>Origen</th>
                                         <th>Destino</th>
                                     </tr>
                              </thead>
                              <tbody>';
              while($row = mysql_fetch_array($result)){
                         $tabla.="<tr>
                                      <td><input type=\"checkbox\" id=\"$row[id]\" onclick=\"cargarCheck(this);\"></td>
                                      <td>$row[1]</td>
                                      <td>$row[2]</td>
                                      <td>".htmlentities($row[5])."</td>
                                      <td>".htmlentities($row[6])."</td>
                                      <td>".htmlentities($row[3])."</td>
                                      <td>".htmlentities($row[4])."</td>
                                 </tr>";
              }
              $tabla.="</tbody>
                       </table>

                        <script>
                                             $('#tablita').tablesorter({widgets: ['zebra']});
                        </script>";
              mysql_free_result($result);
              mysql_close($conn);
              print $tabla;
  }
  elseif($accion == 'dss'){
     $fecha = explode('/', $_POST['fecd']);
     $fecha = "$fecha[2]-$fecha[1]-$fecha[0]";
     $serv = "SELECT c.vacio, c.nombre, s.hcitacion, s.hsalida, s.hllegada, s.hfinserv, c.km, s.id, c.ciudades_id_origen, c.ciudades_id_destino, c.id_cliente, c.id_cliente_vacio
              FROM cronogramas c
              inner join servicios s on (s.id_cronograma = c.id) and (s.id_estructura_cronograma)
              where (s.id_estructura = $_SESSION[structure]) and (s.id IN ($_POST[ords]))";
     $conn = conexcion();
     $result = mysql_query($serv, $conn);
     mysql_close($conn);
     while ($data = mysql_fetch_array($result)){
           $campos = "id, id_estructura, fservicio, nombre, hcitacion, hsalida, hllegada, hfinservicio, km, id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, id_estructura_cliente, finalizada, borrada";
           $values = "$_SESSION[structure], '$fecha', '$data[nombre]', '$data[hcitacion]', '$data[hsalida]', '$data[hllegada]', '$data[hfinserv]', $data[km], '$data[id]', $_SESSION[structure], $data[ciudades_id_origen], $_SESSION[structure], $data[ciudades_id_destino], $_SESSION[structure], $data[id_cliente], $_SESSION[structure], 0, 0";
           if ($data['vacio'] == 1){
              $campos.=", id_cliente_vacio, id_estructura_cliente_vacio";
              $values.=", $data[id_cliente_vacio], $_SESSION[structure]";
           }
           insert('ordenes', $campos, $values);
     }
  }
?>

