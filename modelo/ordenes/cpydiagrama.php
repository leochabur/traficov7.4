<?php

  //////COPIADO EL DIA 18/07/2020 
  
  set_time_limit(0);
     error_reporting(0);
  session_start();
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];
  
if ($accion == 'cpy')
{
  $conn = conexcion(true);
  try
  {
     //begin($conn);
   // mysqli_begin_transaction($conn);

     $ordenes = $_POST['orders'];
     $fecha = $_POST['fecha'];
     $ordenesAuto = getOrdenesDiagramadas($conn, $fecha, STRUCTURED); //son todas las ordenes automaticas generadas para la fecha destino

     //el 30/05/2020 se modifico para que al copiar el diagrama arrastre el nombre de la orden como consecuencia de los casos de covid en Toyota
     $sql = "SELECT o.nombre as nombre, o.hcitacion, o.hsalida, o.hllegada, o.hfinservicio as hfinservicio, c.km, o.id_micro, o.id_servicio, 
                    o.id_estructura_servicio,
                    de.id as id_ciudad_origen, de.id_estructura as id_estructura_ciudad_origen, ha.id as id_ciudad_destino, 
                    ha.id_estructura as id_estructura_ciudad_destino, 
                    o.id_cliente, o.id_estructura_cliente,
                    o.id_cliente_vacio, o.id_estructura_cliente_vacio, o.id_chofer_1, o.id_estructura_chofer1, o.finalizada, o.id_chofer_2, 
                    o.id_estructura_chofer2,
                    o.borrada, o.comentario, o.vacio, s.id as idServ
                FROM (SELECT * FROM ordenes WHERE fservicio = '$_POST[forigen]' and id_estructura = ".STRUCTURED.") o
                inner join servicios s on (s.id = o.id_servicio) and (s.id_estructura = o.id_estructura_servicio)
                inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                inner join ciudades de on (de.id = c.ciudades_id_origen) and (de.id_estructura = c.ciudades_id_estructura_origen)
                inner join ciudades ha on (ha.id = c.ciudades_id_destino) and (ha.id_estructura = c.ciudades_id_estructura_destino)
                WHERE (o.id in ($ordenes)) or (c.tipoServicio = 'charter')";

     $result = mysqli_query($conn, $sql) or die($sql);

     $valores = "";
     $ok = 0;
     while ($data = mysqli_fetch_array($result))
     {
        $keySrv = $data['idServ'];
        if (!in_array($keySrv, $ordenesAuto)) //es un servicio de charter debe solo actualizar la orden con id_servicio $keySrv en la fecha destino seleccionada
        {
           $campos = "id,id_estructura,fservicio,nombre,hcitacion,hsalida,hllegada,hfinservicio,km,id_ciudad_origen,id_estructura_ciudad_origen,id_ciudad_destino,id_estructura_ciudad_destino,id_cliente,id_estructura_cliente,finalizada,borrada,comentario, id_user, vacio";
           $valores = STRUCTURED.", '$fecha', '$data[nombre]','$data[hcitacion]','$data[hsalida]','$data[hllegada]','$data[hfinservicio]','$data[km]',$data[id_ciudad_origen],$data[id_estructura_ciudad_origen],$data[id_ciudad_destino],$data[id_estructura_ciudad_destino],$data[id_cliente],$data[id_estructura_cliente],'0','0','', $_SESSION[userid], $data[vacio]";
           if($data['id_micro'])
           {
                   $campos.=", id_micro";
                   if ($_SESSION['structure'] == 2)
                   {
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
           $ok+= insertPDO('ordenes', $campos, $valores, $conn);
         }
         else
         {
            if (!$data['borrada'])
            {
               $campos ="id_chofer_1, id_estructura_chofer1, id_chofer_2, id_estructura_chofer2, id_micro";
               $values = "";
               if ($data['id_chofer_1'])
               {  
                  $values = "$data[id_chofer_1], 1";
               }
               else
               {
                  $values = "NULL, 1";
               }

               if ($data['id_chofer_2'])
               {  
                  $values.= ", $data[id_chofer_2], 1";
               }
               else
               {
                  $values.= ", NULL, 1";
               }

               if($data['id_micro'])
               {
                  $values.=", $data[id_micro]";
               }
               else
               {
                  $values.=", NULL";
               }
               updatePDO('ordenes', 
                      $campos, 
                      $values, 
                      "(fservicio = '$fecha') and (id_servicio = $keySrv) and (id_estructura_servicio = $_SESSION[structure])", 
                      $conn);
            }
         }
     }
     

   //  $conn = conexcion();
     
     $sql = "select id as id_orden
             from ordenes
             where id_estructura = $_SESSION[structure] and fservicio = '$fecha' and id_chofer_1 in (select id_empleado
                                                            from novedades n
                                                            inner join cod_novedades cn on cn.id = n.id_novedad
                                                            where ('$fecha' between desde and hasta) and (n.activa) and (afecta_diagrama) and (n.id_estructura = $_SESSION[structure])
                                                            group by id_empleado)";
     $result = mysqli_query($conn, $sql);

     $campo='id_chofer_1, id_estructura_chofer1, id_user, fecha_accion';
     $value="null, null, $_SESSION[userid], now()";
     while ($row = mysqli_fetch_array($result))
     {
           backupPDO('ordenes', 'ordenes_modificadas', "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])", $conn);
           updatePDO('ordenes', $campo, $value, "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])", $conn);
     }

     $sql = "select id as id_orden
             from ordenes
             where id_estructura = $_SESSION[structure] and fservicio = '$fecha' and id_chofer_2 in (select id_empleado
                                                            from novedades n
                                                            inner join cod_novedades cn on cn.id = n.id_novedad
                                                            where ('$fecha' between desde and hasta) and (afecta_diagrama) and (n.activa) and (n.id_estructura = $_SESSION[structure])
                                                            group by id_empleado)";
     $result = mysqli_query($conn, $sql);

     $campo='id_chofer_2, id_estructura_chofer2, id_user, fecha_accion';
     $value="null, null, $_SESSION[userid], now()";
     while ($row = mysqli_fetch_array($result))
     {
           backupPDO('ordenes', 'ordenes_modificadas', "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])", $conn);
           updatePDO('ordenes', $campo, $value, "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])", $conn);
     }
     $sql = "INSERT INTO copiasDiagramaDiario (fecha, id_estructura, id_usuario, fecha_copia) VALUES ('$fecha', $_SESSION[structure], $_SESSION[userid], now())";
     ejecutarSQLPDO($sql, $conn);
     
   //  mysqli_commit($conn);
     @mysqli_close($conn);
     print (json_encode(array("status" => true)));
  }catch (Exception $e) {
                         //  mysqli_rollback($conn);
                           mysqli_close($conn);
                           print (json_encode(array("status" => false, "message" => $e->getMessage())));
                           }
}
elseif($accion == 'ydg'){
       $conn = conexcion(true);
       $sql = "SELECT *
               FROM copiasDiagramaDiario
               where fecha = '$_POST[fecha]' and id_estructura = $_SESSION[structure]";
       
       $result = mysqli_query($conn, $sql);
       mysqli_close($conn);
       if (mysqli_num_rows($result)){
          print (json_encode(array("status" => true)));
       }
       else{
            print (json_encode(array("status" => false)));
       }
}

function getOrdenesDiagramadas($conn, $fecha, $str) //recupera para la fecha dada todas las ordenes marcadas como charter
{
  /*  $sql = "SELECT s.id as serv, o.id as ord
            FROM (SELECT * FROM ordenes WHERE fservicio = '$fecha' and id_estructura = $str) o
            inner join servicios s on (s.id = o.id_servicio) and (s.id_estructura = o.id_estructura_servicio)
            inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
            where c.tipoServicio = 'charter' AND o.id_cliente <> 13";*/
    $sql = "SELECT s.id as idServ
                FROM servicios s
                inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                where c.activo and s.activo and c.tipoServicio = 'charter' and id_cliente = 10";
    $result = mysqli_query($conn, $sql);
    $ordenes = array();
    while ($row = mysqli_fetch_array($result))
    {
        //$key = $row['serv'];
        $ordenes[] = $row['idServ'];
        //$ordenes[$key] = $row['ord'];
    }
    return $ordenes;
}
  
?>

