<?php
  set_time_limit(0);
  session_start();

  ////////////////// modulo para dar de alta Ciudades /////////////////////

  include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');
   include ('../enviomail/sendmail.php');
  $accion = $_POST['accion'];
  $estado='';
  if (isset($_POST['estados']))
  {
    $estado = $_POST['estados'];
  }
  
  if ($accion == 'sve'){ ///codigo para guardar ////
        $ok=1;
        $fecha = dateToMysql($_POST['fecha'], '/');
        $conn = conexcion();

        
		    $sql="DELETE FROM estadoDiagramasDiarios WHERE (fecha ='$fecha') and (id_estructura = $_SESSION[structure])";
		    $resul = mysql_query($sql, $conn);

        $sql = "update ordenes set hcitacionreal = hcitacion, hsalidaplantareal = hsalida, hllegadaplantareal = hllegada, hfinservicioreal = hfinservicio WHERE (fservicio ='$fecha') and (id_estructura = $_SESSION[structure])";
        $result = mysql_query($sql, $conn);
        
        $sql="INSERT INTO estadoDiagramasDiarios (id_estado, fecha, finalizado, usuario, fechahorafinalizacion, id_estructura) values ($_POST[estados], '$fecha', $estado, $_SESSION[userid] , now(), $_SESSION[structure])";
        $resul = mysql_query($sql, $conn);

        
        $sql = "select s.id as idServicio,
                       ord.id as idOrden,
                       c.id as idCronograma,
                       ord.nombre as Cronograma,
                       cl.id as idCliente,
                       cl.razon_social as Cliente,
                       o.ciudad as Origen, 
                       d.ciudad as Destino,
                       ord.fservicio as Fecha_Servicio,
                       u.interno as interno,
                       ord.hsalidaplantareal as Horario_Cabecera,
                       ord.hllegadaplantareal as hllegada
        from (select * from ordenes where fservicio = '$fecha' and not suspendida and not borrada and id_servicio is not null) ord
        inner join servicios s on s.id = ord.id_servicio and s.id_estructura = ord.id_estructura_servicio
        inner join cronogramas c on s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura
        inner join ciudades o on o.id = ciudades_id_origen and o.id_estructura = ciudades_id_estructura_origen
        inner join ciudades d on d.id = ciudades_id_destino and d.id_estructura = ciudades_id_estructura_destino
        inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
        left join unidades u on u.id = ord.id_micro
        where not c.vacio and c.id_estructura = 1 and (c.id in (3679, 3681, 6355, 264, 3125, 3126, 3243, 3244, 3302, 5339, 5624, 5625, 5626) or cl.id <> 13)";

        $result = mysql_query($sql, $conn) or die(mysql_error($conn));
        $ordenes = array();
        while ($row = mysql_fetch_array($result))
        {
          $ordenes[] = array('idServicio' => $row['idServicio'],
                             'idOrden' => $row['idOrden'],
                             'idCronograma' => $row['idCronograma'],
                             'Cronograma' => $row['Cronograma'],
                              'idCliente' => $row['idCliente'],
                              'Cliente' => $row['Cliente'],
                              'Origen' => $row['Origen'],
                              'Destino' => $row['Destino'],
                              'Fecha_Servicio' => $row['Fecha_Servicio'],
                              'interno' => $row['interno'],
                              'Horario_Cabecera' => $row['Horario_Cabecera'],
                              'Horario_Llegada' => $row['hllegada']);

        }
        $payload = json_encode($ordenes);
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://admtickets.masterbus.net/api/integrations/traffic/trips",
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS =>"{'trips':$payload}",
          CURLOPT_RETURNTRANSFER => 1, 
          CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer d8Ypl7DMuQsHjjW/INIHxRXjiV1BSezxrmbTV8EWZvk=",
            "Content-Type: text/plain"
          ),
        ));
        $response = curl_exec($curl);

        $json = json_decode($response, true);
        if (isset($json['success']))
        {
          $result = ($json['success']?1:0);
        }
        else
        {
          $result = 0;
        }
        curl_close($curl);
        $sql = "INSERT INTO estadocomunicaciones (fecha, fecha_diagrama, estado) VALUES (now(), '$fecha',".$result.")";
        mysql_query($sql, $conn);

        mysql_close($conn);
        print (json_encode(array('ok' => true)));
        
  }
  elseif($accion == 'vdga'){
        $fecha = dateToMysql($_POST['fechav'], '/');
        $conn = conexcion();
        $sql = "Select upper(razon_social) as nombre, upper(c.nombre) as servicio, date_format(hcitacion, '%H:%i') as cita, date_format(hsalida, '%H:%i') as sale,
                       date_format(hllegada, '%H:%i') as llega
                FROM servicioscontroldiagrama scd
                inner join servicios s on s.id = scd.id_servicio
                inner join cronogramas c on c.id = s.id_cronograma
                inner join clientes cl on cl.id = c.id_cliente
                where (scd.id_controlDiagrama = $_POST[ctrl]) and (scd.id_servicio not in (select id_servicio from ordenes where fservicio = '$fecha' and id_estructura = $_SESSION[structure]))";

        $tabla='<div id="tabs">
                     <ul>
                         <li><a href="#tabs-1">Faltante de diagramar</a></li>
                         <li><a href="#tabs-2">Diagramado en exceso</a></li>
                     </ul>
                     <div id="tabs-1">
                          <table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                                 <thead>
                                        <tr class="ui-widget-header">
                                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th>H. Citacion</th>
                        <th>H. Salida</th>
                        <th>H. Llegada</th>
                    </tr>
                    </thead>
                    <tbody>';
        $result = ejecutarSQL($sql, $conn);
        while ($row = mysql_fetch_array($result)){
              $tabla.="<tr>
                           <td>".htmlentities($row[0])."</td>
                           <td>".htmlentities($row[1])."</td>
                           <td>$row[2]</td>
                           <td>$row[3]</td>
                           <td>$row[4]</td>
                       </tr>";
        }
        $tabla.='</tbody>
                 </table>
                 </div>';
        $sql="Select upper(razon_social) as nombre, upper(c.nombre) as servicio, date_format(o.hcitacion, '%H:%i') as cita, date_format(o.hsalida, '%H:%i') as sale,
                     date_format(o.hllegada, '%H:%i') as llega, interno, concat(if(o.id_chofer_1 is null, '', e1.apellido),' / ',if(o.id_chofer_2 is null,'',e2.apellido)) as conductor
              FROM  ordenes o
              inner join servicios s on s.id = o.id_servicio
              inner join cronogramas c on c.id = s.id_cronograma
              inner join clientes cl on cl.id = c.id_cliente
              left join unidades u on u.id = o.id_micro
              left join empleados e1 on e1.id_empleado = o.id_chofer_1
              left join empleados e2 on e2.id_empleado = o.id_chofer_2
              where (fservicio = '$fecha')and (not borrada) and (not suspendida) and (o.id_servicio not in (select id_servicio from servicioscontroldiagrama where id_controlDiagrama = $_POST[ctrl])) and o.id_estructura = $_SESSION[structure]";
        $tabla.='<div id="tabs-2">
                 <table id="example_out" name="example_out" class="ui-widget ui-widget-content" width="100%" align="center">
                        <thead>
                               <tr class="ui-widget-header">
                                        <th>Cliente</th>
                                        <th>Servicio</th>
                                        <th>H. Salida</th>
                                        <th>H. Llegada</th>
                                        <th>Conductores</th>
                                        <th>Interno</th>
                                        </tr>
                    </thead>
                    <tbody>';
        $result = ejecutarSQL($sql, $conn);
        while ($row = mysql_fetch_array($result)){
              $tabla.="<tr>
                           <td>".htmlentities($row[0])."</td>
                           <td>".htmlentities($row[1])."</td>
                           <td>$row[3]</td>
                           <td>$row[4]</td>
                           <td>$row[6]</td>
                           <td>$row[5]</td>
                       </tr>";
        }
                 
        $tabla.='</tbody>
                 </table>
                 </div>
                 </div>';
        
        
        $tabla.='
<style>
                         #example { font-size: 85%; }
                         #example tbody tr:hover {

                                        background-color: #FF8080;
                                        }
                         #example_out { font-size: 85%; }
                         #example_out tbody tr:hover {

                                        background-color: #FF8080;
                                        }

                         #example tr:nth-child(odd) {
                                           background-color:#f2f2f2;
                                           }
                         #example tr:nth-child(even) {
                                            background-color:#fbfbfb;
                                            }
                         #example_out tr:nth-child(odd) {
                                           background-color:#f2f2f2;
                                           }
                         #example_out tr:nth-child(even) {
                                            background-color:#fbfbfb;
                                            }
                  </style>
        <script>
                              $( "#tabs" ).tabs();
                    </script>';
        print $tabla;
  }
  elseif($accion == 'vdsrv'){
        $fecha = dateToMysql($_POST['fecha'], '/');
        $conn = conexcion();
        $sql = "select upper(o.nombre), upper(razon_social), time_format(hsalida, '%H:%i') as salida, time_format(hllegada, '%H:%i') as llegada, upper(concat(apellido,', ', e1.nombre)) as conductor, interno
from ordenes o
inner join clientes c on c.id = o.id_cliente and c.id_estructura = o.id_estructura_cliente
left join unidades u on u.id = o.id_micro
left join empleados e1 on e1.id_empleado = o.id_chofer_1
where fservicio = '$fecha' and o.id_estructura = 1 and not suspendida and not borrada
      and o.id_servicio in (SELECT id_servicio
                                FROM agendaDiagramas
                                where fecha_diagrama = '$fecha' and id_estructura_servicio = $_SESSION[structure] and sino)
order by hsalida";
//die($sql);
        $tabla='<div id="tabs">
                     <ul>
                         <li><a href="#tabs-1">Servicios en Seguimiento Diagramados</a></li>
                         <li><a href="#tabs-2">Servicios en Seguimiento NO Diagramados</a></li>
                         <li><a href="#tabs-3">Servicios Diagramados Fuera del Seguimiento</a></li>
                     </ul>
                     <div id="tabs-1">
                          <table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                                 <thead>
                                        <tr class="ui-widget-header">
                                            <th>Servicio</th>
                                            <th>Cliente</th>
                                            <th>H. Salida</th>
                                            <th>H. Llegada</th>
                                            <th>Conductor</th>
                                            <th>Interno</th>
                                        </tr>
                                 </thead>
                    <tbody>';
        $result = ejecutarSQL($sql, $conn);
        while ($row = mysql_fetch_array($result)){
              $tabla.="<tr>
                           <td>".htmlentities($row[0])."</td>
                           <td>".htmlentities($row[1])."</td>
                           <td>$row[2]</td>
                           <td>$row[3]</td>
                           <td>$row[4]</td>
                           <td>$row[5]</td>
                       </tr>";
        }
        $tabla.='</tbody>
                 </table>
                 </div>';
        $sql="select upper(c.nombre), upper(razon_social), time_format(hsalida, '%H:%i') as salida, time_format(hllegada, '%H:%i') as llegada
                     from cronogramas c
inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
inner join servicios s on s.id_cronograma = c.id and s.id_estructura_cronograma  = c.id_estructura
inner join (select * from agendaDiagramas where fecha_diagrama = '$fecha' and sino) ad on ad.id_servicio = s.id and ad.id_estructura_servicio = s.id_estructura
where  s.id not in (SELECT id_servicio
                    FROM ordenes o
                    where fservicio = '$fecha' and o.id_estructura = $_SESSION[structure] and not suspendida and not borrada and id_servicio is not null)
order by hsalida";
        $tabla.='<div id="tabs-2">
                 <table id="example_out" name="example_out" class="ui-widget ui-widget-content" width="100%" align="center">
                        <thead>
                               <tr class="ui-widget-header">
                                        <th>Servicio</th>
                                        <th>Cliente</th>
                                        <th>H. Salida</th>
                                        <th>H. Llegada</th>
                                        </tr>
                    </thead>
                    <tbody>';
        $result = ejecutarSQL($sql, $conn);
        while ($row = mysql_fetch_array($result)){
              $tabla.="<tr>
                           <td>".htmlentities($row[0])."</td>
                           <td>".htmlentities($row[1])."</td>
                           <td>$row[2]</td>
                           <td>$row[3]</td>
                       </tr>";
        }
        
        $sql="select upper(o.nombre), upper(razon_social), time_format(hsalida, '%H:%i') as salida, time_format(hllegada, '%H:%i') as llegada, upper(concat(apellido,', ', e1.nombre)) as conductor, interno
              from ordenes o
              inner join clientes c on c.id = o.id_cliente and c.id_estructura = o.id_estructura_cliente
              left join unidades u on u.id = o.id_micro
              left join empleados e1 on e1.id_empleado = o.id_chofer_1
              where fservicio = '$fecha' and o.id_estructura = $_SESSION[structure] and not suspendida and not borrada
                    and o.id_servicio in (SELECT id_servicio
                                          FROM agendaDiagramas
                                          where fecha_diagrama <> '$fecha' and id_estructura_servicio = $_SESSION[structure] and sino
                                                and id_servicio not in (SELECT id_servicio
                                                                        FROM agendaDiagramas
                                                                        where fecha_diagrama = '$fecha' and id_estructura_servicio = $_SESSION[structure] and sino))";

        $tabla.='</tbody>
                 </table>
                 </div>
                 <div id="tabs-3">
                          <table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                                 <thead>
                                        <tr class="ui-widget-header">
                                            <th>Servicio</th>
                                            <th>Cliente</th>
                                            <th>H. Salida</th>
                                            <th>H. Llegada</th>
                                            <th>Conductor</th>
                                            <th>Interno</th>
                                        </tr>
                                 </thead>
                    <tbody>';
        $result = ejecutarSQL($sql, $conn);
        while ($row = mysql_fetch_array($result)){
              $tabla.="<tr>
                           <td>".htmlentities($row[0])."</td>
                           <td>".htmlentities($row[1])."</td>
                           <td>$row[2]</td>
                           <td>$row[3]</td>
                           <td>$row[4]</td>
                           <td>$row[5]</td>
                       </tr>";
        }
        $tabla.='</tbody>
                 </table>
                 </div>
                 </div>';


        $tabla.='
<style>
                         #example { font-size: 85%; }
                         #example tbody tr:hover {

                                        background-color: #FF8080;
                                        }
                         #example_out { font-size: 85%; }
                         #example_out tbody tr:hover {

                                        background-color: #FF8080;
                                        }

                         #example tr:nth-child(odd) {
                                           background-color:#f2f2f2;
                                           }
                         #example tr:nth-child(even) {
                                            background-color:#fbfbfb;
                                            }
                         #example_out tr:nth-child(odd) {
                                           background-color:#f2f2f2;
                                           }
                         #example_out tr:nth-child(even) {
                                            background-color:#fbfbfb;
                                            }
                  </style>
        <script>
                              $( "#tabs" ).tabs();
                    </script>';
        print $tabla;
  }
  elseif($accion == 'sendinf')
  {
    $fecha = dateToMysql($_POST['fecha'], '/');
    $conn = conexcion(true);

    $state = mysqli_query($conn,"SELECT * FROM estadoDiagramasDiarios where fecha = '$fecha' and id_estructura = $_SESSION[structure] and id_estado = 1");
    
    if (mysqli_num_rows($state))
    {
            $dataUser = mysqli_query($conn, "select apenom, mail, passwordProvisoria from usuarios where id = $_SESSION[userid]");
            $user = $clave = null;
            if ($row = mysqli_fetch_array($dataUser, MYSQLI_ASSOC))
            {
              $user = $row['mail'];
              $clave = $row['passwordProvisoria'];
            }
            mysqli_free_result($dataUser);
            if ($user && $clave)
            {
                $sql = getSqlSend($fecha, $_SESSION['structure'], $_POST['prop']);
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                $tabla = "";
                $i = 0;
                $noConfig = array();
                $success = array();
                $errors = array();
                while ($row)
                {
                    $emp = $row['idEmp'];
                    $mail = $row['email'];
                    $fec = $_POST['fecha'];
                    $razonSocial = $row['empleador'];
                    $tabla = "<h3>Servicios diagramados para: $razonSocial</h3>";
                    $emailDestino = $row['email'];
                    $enviar = false;
                    while ($row && ($emp == $row['idEmp']))
                    {   $color = '#CFCFCF';
                        $emple = $row['id_chofer_1'];
                        $tabla.= "<table width='100%' class='order'>
                                       <thead>
                                            <tr><th colspan='10'>Conductor:  ".($row['conductor'])."<br></th></tr>
                                            <tr>
                                                <th>Fecha de Servicio</th>
                                                <th>Hora de Citacion</th>
                                                <th>Hora de Salida</th>
                                                <th>Servicio</th>
                                                <th>Interno</th>
                                                <th>Cliente</th>
                                                <th>Hora Llegada</th>
                                            </tr>
                                       </thead>
                                       <tbody>";
                        while (($row) && ($emp == $row['idEmp']) && ($emple == $row['id_chofer_1']))
                        {
                           $enviar = true;
                           $tabla.="<tr bgcolor='$color'>
                                        <td width='10%' align='center'>$row[fecha]</td>
                                        <td width='7%' align='center'>$row[citacion]</td>
                                        <td width='7%' align='center'>$row[salida]</td>
                                        <td width='25%'>".htmlentities($row['nombre'])."</td>
                                        <td width='5%'>$row[interno]</td>
                                        <td width='25%'>".htmlentities($row['cliente'])."</td>
                                        <td width='10%' align='center'>$row[llegada]</td>
                                    </tr>";
                            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                        }
                        $i++;
                        $tabla.='</tbody>
                                </table>
                                <br>';
                    }
                    $tabla.='<style type="text/css">
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
                              </style>';
                    if ($enviar)
                    {
                        if ($emailDestino)
                        {
                            $emailDestino.= ','.$user.',mdepeon@masterbus.net,leochabur@gmail.com';
                            if (enviarMailFromUser($emailDestino, $tabla, 'Diagrama de servicios Master Bus - Fecha: '.$fec, $user, $clave))
                            {
                                $success[] = array(0 => $razonSocial, 1 => $emailDestino);
                            }
                            else
                            {
                                $errors[] = array(0 => $razonSocial, 1 => $emailDestino);
                            }
                        }
                        else
                        {
                            $noConfig[] = $razonSocial;
                        }
                    }
                }

                $response = '<h3>';
                if (!count($success))
                {
                  $response.= 'No se ha enviado ningun correo electronico';
                }
                else
                {
                  $response.= 'Se han enviado los correos a los siguientes destinatarios:   
                                <lu>';
                  foreach ($success as $v)
                  {
                      $response.= "<li>$v[0] ($v[1])</li>";
                  }
                  $response.'</lu> <br>';
                }

                if (count($errors))
                {
                    $response.="NO se han podido enviar los correso a los siguientes destinatarios:
                                <lu>";
                    foreach ($errors as $v)
                    {
                        $response.= "<li>$v[0] ($v[1])</li>";
                    }
                    $response.'</lu> <br>';
                }
                if (count($noConfig))
                {
                    $response.="No se han configurado los e-mail a los siguientes :
                                <lu>";
                    foreach ($noConfig as $v)
                    {
                        $response.= "<li>$v</li>";
                    }
                    $response.'</lu> <br>';
                }
                $response.='</h3>';
                mysqli_free_result($result);
                mysqli_close($conn);
                print $response;
                exit();
            }
            else
            {
              mysqli_close($conn);
              print "<br><h3>El usuario ".strtoupper($_SESSION['apenomUser'])." no tiene configurado correctamente los datos para enviar correos electronicos!</h3>";
              exit();
            }
    }
    else
    {
        mysqli_free_result($state);
        mysqli_close($conn);
        print "<br><h3>El diagrama del $_POST[fecha] aun no se ha finalizado!</h3>";
        exit();
    }
}

