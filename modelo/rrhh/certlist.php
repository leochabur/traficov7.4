<?php
     session_start();
     include ('../../controlador/bdadmin.php');
     include_once ('../../controlador/ejecutar_sql.php');
     include_once ('../../modelo/utils/dateutils.php');
     $accion = $_POST['accion'];
     
     if($accion == 'lcert'){
        $desde = dateToMysql($_POST['desde'],'/');
        $hasta = dateToMysql($_POST['hasta'], '/');
        $conn = conexcion();                                              //em.id_estructura in (SELECT uxe.id_estructura
                                                                             ///        FROM usuarios u
                                                                               //      inner join usuariosxestructuras uxe on uxe.id_usuario = u.id
                                                                               //      where u.id = $_SESSION[userid]))
        $empleado="";
        $diag="";
        if ($_POST['emple']){
           $empleado = "(cm.id_empleado = $_POST[emple]) and ";
        }
        if ($_POST['diag']){
           $diag = "(cm.id_diagnostico = $_POST[diag]) and ";
        }
              $sql = "SELECT cm.id, date_format(fecha_cert, '%d/%m/%Y') as fecha, date_format(vigente_hasta, '%d/%m/%Y') as vigencia, upper(concat(em.apellido, ', ',em.nombre)) as empleado, upper(concat(m.apellido, ', ',m.nombre)) as medico, upper(especialidad) as esp, upper(diagnostico) as diag, upper(ca.nombre) as ctro
                      FROM certmedicos cm
                      inner join empleados em on em.id_empleado = cm.id_empleado
                      inner join medicos m on m.id = cm.id_medico
                      inner join ctrosasistenciales ca on ca.id = cm.id_ctroAsis
                      inner join especialidades es on es.id = cm.id_especialidad
                      inner join diagnosticos d on d.id = cm.id_diagnostico
                      left join novedades n on n.id = cm.id_novedad
                      where $empleado $diag true
                      and ((fecha_cert between '$desde' and '$hasta') or (vigente_hasta between '$desde' and '$hasta'))
                      order by fecha_cert, em.apellido";
              $result = mysql_query($sql, $conn) or die (mysql_error($conn));
              $tabla ='<fieldset class="ui-widget ui-widget-content ui-corner-all">
                      <legend class="ui-widget ui-widget-header ui-corner-all">Certificados Medicos</legend>
                      <table id="example">
	                      <thead>
		                         <tr>
                                     <th>Fecha Cert.</th>
                                     <th>Vigencia Cert.</th>
			                         <th>Empleados</th>
			                         <th>Medico</th>
			                         <th>Especialidad</th>
			                         <th>Diagnostico</th>
			                         <th>Ctro. Asistencial</th>
			                         <th>Fecha Novedad</th>
			                         <th></th>
		                          </tr>
                          </thead>
                          <tbody>';
              while ($data = mysql_fetch_array($result)){
                    $tabla.="<tr id='tr-$data[id]'>
                                 <td>$data[fecha]</td>
                                 <td>$data[vigencia]</td>
                                 <td>".htmlentities($data['empleado'])."</td>
                                 <td>$data[medico]</td>
                                 <td>$data[esp]</td>
                                 <td>$data[diag]</td>
                                 <td>$data[ctro]</td>
                                 <td></td>
                                 <td><div id='$data[id]' class='delcert'> <img src='../../delete.png' border='0' text='Eliminar Certificado'></div></td>
                             </tr>";
              }


        $tabla.='</tbody></table>
       	         </fieldset>
                                   <style type="text/css">
                         #example { font-size: 85%; }
                         #example tbody tr.even:hover, #example tbody tr.even td.highlighted {background-color: #ECFFB3;}
                         #example tbody tr.odd:hover, #example tbody tr.odd td.highlighted {background-color: #E6FF99;}
                         #example tr.even:hover {background-color: #ECFFB3;}
                         #example tr.even:hover td.sorting_1 {background-color: #DDFF75;}
                         #example tr.even:hover td.sorting_2 {background-color: #E7FF9E;}
                         #example tr.even:hover td.sorting_3 {background-color: #E2FF89;}
                         #example tr.odd:hover {background-color: #E6FF99;}
                         #example tr.odd:hover td.sorting_1 {background-color: #D6FF5C;}
                         #example tr.odd:hover td.sorting_2 {background-color: #E0FF84;}
                         #example tr.odd:hover td.sorting_3 {background-color: #DBFF70;}
                  </style>
                  <script>
                          		$("#example").dataTable({
					                                    "sScrollY": "200px",

					                                    "bPaginate": true,
					                                    "bScrollCollapse": true,
					                                    "bJQueryUI": true,
					                                    "oLanguage": {
                                                                     "sLengthMenu": "",
                                                                     "sZeroRecords": "Sin Registros para mostrar",
                                                                     "sInfo": "",
                                                                     "sInfoEmpty": "",
                                                                     "sInfoFiltered": ""}
				                                       });
                                $(".delcert").click(function(event){
                                                                      event.preventDefault();
                                                                      var cer = $(this).attr("id");
                                                                      if(confirm("Seguro eliminar el certificado?")){
                                                                          $.post("/modelo/rrhh/certlist.php", {accion:"delcert", cert: cer}, function(data){
                                                                                                                                                          $("#tr-"+cer).remove();
                                                                                                                                                          });

                                                                      }
                                                                  });
                  </script>';
        mysql_free_result($result);
        mysql_close($conn);
        print $tabla;
     }
     elseif($accion == 'delcert'){
        $cert = $_POST['cert'];
        $ok = delete("certmedicos", "id", "$cert");
        print $ok;
     }

?>

