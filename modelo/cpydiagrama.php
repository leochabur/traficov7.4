<?
  set_time_limit(0);
  session_start();
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];
  
if ($accion == 'cpy'){
  $conn = conexcion();
  try{
     begin($conn);
     $ordenes = $_POST['orders'];
     $fecha = $_POST['fecha'];
     $sql = "SELECT c.nombre as nombre, o.hcitacion, o.hsalida, o.hllegada, o.hfinservicio, c.km, o.id_micro, o.id_servicio, o.id_estructura_servicio,
                    de.id as id_ciudad_origen, de.id_estructura as id_estructura_ciudad_origen, ha.id as id_ciudad_destino, ha.id_estructura as id_estructura_ciudad_destino, 
                    o.id_cliente, o.id_estructura_cliente,
                    o.id_cliente_vacio, o.id_estructura_cliente_vacio, o.id_chofer_1, o.id_estructura_chofer1, o.finalizada, o.id_chofer_2, o.id_estructura_chofer2,
                    o.borrada, o.comentario, o.vacio
                FROM (SELECT * FROM ordenes WHERE fservicio = '$_POST[forigen]' and id_estructura = ".STRUCTURED.") o
                inner join servicios s on (s.id = o.id_servicio) and (s.id_estructura = o.id_estructura_servicio)
                inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                inner join ciudades de on (de.id = c.ciudades_id_origen) and (de.id_estructura = c.ciudades_id_estructura_origen)
                inner join ciudades ha on (ha.id = c.ciudades_id_destino) and (ha.id_estructura = c.ciudades_id_estructura_destino)
                WHERE (o.id in ($ordenes))";

     $result = mysql_query($sql, $conn) or die($sql);

   //  die("numeros ".mysql_num_rows($result));

   //  @mysql_close($conn);
     $valores = "";
     $ok = 0;
     while ($data = mysql_fetch_array($result)){
           $campos = "id,id_estructura,fservicio,nombre,hcitacion,hsalida,hllegada,hfinservicio,km,id_ciudad_origen,id_estructura_ciudad_origen,id_ciudad_destino,id_estructura_ciudad_destino,id_cliente,id_estructura_cliente,finalizada,borrada,comentario, id_user, vacio";
           $valores = STRUCTURED.", '$fecha', '$data[nombre]','$data[hcitacion]','$data[hsalida]','$data[hllegada]','$data[hfinservicio]','$data[km]',$data[id_ciudad_origen],$data[id_estructura_ciudad_origen],$data[id_ciudad_destino],$data[id_estructura_ciudad_destino],$data[id_cliente],$data[id_estructura_cliente],'0','0','', $_SESSION[userid], $data[vacio]";
           if($data['id_micro']){
                   $campos.=", id_micro";
                   if ($_SESSION['structure'] == 2){
                      $valores.=", NULL";
                   }
                   else{
                        $valores.=", $data[id_micro]";
                   }
           }
           if ($data['id_servicio'] && $data['id_estructura_servicio']){
                   $campos.=", id_servicio, id_estructura_servicio";
                   $valores.=", $data[id_servicio], $data[id_estructura_servicio]";
           }
           if ($data['id_cliente_vacio']){
                   $campos.=", id_cliente_vacio, id_estructura_cliente_vacio";
                   $valores.=", $data[id_cliente_vacio], ".STRUCTURED;
           }
           if ($data['id_chofer_1']){
                   $campos.=", id_chofer_1, id_estructura_chofer1";
                   if ($_SESSION['structure'] == 2){
                      $valores.=", NULL, NULL";
                   }
                   else{
                        $valores.= ", $data[id_chofer_1], 1";
                   }
           }
           if ($data['id_chofer_2']){
                   $campos.=", id_chofer_2, id_estructura_chofer2";
                   if ($_SESSION['structure'] == 2){
                      $valores.=", NULL, NULL";
                   }
                   else{
                        $valores.= ", $data[id_chofer_2],1";
                   }
           }
           $ok+= insert('ordenes', $campos, $valores, $conn);
     }
     

   //  $conn = conexcion();
     
     $sql = "select id as id_orden
             from ordenes
             where id_estructura = $_SESSION[structure] and fservicio = '$fecha' and id_chofer_1 in (select id_empleado
                                                            from novedades n
                                                            inner join cod_novedades cn on cn.id = n.id_novedad
                                                            where ('$fecha' between desde and hasta) and (n.activa) and (afecta_diagrama) and (n.id_estructura = $_SESSION[structure])
                                                            group by id_empleado)";
     $result = mysql_query($sql, $conn);

     $campo='id_chofer_1, id_estructura_chofer1, id_user, fecha_accion';
     $value="null, null, $_SESSION[userid], now()";
     while ($row = mysql_fetch_array($result)){
           backup('ordenes', 'ordenes_modificadas', "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])", $conn);
           update('ordenes', $campo, $value, "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])", $conn);
     }
    // @mysql_close($conn);
     
    // $conn = conexcion();

     $sql = "select id as id_orden
             from ordenes
             where id_estructura = $_SESSION[structure] and fservicio = '$fecha' and id_chofer_2 in (select id_empleado
                                                            from novedades n
                                                            inner join cod_novedades cn on cn.id = n.id_novedad
                                                            where ('$fecha' between desde and hasta) and (afecta_diagrama) and (n.activa) and (n.id_estructura = $_SESSION[structure])
                                                            group by id_empleado)";
     $result = mysql_query($sql, $conn);

     $campo='id_chofer_2, id_estructura_chofer2, id_user, fecha_accion';
     $value="null, null, $_SESSION[userid], now()";
     while ($row = mysql_fetch_array($result)){
           backup('ordenes', 'ordenes_modificadas', "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])", $conn);
           update('ordenes', $campo, $value, "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])", $conn);
     }
     $sql = "INSERT INTO copiasDiagramaDiario (fecha, id_estructura, id_usuario, fecha_copia) VALUES ('$fecha', $_SESSION[structure], $_SESSION[userid], now())";
     ejecutarSQL($sql, $conn);
     
     //@mysql_close($conn);
     commit($conn);
     @mysql_close($conn);
     print (json_encode(array("status" => true)));
  }catch (Exception $e) {
                           rollback($conn);
                           mysql_close($conn);
                           print (json_encode(array("status" => false)));
                           }
}
elseif($accion == 'ydg'){
       $conn = conexcion();
       $sql = "SELECT *
               FROM copiasDiagramaDiario
               where fecha = '$_POST[fecha]' and id_estructura = $_SESSION[structure]";
       
       $result = mysql_query($sql, $conn);
       mysql_close($conn);
       if (mysql_num_rows($result)){
          print (json_encode(array("status" => true)));
       }
       else{
            print (json_encode(array("status" => false)));
       }
}
  
?>

