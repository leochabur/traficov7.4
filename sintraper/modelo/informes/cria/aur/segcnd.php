<?php
  session_start();

  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('../../../../controlador/bdadmin.php');
  include ('../../../../modelo/utils/dateutils.php');

  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];

  if (true){

     $cond = $_POST['cond'];
     $desde = $_POST['desde'];
     $hasta = $_POST['hasta'];
     
     $tabla.="<table width='100%' class='treetable' id='example-advanced'>
                     <thead>
                              <tr class='ui-widget-header'>
                                  <th>Fecha Servicio</th>
                                  <th>H. Citacion</th>
                                  <th>H. Salida</th>
                                  <th>H. LLegada</th>
                                  <th>H. Fin Serv.</th>
                                  <th>Cliente</th>
                                  <th>Conductor 1</th>
                                  <th>Conductor 2</th>
                                  <th>Interno</th>
                                  <th>Fecha Accion</th>
                                  <th>Usuario Responsable</th>
                              </tr>
                              </thead>
                              <tbody>";
                                 $con = conexcion();
                                 $sql = "select date_format(fservicio, '%d/%m/%Y'),
                                                date_format(hcitacion, '%H:%i'),
                                                date_format(hsalida, '%H:%i'),
                                                date_format(hllegada, '%H:%i'),
                                                date_format(hfinservicio, '%H:%i'),
                                                razon_social,
                                                ch1.id_empleado,
                                                upper(concat(ch1.apellido,', ',ch1.nombre)),
                                                ch2.id_empleado,
                                                upper(concat(ch2.apellido,', ',ch2.nombre)),
                                                interno,
                                                upper(apenom),
                                                checkeada,
                                                finalizada,
                                                date_format(fecha_accion, '%d/%m/%Y - %H:%i:%s'),
                                                upper(o.nombre),
                                                o.id as ir_orden,
                                                suspendida,
                                                checkeada,
                                                borrada,
                                                finalizada
from(
      select *, 0 as modif
      from ordenes
      union all
      select *, 1 as modif
      from ordenes_modificadas
)o
left JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
left JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
inner join clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
left join unidades un on (un.id = o.id_micro)
inner join usuarios usu on usu.id = id_user
where (o.id in (select id from ordenes where (fservicio between '$desde' and '$hasta') and ($cond in (id_chofer_1, id_chofer_2))))
order by fservicio, o.id, fecha_accion desc";

     $query = mysql_query($sql, $con) or die(mysql_error($con));
     $row = mysql_fetch_array($query);
     while ($row){
           $id = $row[16];
           $ulcheck="";
           $ulcerr="";
           $ulint="";
           $ulch1="";
           $ulch2="";
           $ulhc="";
           $ulhs="";
           $ulhl="";
           $ulhf="";
           $tabla.="<tr data-tt-id='$id' >
                        <td>(+)$row[0]</td>
                        <td>$row[1]</td>
                        <td>$row[2]</td>
                        <td>$row[3]</td>
                        <td>$row[4]</td>
                        <td>".htmlentities($row[5])."</td>
                        <td>".htmlentities($row[7])."</td>
                        <td>".htmlentities($row[9])."</td>
                        <td>$row[10]</td>
                        <td>$row[14]</td>
                        <td>$row[11]</td>
                    </tr>";
           while (($row) && ($id == $row[16])){
                                 $i=0;
                                     if ($i > 0){
                                            if ($ulhc != $row[1]){
                                               $accion = "Cambio H. Citacion";
                                            }
                                            elseif ($ulhs != $row[2]){
                                                   $accion = "Cambio H. Salida";
                                            }
                                            elseif($ulhl != $row[3]){
                                                         $accion = "Cambio H. Llegada";
                                            }
                                            elseif ($ulhf != $row[4]){
                                                   $accion = "Cambio H. Fin Servicio";
                                            }
                                            elseif($ulch1 != $row[6]){
                                                          $accion = "Cambio Conductor 1";
                                            }
                                            elseif($ulch2 != $row[8]){
                                                          $accion = "Cambio Conductor 2";
                                            }
                                            elseif($ulint != $row[10]){
                                                          $accion = "Cambio Interno";
                                            }
                                            elseif($ulcheck != $row[12]){
                                                          $accion = "Orden Chequeada";
                                            }
                                            elseif($ulcerr != $row[13]){
                                                            if ($row[13] == 0)
                                                               $accion = "Orden Abierta";
                                                            else
                                                                $accion = "Orden Cerrada";
                                            }
                                     }
                                            else{
                                                $accion = "Orden Creada";
                                                 $i++;
                                                }
                                                 $ulhc = $row[1];
                                                 $ulhs = $row[2];
                                                 $ulhl = $row[3];
                                                 $ulhf = $row[4];
                                                 $ulch1 = $row[6];
                                                 $ulch2 = $row[8];
                                                 $ulint = $row[10];
                                                  $ulcheck = $row[12];
                                                  $ulcerr = $row[13];

                                            $tabla.="<tr data-tt-id='1000$id' data-tt-parent-id='$id' class='detalle'>
                                                         <td>$row[0]</td>
                                                        <td>$row[1]</td>
                                                        <td>$row[2]</td>
                                                        <td>$row[3]</td>
                                                        <td>$row[4]</td>
                                                        <td>$row[5]</td>
                                                        <td>$row[7]</td>
                                                        <td>$row[9]</td>
                                                        <td>$row[10]</td>
                                                        <td>$row[14]</td>
                                                        <td>$row[11]</td>
                                                    </tr>";
                                            $row = mysql_fetch_array($query);
                                 }
}
 $tabla.='<script>
                  $("#example-advanced").treetable({ expandable: true });
         </script>
                           <style>
                         #example-advanced { font-size: 85%; }
                         #example-advanced tbody tr:hover {
                                                          background-color: #FF8080;
                                                          }
                          #example-advanced .detalle { font-size: 75%; }
                  </style>';
     print $tabla;
  }
  
?>

