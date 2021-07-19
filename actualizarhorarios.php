<?php
     set_time_limit(0);

     $remoto = mysql_connect('traficonuevo.masterbus.net', 'c0mbexpuser', 'Mb2013Exp');
     mysql_select_db('c0mbexport', $remoto);
     $sql = "SELECT id_cliente, id_estructuraCliente, ant_max FROM restricciongralcliente r";

     $result = mysql_query($sql, $remoto);

     while ($row = mysql_fetch_array($result)){

           $sql = "update restclientetipounidad set antiguedad = $row[ant_max]
                   WHERE id_tipovto is null and id_cliente = $row[id_cliente] and id_estructuracliente = $row[id_estructuraCliente]";
           
           $srv = mysql_query($sql, $remoto);
          /* while ($data = mysql_fetch_array($srv)){
                 $sql = "INSERT INTO fact_dias_semana_por_tarifa (id_tarifa_servicio, id_dia_semana) VALUES ($row[0], $data[0])";
                 mysql_query($sql, $remoto) or die ($sql);
                 $i++;
           }    */
     }
     print "se actualizaron $i ordenes";

?>
