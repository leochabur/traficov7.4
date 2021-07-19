<?php
     set_time_limit(0);

                  /*   $conn = mysql_connect('rrhh.masterbus.net', 'masterbus', 'master,07a');// conectar_mysql() or die(alerta(mysql_error()));//CONECTAR
                     mysql_select_db('rrhh', $conn);
                     $sql="SELECT legajo, id_licencia
                           FROM licenciasxconductor l
                           inner join empleados e on e.id_empleado = l.id_conductor
                           where e.activo";
                     $result = mysql_query($sql, $conn);   */
                     //";
                     
                     $export = mysql_connect('127.0.0.1', 'c0mbexpuser', 'Mb2013Exp');
                     mysql_select_db('c0mbexport', $export);
                     
                     $sql="select c.id as cron, tu.id as tipo, c.nombre, o.id as orden
                                  from ordenes o
                                  inner JOIN unidades m ON (m.id = o.id_micro)
                                  inner join tipounidad tu on tu.id = m.id_tipounidad
                                  inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                                  inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                                  where o.id_estructura = 1 and fservicio between '$_GET[desde]' and '$_GET[hasta]' and c.id in (select id_cronograma from peajesporcronogramas group by id_cronograma)";
                   //  die($sql);
                     $result = mysql_query($sql, $export);
                     $i=0;
                     while ($data = mysql_fetch_array($result)){
                          // $update = "SELECT id_empleado FROM empleados where (legajo = $data[legajo])";
                           $peaje = "select sum(precio_peaje)
                                    from peajesporcronogramas pxc
                                    inner join estacionespeaje ep on ep.id = pxc.id_estacion_peaje
                                    inner join preciopeajeunidad ppu on ppu.id_estacionpeaje = ep.id
                                    where id_cronograma = $data[cron] and id_tipounidad = $data[tipo]";
                           $re = mysql_query($peaje, $export);
                           if ($row = mysql_fetch_array($re)){
                              $update = "UPDATE ordenes SET peajes = $row[0] WHERE id = $data[orden]";
                              $reza = mysql_query($update, $export);
                              $i++;
                           }
                         /*  if (mysql_num_rows($re) == 0){
                              $insert="INSERT INTO licenciasxconductor (id_licencia, id_conductor) values ($row[id_empleado], $data[id_licencia])";
                              mysql_query($insert, $export) or die($insert);
                              $i++;
                           }      */
                     }
                     
                     /*$query="select o.*
                                       from ordenes o
                                       inner join servicios s on s.id_servicio = o.id_servicio
                                       inner join cronogramas c on c.codigo = s.cod_cronograma
                                       where (fservicio='2013-1-14') and (nombreCliente like '%nautico%')";  */
                    /* $query="SELECT o.id_servicio, ch.legajo as legajo, ch2.legajo as legajo2, m.interno
                                    FROM ordenes o
                                    inner join servicios s on s.id_servicio = o.id_servicio
                                    inner join cronogramas c on c.codigo = s.cod_cronograma
                                    left join choferes ch on ch.id_chofer = o.id_chofer1
                                    left join choferes ch2 on ch2.id_chofer = o.id_chofer2
                                    left join micros m on m.id_micro = o.id_micro
                                    where fservicio = '2013-07-17'";


                  /*   $query="select c.nombre, hcitacion, hsalida, hllegada, hfinserv, kmpactados, o.id_servicio, origen, destino, cl.id_cliente, ch1.legajo as legemp1, if(o.id_chofer2, ch2.legajo, 0) as legemp2, id_micro
                            from (select * from ordenes where fservicio = '2013-04-09') o
                            inner join servicios s on  s.id_servicio = o.id_servicio
                            inner join cronogramas c on c.codigo = s.cod_cronograma
                            inner join clientes cl on cl.nombrecliente = c.nombrecliente
                            left join choferes ch1 on ch1.id_chofer = o.id_chofer1
                            left join choferes ch2 on ch2.id_chofer = o.id_chofer2
                            where cl.activo";    */
           /*          $result=mysql_query($query,$conn) or die(mysql_error($conn)."<br>".$query);
                     

                    /* $conn = mysql_connect('trafico.masterbus.net', 'mbexpuser', 'Mb2013Exp');// conectar_mysql() or die(alerta(mysql_error()));//CONECTAR
                     mysql_select_db('mbexport', $conn);         */
      /*               $i=0;


                     while($data=mysql_fetch_array($result)){
                        $sql = "SELECT nombre, c.vacio, hcitacion, hsalida, hllegada, hfinserv, km, s.id as ids, s.id_estructura as ides, ciudades_id_origen, ciudades_id_estructura_origen, ciudades_id_destino, ciudades_id_estructura_destino, id_cliente, id_estructura_cliente, if(id_cliente_vacio is null, 'null', id_cliente_vacio) as id_cliente_vacio, if(id_estructura_cliente_vacio is null, 'null', id_estructura_cliente_vacio) as id_estructura_cliente_vacio
                                FROM servicios s
                                inner join cronogramas c on c.id = s.id_cronograma
                                where (s.id = $data[id_servicio]) and (s.id_estructura = 1)";
                        if ($row = mysql_fetch_array(mysql_query($sql,$export))){
                           $chofer1 = 'null';
                           $est_chofer1 = 'null';
                           if ($data['legajo']){
                           $fercho = mysql_query("SELECT id_empleado FROM empleados e where legajo = $data[legajo]", $export);
                           if ($row_fer = mysql_fetch_array($fercho)){
                              $chofer1 = $row_fer['id_empleado'];
                              $est_chofer1 = 1;
                           }
                           }
                           $chofer2 = 'null';
                           $est_chofer2 = 'null';
                           if ($data['legajo2']){
                           $fercho2 = mysql_query("SELECT id_empleado FROM empleados e where legajo = $data[legajo2]", $export);
                           if ($row_fer2 = mysql_fetch_array($fercho2)){
                              $chofer2 = $row_fer2['id_empleado'];
                              $est_chofer2 = 1;
                           }
                           }
                           $coche = 'null';
                           if ($data['interno']){
                           $interno = mysql_query("SELECT id FROM unidades where interno = $data[interno]", $export);
                           if ($idcoche = mysql_fetch_array($interno)){
                              $coche = $idcoche['id'];
                           }
                           }
                           $insert = "INSERT INTO ordenes (id_estructura,
                                                           fservicio,
                                                           nombre,
                                                           hcitacion,
                                                           hsalida,
                                                           hllegada,
                                                           hfinservicio,
                                                           km,
                                                           id_servicio,
                                                           id_estructura_servicio,
                                                           id_ciudad_origen,
                                                           id_estructura_ciudad_origen,
                                                           id_ciudad_destino,
                                                           id_estructura_ciudad_destino,
                                                           id_cliente,
                                                           id_estructura_cliente,
                                                           id_cliente_vacio,
                                                           id_estructura_cliente_vacio,
                                                           id_chofer_1,
                                                           id_estructura_chofer1,
                                                           finalizada,
                                                           id_chofer_2,
                                                           id_estructura_chofer2,
                                                           borrada,
                                                           comentario,
                                                           id_micro,
                                                           vacio,
                                                           id_user,
                                                           fecha_accion)
                                                   VALUES (1, '2013-08-14', '$row[nombre]', '$row[hcitacion]', '$row[hsalida]', '$row[hllegada]', '$row[hfinserv]', '$row[km]',
                                                           $row[ids], $row[ides], $row[ciudades_id_origen], $row[ciudades_id_estructura_origen], $row[ciudades_id_destino], $row[ciudades_id_estructura_destino],
                                                           $row[id_cliente], $row[id_estructura_cliente], $row[id_cliente_vacio], $row[id_estructura_cliente_vacio],
                                                           $chofer1, $est_chofer1, 0, $chofer2, $est_chofer2, 0, '', $coche, $row[vacio], 25, now())";



                               mysql_query($insert, $export) or die(mysql_error($export).'<br>'.$insert);














                           $i++;
                        }
                        

                        
                       /*   $emp1 = mysql_query("SELECT id_empleado FROM empleados e where legajo = '$data[legemp1]'", $conn);
                          if ($row = mysql_fetch_array($emp1)){
                             $emp1 = $row['id_empleado'];
                          }
                          $emp2="null";
                          $estemp2="null";
                          if ($data['legemp2']){
                             $emp2 = mysql_query("SELECT id_empleado FROM empleados e where legajo = '$data[legemp2]'", $conn);
                             if ($row = mysql_fetch_array($emp2)){
                                $emp2 = $row['id_empleado'];
                                $estemp2 = "1";
                             }
                          } */
                          
                      /*    $sql="INSERT INTO ordenes (id_estructura, fservicio, nombre, hcitacion, hsalida, hllegada, hfinservicio, km, id_servicio, id_estructura_servicio,
                                                     id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino,
                                                     id_cliente, id_estructura_cliente, id_chofer_1, id_estructura_chofer1, finalizada, id_chofer_2, id_estructura_chofer2, borrada, comentario, id_micro, id_estructura_micro, vacio, id_user)
                                              VALUES(1, '2013-05-13', '$data[nombre]', '$data[hcitacion]', '$data[hsalida]', '$data[hllegada]', '$data[hfinserv]', '$data[km]', $data[id_servicio], 1,
                                                     $data[origen], 1, $data[destino], 1, $data[id_cliente], 1, $emp1, 1, 0, $emp2, $estemp2, 0, '', $data[id_micro], 1, 0, 25)";      */
                         /*  $id_servicio= $data['id_servicio'];
                           $id_micro= $data['id_micro'];
                           $id_chofer1= $data['id_chofer1'];
                           $id_chofer2= $data['id_chofer2']; */
                        /*   $consulta = "INSERT INTO ordenes (id_servicio, fcreacion, fservicio, ffin, id_micro, kmreales, id_chofer1, id_chofer2, kmsalida, kmregreso, kminicial, kmfinal, hfinservreal, pasajeros, nrolistapas, excursion, lugar, video, jugo, cafe, pelicula, peajesac, viaticosac, alojamientoac, estac, valorviaje, observaciones, finalizada, responsable, chequeada)".
                            " values($data[id_servicio],'2013-05-11','2013-05-13','2013-05-13',$data[id_micro],'$data[kmreales]','$data[id_chofer1]','$data[id_chofer2]','$data[kmsalida]','$data[kmregreso]','$data[kminicial]','$data[kmfinal]','','','$data[nrolistapas]','$data[excursion]','$data[lugar]','$data[video]','$data[jugo]','$data[cafe]','$data[pelicula]','$data[peajesac]','$data[viaticosac]','$data[alojamientoac]','$data[estac]','$data[valor]','','0','De Peon, Maximiliano',0);";
                          // echo ($consulta); */
                       //   mysql_query($consulta, $miconexion) or die("micro  ".$data['id_micro']."  ".$sql);
                        //   $i++;//=$i + mysql_affected_rows();
                    //       echo $consulta;       */
                     //}
                 //    mysql_close();
                     print ('Se actualizaron '.$i.' ordenes con exito');
?>
