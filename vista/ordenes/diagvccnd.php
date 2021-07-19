<?php
     session_start();
     include_once('../../controlador/bdadmin.php');
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
              $("#tablita input:checkbox").click(function(){
                                                            var sino=0;
                                                            if ($(this).is(":checked")){
                                                               sino=1;
                                                            }
                                                            var id = $(this).attr("id");
                                                            $.post("/modelo/ordenes/smlvac.php",
                                                                   {accion:"updcnd", sn: sino, cnd:id} ,
                                                                   function(datos) {
                                                                                   });
                                                            });

	});
	
</script>

<BODY>';
    $tabla.="<br><br>
             <div id='result'></div>
             <form id='modorden'>
             <fieldset class='ui-widget ui-widget-content ui-corner-all'>
                       <legend class='ui-widget ui-widget-header ui-corner-all'>Seleccione los conductores a los cuales no se les diagramaran los vacios</legend>
                       <table id='tablita'>";
                                 $con = conexcion();
                                 $sql = "select e.id_empleado, upper(concat(apellido,', ', nombre)) as conductor, if (diagrama_sino, 'checked', '')
                                         from empleados e
                                         inner join empleadores em on e.id_empleador = em.id
                                         left join diagramaVaciosACodnuctor d on d.id_conductor = e.id_empleado and d.id_estructura = e.afectado_a_estructura
                                         where e.activo and afectado_a_estructura = $_SESSION[structure] and id_cargo = 1
                                         order by apellido";

                                 $result = mysql_query($sql, $con) or die(mysql_error($con));
                                 $tabla.="<tr>
                                              <td>Conductor</td>
                                              <td>No Diagramar Vacios</td>
                                         </tr>";
                                 while ($row = mysql_fetch_array($result)){
                                       $tabla.="<tr>
                                                    <td>$row[1]</td>
                                                    <td><input id='$row[0]' type='checkbox' $row[2]></td>
                                                </tr>";
                                 
                                 }

                                 $tabla.='</table>
                                          </div>
	                                      </fieldset>
	                                      <input type="hidden" name="nroorden" value="'.$_POST[orden].'">
	                                      <input type="hidden" id="tieneasoc" name="tieneasoc" value="'.$row[tiene_asoc].'">
	                                      </form>
                                          </BODY>
                                          </HTML>';
                                 @mysql_free_result($query);
                                 @mysql_close($con);
print $tabla;
?>

