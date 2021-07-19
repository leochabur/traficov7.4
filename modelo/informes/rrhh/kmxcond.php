<?php
  session_start();
  set_time_limit(0);
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');

  define('STRUCTURED', $_SESSION['structure']);
  $accion= $_POST['accion'];

  if ($accion == 'list'){
     $conn = conexcion(true);
     $cond = $_POST['cond'];
     $desde = $_POST['desde'];
     $hasta = $_POST['hasta'];
     if ($cond != 0)
        $cond = "where id_empleado = $cond";
     else
         $cond = "";
     $sql = "SELECT date_format(fecha, '%d/%m/%Y') as fecha FROM estadoDiagramasDiarios where (fecha between '$desde' and '$hasta') and (finalizado = 1) and (id_estructura = $_SESSION[structure])";
     $result = mysqli_query($conn, $sql);
     $state = array();
     while($row = mysqli_fetch_array($result)){
                $state[]=$row['fecha'];
     }


    if ($_SESSION['structure'] != 2)
    {
    $sqlNovedades = "SELECT n.id_empleado, concat(apellido,', ', nombre) as cond, legajo, 
                            concat(apellido,', ', nombre) as cond,
                            if (desde < '$desde', '$desde', desde) as desde,
                            if (hasta > '$hasta', '$hasta', hasta) as hasta,
                            upper(CONCAT(nov_text, ' (',date_format(desde, '%d/%m/%Y'), ' - ', date_format(hasta, '%d/%m/%Y'),')')) as descripcion
                     FROM novedades n
                     inner join cod_novedades cn on cn.id = n.id_novedad
                     inner join (SELECT * FROM empleados $cond) em on (em.id_empleado = n.id_empleado) and (em.id_cargo = 1)
                     inner join empleadores emp on (emp.id = em.id_empleador) and (emp.id = $_POST[emp])
                     WHERE ((hasta between '$desde' and '$hasta') or 
                           (desde between '$desde' and '$hasta') or 
                           ('$desde' between desde and hasta) or 
                           ('$hasta' between desde and hasta)) and 
                           (n.activa) and (n.id_estructura = $_SESSION[structure])
                      order by desde";



      $empleados = array();

      $resultNovedades = mysqli_query($conn, $sqlNovedades);
      $data = array();
      while($row = mysqli_fetch_array($resultNovedades))
      {
          $empleados[$row['id_empleado']] = $row['cond'];
          if (!array_key_exists($row['id_empleado'],$data))
          {
            $data[$row['id_empleado']] = array();
          }

          $fdesde = DateTime::createFromFormat('Y-m-d', $row['desde']);
          $fhasta = DateTime::createFromFormat('Y-m-d', $row['hasta']);
          while ($fdesde <= $fhasta)
          {
              if (!array_key_exists($fdesde->format('Ymd'), $data[$row['id_empleado']])) //crea un registro para la fecha dada para el conductor
              {
                $data[$row['id_empleado']][$fdesde->format('Ymd')] = array();
              }
              $data[$row['id_empleado']][$fdesde->format('Ymd')][] = array('desc' => $row['descripcion'], 
                                                                            'fec' => $fdesde->format('d/m/Y'),
                                                                            'hc' => '',
                                                                             'hsr' => '',
                                                                             'hfs' => '',
                                                                             'hfr' => '',
                                                                             'int' => '',
                                                                             'cli' => '');
              $fdesde->add(new DateInterval('P1D'));
          }
      }

     // die($sqlNovedades);

      $sqlOrdenes = "SELECT fservicio, citaReal, saleReal, hfinservicio, finaReal, ordenes.nombre, 
                            concat(apellido,', ', e.nombre) as cond, e.id_empleado as idEmp, c.razon_social, interno
                     FROM
                     (
                        SELECT *
                        FROM (SELECT fservicio, nombre,
                                     time_format(hcitacionreal, '%H:%i') as citaReal,
                                     time_format(hsalidaplantareal, '%H:%i') as saleReal,
                                     time_format(hfinservicioreal, '%H:%i') as finaReal,
                                     time_format(hfinservicio, '%H:%i') as hfinservicio,
                                     id_chofer_1,
                                     id_cliente,
                                     id_estructura_cliente,
                                     id_micro
                              FROM ordenes
                              WHERE fservicio between '$desde' and '$hasta' and not borrada and not suspendida and id_estructura = $_SESSION[structure] ) o
                        union
                        SELECT *
                        FROM (SELECT fservicio, nombre,
                                     time_format(hcitacionreal, '%H:%i') as citaReal,
                                     time_format(hsalidaplantareal, '%H:%i') as saleReal,
                                     time_format(hfinservicioreal, '%H:%i') as finaReal,
                                     time_format(hfinservicio, '%H:%i') as hfinservicio,
                                     id_chofer_2,
                                     id_cliente,
                                     id_estructura_cliente,
                                     id_micro
                              FROM ordenes
                              WHERE fservicio between '$desde' and '$hasta' and not borrada and not suspendida
                                    and id_estructura = $_SESSION[structure] and id_chofer_2 is not null) o
                    ) ordenes
                    inner join clientes c on c.id = ordenes.id_cliente and c.id_estructura = ordenes.id_estructura_cliente
                    inner join (SELECT nombre, apellido, id_empleado, id_empleador FROM empleados $cond)e on e.id_empleado = ordenes.id_chofer_1
                    inner join (SELECT id FROM empleadores WHERE id = $_POST[emp]) emp on emp.id = e.id_empleador
                    left join unidades u on u.id = id_micro
                    order by e.id_empleado, fservicio, citaReal";


        $resultOrdenes = mysqli_query($conn, $sqlOrdenes) or die (mysqli_error($conn));
        while ($row = mysqli_fetch_array($resultOrdenes))
        { 
            $empleados[$row['idEmp']] = $row['cond'];
            $fecha = DateTime::createFromFormat('Y-m-d', $row['fservicio']);
            if (!array_key_exists($row['idEmp'], $data))
            {
              $data[$row['idEmp']] = array();
            }
            if (!array_key_exists($fecha->format('Ymd'), $data[$row['idEmp']]))
            {
              $data[$row['idEmp']][$fecha->format('Ymd')] = array();
            }
            $data[$row['idEmp']][$fecha->format('Ymd')][] = array('desc' => $row['nombre'], 
                                                                  'fec' => $fecha->format('d/m/Y'),
                                                                   'hc' => $row['citaReal'],
                                                                   'hsr' => $row['saleReal'],
                                                                   'hfs' => $row['hfinservicio'],
                                                                   'hfr' => $row['finaReal'],
                                                                    'int' => $row['interno'],
                                                                    'cli' => $row['razon_social']);
        }

        asort($empleados);
        foreach ($empleados as $k => $cond)
        {
           $tabla.= "<table width='100%' class='order'>
                           <thead>
                                <tr>
                                    <th colspan='9'>Conductor:  ".htmlentities($cond)."</th>
                                </tr>
                                <tr>
                                    <th>Fecha de Servicio</th>
                                    <th>Hora de Citacion</th>
                                    <th>Hora de Salida</th>
                                    <th>Hora Fin Diag.</th>
                                    <th>Hora Fin Real</th>
                                    <th>Servicio</th>
                                    <th>Interno</th>
                                    <th>Cliente</th>
                                    <th>Observaciones</th>
                                </tr>
                           </thead>
                           <tbody>
                           ";
          $detalle = $data[$k]; //recupera todas las ordenes y novedades de un conductor
          $j=0;
          ksort($detalle);
          foreach ($detalle as $ordenes)
          {   
             
             $color = (($j%2)==0)?'#CFCFCF':'#96B8B6';
             foreach ($ordenes as $ord)
             {
                       $tabla.="<tr bgcolor='$color' class='modord'>
                                    <td width='10%' align='center'>".$ord['fec']."</td>
                                    <td width='7%' align='center'>".$ord['hc']."</td>
                                    <td width='7%' align='center'>".$ord['hsr']."</td>
                                    <td width='10%' align='center'><font color='red'>".$ord['hfs']."</font></td>
                                    <td width='10%' align='center'>".$ord['hfr']."</td>
                                    <td width='25%'>".$ord['desc']."</td>
                                    <td width='5%'>".$ord['int']."</td>
                                    <td width='10%'>".$ord['cli']."</td>
                                    <td width='25%'></td>

                                </tr>";
             }
             $tabla.="<tr><td colspan='9' bgcolor='#FFFFFF'><hr align='tr'></td></tr>";
             $j++;
          }
          $tabla.="
                  </tbody
                           </table>";
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
               </script>";
        print $tabla;
      }
      else
      {

/*
                 time_format(if (hsalidaplantareal < hcitacionreal, hsalidaplantareal, hcitacionreal), '%H:%i') as citaReal,     
                 time_format(hsalidaplantareal, '%H:%i') as saleReal,
                 time_format(hllegadaplantareal, '%H:%i') as llegadaReal,
                 time_format(if (vacio, hfinservicioreal, if (hllegadaplantareal > hfinservicioreal, hllegadaplantareal, hfinservicioreal)), '%H:%i') as finaReal      */  
    $sql="SELECT o.*,
                 time_format(hcitacionreal, '%H:%i') as citaReal,     
                 time_format(hsalidaplantareal, '%H:%i') as saleReal,
                 time_format(hllegadaplantareal, '%H:%i') as llegadaReal,
                 time_format(hfinservicioreal, '%H:%i') as finaReal,     
                 hfinservicio,
                 date_format(citacion, '%d/%m/%Y %H:%i') as citasur,     
                 date_format(salida, '%d/%m/%Y %H:%i') as salesur,
                 date_format(llegada, '%d/%m/%Y %H:%i') as llegasur,
                 date_format(finalizacion, '%d/%m/%Y %H:%i') as finasur,
                 hhs.id as idsur,
                 if (hhs.id is not null, date(citacion), flimite) as li
          FROM(
			    (SELECT interno, upper(c.razon_social)as razon_social, o.id_chofer_1 as id_cond, fservicio as fsrv, upper(servicio) as servicio, hsalida, 
                       o.id, date_format(fservicio, '%d/%m/%Y') as fservicio, hcitacion, upper(if(emp.id = 1,concat(ch1.apellido, ', ',ch1.nombre), 
                       concat('(',emp.razon_social,') ', ch1.apellido, ', ',ch1.nombre))) as apellido, legajo, fservicio as flimite, fservicio as ls, DATEDIFF(fservicio, fservicio) as dias, 
                       date_format(hfinservicio, '%H:%i') as hfinservicio, comentario, hllegadaplantareal, hsalidaplantareal, hfinservicioreal, hcitacionreal, vacio
                FROM (SELECT  nombre as servicio, hsalida, hfinservicio, id, id_chofer_1, fservicio, hcitacion, id_cliente, 
                              id_estructura_cliente, id_micro, comentario, hllegadaplantareal, hsalidaplantareal, hfinservicioreal, hcitacionreal, vacio
			                FROM (SELECT * FROM ordenes WHERE id_estructura = $_SESSION[structure]) o
			          WHERE (not suspendida) and (not borrada)) o
                inner JOIN (SELECT * from empleados $cond) ch1 ON (ch1.id_empleado = o.id_chofer_1) and (ch1.id_cargo = 1)
                inner join empleadores emp on (emp.id = ch1.id_empleador) and (emp.id = $_POST[emp])
			          inner join clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
                left join unidades un on (un.id = o.id_micro)
                )
                UNION
			         (SELECT interno, upper(c.razon_social)as razon_social, o.id_chofer_2 as id_cond, fservicio as fsrv, upper(servicio) as servicio, hsalida, 
                       o.id, date_format(fservicio, '%d/%m/%Y') as fservicio, hcitacion, upper(if(emp.id = 1,concat(ch2.apellido, ', ',ch2.nombre), 
                       concat('(',emp.razon_social,') ', ch2.apellido, ', ',ch2.nombre))) as apellido, legajo, fservicio as flimite, fservicio as ls, DATEDIFF(fservicio, fservicio) as dias, 
                       date_format(hfinservicio, '%H:%i') as hfinservicio, comentario, hllegadaplantareal, hsalidaplantareal, hfinservicioreal, hcitacionreal, vacio
                FROM (SELECT  nombre as servicio,hfinservicio, hsalida, id, id_chofer_2, fservicio, hcitacion, id_cliente, 
                              id_estructura_cliente, id_micro, comentario, hllegadaplantareal, hsalidaplantareal, hfinservicioreal, hcitacionreal, vacio
			                FROM (SELECT * FROM ordenes WHERE id_estructura = $_SESSION[structure]) o
			                WHERE (not suspendida) and (not borrada)) o
                inner JOIN (SELECT * FROM empleados $cond) ch2 ON (ch2.id_empleado = o.id_chofer_2) and (ch2.id_cargo = 1)
                inner join empleadores emp on (emp.id = ch2.id_empleador) and (emp.id = $_POST[emp])
        			  inner join clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
                left join unidades un on (un.id = o.id_micro)
			         )
               UNION ALL
        			(SELECT '' as interno, '' as razon_social, n.id_empleado as id_cond, '$desde' as fsrv, upper(CONCAT(nov_text, ' (',date_format(desde, '%d/%m/%Y'), ' - ', date_format(hasta, '%d/%m/%Y'),')')) as servicio, '00:00' as hsalida, 
                      0 as id, date_format('$desde', '%d/%m/%Y') as fservicio, '00:00' as hcitacion, upper(if(emp.id = 1,concat(em.apellido, ', ',em.nombre), concat('(',emp.razon_social,') ', em.apellido, ', ',em.nombre))) as apellido, legajo, 
                      if (desde < '$desde', '$desde', desde) as flimite, if (hasta > '$hasta', '$hasta', hasta) as ls, DATEDIFF(if (hasta > '$hasta', '$hasta', hasta), if (desde < '$desde', '$desde', desde)) as dias, '00:00' as hfinservicio, 
                      '' as comentario, '00:00' as hllegadaplantareal, '00:00' as hsalidaplantareal, '00:00' as hfinservicioreal, '00:00' as hcitacionreal, 0 as vacio
        			 FROM (SELECT * FROM novedades WHERE (((hasta between '$desde' and '$hasta') or (desde between '$desde' and '$hasta') or ('$desde' between desde and hasta) or ('$hasta' between desde and hasta)) and (activa) and (id_estructura = $_SESSION[structure])) ) n
        			 inner join cod_novedades cn on cn.id = n.id_novedad
        			 inner join (SELECT * FROM empleados $cond)em on (em.id_empleado = n.id_empleado) and (em.id_cargo = 1)
        			 inner join empleadores emp on (emp.id = em.id_empleador) and (emp.id = $_POST[emp])
        			)) o
        left join horarios_ordenes_sur hhs ON hhs.id_orden = o.id
        WHERE (date(citacion) between '$desde' and '$hasta')
        order by apellido, citacion, hcitacionreal";
    //  die($sql);

     $result = mysqli_query($conn, $sql) or die ($sql);

     $data = mysqli_fetch_array($result);
     $tabla="<input type='text' readonly size='27' value='Diagrama Sujeto a Modificaciones' style='background-color:#FFC0C0;'><br><br>
     Exportar <a href='/modelo/informes/rrhh/kmxcondpdf.php?desde=$desde&hasta=$hasta&cond=$_POST[cond]&emp=$_POST[emp]' target='_blank'><img src='../../../pdf.png' width='30' height='30' border='0'></a><br><br>";
     while ($data){
           $cond = $data['legajo'];
           $tabla.= "<table width='100%' class='order'>
                           <thead>
                                <tr>
                                    <th colspan='9'>Conductor:  ".htmlentities($data['apellido'])."     -     Legajo:  $data[legajo]</th>
                                </tr>
                                <tr>
                                    <th>Fecha de Servicio</th>
                                    <th>Hora de Citacion</th>
                                    <th>Hora de Salida</th>
                                    <th>Hora Fin Diag.</th>
                                    <th>Hora Fin Real</th>
                                    <th>Servicio</th>
                                    <th>Interno</th>
                                    <th>Cliente</th>
                                    <th>Observaciones</th>
                                </tr>
                           </thead>
                           <tbody>";
           $ult='';
           $j=0;
           while (($data) && ($cond == $data['legajo'])){

              //   while (($data) && ($cond == $data['legajo'])){
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

                       $cita = $data['citaReal'];
                       $sale = $data['saleReal'];
                       $fina = $data['hfinservicio'];
                       $finaReal = $data['finaReal'];
                       if ($data['idsur'])
                       {
                         $cita = $data['citasur'];
                         $sale = $data['salesur'];
                         $fina = $data['finasur'];
                         $finaReal = $data['finasur'];
                       }
                       $tabla.="<tr bgcolor='$color' id='$data[id]' class='modord'>
                                    <td width='10%' align='center'>".date_format($fec,'d/m/Y')."</td>
                                    <td width='7%' align='center'>$cita</td>
                                    <td width='7%' align='center'>$sale</td>
                                    <td width='10%' align='center'><font color='red'>$fina</font></td>
                                    <td width='10%' align='center'>$finaReal</td>
                                    <td width='25%'>".$data['servicio']."</td>
                                    <td width='5%'>$data[interno]</td>
                                    <td width='10%'>".htmlentities($data['razon_social'])."</td>
                                    <td width='25%'>$data[comentario]</td>

                                </tr>";
                       $ult = $fec;
                       date_modify($fec, '+1 day');
                    }
                    $data = mysqli_fetch_array($result);
                    
                    if ($fserv != $data['li']){
                       $tabla.="<tr><td colspan='9' bgcolor='#FFFFFF'><hr align='tr'></td></tr>";
                       $j++;
                    }
               //  }

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
               </script>";
     print $tabla;
   }
     }
     elseif ($accion == 'loadcnd')
     {
        $sql = "select id_empleado, upper(concat(apellido,', ',nombre))
                from empleados
                where id_empleador = $_POST[emp] and activo
                order by apellido, nombre";
        $conn = conexcion(true);
        $result = mysqli_query($conn, $sql);
        $option = "<option value='0'>Todos</option>";
        while ($row = mysqli_fetch_array($result))
        {
          $option.="<option value='$row[0]'>$row[1]</option>";
        }
        print $option;
     }
  
?>

