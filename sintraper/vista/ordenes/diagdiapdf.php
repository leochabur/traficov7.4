<?php
     session_start();
     include('../pdfexport.php');
     include ('../../modelo/utils/dateutils.php');
     $fecha = dateToMysql($_GET['fec'], '/');
     $sql="SELECT date_format(hcitacion, '%H:%i') as hcitacion, o.nombre, if(em1.id = 1,concat(ch1.apellido, ', ',ch1.nombre), concat('(',em1.razon_social,') ', ch1.apellido, ', ',ch1.nombre)) as chofer1, upper(c.razon_social) as razon_social, interno
                                                       FROM (SELECT id, id_estructura, fservicio, nombre, hcitacion, hsalida, hllegada, hfinservicio, km, id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, id_estructura_cliente, id_cliente_vacio, id_estructura_cliente_vacio, id_chofer_1, id_estructura_chofer1, finalizada, borrada, comentario, id_micro, vacio, id_user, fecha_accion
                                                             FROM ordenes o
                                                             WHERE (fservicio = '$fecha') and (not borrada) and (o.id_estructura = $_SESSION[structure]) and (id_chofer_1 is not null)
                                                             union all
                                                             SELECT id, id_estructura, fservicio, nombre, hcitacion, hsalida, hllegada, hfinservicio, km, id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, id_estructura_cliente, id_cliente_vacio, id_estructura_cliente_vacio, id_chofer_2, id_estructura_chofer2, finalizada, borrada, comentario, id_micro, vacio, id_user, fecha_accion
                                                             FROM ordenes o
                                                             WHERE (fservicio = '$fecha') and (not borrada) and (o.id_estructura = $_SESSION[structure]) and (id_chofer_2 is not null)) o
                                                       LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
                                                       LEFT JOIN empleadores em1 ON (em1.id = ch1.id_empleador)
                                                       LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                                                       LEFT JOIN unidades m ON (m.id = o.id_micro)
                                         UNION all
                                         SELECT '00:00' as hcitacion,  cn.nov_text as nombre, concat(e.apellido, ', ',e.nombre) as chofer1, '' as razon_social, '' as interno
                                         FROM novedades n
                                         inner join cod_novedades cn on cn.id = n.id_novedad
                                         inner join empleados e on e.id_empleado = n.id_empleado
                                         where ('$fecha' between desde and hasta) and (n.id_estructura = $_SESSION[structure])
                                         order by chofer1, hcitacion";
     $pdf = new PDF('L');
     $header = array('Citacion', 'Servicio', 'Conductor', 'Cliente', 'Interno');
     $size = array(15, 70, 90, 60, 20);
     $pdf->AddPage();
     $pdf->SetFont('Arial','',14);
     $pdf->Text(60,10,"DIAGRAMA DE TRABAJO CORRESPONDIENTE AL DIA $_GET[fec]");
     $pdf->SetFont('Arial','',10);
     $pdf->setY(15);
     $pdf->ImprovedTable($header, $sql, $size);
     //$pdf->Output();
     $pdf->Output("diagrama-$fecha.pdf", 'D');
?>

