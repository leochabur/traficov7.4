<?php
  session_start();
     set_time_limit(0);
     error_reporting(0);
   // date_default_timezone_set('America/New_York');
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');

  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];
  
  if (true){
     $conn = conexcion();
     $cond = $_POST['cond'];
     $desde = $_POST['desde'];
     $hasta = $_POST['hasta'];
     if ($cond != 0)
        $cond = "where id_cond = $cond";
     else
         $cond = "";
     $sql = "SELECT date_format(fecha, '%d/%m/%Y') as fecha FROM estadoDiagramasDiarios where (fecha between '$desde' and '$hasta') and (finalizado = 1) and (id_estructura = $_SESSION[structure])";
     $result = mysql_query($sql, $conn);
     $state = array();
     while($row = mysql_fetch_array($result)){
                $state[]=$row['fecha'];
     }



    $sql="SELECT o.*
          FROM( (SELECT interno, upper(c.razon_social)as razon_social, 0 as id_cond, fservicio as fsrv, upper(servicio) as servicio, date_format(salida, '%d/%m/%Y %H:%i') as hsalida, o.id, date_format(fservicio, '%d/%m/%Y') as fservicio, date_format(citacion, '%d/%m/%Y %H:%i') as hcitacion, '                  ORDENES A DESIGNAR' as apellido, 0 as legajo, citacion as li, fservicio as ls, DATEDIFF(fservicio, fservicio) as dias, date_format(finalizacion, '%d/%m/%Y %H:%i') as hfinservicio, emp.color, date_format(llegada, '%d/%m/%Y %H:%i') as hllegada, id_turismo, 0 as id_empleador, cod_servicio, date_format(citacion_real, '%d/%m/%Y %H:%i') as citacion_real, date_format(salida_real, '%d/%m/%Y %H:%i') as salida_real, date_format(llegada_real, '%d/%m/%Y %H:%i') as llegada_real, date_format(finalizacion_real, '%d/%m/%Y %H:%i') as finalizacion_real
                 FROM (SELECT  if (ot.id_viaje is null, 0, ot.id_viaje) as id_turismo, nombre as servicio, salida, finalizacion, ord.id, id_chofer_1, date(citacion) as fservicio,  citacion, id_cliente, id_estructura_cliente, id_micro, llegada, cod_servicio, citacion_real, salida_real, llegada_real, finalizacion_real
                       FROM ordenes ord
                       LEFT JOIN tur_ordenes_por_viaje ot on ot.id_orden = ord.id
                       INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = ord.id AND hhs.id_estructura_orden = ord.id_estructura
                       WHERE (((date(citacion) between '$desde' and '$hasta')OR (date(finalizacion) between '$desde' and '$hasta') OR 
                             ((date(citacion) < '$desde')AND(date(finalizacion)>'$hasta'))) and (id_estructura = $_SESSION[structure])) and (not suspendida) and (not borrada) and ((id_chofer_1 is null) or (id_chofer_1 = 0)) and ((id_chofer_2 is null) or (id_chofer_2 = 0))
                       ) o
                 INNER JOIN clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
                 LEFT JOIN unidades un on (un.id = o.id_micro)
                 left join empleadores emp on (emp.id = un.id_propietario) and (emp.id_estructura = un.id_estructura_propietario))
                UNION
            (SELECT interno, upper(c.razon_social)as razon_social, o.id_chofer_1 as id_cond, fservicio as fsrv, upper(servicio) as servicio, date_format(salida, '%d/%m/%Y %H:%i') as hsalida, o.id, date_format(fservicio, '%d/%m/%Y') as fservicio, date_format(citacion, '%d/%m/%Y %H:%i') as hcitacion, upper(if(emp.id = 1,concat(ch1.apellido, ', ',ch1.nombre), concat('(',emp.razon_social,') ', ch1.apellido, ', ',ch1.nombre))) as apellido, concat(legajo, emp.id) as legajo, citacion as li, fservicio as ls, DATEDIFF(fservicio, fservicio) as dias, date_format(finalizacion, '%d/%m/%Y %H:%i') as hfinservicio, empl.color, date_format(llegada, '%d/%m/%Y %H:%i') as hllegada, id_turismo, ch1.id_empleador, cod_servicio, date_format(citacion_real, '%d/%m/%Y %H:%i') as citacion_real, date_format(salida_real, '%d/%m/%Y %H:%i') as salida_real, date_format(llegada_real, '%d/%m/%Y %H:%i') as llegada_real, date_format(finalizacion_real, '%d/%m/%Y %H:%i') as finalizacion_real
       FROM (SELECT  if (ot.id_viaje is null, 0, ot.id_viaje) as id_turismo, nombre as servicio, salida, finalizacion, ord.id, id_chofer_1, date(citacion) as fservicio, citacion, id_cliente, id_estructura_cliente, id_micro, llegada, cod_servicio, citacion_real, salida_real, llegada_real, finalizacion_real
             FROM ordenes ord
             LEFT JOIN tur_ordenes_por_viaje ot on ot.id_orden = ord.id 
             INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = ord.id AND hhs.id_estructura_orden = ord.id_estructura
                   WHERE (((date(citacion) between '$desde' and '$hasta')OR (date(finalizacion) between '$desde' and '$hasta') OR ((date(citacion) < '$desde')AND(date(finalizacion)>'$hasta'))) and (id_estructura = $_SESSION[structure])) and (not suspendida) and (not borrada) and (id_chofer_1)
                   ) o
                         inner JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
                         inner join empleadores emp on (emp.id = ch1.id_empleador)
                         inner join clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
                         left join unidades un on (un.id = o.id_micro)
                         left join empleadores empl on (empl.id = un.id_propietario) and (empl.id_estructura = un.id_estructura_propietario)
             )
        UNION
            (SELECT interno, upper(c.razon_social)as razon_social, o.id_chofer_2 as id_cond, fservicio as fsrv, upper(servicio) as servicio, date_format(salida, '%d/%m/%Y %H:%i') as hsalida, o.id, date_format(fservicio, '%d/%m/%Y') as fservicio, date_format(citacion, '%d/%m/%Y %H:%i') as hcitacion, upper(if(emp.id = 1,concat(ch2.apellido, ', ',ch2.nombre), concat('(',emp.razon_social,') ', ch2.apellido, ', ',ch2.nombre))) as apellido, concat(legajo, emp.id) as legajo, citacion as li, fservicio as ls, DATEDIFF(fservicio, fservicio) as dias, date_format(finalizacion, '%d/%m/%Y %H:%i') as hfinservicio, empl.color, date_format(llegada, '%d/%m/%Y %H:%i') as hllegada, id_turismo, ch2.id_empleador, cod_servicio, date_format(citacion_real, '%d/%m/%Y %H:%i') as citacion_real, date_format(salida_real, '%d/%m/%Y %H:%i') as salida_real, date_format(llegada_real, '%d/%m/%Y %H:%i') as llegada_real, date_format(finalizacion_real, '%d/%m/%Y %H:%i') as finalizacion_real
             FROM (SELECT  if (ot.id_viaje is null, 0, ot.id_viaje) as id_turismo, nombre as servicio,finalizacion, salida, ord.id, id_chofer_2, date(citacion) as fservicio, citacion, id_cliente, id_estructura_cliente, id_micro, llegada, cod_servicio, citacion_real, salida_real, llegada_real, finalizacion_real
                   FROM ordenes ord
                   LEFT JOIN tur_ordenes_por_viaje ot on ot.id_orden = ord.id
                   INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = ord.id AND hhs.id_estructura_orden = ord.id_estructura
                         WHERE (((date(citacion) between '$desde' and '$hasta')OR (date(finalizacion) between '$desde' and '$hasta') OR ((date(citacion) < '$desde')AND(date(finalizacion)>'$hasta')))  and (id_estructura = $_SESSION[structure])) and (not suspendida) and (not borrada) and (id_chofer_2)
                   UNION ALL
                   SELECT  if (ot.id_viaje is null, 0, ot.id_viaje) as id_turismo, nombre as servicio,finalizacion, salida, ord.id, id_empleado, date(citacion) as fservicio, citacion, id_cliente, id_estructura_cliente, id_micro, llegada, cod_servicio, citacion_real, salida_real, llegada_real, finalizacion_real
                   FROM ordenes ord
                   LEFT JOIN tur_ordenes_por_viaje ot on ot.id_orden = ord.id
                   INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = ord.id AND hhs.id_estructura_orden = ord.id_estructura
                   INNER JOIN tripulacionXOrdenes txo ON txo.id_orden = ord.id AND txo.id_estructura_orden = ord.id_estructura
                   WHERE (((date(citacion) between '$desde' and '$hasta')OR (date(finalizacion) between '$desde' and '$hasta') OR ((date(citacion) < '$desde')AND(date(finalizacion)>'$hasta')))  and (id_estructura = $_SESSION[structure])) and (not suspendida) and (not borrada)                 
                   ) o
                         inner JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
                         inner join empleadores emp on emp.id = ch2.id_empleador
                         inner join clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
                         left join unidades un on (un.id = o.id_micro)
                         left join empleadores empl on (empl.id = un.id_propietario) and (empl.id_estructura = un.id_estructura_propietario)
             )
        UNION ALL
            (SELECT '' as interno, '' as razon_social, n.id_empleado as id_cond, '$desde' as fsrv, upper(CONCAT(nov_text, ' (',date_format(desde, '%d/%m/%Y'), ' - ', date_format(hasta, '%d/%m/%Y'),')')) as servicio,
                    '00:00' as hsalida, 0 as id, date_format('$desde', '%d/%m/%Y') as fservicio, '00:00' as hcitacion, upper(if(emp.id = 1,concat(em.apellido, ', ',em.nombre), concat('(',emp.razon_social,') ', em.apellido, ', ',em.nombre))) as apellido, concat(legajo, emp.id) as legajo, if (desde < '$desde', '$desde', desde) as li, if (hasta > '$hasta', '$hasta', hasta) as ls, DATEDIFF(if (hasta > '$hasta', '$hasta', hasta), if (desde < '$desde', '$desde', desde)) as dias, '00:00' as hfinservicio, 'FFFFFF' as color, '00:00' as hllegada, 0 as id_turismo, em.id_empleador, '' as cod_servicio, '00:00' as citacion_real, '00:00' as salida_real, '00:00' as llegada_real, '00:00' as finalizacion_real
             FROM (SELECT * FROM novedades WHERE ((activa) and((hasta between '$desde' and '$hasta') or (desde between '$desde' and '$hasta') or ('$desde' between desde and hasta) or ('$hasta' between desde and hasta))) ) n
             inner join cod_novedades cn on cn.id = n.id_novedad
             inner join empleados em on em.id_empleado = n.id_empleado and em.id_estructura = $_SESSION[structure]
             inner join empleadores emp on emp.id = em.id_empleador
            )
        ) o
        $cond
        order by apellido, id_cond, li, hcitacion, id";
    //die($sql);
     $result = mysql_query($sql, $conn) or die(mysql_error($conn));

     //die('..'.mysql_num_rows($result));

     $aux = [];

     while ($r = mysql_fetch_array($result))
     {
        if (!array_key_exists($r['id_cond'], $aux))
        {
            $aux[$r['id_cond']] = [0 => $r['apellido'], 1 => $r['legajo'], 2 => []];
        }

        $aux[$r['id_cond']][2][] = $r;
     }

     $llegada = 'Hora LLegada';
     $finalizacion = "Hora Finalizacion";

     if (isset($_POST['corregir']))
     {
         $llegada = 'Hora Finalizacion';
         $finalizacion = "Hora llegada";
     }


     foreach ($aux as $a)
     {
           $tabla.= "<table width='100%' class='order'>
                           <thead>
                                <tr>
                                    <th colspan='10'>Conductor:  ".htmlentities($a[0])."     -     Legajo:  $a[1]</th>
                                </tr>
                                <tr>
                                    <th>Fecha de Servicio</th>
                                    <th>Hora de Citacion</th>
                                    <th>Hora de Salida</th>
                                    <th>Servicio</th>
                                    <th>Interno</th>
                                    <th>Cliente</th>
                                    <th>$llegada</th>
                                    <th>$finalizacion</th>
                                    <th>Codigo</th>
                                </tr>
                           </thead>
                           <tbody>";
            $j=0;
            foreach ($a[2] as $data)
            {
                        $fserv=$data['li'];
                        $fec = date_create($fserv);

                        $dtcita = $data['hcitacion'];
                        $dtsalida = $data['hsalida'];
                        $dtLlegada = $data['hllegada'];
                        $dtFinalizacion = $data['hfinservicio'];

                        $fieldCita = 'hcitacion';
                        $fieldSale = 'hsalida';
                        $fieldLlega = 'hllegada';
                        $fieldFina = 'hfinservicio';

                       if ($data['legajo'] == 0){
                          $color="#C0FFFF";
                       }
                       else
                       if(!in_array($data['fservicio'], $state))
                       {
                           $color="#FFC0C0";
                       }
                       else
                       { 
                            $dtcita = $data['citacion_real'];
                            $dtsalida = $data['salida_real'];
                            $dtLlegada = $data['llegada_real'];
                            $dtFinalizacion = $data['finalizacion_real'];

                            $fieldCita = 'citacion_real';
                            $fieldSale = 'salida_real';
                            $fieldLlega = 'llegada_real';
                            $fieldFina = 'finalizacion_real';

                            $color = (($j%2)==0)?'#CFCFCF':'#96B8B6';
                       }

                       if (isset($_POST['corregir']))
                       {
                            if ($data['id']) //es una orden, puede correjir el horario
                            {
                                $dtcita = DateTime::createFromFormat('d/m/Y H:i', $data[$fieldCita]);
                                $dtsalida = DateTime::createFromFormat('d/m/Y H:i', $data[$fieldSale]);
                                $dtLlegada = DateTime::createFromFormat('d/m/Y H:i', $data[$fieldLlega]);
                                $dtFinalizacion = DateTime::createFromFormat('d/m/Y H:i', $data[$fieldFina]);

                                $dtcita = $dtcita->format('H:i');
                                $dtsalida = $dtsalida->format('H:i');
                                $dtLlegada = $dtLlegada->format('H:i');
                                $dtFinalizacion = $dtFinalizacion->format('H:i');

                             }
                       }               


                    
                       $tabla.="<tr bgcolor='$color' id='$data[id]'>
                                    <td width='10%' align='center' class='modord'>".date_format($fec,'d/m/Y')."</td>
                                    <td width='7%' align='center' class='modord'>".$dtcita."</td>
                                    <td width='7%' align='center' class='modord'>".$dtsalida."</td>
                                    <td width='25%' class='modord'>".htmlentities($data['servicio'])."</td>
                                    <td width='5%' class='modord'><div style='color:#$data[color];'>$data[interno]</div></td>
                                    <td width='25%' class='modord'>".htmlentities($data['razon_social'])."</td>
                                    <td width='10%' align='center' class='modord'>".$dtLlegada."</td>
                                    <td width='10%' align='center' class='modord'>".$dtFinalizacion."</td>
                                    <td width='10%' align='center' class='modord'>$data[cod_servicio]</td>";
                        $j++;
            }
            $tabla.="</tbody></table><br>";
     }

/*
    // die(print_r($aux));

     $data = mysql_fetch_array($result);
     $tabla="<input type='text' readonly size='27' value='Diagrama Sujeto a Modificaciones' style='background-color:#FFC0C0;'><br><br>";
     $noves = array();

     $llegada = 'Hora LLegada';
     $finalizacion = "Hora Finalizacion";

     if (isset($_POST['corregir']))
     {
         $llegada = 'Hora Finalizacion';
         $finalizacion = "Hora llegada";
     }

     while ($data)
     {
           $cond = $data['legajo'];
           $id_cond = $data['id_cond'];
           $id_emp = $data['id_empleador'];
           $tabla.= "<table width='100%' class='order'>
                           <thead>
                                <tr>
                                    <th colspan='10'>Conductor:  ".htmlentities($data['apellido'])."     -     Legajo:  $data[legajo]</th>
                                </tr>
                                <tr>
                                    <th>Fecha de Servicio</th>
                                    <th>Hora de Citacion</th>
                                    <th>Hora de Salida</th>
                                    <th>Servicio</th>
                                    <th>Interno</th>
                                    <th>Cliente</th>
                                    <th>$llegada</th>
                                    <th>$finalizacion</th>
                                    <th colspan='2'></th>
                                </tr>
                           </thead>
                           <tbody>";
           while (($data) && ($cond == $data['legajo'])){
                 $j=0;

                // $ordenes = array();
                 while (($data) && ($cond == $data['legajo']))
                 {

                    $fserv=$data['li'];
                    if (!array_key_exists($noves, $fserv)){
                       $noves[$fserv] = arrayNovedades($fserv, $conn);
                       
                    }
                    $fec = date_create($fserv);

                    ///inicio del for
                    for ($i=0; $i <= $data['dias']; $i++){
                       if ($data['legajo'] == 0){
                          $color="#C0FFFF";
                       }
                       else
                       if(!in_array($data['fservicio'], $state)){
                           $color="#FFC0C0";
                       }
                       else{
                            $color = (($j%2)==0)?'#CFCFCF':'#96B8B6';
                       }

                        $dtcita = $data['hcitacion'];
                        $dtsalida = $data['hsalida'];
                        $dtLlegada = $data['hllegada'];
                        $dtFinalizacion = $data['hfinservicio'];

                       if (isset($_POST['corregir']))
                       {
                            if ($data['id']) //es una orden, puede correjir el horario
                            {
                                $dtcita = DateTime::createFromFormat('d/m/Y H:i', $data['hcitacion']);
                                $dtsalida = DateTime::createFromFormat('d/m/Y H:i', $data['hsalida']);
                                $dtLlegada = DateTime::createFromFormat('d/m/Y H:i', $data['hfinservicio']);
                                $dtFinalizacion = DateTime::createFromFormat('d/m/Y H:i', $data['hllegada']);

                                $dtcita = $dtcita->format('H:i');
                                $dtsalida = $dtsalida->format('H:i');
                                $dtLlegada = $dtLlegada->format('H:i');
                                $dtFinalizacion = $dtFinalizacion->format('H:i');

                             }
                       }                     


                    
                       $tabla.="<tr bgcolor='$color' id='$data[id]'>
                                    <td width='10%' align='center' class='modord'>".date_format($fec,'d/m/Y')."</td>
                                    <td width='7%' align='center' class='modord'>".$dtcita."</td>
                                    <td width='7%' align='center' class='modord'>".$dtsalida."</td>
                                    <td width='25%' class='modord'>".htmlentities($data['servicio'])."</td>
                                    <td width='5%' class='modord'><div style='color:#$data[color];'>$data[interno]</div></td>
                                    <td width='25%' class='modord'>".htmlentities($data['razon_social'])."</td>
                                    <td width='10%' align='center' class='modord'>".$dtLlegada."</td>
                                    <td width='10%' align='center' class='modord'>".$dtFinalizacion."</td>";
                                    
                       /*if ($data[id_turismo])
                          $tabla.='<td><img class="vwt" src="../../../vista/maleta.png" width="20" height="20" border="0"></td>';
                       else
                           $tabla.="<td></td>";
                       if (($data[id_cond]) && ($data[id]) && ($data[id_turismo]))
                          $tabla.='<td id="cond-'.$data[id_cond].'"><img class="liq" src="../../../vista/dinero.png" width="25" height="25" border="0"></td></tr>';
                       else
                           $tabla.="<td></td>";*/
                     /*  date_modify($fec, '+1 day');
                                
                    }*
                    ///FIN DEL FOR


                    $data = mysql_fetch_array($result);             #FFFFFF#FF0000
                   /* if (($fserv != $data['li']) && ($cond == $data['legajo']))
                    {
                         if (($id_cond) &&($id_emp == 1)){
                           $ordenes = array();
                         }
                    }*/

              //   }
                 /*if (($id_cond) && ($id_emp == 1))
                 {
                    $ordenes = array();
                 }*/
         /*  }
           $tabla.="</tbody></table><br>";
     }*/
     $tabla.="<style type='text/css'>
                     table.order {
	                              font-family:arial;
	                              background-color: #CDCDCD;
                                  font-size: 8pt;
	                              text-align: left;
                               }
                     table.order thead tr th, table.tablesorter tfoot tr th {
                                                                            background-color: #e6EEEE;
                                                                            border: 1px solid #FFF;
	                                                                        font-size: 8pt;
	                                                                        padding: 4px;}
                     table.order tbody td {
	                                        color: #3D3D3D;
	                                        padding: 4px;
	                                        vertical-align: top;
                                         }
                     td.click, th.click{
                                        background-color: #bbb;
                                        }
                     td.hover, tr.hover{
                                        background-color: #69f;
                                        }
                     th.hover, tfoot td.hover{
                                              background-color: ivory;
                                              }
                     td.hovercell, th.hovercell{
                                                background-color: #abc;
                                                }
                     td.hoverrow, th.hoverrow{
                                              background-color: #6df;
                                              }
                     .interno{
                                              background-color: #6df;
                                              }
              </style>
               <script type='text/javascript'>
                                $('.order').tableHover();
                                $('.modord').click(function(){
                                                                    var id_orden = $(this).parent().attr('id');
                                                                    var dialog = $('<div style=\"display:none\" id=\"dialog\" class=\"loading\" align=\"center\"></div>').appendTo('body');
                                                                    dialog.dialog({
                                                                                   close: function(event, ui) {dialog.remove();},
                                                                                   title: 'Modificar orden',
                                                                                   width:850,
                                                                                   height:600,
                                                                                   modal:true,
                                                                                         show: {
                                                                                                effect: 'blind',
                                                                                                duration: 300
                                                                                         },
                                                                                         hide: {
                                                                                               effect: 'blind',
                                                                                               duration: 300
                                                                                               }
                                                                                   });
                                                                                   dialog.load('/vista/ordenes/modordsur.php',{orden:id_orden},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});

                                                                    });
                                $('.liq').click(function(){
                                                                    var id_orden = $(this).parent().parent().attr('id');
                                                                    var conductor = $(this).parent().attr('id');
                                                                    var dialog = $('<div style=\"display:none\" id=\"dialog\" class=\"loading\" align=\"center\"></div>').appendTo('body');
                                                                    dialog.dialog({
                                                                                   close: function(event, ui) {dialog.remove();},
                                                                                   title: 'Liquidacion de Conductores',
                                                                                   width:850,
                                                                                   height:450,
                                                                                   modal:true,
                                                                                         show: {
                                                                                                effect: 'blind',
                                                                                                duration: 300
                                                                                         },
                                                                                         hide: {
                                                                                               effect: 'blind',
                                                                                               duration: 300
                                                                                               }
                                                                                   });
                                                                                   dialog.load('/vista/ordenes/uplqvje.php',{orden:id_orden, cond:conductor},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});

                                                                    });
                                $('.vwt').click(function(){
                                                                    var id_orden = $(this).parent().parent().attr('id');
                                                                    var conductor = $(this).parent().attr('id');
                                                                    var dialog = $('<div style=\"display:none\" id=\"dialog\" class=\"loading\" align=\"center\"></div>').appendTo('body');
                                                                    dialog.dialog({
                                                                                   close: function(event, ui) {dialog.remove();},
                                                                                   title: 'Detalle orden turismo',
                                                                                   width:850,
                                                                                   height:450,
                                                                                   modal:true,
                                                                                         show: {
                                                                                                effect: 'blind',
                                                                                                duration: 350
                                                                                         },
                                                                                         hide: {
                                                                                               effect: 'blind',
                                                                                               duration: 350
                                                                                               }
                                                                                   });
                                                                                   dialog.load('/vista/ordenes/ordturview.php',{orden:id_orden, cond:conductor},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});

                                                                    });
               </script>";
     print $tabla;
  }
  
  function arrayNovedades($fecha, $conn){
           $sql = "select id_empleado, id_novedad
                   from novedades
                   where '$fecha' between desde and hasta and id_novedad in (16, 17, 15) and activa";
           $result = mysql_query($sql, $conn);
           $data = array();
           while ($row = mysql_fetch_array($result)){
                 $data[$row[0]] = $row[1];
           }
           return $data;
  }
  
  function calcularHsChofer($result, $novedades, $ajuste=15)
  {       /// $novedades[id_empleado] = id_novedad

     /*      $hs100 = 0;
           $hsNormales = 0;
           $hs50 = 0;
           $francoTrabajado = false; /// para saber si al finalizar el procedimiento debe calcular las horas como franco trabajado
           $log="cantidad de servicios ".count($result)."</br>";
           $i=1;
           $pendiente = true;
           $resultado = array();
           $draw = array();
           $paint = array();
           $ultorden="";
           $corte = 0;
           $pendienteDraw;
           //die(print_r($novedades));
           foreach ($result as $data){
                   try {
                   $inicio = new DateTime($data['dtdesde']);///inicio de la vuelta
                   $fin = new DateTime($data['dthasta']);    /// fin de la vuelta
                   } catch (Exception $e) { die('error al crear fechas calculo hs');}
                   if (array_key_exists($data['cond'], $novedades) && ($novedades[$data['cond']] == 18)){      ///feriado trabajado calcula las horas efectivas al 100%
                      $hs100+= ($fin->format('U') - $inicio->format('U'))/60;
                      $ultorden = $data;
                    //  $paint = pintarFila($inicio, $fin, 3, $paint);
                   }
                   elseif($novedades[$data['cond']] == 16){ ///franco trabajado desde el inicio al fin todo al 100
                      if (!$ultorden){
                         $comienzaFranco = clone $inicio;
                      }
                      $finalizaFranco = clone $fin;   ///hay que analizar si comienza un dia y termina al otro dia, seria franco trabajado???
                      $francoTrabajado = true;
                      $ultorden = $data;

                      //;
                   }
                   elseif($novedades[$data['cond']] == 15){ ///Si tiene diagramado un Franco no se le computan las horas, aunque haya trabajado
                   }
                   else{  ///jornada normal
                          $mins = ($fin->format('U') - $inicio->format('U'))/60; ///calcula el tiempo de la orden actual
                          if (!$ultorden){   ////primera orden procesada
                             $resultado = procesarOrdenInicio($inicio, $fin, $paint);
                             $hsNormales = $resultado[0];
                             $hs50 = $resultado[1];
                             $hs100 = $resultado[2];
                             $corte12 = $resultado[3];
                             $ultorden = $data;
                             $pendiente = true;
                             $pendienteDraw = $resultado[4];
                          }
                          else{
                                ////proceso al menos una orden
                                $finUltOrden = new DateTime($ultorden['dthasta']);   ////hora de fin de la ultima orden procesada
                                //////fragmento de codigo para corregir superpocicion horaria
                                if ($finUltOrden > $inicio){
                                   $inicio = clone $finUltOrden;
                                   if ($inicio > $fin){
                                      $fin = clone $inicio;
                                   }
                                }
                                //////////////////fin correccion solapamiento
                                $minCorte = ($inicio->format('U') - $finUltOrden->format('U'))/60;
                                if ($minCorte > 480){ ///esta procesando una orden correspondiente aldia anterior, se debe descartar
                                   if ($pendiente){
                                      $paint = array();
                                      $resultado = procesarOrdenInicio($inicio, $fin, $paint);
                                      $hsNormales = $resultado[0];
                                      $hs50 = $resultado[1];
                                      $hs100 = $resultado[2];
                                      $corte12 = $resultado[3];
                                      $paint = $resultado[4];
                                      $ultorden = $data;
                                   }
                                }
                                else{
                                        if ($pendienteDraw){
                                           $paint = $pendienteDraw;
                                           $pendienteDraw = "";
                                        }
                                        if (($minCorte +$ajuste) >= 120){
                                           if (!$corte){
                                              $minCorte = 0;
                                              $corte++;
                                           }
                                           else{
                                                $corte++;
                                           }
                                        }
                                        if ($fin <= $corte12){ ///la orden finaliza dentro de las 12 primeras horas
                                           if ($hsNormales+$mins+$minCorte > 480){ /// trabajo mas de 8 hs
                                              $faltante8 = 480 - $hsNormales;
                                              $saldo8hs = ($mins+$minCorte) - $faltante8;
                                              if ($corte == 1){
                                                 $finPaint8hs = clone $inicio;
                                                 $finPaint8hs->add(new DateInterval("PT".$faltante8."M"));
                                               //  $paint = pintarFila($inicio, $finPaint8hs, 1, $paint);
                                               //  $paint = pintarFila($finPaint8hs, $fin, 2, $paint);
                                              }
                                              else{
                                                 $finPaint8hs = clone $finUltOrden;
                                                 $finPaint8hs->add(new DateInterval("PT".$faltante8."M"));
                                              //   $paint = pintarFila($finUltOrden, $finPaint8hs, 1, $paint);
                                              //   $paint = pintarFila($finPaint8hs, $fin, 2, $paint);
                                              }
                                              $mins50 = ($hsNormales+$mins+$minCorte)-480;
                                              $hs50+= ($hsNormales+$mins+$minCorte)-480;
                                              $hsNormales = 480;
                                           }
                                           else{
                                                if ($corte == 1){
                                              //     $paint = pintarFila($inicio, $fin, 1, $paint);
                                                }
                                                else{
                                               //      $paint = pintarFila($finUltOrden, $fin, 1, $paint);

                                                }
                                                   // pintarFila($inicio, $fin, $color, $draw)
                                                $hsNormales+=$mins+$minCorte;
                                           }
                                        }
                                        elseif(($inicio <= $corte12)&&($fin > $corte12)){ ////la orden comienza antes de las 12 hs y termina despues de las 12hs de iniciado el turno
                                                $minsAlCorte = (($corte12->format('U') - $inicio->format('U'))/60);
                                                if (($hsNormales+$minsAlCorte+$minCorte) > 480){ /// trabajo mas de 8 hs
                                                   ///solo paa saber de que color debe pintar la celda  ///////////
                                                   $faltante8hs = (480 - $hsNormales);
                                                   $saldoExedente = ($minsAlCorte+$minCorte) - $faltante8hs;
                                                   if ($corte == 0){
                                                      $finPaint8hs = clone $finUltOrden;
                                                      $finPaint8hs->add(new DateInterval("PT".$faltante8hs."M"));
                                                 //     $paint = pintarFila($finUltOrden, $finPaint8hs, 1, $paint);
                                                 //     $paint = pintarFila($finPaint8hs, $corte12, 2, $paint);
                                                      //$paint = pintarFila($corte12, $fin, 3, $paint);
                                                   }
                                                   else{

                                                      $finPaint8hs = clone $inicio;
                                                      if ($faltante8hs > 0){
                                                         try{
                                                            /* if ($faltante8hs > 60){
                                                                $mins = $faltante8hs % 60;
                                                                $hour = floor($faltante8hs/60);
                                                                $finPaint8hs->add(new DateInterval("PT.".$hour."H".$mins."M"));
                                                             }
                        /*                                     else{ */
                       /*                                          $finPaint8hs->add(new DateInterval("PT".$faltante8hs."M"));
                                                             //}
                                                         } catch (Exception $e) {die("conductor $data[cond] horario $faltante8hs");}
                                                      }

                                               //       $paint = pintarFila($inicio, $finPaint8hs, 1, $paint);
                                                //      $paint = pintarFila($finPaint8hs, $corte12, 2, $paint);
                                                      //$paint = pintarFila($corte12, $fin, 3, $paint);

                                                   }
                                                   ////////////////////////////////////////////////////////////////
                                                   $alCorte50 = (($hsNormales+$minsAlCorte+$minCorte)-480);
                                                   $hs50+=(($hsNormales+$minsAlCorte+$minCorte)-480);
                                                   $hsNormales = 480;
                                                }
                                                else{
                                                     if ($corte == 0){
                                                     //   $paint = pintarFila($finUltOrden, $fin, 1, $paint);
                                                     }
                                                     else{
                                                        //  $paint = pintarFila($inicio, $fin, 1, $paint);
                                                     }
                                                     $hsNormales+=($minsAlCorte+$minCorte);
                                                }
                                            //    $paint = pintarFila($corte12, $fin, 3, $paint);
                                                $minsPostCorte = ($fin->format('U') - $corte12->format('U'))/60;
                                                $hs100+=$minsPostCorte;
                                        }
                                        else{ ////inicia despus de las 12 hs
                                             $hs100+=$mins+$minCorte;
                                           //  $paint = pintarFila($inicio, $fin, 3, $paint);
                                        }
                                        $ultorden = $data;
                              }
                          }
                   }
                   $i++;
           }
           if ($francoTrabajado){
              if ($finalizaFranco->format('H') < $comienzaFranco->format('H')){
                 $finzalizaFranco = clone $comienzaFranco;
                 $finzalizaFranco->setTime(23, 59, 59);
              }
              $hs100 = ($finalizaFranco->format('U') - $comienzaFranco->format('U'))/60;

           //   $paint = pintarFila($comienzaFranco, $finalizaFranco, 3, $paint);
           }
           return array(0=>$hsNormales, 1=>$hs50, 2=>$hs100, 3=>$paint);*/
  }

  function procesarOrdenInicio($inicio, $fin, $draw){
           //$draw = array();
           $mins = ($fin->format('U') - $inicio->format('U'))/60;
           $corte12 = clone $inicio;
           $corte12->add(new DateInterval("PT12H")); ///hora hasta la cual debe calcular horas al 50
           if ($mins <= 480){ //trabajo 8 hs
              $hsNormales=$mins;
              //$draw = pintarFila($inicio, $fin, 1, $draw);
           }
           elseif($mins <= 720){ //trabajo menos de 12 hs
                        $hfin8 = clone $inicio;
                        $hfin8->add(new DateInterval('PT8H'));
                       // $draw = pintarFila($inicio, $hfin8, 1, $draw);
                        $hsNormales=480;
                        $hs50=($mins-480);
                       // $draw = pintarFila($hfin8, $fin, 2, $draw);
           }
           else{
                        $hfin8 = clone $inicio;
                        $hfin8->add(new DateInterval('PT8H'));
                        //$draw = pintarFila($inicio, $hfin8, 1, $draw);
                        $hfin12 = clone $hfin8;
                        $hfin12->add(new DateInterval('PT4H'));
                       // $draw = pintarFila($hfin8, $hfin12, 2, $draw);
                       // $draw = pintarFila($hfin12, $fin, 3, $draw);
                        $hsNormales=480;
                        $hs50=240;
                        $hs100=($mins-720);
           }
           return array(0=>$hsNormales, 1=>$hs20, 2=>$hs100, 3=>$corte12, 4=> $draw);
  }
  
  function mintohour($min){
         $hours=floor($min/60);
         $min=$min%60;
         if($min < 10)
         return "$hours:0$min";
         else
         return "$hours:$min";
}
  
?>

