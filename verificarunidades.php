<?php
  set_time_limit(0);
  error_reporting(E_ALL);

  ////////////////// modulo para dar de alta Ciudades /////////////////////

  include ('./controlador/bdadmin.php');



        $conn = conexcion();

        $sql = "select s.id as idServicio,
                       ord.id as idOrden,
                       c.id as idCronograma,
                       c.nombre as Cronograma,
                       cl.id as idCliente,
                       cl.razon_social as Cliente,
                       o.ciudad as Origen, 
                       d.ciudad as Destino,
                       ord.fservicio as Fecha_Servicio,
                       u.interno as interno,
                       s.hsalida as Horario_Cabecera,
                       s.hllegada,
                       u.id as idMicro,
                       concat(ord.fservicio,' ', s.hsalida) as dts,
                       concat(ord.fservicio,' ', s.hllegada) as dtl
        from (select * from ordenes where fservicio = '2020-05-18' and not suspendida and not borrada and id_servicio is not null) ord
        inner join servicios s on s.id = ord.id_servicio and s.id_estructura = ord.id_estructura_servicio
        inner join cronogramas c on s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura
        inner join ciudades o on o.id = ciudades_id_origen and o.id_estructura = ciudades_id_estructura_origen
        inner join ciudades d on d.id = ciudades_id_destino and d.id_estructura = ciudades_id_estructura_destino
        inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
        inner join unidades u on u.id = ord.id_micro
        where c.activo and s.activo and cl.activo and not c.vacio and c.id_estructura = 1 and (c.id in (3679, 3681, 6355, 264, 3125, 3126, 3243, 3244, 3302, 5339, 5624, 5625, 5626) or cl.id <> 13)
        order by u.id";

        $result = mysql_query($sql, $conn) or die(mysql_error($conn));
        $row = mysql_fetch_array($result);
        while ($row)
        {
            print "$row[idOrden]<br>";
            $id = $row['idMicro'];
            $last = null;
            while (($row) && ($id == $row['idMicro'])) 
            {
              try{
                $dts = DateTime::createFromFormat('Y-m-d H:i:s', $row['dts']);
                if ($last)
                {
                   $dtl = DateTime::createFromFormat('Y-m-d H:i:s', $last['dtl']);
                   if ($dts < $dtl)
                   {
                      print "ultima";
                      print "<br>ultima";
                   }
                   print "no last<br>";
                }
              }
              catch (Exception $e) { die ($e->getMessage());}
                $row = mysql_fetch_array($result);
              
            }
            $last = null;
        }


?>

