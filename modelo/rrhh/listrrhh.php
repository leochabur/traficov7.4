<?
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  include ('../../vista/paneles/viewpanel.php');
  $accion = $_POST['accion'];
  if($accion == 'list'){
          $emp="";
        if ($_POST['empleador']){
             $emp="and (e.id_empleador = $_POST[empleador])";
        }
        switch ($_POST['show']) {
               case 1:
                    $mostrar=" and (e.activo) and (not e.borrado)";
                    break;
               case 2:
                    $mostrar="and (not e.activo)";
                    break;
               case 3:
                      $mostrar="and (e.borrado)";
                      break;
               case 4:
                      $mostrar="";
                      break;
        }
        $cargo="(id_cargo = 1) and ";
        if ($_SESSION['modaf'] == 1){
           $cargo="";
        }
          $sql="SELECT upper(c.descripcion) as cargo, e.id_empleado, legajo, upper(concat(apellido, ', ',e.nombre)) as apenom, nrodoc, upper(razon_social) as empleador, if(e.activo, 'checked', '') as activo, es.nombre as str, e.activo as act, e.borrado as bor
                FROM empleados e
                left join empleadores em on (em.id = e.id_empleador)
                left join cargo c on (c.id = e.id_cargo)
                left join estructuras es on es.id = e.id_estructura
                where $cargo (e.id_estructura in (SELECT id_estructura FROM usuariosxestructuras where id_usuario = $_SESSION[userid])) $emp $mostrar
                order by empleador, $_POST[order]";

          $conn = conexcion();
          $result = mysql_query($sql, $conn);
          $tabla = "<fieldset class='ui-widget ui-widget-content ui-corner-all'>
                    <legend class='ui-widget ui-widget-header ui-corner-all'>Personal</legend>
                    <table id='example' align='center' border='0' width='100%'>
                     <thead>
            	            <tr>
                                <th>Legjo</th>
                                <th>Apellido, Nombre</th>
                                <th>DNI</th>
                                <th>Empleador</th>
                                <th>Afectado a...</th>
                                <th>Puesto</th>
                                <th>Accion</th>
                            </tr>
                     </thead>
                     <tbody>";
          $del = (($_SESSION['permisos'][4] > 0) || ($_SESSION['permisos'][2] > 1));
          while($data = mysql_fetch_array($result)){
                      $tabla.="<tr id='tr-$data[id_empleado]'>
                                   <td class='redi' text='$data[id_empleado]'>$data[legajo]</td>
                                   <td>".htmlentities($data['apenom'])."</td>
                                   <td>$data[nrodoc]</td>
                                   <td>".htmlentities($data['empleador'])."</td>
                                   <td>".htmlentities($data['str'])."</td>
                                   <td>".htmlentities($data['cargo'])."</td>";
                      if ($del){
                         if (!$data['act'] || $data['bor'])
                            $tabla.="<td align='center'><a href='' text='$data[id_empleado]' class='restemple'><img src='../../vista/css/images/restaurar.png'border='0'></a></td>";
                         else
                            $tabla.="<td align='center'><a href='' id='$data[id_empleado]' class='delemple'><img src='../../vista/css/images/eliminar.gif'border='0'></a></td>";

                      }
                      else{
                           $tabla.="<td></td>";
                      }
                      $tabla.= "</tr>";
          }
          $tabla.='</tbody>
                  </table>
                  <a href="/modelo/rrhh/export-rrhh.php?empl='.$_POST['empleador'].'&order='.$_POST['order'].'"><img title="Exportar a Excel" src="../../vista/excel.jpg" width="35" height="35" border="0"></a>
                  </fieldset>
                  <style type="text/css">
                         .redi {cursor: pointer}
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
                                                         "sScrollY": "350px",
					                                    "bPaginate": false,
					                                    "bScrollCollapse": true,
					                                    "bJQueryUI": true,
					                                    "oLanguage": {
                                                                     "sLengthMenu": "",
                                                                     "sZeroRecords": "Sin Registros para mostrar",
                                                                     "sInfo": "",
                                                                     "sInfoEmpty": "",
                                                                     "sInfoFiltered": ""}
				                                       });
                               $(".delemple").click(function(event){
                                                                           event.preventDefault();
                                                                           var emple = $(this).attr("id");
                                                                           fila=$(this).parents("tr");
                                                                           var texto=fila.find("td:eq(1)").text();
                                                                           if (confirm("Seguro elimiar al emppleado "+texto)){
                                                                              $.post("/modelo/rrhh/listrrhh.php",{emp: emple, accion:"borrar"}, function(data){$("#tr-"+emple).remove();});
                                                                           }
                                                                           });
                               $(".restemple").click(function(event){
                                                                           event.preventDefault();
                                                                           var emple = $(this).attr("text");
                                                                           fila=$(this).parents("tr");
                                                                           var texto=fila.find("td:eq(1)").text();
                                                                           if (confirm("Seguro reincorporar al emppleado "+texto)){
                                                                              $.post("/modelo/rrhh/listrrhh.php",{emp: emple, accion:"restaurar"}, function(data){$("#tr-"+emple).remove();});
                                                                           }
                                                                           });
                               $(".redi").click(function(event){
                                                                var emple = $(this).attr("text");
                                                                $(location).attr("href","/vista/rrhh/moddriv.php?dri="+emple);
                                                                });
                  </script>';
          mysql_free_result($result);
          cerrarconexcion($conn);
          print $tabla;
  }
  elseif($accion == 'borrar'){
                 print update("empleados", "borrado, activo", "1, 0", "(id_empleado = $_POST[emp])");
  }
  elseif($accion == 'restaurar'){
                 print update("empleados", "borrado, activo", "0, 1", "(id_empleado = $_POST[emp])");
  }
?>

