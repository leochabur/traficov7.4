<?php
session_start();

header("Content-Type: application/vnd.ms-excel");

header("Expires: 0");

header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

header("content-disposition: attachment;filename=vacaciones_personal.xls");



     include ('../../../controlador/bdadmin.php');


     $accion = 'lnov';
     
    if($accion == 'lnov'){

        $des = new DateTime();
        $has = new DateTime();

        $des->setTimestamp($_GET['des']);
        $has->setTimestamp($_GET['has']);


        $desde = $des->format('Y-m-d');
        $hasta = $has->format('Y-m-d');
        $conn = conexcion();
        $empleado="";
        $nofranco="";

        if ($_GET['modaf'] == 1)
        {
           $nofranco="and (cn.id <> 15)";
        }
        if (isset($_GET['emp']))
        {
           $empleado = "and (n.id_empleado = $_GET[emp])";
        }

        if ($_GET['tt']){
           $titular = "and (em.id_empleador = $_GET[tt])";
        }
              $sql = "SELECT ca.descripcion, legajo, n.id_empleado, n.id, upper(concat(em.apellido, ', ',em.nombre)) as empleado, date_format(desde, '%d/%m/%Y') as desde, date_format(hasta, '%d/%m/%Y') as hasta, cn.nov_text, e.nombre, u.apenom, empl.razon_social as emplea
                      FROM novedades n
                      inner join cod_novedades cn on cn.id = n.id_novedad
                      inner join estructuras e on e.id = n.id_estructura
                      inner join usuarios u on u.id = n.usuario
                      inner join empleados em on em.id_empleado = n.id_empleado
                      inner join empleadores empl ON empl.id = em.id_empleador
                      left join cargo ca on ca.id = em.id_cargo AND ca.id_estructura = em.id_estructura_cargo
                      where  (n.id_estructura in (SELECT uxe.id_estructura
                                                                                     FROM usuarios u
                                                                                     inner join usuariosxestructuras uxe on uxe.id_usuario = u.id
                                                                                     where u.id = $_SESSION[userid]))
                             and ((desde between '$desde' and '$hasta') or (hasta between '$desde' and '$hasta') or ('$desde' between  desde and hasta)or ('$hasta' = hasta) or ('$desde' = desde) or ('$hasta' between desde and hasta)) and (n.activa) $nofranco $empleado $titular
                      order by nombre, desde, nov_text";

              $result = mysql_query($sql, $conn) or die ("error al conectar ".mysql_error($conn));

              $tabla ='<table id="example" name="example">
	                      <thead>
		                         <tr>
                                     <th>Legajo</th>
                                     <th>Apellido, Nombre</th>
                                     <th>Empleador</th>
                                     <th>Puesto</th>
                                     <th>Fecha Desde</th>
			                         <th>Fecha Hasta</th>
			                         <th>Novedad</th>
			                         <th>Afectado a estructura:</th>
			                         <th>Usuario Alta</th>
		                          </tr>
                          </thead>
                          <tbody>';
              $j=0;
              while ($data = mysql_fetch_array($result)){
                    $color = (($j++%2)==0) ? "#D3D3D3" : "#F3F3F3";
                    $tabla.="<tr bgcolor='$color'>
                                 <td>$data[legajo]</td>
                                 <td>".($data['empleado'])."</td>
                                 <td>$data[emplea]</td>
                                 <td>$data[descripcion]</td>
                                 <td>$data[desde]</td>
                                 <td>$data[hasta]</td>
                                 <td>$data[nov_text]</td>
                                 <td>$data[nombre]</td>
                                 <td>".($data[apenom])."</td>
                             </tr>";
              }


        $tabla.='</table>';
        mysql_free_result($result);
        mysql_close($conn);
        print $tabla;
     }

?>

