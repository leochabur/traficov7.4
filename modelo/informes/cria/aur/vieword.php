<?php
     session_start();
     include_once('../../../../vista/paneles/viewpanel.php');
$tabla='<link type="text/css" href="/vista/css/blue/style.css" rel="stylesheet"/>
 <link href="/vista/css/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
 <link type="text/css" href="/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
 <script type="text/javascript" src="/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.tablesorter.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.contextMenu.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.ui.selectmenu.js"></script>
  <script type="text/javascript" src="/vista/js/validate-form/jquery.validate.min.js"></script>
  
  
<script type="text/javascript" src="/vista/js/validate-form/jquery.metadata.js"></script>
<script>
 
	$(function() {

                 $.mask.definitions["~"]="[012]";
                 $.mask.definitions["%"]="[012345]";
                 $(".hora").mask("~9:%9",{completed:function(){}});
                 $("#fservicio").datepicker({dateFormat:"dd/mm/yy"});

                 $("#close").button().click(function(){
                                                       $("#dialog").dialog("close");
                                                       });

	
</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
.small.button, .small.button:visited {
font-size: 11px ;
}

#modorden .error{
	font-size:0.8em;
	color:#ff0000;
}

</style>
<BODY>';
    $tabla.="<br><br>
             <div id='result'></div>
             <form id='modorden'>
             <fieldset class='ui-widget ui-widget-content ui-corner-all'>
                       <legend class='ui-widget ui-widget-header ui-corner-all'>$_POST[serv]</legend>
                       <div id='tablaordenes'>
                       <table width='100%' class='orders'>
                              <tr>
                                  <th>H. Citacion</th>
                                  <th>H. Salida</th>
                                  <th>H. LLegada</th>
                                  <th>H. Fin Serv.</th>
                                  <th>Cliente</th>
                                  <th>Conductor 1</th>
                                  <th>Conductor 2</th>
                                  <th>Interno</th>
                                  <th>Accion Realizada</th>
                                  <th>Fecha Accion</th>
                                  <th>Usuario Responsable</th>
                              </tr>
                              </thead>
                              <tbody>";
                                 $con = conexcion();
                                 $sql = "select date_format(fservicio, '%d/%m/%Y'),
                                                date_format(hcitacionreal, '%H:%i'),
                                                date_format(hsalidaplantareal, '%H:%i'),
                                                date_format(hllegadaplantareal, '%H:%i'),
                                                date_format(hfinservicioreal, '%H:%i'),
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
                                                borrada,
                                                suspendida,
                                                fecha_accion
                                         from(SELECT *
                                              FROM ordenes o
                                              where id = $_POST[orden]
                                              union all
                                              SELECT *
                                              FROM ordenes_modificadas o
                                              where id = $_POST[orden]
                                              
                                         ) o
                                         left JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
                                         left JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
                                         inner join clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
                                         left join unidades un on (un.id = o.id_micro)
                                         inner join usuarios usu on usu.id = id_user
                                         order by fecha_accion";

                                 $query = mysql_query($sql, $con) or die(mysql_error($con));
                                 $ulcheck="";
                                 $ulcerr="";
                                 $ulint="";
                                 $ulch1="";
                                 $ulch2="";
                                 $ulhc="";
                                 $ulhs="";
                                 $ulhl="";
                                 $ulhf="";
                                 $i=0;
				                 while($row = mysql_fetch_array($query)){
                                     if ($i > 0){
                                            if ($ulhc != $row[1]){
                                               $accion.= "/Cambio H. Citacion";
                                            }
                                            if ($ulhs != $row[2]){
                                                   $accion.= " /Cambio H. Salida";
                                            }
                                            if($ulhl != $row[3]){
                                                         $accion.= " /Cambio H. Llegada";
                                            }
                                            if ($ulhf != $row[4]){
                                                   $accion.= " /Cambio H. Fin Servicio";
                                            }
                                            if($ulch1 != $row[6]){
                                                          $accion.= " /Cambio Conductor 1";
                                            }
                                            if($ulch2 != $row[8]){
                                                          $accion.= " /Cambio Conductor 2";
                                            }
                                            if($ulint != $row[10]){
                                                          $accion.= " /Cambio Interno";
                                            }
                                            if($ulcheck != $row[12]){
                                                          $accion.= " /Orden Chequeada";
                                            }
                                            if($ulcerr != $row[13]){
                                                            if ($row[13] == 0)
                                                               $accion.= " /Orden Abierta";
                                                            else
                                                                $accion.= " /Orden Cerrada";
                                            }
                                            if ($ultDel != $row['borrada']){
                                                      $accion.=" /Orden Eliminada";
                                            }
                                            if ($ultSus != $row['suspendida']){
                                                      $accion.=" /Orden Suspendida";
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
                                                  $ultDel = $row['borrada'];
                                                  $ultSus = $row['suspendida'];
                                                  
                                            $tabla.="<tr>
                                                        <td>$row[1]</td>
                                                        <td>$row[2]</td>
                                                        <td>$row[3]</td>
                                                        <td>$row[4]</td>
                                                        <td>$row[5]</td>
                                                        <td>$row[7]</td>
                                                        <td>$row[9]</td>
                                                        <td>$row[10]</td>
                                                        <td>$accion</td>
                                                        <td>$row[14]</td>
                                                        <td>$row[11]</td>
                                                    </tr>";
                                            $accion='';
                                 }
                                 $tabla.="</tbody>
                                          </table>
                                          </div>
	                                      </fieldset>
	                                      </form>
	                                      <style type='text/css'>
                     table.orders {
	                              font-family:arial;
	                              background-color: #CDCDCD;
                                  font-size: 7pt;
	                              text-align: center;
                               }
                     table.orders thead tr th, table.tablesorter tfoot tr th {
                                                                            background-color: #e6EEEE;
                                                                            border: 1px solid #FFF;
	                                                                        font-size: 8pt;
	                                                                        padding: 4px;}
                     table.orders tbody td {
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
                </script>
                                          </BODY>
                                          </HTML>";
                                 @mysql_free_result($query);
                                 @mysql_close($con);
print $tabla;
?>

