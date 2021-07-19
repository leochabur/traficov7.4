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

     //////////////////////////////////////nuev_ooooooooooooooooooooooooooooo 
     $conn = conexcion(true);
     $cond = $_POST['cond'];
     $desde = $_POST['desde'];
     $hasta = $_POST['hasta'];
     if ($cond != 0)
        $cond = "where id_empleado = $cond";
     else
         $cond = "";
     $sql = "SELECT fecha as fechafea, date_format(fecha, '%d/%m/%Y') as fecha FROM estadoDiagramasDiarios where (fecha between '$desde' and '$hasta') and (finalizado = 1) and (id_estructura = $_SESSION[structure])";
     $result = mysqli_query($conn, $sql);
     $state = array();

     $fechasdiagramas = "'0000-00-00'";
     while($row = mysqli_fetch_array($result)){
                $state[]=$row['fechafea'];
                if ($fechasdiagramas)
                {
                  $fechasdiagramas.=",";
                }
                $fechasdiagramas.="'$row[fechafea]'";
     }

     //die($fechasdiagramas);

    $sqlNovedades = "SELECT n.id_empleado, upper(concat(if (emp.id = 1, '', concat('(', upper(emp.razon_social),') - ')), apellido,', ', nombre)) as cond, legajo, 
                            if (desde < '$desde', '$desde', desde) as desde,
                            if (hasta > '$hasta', '$hasta', hasta) as hasta,
                            upper(CONCAT(nov_text, ' (',date_format(desde, '%d/%m/%Y'), ' - ', date_format(hasta, '%d/%m/%Y'),')')) as descripcion
                     FROM novedades n
                     inner join cod_novedades cn on cn.id = n.id_novedad
                     inner join (SELECT * FROM empleados $cond) em on (em.id_empleado = n.id_empleado) and (em.id_cargo = 1)
                     inner join empleadores emp on (emp.id = em.id_empleador) and (emp.id_estructura = em.id_estructura_empleador)
                     WHERE ((hasta between '$desde' and '$hasta') or 
                           (desde between '$desde' and '$hasta') or 
                           ('$desde' between desde and hasta) or 
                           ('$hasta' between desde and hasta)) and 
                           (n.activa) and (n.id_estructura = $_SESSION[structure])
                      order by desde";
                    //  die($sqlNovedades);



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
                                                                             'cli' => ''
                                                                             );
              $fdesde->add(new DateInterval('P1D'));
          }
      }

      //die($sqlNovedades);

      $sqlOrdenes = "SELECT fservicio, citaReal, saleReal, llegadaReal, hfinservicioReal, hcitacion, hsalida, hllegada, hfinservicio, upper(ordenes.nombre) as nombre, 
                            concat(if (emp.id = 1, '', concat('(', upper(emp.razon_social),') - ')), apellido,', ', e.nombre) as cond, 
                            e.id_empleado as idEmp, upper(c.razon_social) as razon_social, interno, idOrden
                     FROM
                     (   
                        SELECT *
                        FROM (SELECT fservicio, nombre,
                                     time_format(hcitacionreal, '%H:%i') as citaReal,
                                     time_format(hsalidaplantareal, '%H:%i') as saleReal,
                                     time_format(hllegadaplantareal, '%H:%i') as llegadaReal,
                                     time_format(hfinservicioreal, '%H:%i') as hfinservicioReal,
                                     time_format(hcitacion, '%H:%i') as hcitacion,
                                     time_format(hsalida, '%H:%i') as hsalida,
                                     time_format(hllegada, '%H:%i') as hllegada,
                                     time_format(hfinservicio, '%H:%i') as hfinservicio,
                                     id_chofer_1,
                                     id_cliente,
                                     id_estructura_cliente,
                                     id_micro,
                                     id as idOrden,
                                     if (fservicio in ($fechasdiagramas), hcitacionreal, hcitacion) as fechasort
                              FROM ordenes
                              WHERE fservicio between '$desde' and '$hasta' and not borrada and not suspendida and id_estructura = $_SESSION[structure] ) o
                        union
                        SELECT *
                        FROM (SELECT fservicio, nombre,
                                     time_format(hcitacionreal, '%H:%i') as citaReal,
                                     time_format(hsalidaplantareal, '%H:%i') as saleReal,
                                     time_format(hllegadaplantareal, '%H:%i') as llegadaReal,
                                     time_format(hfinservicioreal, '%H:%i') as hfinservicioreal,
                                     time_format(hcitacion, '%H:%i') as hcitacion,
                                     time_format(hsalida, '%H:%i') as hsalida,
                                     time_format(hllegada, '%H:%i') as hllegada,
                                     time_format(hfinservicio, '%H:%i') as hfinservicio,
                                     id_chofer_2,
                                     id_cliente,
                                     id_estructura_cliente,
                                     id_micro,
                                     id as idOrden,
                                     if (fservicio in ($fechasdiagramas), hcitacionreal, hcitacion) as fechasort
                              FROM ordenes
                              WHERE fservicio between '$desde' and '$hasta' and not borrada and not suspendida
                                    and id_estructura = $_SESSION[structure] and id_chofer_2 is not null) o                        
                    ) ordenes
                    inner join clientes c on c.id = ordenes.id_cliente and c.id_estructura = ordenes.id_estructura_cliente
                    inner join (SELECT nombre, apellido, id_empleado, id_empleador FROM empleados $cond)e on e.id_empleado = ordenes.id_chofer_1
                    inner join  empleadores emp on emp.id = e.id_empleador
                    left join unidades u on u.id = id_micro
                    order by e.id_empleado, fservicio, fechasort ASC";
