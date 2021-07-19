<?php
  session_start();
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  define('STRUCTURED', $_SESSION['structure']);
  $accion= $_POST['accion'];
  
  if ($accion == 'cpy'){
     $conn = conexcion();
     $ordenes = $_POST['orders'];
     $fecha = $_POST['fecha'];
     $sql = "SELECT o.nombre, o.hcitacion, o.hsalida, o.hllegada, o.hfinservicio, o.km, o.id_micro, o.id_servicio,
                    o.id_estructura_servicio, o.id_ciudad_origen, o.id_estructura_ciudad_origen,
                    o.id_ciudad_destino, o.id_estructura_ciudad_destino, o.id_cliente, o.id_estructura_cliente, o.id_cliente_vacio, o.id_estructura_cliente_vacio,
                    o.id_chofer_1, o.id_estructura_chofer1, o.finalizada, o.id_chofer_2, o.id_estructura_chofer2, o.borrada, o.comentario, o.vacio,
                    ho.nombre as nombreho, ho.hcitacion as hcitacionho, ho.hsalida as hsalidaho, ho.hllegada as hllegadaho, ho.hfinservicio as hfinservicioho,
                    ho.km as kmho, ho.id_servicio as id_servicioho, ho.id_estructura_servicio as id_estructura_servicioho,
                    ho.id_ciudad_origen as id_ciudad_origenho, ho.id_estructura_ciudad_origen as id_estructura_ciudad_origenho,
                    ho.id_ciudad_destino as id_ciudad_destinoho, ho.id_estructura_ciudad_destino as id_estructura_ciudad_destinoho,
                    ho.id_cliente as id_clienteho, ho.id_estructura_cliente as id_estructura_clienteho,
                    ho.id_cliente_vacio as id_cliente_vacioho, ho.id_estructura_cliente_vacio as id_estructura_cliente_vacioho,
                    ho.id_chofer_1 as id_chofer_1ho, ho.id_estructura_chofer1 as id_estructura_chofer1,
                    ho.finalizada as finalizadaho, ho.id_chofer_2 as id_chofer_2ho, ho.id_estructura_chofer2 as id_estructura_chofer2ho,
                    ho.borrada as borradaho, ho.comentario as comentarioho, ho.id_micro as id_microho,
                    ho.vacio as vacioho, ho.id_user as id_userho, ho.fecha_accion as fecha_accionho,
                    ho.cantpax as cantpaxho, ho.suspendida as suspendidaho, ho.checkeada as checkeadaho,
                    ho.id_claseservicio as id_claseservicioho, ho.id_estructuraclaseservicio as id_estructuraclaseservicioho, ho.peajes as peajesho
                    FROM ordenes o
                    LEFT JOIN horarios_ordenes ho ON ho.id = o.id
                    WHERE (o.id in ($ordenes)) and (o.id_estructura = ".STRUCTURED.")";

    // die($sql);
     $result = mysql_query($sql, $conn);
     @mysql_close($conn);
     $valores = "";
     $ok = 0;
     while ($data = mysql_fetch_array($result)){
           $campos = "id,id_estructura,fservicio,nombre,hcitacion,hsalida,hllegada,hfinservicio,km,id_ciudad_origen,id_estructura_ciudad_origen,id_ciudad_destino,id_estructura_ciudad_destino,id_cliente,id_estructura_cliente,finalizada,borrada,comentario, id_user, vacio";
           $valores = STRUCTURED.", '$fecha'";
           $valores.= ",".($data['nombreho'] ? "'$data[nombreho]'":"'$data[nombre]'");
           $valores.= ",".($data['hcitacionho'] ? "'$data[hcitacionho]'" : "'$data[hcitacion]'");
           $valores.= ",".($data['hsalidaho'] ? "'$data[hsalidaho]'": "'$data[hsalida]'");
           $valores.= ",".($data['hllegadaho'] ? "'$data[hllegadaho]'": "'$data[hllegada]'");
           $valores.= ",".($data['hfinservicioho'] ? "'$data[hfinservicioho]'" : "'$data[hfinservicio]'");
           $valores.= ",".($data['kmho'] ? "$data[kmho]" : "$data[km]");
           $valores.= ",".($data['id_ciudad_origen'] ? "$data[id_ciudad_origenho]" : "$data[id_ciudad_origen]");
           $valores.= ",".($data['id_estructura_ciudad_origenho'] ? "$data[id_estructura_ciudad_origenho]" : "$data[id_estructura_ciudad_origen]");
           $valores.= ",".($data['id_ciudad_destinoho'] ? "$data[id_ciudad_destinoho]" : "$data[id_ciudad_destino]");
           $valores.= ",".($data['id_estructura_ciudad_destinoho'] ? "$data[id_estructura_ciudad_destinoho]" : "$data[id_estructura_ciudad_destino]");
           $valores.= ",".($data['id_clienteho'] ? "$data[id_cliente]" : "$data[id_cliente]");
           $valores.= ",".($data['id_estructura_clienteho'] ? "$data[id_estructura_clienteho]" : "$data[id_estructura_cliente]");
           $valores.= ",'0','0','', $_SESSION[userid]";
           $valores.= ",".($data['vacioho'] ? "$data[vacioho]" : "$data[vacio]");

           if($data['id_microho']){
                   $campos.=", id_micro";
                   $valores.=", $data[id_microho]";
           }
           elseif($data['id_micro']){
                   $campos.=", id_micro";
                   $valores.=", $data[id_micro]";
           }
           if ($data['id_servicioho'] && $data['id_estructura_servicioho']){
                   $campos.=", id_servicio, id_estructura_servicio";
                   $valores.=", $data[id_servicioho], $data[id_estructura_servicioho]";
           }
           elseif ($data['id_servicio'] && $data['id_estructura_servicio']){
                   $campos.=", id_servicio, id_estructura_servicio";
                   $valores.=", $data[id_servicio], $data[id_estructura_servicio]";
           }
           
           if ($data['id_cliente_vacioho']){
                   $campos.=", id_cliente_vacio, id_estructura_cliente_vacio";
                   $valores.=", $data[id_cliente_vacioho], ".STRUCTURED;
           }
           elseif ($data['id_cliente_vacio']){
                   $campos.=", id_cliente_vacio, id_estructura_cliente_vacio";
                   $valores.=", $data[id_cliente_vacio], ".STRUCTURED;
           }
           
           if ($data['id_chofer_1ho']){
                   $campos.=", id_chofer_1, id_estructura_chofer1";
                   $valores.= ", $data[id_chofer_1ho], 1";
           }
           elseif ($data['id_chofer_1']){
                   $campos.=", id_chofer_1, id_estructura_chofer1";
                   $valores.= ", $data[id_chofer_1], 1";
           }
           
           if ($data['id_chofer_2ho']){
                   $campos.=", id_chofer_2, id_estructura_chofer2";
                   $valores.= ", $data[id_chofer_2ho],1";
           }
           elseif ($data['id_chofer_2']){
                   $campos.=", id_chofer_2, id_estructura_chofer2";
                   $valores.= ", $data[id_chofer_2],1";
           }
           //die($campos."<br><br>".$valores);
           try{
           $ok+= insert('ordenes', $campos, $valores);
           } catch (Exception $e) {die($e->getMessage());}
     }
     

     $conn = conexcion();
     
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

