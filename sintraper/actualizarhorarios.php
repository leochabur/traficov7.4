<?php
     set_time_limit(0);

     $conn = mysql_connect('traficonuevo.masterbus.net', 'c0mbexpuser', 'Mb2013Exp');
     mysql_select_db('c0mbexport', $conn);
     $sql = "select *
from(
select o.id, i_v, o.hllegada as horario_std, o.hllegadaplantareal as horario_real, s.hllegada as horario_servicio
from ordenes o
inner join servicios s on s.id = o.id_servicio
inner join cronogramas c on c.id = s.id_cronograma
where o.fservicio between '2016-03-01' and '2016-03-29' and o.id_estructura = 1 and c.id_cliente = 10 and not o.borrada and not o.suspendida and i_v = 'i') o
where hour(horario_std) = 22";

    /* $sql = "SELECT o.id as id_orden, s.*
FROM (select id, id_servicio from ordenes where fservicio between '2016-03-01' and '2016-03-31' and id_estructura = 1 and id_cliente = 161) o
inner join (select id, i_v, id_turno, id_estructura_turno, id_TipoServicio, id_estructura_TipoServicio from servicios where id_estructura = 1) s on s.id = o.id_servicio
inner join tipotnoordenes tt on tt.id_orden = o.id";
     $i=0;         */
     $res_estados = mysql_query($sql, $conn);
     while ($data = mysql_fetch_array($res_estados)){
  //      $exist = "UPDATE tipotnoordenes set id_turno = $data[id_turno], id_tipo_servicio = $data[id_TipoServicio], i_v = '$data[i_v]' WHERE id_orden = $data[id_orden]";
          $sql = "UPDATE ordenes SET hllegada = '22:20:00' WHERE id = $data[id]";
          mysql_query($sql, $conn);
          $i++;
     }
     print "se actualizaron $i ordenes";

?>