function getSqlSend($fecha, $str, $empl)
{
    $where = '';
    if ($empl)
    {
      $where = " AND emp.id = $empl";
    }

    $sql = "select date_format(fservicio, '%d/%m/%Y') as fecha,
                   time_format(hcitacionreal, '%H:%i') as citacion,
                   time_format(hsalidaplantareal, '%H:%i') as salida,
                   ord.nombre,
                   interno,
                   cl.razon_social as cliente,
                   time_format(hllegadaplantareal, '%H:%i') as llegada,
                   o.ciudad,
                    d.ciudad,
                    concat(apellido,', ',e.nombre) as conductor,
                    id_chofer_1,
                    emp.razon_social as empleador,
                    emp.id as idEmp,
                    emp.mail as email
            from (select nombre, fservicio, hllegadaplantareal, hsalidaplantareal, hcitacionreal, id_chofer_1, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, id_estructura_cliente, id_micro
                  from ordenes where fservicio = '$fecha' and not suspendida and not borrada and id_chofer_2 is null and id_estructura = $str
                  union all
                  select nombre, fservicio, hllegadaplantareal, hsalidaplantareal, hcitacionreal, id_chofer_2, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, id_estructura_cliente, id_micro
                  from ordenes where fservicio = '$fecha' and not suspendida and not borrada and id_chofer_1 is null and id_estructura = $str) ord
            inner join ciudades o on o.id = id_ciudad_origen and o.id_estructura = id_estructura_ciudad_origen
            inner join ciudades d on d.id = id_ciudad_destino and d.id_estructura = id_estructura_ciudad_destino
            inner join clientes cl on cl.id = ord.id_cliente and cl.id_estructura = ord.id_estructura_cliente
            left join unidades u on u.id = ord.id_micro
            left join empleados e on e.id_empleado = id_chofer_1
            left join empleadores emp on emp.id = e.id_empleador AND emp.id_estructura = e.id_estructura_empleador
            WHERE emp.id <> 1 $where 
            order by emp.id, apellido, id_chofer_1, hcitacionreal";
    return $sql;
}
?>