//die($sqlOrdenes);
        
        $tabla="<input type='text' readonly size='27' value='Diagrama Sujeto a Modificaciones' style='background-color:#FFC0C0;'><br><br>";
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

            if(!in_array($row['fservicio'], $state))
            {
              $cita = $row['hcitacion'];
              $sale = $row['hsalida'];
              $llega = $row['hllegada'];
              $fina = $row['hfinservicio'];                        
            }
            else{
              $cita = $row['citaReal'];
              $sale = $row['saleReal'];
              $llega = $row['llegadaReal'];
              $fina = $row['hfinservicioReal'];                                                      
            }          

            $data[$row['idEmp']][$fecha->format('Ymd')][] = array('desc' => $row['nombre'], 
                                                                  'fec' => $fecha->format('d/m/Y'),
                                                                   'hc' => $cita,
                                                                   'hsr' => $sale,
                                                                   'hfs' => $llega,
                                                                   'hfr' => $fina,
                                                                    'int' => $row['interno'],
                                                                    'cli' => $row['razon_social'],
                                                                    'id' => $row['idOrden']);
        }


      $sqlOrdenesSinConductor = "SELECT fservicio, citaReal, saleReal, llegadaReal, hfinservicioReal, hcitacion, hsalida, hllegada, hfinservicio, upper(ordenes.nombre) as nombre, 
                            ' A DESIGNAR' as cond, 
                            0 as idEmp, upper(c.razon_social) as razon_social, interno, idOrden
                     FROM
                     (   
                        SELECT *
                        FROM (SELECT fservicio, nombre,
                                     time_format(hcitacionreal, '%H:%i') as citaReal,
                                     time_format(hsalidaplantareal, '%H:%i') as saleReal,
                                     time_format(hllegadaplantareal, '%H:%i') as llegadaReal,
                                     time_format(hfinservicioreal, '%H:%i') as hfinservicioReal,
                                     time_format(hcitacion, '%H:%i') as hcitacion,
                                     time_format(hsalida, '%H:%i') as hsalida,
                                     time_format(hllegada, '%H:%i') as hllegada,
                                     time_format(hfinservicio, '%H:%i') as hfinservicio,
                                     id_chofer_1,
                                     id_cliente,
                                     id_estructura_cliente,
                                     id_micro,
                                     id as idOrden
                              FROM ordenes
                              WHERE id_chofer_1 is null and id_chofer_2 is null and fservicio between '$desde' and '$hasta' and not borrada and not suspendida and id_estructura = $_SESSION[structure] ) o                    
                    ) ordenes
                    inner join clientes c on c.id = ordenes.id_cliente and c.id_estructura = ordenes.id_estructura_cliente
                    left join unidades u on u.id = id_micro
                    order by fservicio, citaReal";
        $resultOrdenes = mysqli_query($conn, $sqlOrdenesSinConductor) or die (mysqli_error($conn));
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

            if(!in_array($row['fservicio'], $state))
            {
              $cita = $row['hcitacion'];
              $sale = $row['hsalida'];
              $llega = $row['hllegada'];
              $fina = $row['hfinservicio'];                        
            }
            else{
              $cita = $row['citaReal'];
              $sale = $row['saleReal'];
              $llega = $row['llegadaReal'];
              $fina = $row['hfinservicioreal'];                                                      
            }          

            $data[$row['idEmp']][$fecha->format('Ymd')][] = array('desc' => $row['nombre'], 
                                                                  'fec' => $fecha->format('d/m/Y'),
                                                                   'hc' => $cita,
                                                                   'hsr' => $sale,
                                                                   'hfs' => $llega,
                                                                   'hfr' => $fina,
                                                                    'int' => $row['interno'],
                                                                    'cli' => $row['razon_social'],
                                                                    'id' => $row['idOrden']);
        }



       // die($sqlOrdenesSinConductor);
        asort($empleados);
        foreach ($empleados as $k => $cond)
        {
           $tabla.= "<table width='100%' class='order'>
                           <thead>
                                <tr>
                                    <th colspan='8'>Conductor:  ".htmlentities($cond)."</th>
                                </tr>
                                <tr>
                                    <th>Fecha de Servicio</th>
                                    <th>Hora de Citacion</th>
                                    <th>Hora de Salida</th>
                                    <th>Servicio</th>
                                    <th>Interno</th>
                                    <th>Cliente</th>
                                    <th>Hora Llegada</th>
                                    <th>Hora Finalizacion</th>
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
                $ff = DateTime::createFromFormat('d/m/Y', $ord['fec']);
                if(!in_array($ff->format('Y-m-d'), $state))
                {
                    
                 $color="#FFC0C0";
                }

               if (!$k)
               {
                    $color="#C0FFFF";
               }
                       $tabla.="<tr bgcolor='$color' class='modord' data-id='".$ord['id']."'>
                                    <td width='10%' align='center'>".$ord['fec']."</td>
                                    <td width='7%' align='center'>".$ord['hc']."</td>
                                    <td width='7%' align='center'>".$ord['hsr']."</td>
                                    <td width='25%'>".$ord['desc']."</td>
                                    <td width='5%'>".$ord['int']."</td>
                                    <td width='10%'>".$ord['cli']."</td>
                                    <td width='10%' align='center'>".$ord['hfs']."</td>
                                    <td width='10%' align='center'>".$ord['hfr']."</td>                      
                                    

                                </tr>";
             }
             $j++;
          }
          $tabla.="
                  </tbody
                           </table><br>";
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
                                                                    var id_orden = $(this).data('id');
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
                                                                                   dialog.load('/vista/ordenes/modord.php',{orden:id_orden},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});

                                                                    });
               </script>";
        print $tabla;
        exit;

        //////////////////////////////////////NUEVOoooooooooooooooooooooooooooo













     $conn = conexcion(true);
     $cond = $_POST['cond'];
     $desde = $_POST['desde'];
     $hasta = $_POST['hasta'];
     if ($cond != 0)
        $cond = "where id_cond = $cond";
     else
         $cond = "";
     $sql = "SELECT date_format(fecha, '%d/%m/%Y') as fecha FROM estadoDiagramasDiarios where (fecha between '$desde' and '$hasta') and (finalizado = 1) and (id_estructura = $_SESSION[structure])";
     $result = mysqli_query($conn, $sql);
     $state = array();
     while($row = mysqli_fetch_array($result)){
                $state[]=$row['fecha'];
     }

    $sql="SELECT o.*
          FROM(	(SELECT interno, upper(c.razon_social)as razon_social, 0 as id_cond, fservicio as fsrv, upper(servicio) as servicio, 
                        date_format(hsalida, '%H:%i') as hsalida, o.id, date_format(fservicio, '%d/%m/%Y') as fservicio, date_format(hcitacion, '%H:%i') as hcitacion, '                  ORDENES A DESIGNAR' as apellido, 0 as legajo, fservicio as li, fservicio as ls, DATEDIFF(fservicio, fservicio) as dias, date_format(hfinservicio, '%H:%i') as hfinservicio, emp.color, date_format(hllegada, '%H:%i') as hllegada, id_turismo, 0 as id_empleador,
                        date_format(hllegadaplantareal, '%H:%i') as hllegadaplantareal, date_format(hsalidaplantareal, '%H:%i') as hsalidaplantareal,
                        date_format(hfinservicioreal, '%H:%i') as hfinservicioreal, date_format(hcitacionreal, '%H:%i') as hcitacionreal, legEmple
                 FROM (SELECT  0 as id_turismo, nombre as servicio, hsalida, hfinservicio, ord.id, id_chofer_1, fservicio, hcitacion, id_cliente, 
                               id_estructura_cliente, id_micro, hllegada, hllegadaplantareal, hsalidaplantareal, hfinservicioreal, hcitacionreal, '' as legEmple
                       FROM ordenes ord
                       WHERE ((fservicio between '$desde' and '$hasta') and (id_estructura = $_SESSION[structure])) and (not suspendida) and (not borrada) and ((id_chofer_1 is null) or (id_chofer_1 = 0)) and ((id_chofer_2 is null) or (id_chofer_2 = 0))
                       ) o
                       INNER JOIN clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
                       LEFT JOIN unidades un on (un.id = o.id_micro)
                       left join empleadores emp on (emp.id = un.id_propietario) and (emp.id_estructura = un.id_estructura_propietario))
                       UNION
			                 (SELECT interno, upper(c.razon_social)as razon_social, o.id_chofer_1 as id_cond, fservicio as fsrv, upper(servicio) as servicio, date_format(hsalida, '%H:%i') as hsalida, o.id, date_format(fservicio, '%d/%m/%Y') as fservicio, date_format(hcitacion, '%H:%i') as hcitacion, upper(if(emp.id = 1,concat(ch1.apellido, ', ',ch1.nombre), concat('(',emp.razon_social,') ', ch1.apellido, ', ',ch1.nombre))) as apellido, concat(legajo, emp.id) as legajo, fservicio as li, fservicio as ls, DATEDIFF(fservicio, fservicio) as dias, date_format(hfinservicio, '%H:%i') as hfinservicio, null as color, date_format(hllegada, '%H:%i') as hllegada, id_turismo, ch1.id_empleador,date_format(hllegadaplantareal, '%H:%i') as hllegadaplantareal, date_format(hsalidaplantareal, '%H:%i') as hsalidaplantareal,
                               date_format(hfinservicioreal, '%H:%i') as hfinservicioreal, date_format(hcitacionreal, '%H:%i') as hcitacionreal, legajo as legEmple
                        FROM (SELECT  0 as id_turismo, nombre as servicio, hsalida, hfinservicio, ord.id, id_chofer_1, fservicio, hcitacion, id_cliente, id_estructura_cliente, id_micro, hllegada, hllegadaplantareal, hsalidaplantareal, hfinservicioreal, hcitacionreal
                              FROM ordenes ord
			                        WHERE ((fservicio between '$desde' and '$hasta') and (id_estructura = $_SESSION[structure])) and (not suspendida) and (not borrada)
			                  ) o
                        inner JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
                        inner join empleadores emp on (emp.id = ch1.id_empleador)
    			              inner join clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
                        left join unidades un on (un.id = o.id_micro)
                        where ch1.id_cargo in (1,2,3,4)
			 )
        UNION
			(SELECT interno, upper(c.razon_social)as razon_social, o.id_chofer_2 as id_cond, fservicio as fsrv, upper(servicio) as servicio, date_format(hsalida, '%H:%i') as hsalida, o.id, date_format(fservicio, '%d/%m/%Y') as fservicio, date_format(hcitacion, '%H:%i') as hcitacion, upper(if(emp.id = 1,concat(ch2.apellido, ', ',ch2.nombre), concat('(',emp.razon_social,') ', ch2.apellido, ', ',ch2.nombre))) as apellido, concat(legajo, emp.id) as legajo, fservicio as li, fservicio as ls, DATEDIFF(fservicio, fservicio) as dias, date_format(hfinservicio, '%H:%i') as hfinservicio, null as color, date_format(hllegada, '%H:%i') as hllegada, id_turismo, ch2.id_empleador,date_format(hllegadaplantareal, '%H:%i') as hllegadaplantareal, date_format(hsalidaplantareal, '%H:%i') as hsalidaplantareal,
                  date_format(hfinservicioreal, '%H:%i') as hfinservicioreal, date_format(hcitacionreal, '%H:%i') as hcitacionreal, ch2.legajo as legEmple
       FROM (SELECT  0 as id_turismo, nombre as servicio,hfinservicio, hsalida, ord.id, id_chofer_2, fservicio, hcitacion, id_cliente, id_estructura_cliente, id_micro, hllegada, hllegadaplantareal, hsalidaplantareal, hfinservicioreal, hcitacionreal
             FROM ordenes ord
			       WHERE ((fservicio between '$desde' and '$hasta') and (id_estructura = $_SESSION[structure])) and (not suspendida) and (not borrada)
			       ) o
        inner JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
        inner join empleadores emp on emp.id = ch2.id_empleador
			  inner join clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
        left join unidades un on (un.id = o.id_micro)
        where ch2.id_cargo in (1,2,3,4)
			 )
       UNION ALL
			(SELECT '' as interno, '' as razon_social, n.id_empleado as id_cond, '$desde' as fsrv, 
      upper(CONCAT(nov_text, ' (',date_format(desde, '%d/%m/%Y'), ' - ', date_format(hasta, '%d/%m/%Y'),')')) as servicio,
			        '00:00' as hsalida, 0 as id, date_format('$desde', '%d/%m/%Y') as fservicio, '00:00' as hcitacion, upper(if(emp.id = 1,concat(em.apellido, ', ',em.nombre), concat('(',emp.razon_social,') ', em.apellido, ', ',em.nombre))) as apellido, concat(legajo, emp.id) as legajo, if (desde < '$desde', '$desde', desde) as li, if (hasta > '$hasta', '$hasta', hasta) as ls, DATEDIFF(if (hasta > '$hasta', '$hasta', hasta), if (desde < '$desde', '$desde', desde)) as dias, '00:00' as hfinservicio, 'FFFFFF' as color, '00:00' as hllegada, 0 as id_turismo, em.id_empleador,
                '00:00' as hllegadaplantareal, '00:00' as hsalidaplantareal, '00:00' as hfinservicioreal, '00:00' as hcitacionreal, legajo as legEmple
			 FROM (SELECT * FROM novedades WHERE ((activa) and((hasta between '$desde' and '$hasta') or (desde between '$desde' and '$hasta') or ('$desde' between desde and hasta) or ('$hasta' between desde and hasta))) ) n
			 inner join cod_novedades cn on cn.id = n.id_novedad
			 inner join empleados em on em.id_empleado = n.id_empleado and em.id_estructura = $_SESSION[structure]
			 inner join empleadores emp on emp.id = em.id_empleador
			 where em.id_cargo in (1,2,3,4)
			)
        ) o
        $cond
        order by apellido, id_cond, li, hsalida";

    // die($sql);
     $result = mysqli_query($conn, $sql) or die (mysqli_error($conn));

    // die(": ".mysqli_num_rows($result));
     $data = mysqli_fetch_array($result);
     $tabla="<input type='text' readonly size='27' value='Diagrama Sujeto a Modificaciones' style='background-color:#FFC0C0;'><br><br>";
     $noves = array();
     while ($data){
           $cond = $data['legajo'];
           $id_cond = $data['id_cond'];
           $id_emp = $data['id_empleador'];
           $tabla.= "<table width='100%' class='order'>
                           <thead>
                                <tr>
                                    <th colspan='10'>Conductor:  ".htmlentities($data['apellido'])."     -     Legajo:  $data[legEmple]</th>
                                </tr>
                                <tr>
                                    <th>Fecha de Servicio</th>
                                    <th>Hora de Citacion</th>
                                    <th>Hora de Salida</th>
                                    <th>Servicio</th>
                                    <th>Interno</th>
                                    <th>Cliente</th>
                                    <th>Hora Llegada</th>
                                    <th>Hora Finalizacion</th>
                                    <th colspan='2'></th>
                                </tr>
                           </thead>
                           <tbody>";
           while (($data) && ($cond == $data['legajo'])){
                 $j=0;
                 //$print = false;
                 $ordenes = array();
                 while (($data) && ($cond == $data['legajo'])){
                    if(!in_array($data['fservicio'], $state))
                    {
                      $cita = $data['hcitacion'];
                      $sale = $data['hsalida'];
                      $llega = $data['hllegada'];
                      $fina = $data['hfinservicio'];                        
                    }
                    else{
                      $cita = $data['hcitacionreal'];
                      $sale = $data['hsalidaplantareal'];
                      $llega = $data['hllegadaplantareal'];
                      $fina = $data['hfinservicioreal'];                                                      
                    }                  
                    if (($data['id'])&&($id_emp == 1)){
                      /* try{
                          if(!in_array($data['fservicio'], $state))
                          {
                            $desde = new DateTime("$data[li] $data[hcitacion]");
                            $hasta = new DateTime("$data[li] $data[hfinservicio]");                            
                          }
                          else{
                          
                            $desde = new DateTime("$data[li] $data[hcitacionreal]");
                            $hasta = new DateTime("$data[li] $data[hfinservicioreal]");                                
                          }
                        
                        if ($hasta < $desde){
                           $hasta->add(new DateInterval("P1D"));
                        }
                        $ordenes[] = array('cond' => $id_cond, 'dtdesde' => $desde->format('Y-m-d H:i:00'), 'dthasta' => $hasta->format('Y-m-d H:i:00'));

                        } catch (Exception $e) { 
                                                $desde = new DateTime();
                                                $hasta = new DateTime(); 
                                                }*/
                    }
                    $fserv=$data['li'];
                    if (!array_key_exists($noves, $fserv)){
                       $noves[$fserv] = arrayNovedades($fserv, $conn);
                       
                    }
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
                       
                       $tabla.="<tr bgcolor='$color' id='$data[id]'>
                                    <td width='10%' align='center' class='modord'>".date_format($fec,'d/m/Y')."</td>
                                    <td width='7%' align='center' class='modord'>$cita</td>
                                    <td width='7%' align='center' class='modord'>$sale</td>
                                    <td width='25%' class='modord'>".htmlentities($data['servicio'])."</td>
                                    <td width='5%' class='modord'><div style='color:#$data[color];'>$data[interno]</div></td>
                                    <td width='25%' class='modord'>".htmlentities($data['razon_social'])."</td>
                                    <td width='10%' align='center' class='modord'>$llega</td>
                                    <td width='10%' align='center' class='modord'>$fina</td>";
                                    
                       if ($data[id_turismo])
                          $tabla.='<td><img class="vwt" src="../../../vista/maleta.png" width="20" height="20" border="0"></td>';
                       else
                           $tabla.="<td></td>";
                       if (($data[id_cond]) && ($data[id]) && ($data[id_turismo]))
                          $tabla.='<td id="cond-'.$data[id_cond].'"><img class="liq" src="../../../vista/dinero.png" width="25" height="25" border="0"></td></tr>';
                       else
                           $tabla.="<td></td>";
                       date_modify($fec, '+1 day');
                    }
                    $data = mysqli_fetch_array($result);             #FFFFFF#FF0000
                    if (($fserv != $data['li']) && ($cond == $data['legajo'])){
                     if (($id_cond) &&($id_emp == 1)){
                   /*    $horas = calcularHsChofer($ordenes, $noves[$fserv]);
                       $tabla.="<tr><td colspan='10'>Horas Normales = ".mintohour($horas[0])." - Horas al 50 = ".mintohour($horas[1])." - Horas al 100 = ".mintohour($horas[2])."</td></tr>";*/
                       $ordenes = array();
                     }
                    }
                    if ($fserv != $data['li']){
                       $j++;
                    }
                 }
                 if (($id_cond) && ($id_emp == 1)){
               /*  $horas = calcularHsChofer($ordenes, $noves[$fserv]);
                 $tabla.="<tr><td colspan='10'>Horas Normales = ".mintohour($horas[0])." - Horas al 50 = ".mintohour($horas[1])." - Horas al 100 = ".mintohour($horas[2])."</td></tr>";*/
                 $ordenes = array();
                 }
           }
           $tabla.="</tbody></table><br>";
     }

    // die(": ".mysqli_num_rows($result));
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
                                                                                   dialog.load('/vista/ordenes/modord.php',{orden:id_orden},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});

                                                                    });
               </script>";
     print $tabla;
  }
  
  function arrayNovedades($fecha, $conn){
           $sql = "select id_empleado, id_novedad
                   from novedades
                   where '$fecha' between desde and hasta and id_novedad in (16, 17, 15) and activa";
           $result = mysqli_query($conn, $sql);
           $data = array();
           while ($row = mysqli_fetch_array($result)){
                 $data[$row[0]] = $row[1];
           }
           return $data;
  }
  
  function calcularHsChofer($result, $novedades, $ajuste=15){       /// $novedades[id_empleado] = id_novedad

           $hs100 = 0;
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
                   } catch (Exception $e) { 
                                             $inicio = new DateTime();///inicio de la vuelta
                                             $fin = new DateTime();    /// fin de la vuelta
                                            }
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
                                                             else{ */
                                                                 $finPaint8hs->add(new DateInterval("PT".$faltante8hs."M"));
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
           return array(0=>$hsNormales, 1=>$hs50, 2=>$hs100, 3=>$paint);
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

