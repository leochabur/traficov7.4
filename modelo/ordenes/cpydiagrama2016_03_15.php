<?
  set_time_limit(0);
  session_start();
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];
  
  if ($accion == 'cpy'){
     $conn = conexcion();
     $ordenes = $_POST['orders'];
     $fecha = $_POST['fecha'];
     $sql = "SELECT * FROM estadoDiagramasDiarios e where fecha = '$_POST[forigen]' and id_estructura = ".STRUCTURED;
     $result = mysql_query($sql, $conn);
     if (mysql_num_rows($result)){
        $sql = "SELECT o.nombre, ho.hcitacion, ho.hsalida, ho.hllegada, ho.hfinservicio, o.km, o.id_micro, o.id_servicio, o.id_estructura_servicio,
                    o.id_ciudad_origen, o.id_estructura_ciudad_origen, o.id_ciudad_destino, o.id_estructura_ciudad_destino, o.id_cliente, o.id_estructura_cliente,
                    o.id_cliente_vacio, o.id_estructura_cliente_vacio, o.id_chofer_1, o.id_estructura_chofer1, o.finalizada, o.id_chofer_2, o.id_estructura_chofer2,
                    o.borrada, o.comentario, o.vacio
                FROM ordenes o
                inner join horarios_ordenes ho on ho.id = o.id
                WHERE (o.id in ($ordenes)) and (o.id_estructura = ".STRUCTURED.")";
     }
     else{
          $sql="SELECT o.nombre, ho.hcitacion, ho.hsalida, ho.hllegada, ho.hfinservicio, o.km, o.id_micro, o.id_servicio, o.id_estructura_servicio,
                    o.id_ciudad_origen, o.id_estructura_ciudad_origen, o.id_ciudad_destino, o.id_estructura_ciudad_destino, o.id_cliente, o.id_estructura_cliente,
                    o.id_cliente_vacio, o.id_estructura_cliente_vacio, o.id_chofer_1, o.id_estructura_chofer1, o.finalizada, o.id_chofer_2, o.id_estructura_chofer2,
                    o.borrada, o.comentario, o.vacio
                FROM (select * from ordenes WHERE (fservicio = '$_POST[forigen]') and (not borrada) and (id_estructura = $_SESSION[structure]) and (id in ($ordenes))) o
                INNER JOIN (select * from horarios_ordenes where fservicio between DATE_SUB(date(now()), INTERVAL 35 DAY) and date(now()) group by id_servicio) ho on ho.id_servicio = o.id_servicio
                inner join servicios s on (s.id = o.id_servicio) and (s.id_estructura = o.id_estructura_servicio)
                inner join ciudades de on (de.id = o.id_ciudad_origen) and (de.id_estructura = o.id_estructura_ciudad_origen)
                inner join ciudades ha on (ha.id = o.id_ciudad_destino) and (ha.id_estructura = o.id_estructura_ciudad_destino)
                inner join clientes cl on (cl.id = o.id_cliente) and (cl.id_estructura = o.id_estructura_cliente)";
     }
     
    // die("$sql <br> numero ".mysql_num_rows($result));
     $result = mysql_query($sql, $conn);

   //  die("numeros ".mysql_num_rows($result));

   //  @mysql_close($conn);
     $valores = "";
     $ok = 0;
     while ($data = mysql_fetch_array($result)){
           $campos = "id,id_estructura,fservicio,nombre,hcitacion,hsalida,hllegada,hfinservicio,km,id_ciudad_origen,id_estructura_ciudad_origen,id_ciudad_destino,id_estructura_ciudad_destino,id_cliente,id_estructura_cliente,finalizada,borrada,comentario, id_user, vacio";
           $valores = STRUCTURED.", '$fecha', '$data[nombre]','$data[hcitacion]','$data[hsalida]','$data[hllegada]','$data[hfinservicio]','$data[km]',$data[id_ciudad_origen],$data[id_estructura_ciudad_origen],$data[id_ciudad_destino],$data[id_estructura_ciudad_destino],$data[id_cliente],$data[id_estructura_cliente],'0','0','', $_SESSION[userid], $data[vacio]";
           if($data['id_micro']){
                   $campos.=", id_micro";
                   $valores.=", $data[id_micro]";
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
                   $valores.= ", $data[id_chofer_1], 1";
           }
           if ($data['id_chofer_2']){
                   $campos.=", id_chofer_2, id_estructura_chofer2";
                   $valores.= ", $data[id_chofer_2],1";
           }
           $ok+= insert('ordenes', $campos, $valores, $conn);
     }
     

   //  $conn = conexcion();
     
     $sql = "select id as id_orden
             from ordenes
             where fservicio = '$fecha' and id_chofer_1 in (select id_empleado
                                                            from novedades n
                                                            inner join cod_novedades cn on cn.id = n.id_novedad
                                                            where ('$fecha' between desde and hasta) and (afecta_diagrama)
                                                            group by id_empleado)";
     $result = mysql_query($sql, $conn);

     $campo='id_chofer_1, id_estructura_chofer1, id_user, fecha_accion';
     $value="null, null, $_SESSION[userid], now()";
     while ($row = mysql_fetch_array($result)){
           backup('ordenes', 'ordenes_modificadas', "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])");
           update('ordenes', $campo, $value, "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])");
     }
     @mysql_close($conn);
     
     $conn = conexcion();

     $sql = "select id as id_orden
             from ordenes
             where fservicio = '$fecha' and id_chofer_2 in (select id_empleado
                                                            from novedades n
                                                            inner join cod_novedades cn on cn.id = n.id_novedad
                                                            where ('$fecha' between desde and hasta) and (afecta_diagrama)
                                                            group by id_empleado)";
     $result = mysql_query($sql, $conn);

     $campo='id_chofer_2, id_estructura_chofer2, id_user, fecha_accion';
     $value="null, null, $_SESSION[userid], now()";
     while ($row = mysql_fetch_array($result)){
           backup('ordenes', 'ordenes_modificadas', "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])");
           update('ordenes', $campo, $value, "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])");
     }
     @mysql_close($conn);
     
     
     print $ok;
  }
  
?>

