<?
  session_start();

  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
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
          FROM(	(SELECT interno, upper(c.razon_social)as razon_social, 0 as id_cond, fservicio as fsrv, upper(servicio) as servicio, date_format(hsalida, '%H:%i') as hsalida, o.id, date_format(fservicio, '%d/%m/%Y') as fservicio, date_format(hcitacion, '%H:%i') as hcitacion, '                  ORDENES A DESIGNAR' as apellido, 0 as legajo, fservicio as li, fservicio as ls, DATEDIFF(fservicio, fservicio) as dias
                 FROM (SELECT  nombre as servicio, hsalida, id, id_chofer_1, fservicio, hcitacion, id_cliente, id_estructura_cliente, id_micro
                       FROM ordenes
                       WHERE ((fservicio between '$desde' and '$hasta') and (id_estructura = $_SESSION[structure])) and (not suspendida) and (not borrada) and (id_chofer_1 is null) and (id_chofer_2 is null)
                       ) o
                 INNER JOIN clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
                 LEFT JOIN unidades un on (un.id = o.id_micro))
                UNION
			(SELECT interno, upper(c.razon_social)as razon_social, o.id_chofer_1 as id_cond, fservicio as fsrv, upper(servicio) as servicio, date_format(hsalida, '%H:%i') as hsalida, o.id, date_format(fservicio, '%d/%m/%Y') as fservicio, date_format(hcitacion, '%H:%i') as hcitacion, upper(if(emp.id = 1,concat(ch1.apellido, ', ',ch1.nombre), concat('(',emp.razon_social,') ', ch1.apellido, ', ',ch1.nombre))) as apellido, legajo, fservicio as li, fservicio as ls, DATEDIFF(fservicio, fservicio) as dias
                         FROM (SELECT  nombre as servicio, hsalida, id, id_chofer_1, fservicio, hcitacion, id_cliente, id_estructura_cliente, id_micro
			       FROM ordenes
			       WHERE ((fservicio between '$desde' and '$hasta') and (id_estructura = $_SESSION[structure])) and (not suspendida) and (not borrada)
			       ) o
                         inner JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
                         inner join empleadores emp on emp.id = ch1.id_empleador
			             inner join clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
                         left join unidades un on (un.id = o.id_micro)
			 )
        UNION
			(SELECT interno, upper(c.razon_social)as razon_social, o.id_chofer_2 as id_cond, fservicio as fsrv, upper(servicio) as servicio, date_format(hsalida, '%H:%i') as hsalida, o.id, date_format(fservicio, '%d/%m/%Y') as fservicio, date_format(hcitacion, '%H:%i') as hcitacion, upper(if(emp.id = 1,concat(ch2.apellido, ', ',ch2.nombre), concat('(',emp.razon_social,') ', ch2.apellido, ', ',ch2.nombre))) as apellido, legajo, fservicio as li, fservicio as ls, DATEDIFF(fservicio, fservicio) as dias
                         FROM (SELECT  nombre as servicio, hsalida, id, id_chofer_2, fservicio, hcitacion, id_cliente, id_estructura_cliente, id_micro
			       FROM ordenes
			       WHERE ((fservicio between '$desde' and '$hasta') and (id_estructura = $_SESSION[structure])) and (not suspendida) and (not borrada)
			       ) o
                         inner JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
                         inner join empleadores emp on emp.id = ch2.id_empleador
			 inner join clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
                         left join unidades un on (un.id = o.id_micro)
			 )
        UNION ALL
			(SELECT '' as interno, '' as razon_social, n.id_empleado as id_cond, '$desde' as fsrv, upper(nov_text) as servicio,
			        '00:00' as hsalida, 0 as id, date_format('$desde', '%d/%m/%Y') as fservicio, '00:00' as hcitacion, upper(if(emp.id = 1,concat(em.apellido, ', ',em.nombre), concat('(',emp.razon_social,') ', em.apellido, ', ',em.nombre))) as apellido, legajo, if (desde < '$desde', '$desde', desde) as li, if (hasta > '$hasta', '$hasta', hasta) as ls, DATEDIFF(if (hasta > '$hasta', '$hasta', hasta), if (desde < '$desde', '$desde', desde)) as dias
			 FROM (SELECT * FROM novedades WHERE (((hasta between '$desde' and '$hasta') or (desde between '$desde' and '$hasta') or ('$desde' between desde and hasta) or ('$hasta' between desde and hasta)) and (id_estructura = $_SESSION[structure])) ) n
			 inner join cod_novedades cn on cn.id = n.id_novedad
			 inner join empleados em on em.id_empleado = n.id_empleado
			 inner join empleadores emp on emp.id = em.id_empleador
			)
        ) o
        $cond
        order by apellido, li, hcitacion";


     $result = mysql_query($sql, $conn);

     $data = mysql_fetch_array($result);
     $tabla="<input type='text' readonly size='27' value='Diagrama Sujeto a Modificaciones' style='background-color:#FFC0C0;'><br><br>";
     while ($data){
           $cond = $data['legajo'];
           $tabla.= "<table width='80%' class='order'>
                           <thead>
                                <tr>
                                    <th colspan='6'>Conductor:  ".htmlentities($data['apellido'])."     -     Legajo:  $data[legajo]</th>
                                </tr>
                                <tr>
                                    <th>Fecha de Servicio</th>
                                    <th>Hora de Citacion</th>
                                    <th>Hora de Salida</th>
                                    <th>Servicio</th>
                                    <th>Interno</th>
                                    <th>Cliente</th>
                                </tr>
                           </thead>
                           <tbody>";
           while (($data) && ($cond == $data['legajo'])){
                 $j=0;
                 while (($data) && ($cond == $data['legajo'])){
                    $fserv=$data['li'];
                    $fec = date_create($fserv);
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
                       
                       $tabla.="<tr bgcolor='$color' id='$data[id]' class='modord'>
                                    <td width='10%'>".date_format($fec,'d/m/Y')."</td>
                                    <td width='10%'>$data[hcitacion]</td>
                                    <td width='10%'>$data[hsalida]</td>
                                    <td width='25%'>".htmlentities($data['servicio'])."</td>
                                    <td width='10%'>$data[interno]</td>
                                    <td width='25%'>".htmlentities($data['razon_social'])."</td>
                                </tr>";
                       date_modify($fec, '+1 day');
                    }
                    $data = mysql_fetch_array($result);
                    if ($fserv != $data['li']){
                       $j++;
                    }
                 }

           }
           $tabla.="</tbody></table><br>";
     }
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
              </style>
               <script type='text/javascript'>
                                $('.order').tableHover();
                                $('.modord').click(function(){
                                                                    var id_orden = $(this).attr('id');
                                                                    var dialog = $('<div style=\"display:none\" id=\"dialog\" class=\"loading\" align=\"center\"></div>').appendTo('body');
                                                                    dialog.dialog({
                                                                                   close: function(event, ui) {dialog.remove();},
                                                                                   title: 'Modificar orden',
                                                                                   width:850,
                                                                                   height:600,
                                                                                   modal:true,
                                                                                         show: {
                                                                                                effect: 'blind',
                                                                                                duration: 1000
                                                                                         },
                                                                                         hide: {
                                                                                               effect: 'blind',
                                                                                               duration: 1000
                                                                                               }
                                                                                   });
                                                                                   dialog.load('/vista/ordenes/modord.php',{orden:id_orden},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});

                                                                    });
               </script>";
     print $tabla;
  }
  
?>
