<?
  session_start();
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];
  
  if ($accion == 'cpy'){
     $conn = conexcion();
     $ordenes = $_POST['orders'];
     $fecha = $_POST['fecha'];
     $sql = "SELECT if (o.id_cliente_vacio is null, c.nombre, concat(c.nombre,'~(',cli.razon_social,')')) as nombre, o.hcitacion, o.hsalida, o.hllegada, o.hfinservicio, o.km, id_micro, id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, o.id_cliente, o.id_estructura_cliente, o.id_cliente_vacio, o.id_estructura_cliente_vacio, id_chofer_1, id_estructura_chofer1, finalizada, id_chofer_2, id_estructura_chofer2, borrada, comentario, o.vacio
FROM ordenes o
inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
left join clientes cli on cli.id = o.id_cliente_vacio and cli.id_estructura = o.id_estructura_cliente_vacio
             WHERE (o.id in ($ordenes)) and (o.id_estructura = ".STRUCTURED.")";
     $result = mysql_query($sql, $conn);
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
           $ok+= insert('ordenes', $campos, $valores);
     }
     
 /*    $sql = "select id as id_orden
             from ordenes
             where fservicio = '$fecha' and id_chofer_1 in (select id_empleado
                                                            from novedades
                                                            where '$fecha' between desde and hasta
                                                       group by id_empleado)";
     $conn = conexcion();
     $result = mysql_query($sql, $conn);

     $campo='id_chofer_1, id_estructura_chofer1, id_user, fecha_accion';
     $value="null, null, $_SESSION[userid], now()";
     while ($row = mysql_fetch_array($result)){
           backup('ordenes', 'ordenes_modificadas', "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])");
           update('ordenes', $campo, $value, "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])");
     }  */
     print $ok;
  }
  
?>

