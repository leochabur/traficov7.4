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
                     
                     $export = mysql_connect('export.masterbus.net', 'mbexpuser', 'Mb2013Exp');
                     mysql_select_db('mbexport', $export);
                     
                     $estados = "SELECT fecha, fechahorafinalizacion
                                FROM estadoDiagramasDiarios
                                WHERE (fecha between date('2015-07-01 12:05:43') and date('2015-07-13 12:05:43')) and (id_estructura = 1)";
                     $res_estados = mysql_query($estados, $export);
                     while ($data_estados = mysql_fetch_array($res_estados)){
                           $sql = "select max(fecha_accion), id
                                   from ordenes_modificadas o
                                   where fservicio = '$data_estados[0]' and id_estructura = 1 and fecha_accion < '$data_estados[1]'
                                   group by id";
                           $result = mysql_query($sql, $export);
                           while ($data = mysql_fetch_array($result)){
                                 $up = "INSERT INTO horarios_ordenes (select * from ordenes_modificadas where fecha_accion = '$data[0]' and id = $data[1])";
                                 mysql_query($up, $export);
                                 if (mysql_errno($export))
                                    print "ERROR $data[1]<br>";
                                 else
                                     print "SE INSERTO ORDEN $data[1]<br>";
                           }
                          // print $data_estados[0]."<br>";
                     }
                     
                     
           //        $conn = mysql_connect('127.0.0.1', 'root', 'leo1979');
             //      mysql_select_db('canias');
                 //   $i=0;

                   //  $fp = fopen ( "C:/canias/clientes.csv" , "r" );
                     //while (( $data = fgetcsv ( $fp , 1000 , ";" )) !== FALSE ) { // Mientras hay líneas que leer...

                  //          $fnac = explode("/",$data[2]);
                    //        $fnac="$fnac[2]-$fnac[1]-$fnac[0]";
                     //   mysql_set_charset('utf8');

                       //    print html_entity_decode($data[0])."<br>";

                         //  $sql = "INSERT INTO clientes (nombre, direccion, cod_postal, telefono, cuit, cond_iva, empleador, nro_leg_tasa_seg_hig, ganancias)
                           // VALUES ('$data[0]', '$data[1]', '$data[2]', '$data[3]', '$data[4]', $data[6], $data[7], '$data[8]', $data[9])";

                           // if ($data[0])
                               //print "$sql<br>";
                             //  mysql_query($sql);
                             //  print  "$data[0] $data[1] $data[2] $data[3] $data[4] $data[5] $data[6] $data[7]<br>";
                               //print "$sql<br>";
                      /*   $sql = "SELECT id_empleado FROM empleados e where legajo = $data[0]";

                         $result = mysql_query($sql, $export) or die (mysql_error($export));

                         if ($row = mysql_fetch_array($result)){
                         
                            $insert = "INSERT INTO vacacionespersonal (id_empleado, anio, fecha_liquidacion, cant_dias, id_user, detalle)
                                       VALUES ($row[0], 0, now(), $data[2], 19, 'Saldo Inicial')";
                                       
                            mysql_query($insert, $export);
                         } */
                        /*$fecha = explode('/', $data[2]);
                        $fecha = "20".$fecha[2]."-".$fecha[1]."-".$fecha[0];

                        $legajo = $data[1];
                           $sql = "select id_empleado
                                   from empleados
                                   where legajo = $legajo";  
                           $result = mysql_query($sql, $export);
                           if ($row = mysql_fetch_array($result)){
                               
                                $libxcond = "insert into licenciasxconductor (id_licencia, id_conductor) values (6, $row[0])";

                                $vtoslib = "insert into licenciaconductor (id_conductor, id_licencia, vigencia_hasta, id_usuario, fecha_alta)
                                                                    values($row[0], 6, '$fecha', 17, now())";
                                $res = mysql_query($libxcond, $export) or die(mysql_error($export));
                                $res = mysql_query($vtoslib, $export);
                                $i++;
                           }  */
                     //}
                     ///fclose ( $fp );
                     
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
                     



                        

                        

                     print ('Se actualizaron '.$i.' ordenes con exito');  */
?>
