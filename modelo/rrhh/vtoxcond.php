<?php
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include_once('../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];

  if($accion == 'load'){
          $sql="SELECT id_empleado as id, legajo, upper(concat(apellido, ', ',nombre)) as apenom,
       (SELECT id_licencia FROM licenciasxconductor where (id_licencia = 1) and (id_conductor = id_empleado) group by id_licencia) as nac,
       (SELECT id_licencia FROM licenciasxconductor where (id_licencia = 2) and (id_conductor = id_empleado) group by id_licencia) as prov,
       (SELECT id_licencia FROM licenciasxconductor where (id_licencia = 3) and (id_conductor = id_empleado) group by id_licencia) as mun,
       (SELECT id_licencia FROM licenciasxconductor where (id_licencia = 5) and (id_conductor = id_empleado) group by id_licencia) as pre,
       (SELECT id_licencia FROM licenciasxconductor where (id_licencia = 6) and (id_conductor = id_empleado) group by id_licencia) as lt
               FROM empleados e
               inner join empleadores em on em.id = e.id_empleador
               where (e.activo) and (em.activo) and (e.id_estructura in (SELECT id_estructura FROM usuariosxestructuras where id_usuario = $_SESSION[userid]))";

          $conn = conexcion();
          $result = mysql_query($sql, $conn) or die(mysql_error($conn));
          

          
          $tabla = "<table id='example'>
                     <thead>
            	            <tr>
                                <th>Legajo</th>
                                <th>Apellido, Nombre</th>
                                <th>Lic. Munic.</th>
                                <th>Lic. Prov.</th>
                                <th>Lic. Nacional</th>
                                <th>Preocupacional</th>
                                <th>Libreta Trabajo</th>
                            </tr>
                     </thead>
                     <tbody>";
          while($data = mysql_fetch_array($result)){
                      $tabla.="<tr>
                                   <td>$data[legajo]</td>
                                   <td>".utf8_decode($data['apenom'])."</td>";
                      $sel='';
                      if ($data['mun']){
                         $sel = 'checked';
                      }
                      $tabla.="<td align='center'><input type=\"checkbox\" $sel onClick=\"cambioEstado($data[id], 3, this.checked);\"></td>";
                      $sel='';
                      if ($data['prov']){
                         $sel = 'checked';
                      }
                      $tabla.="<td align='center'><input type=\"checkbox\" $sel onClick=\"cambioEstado($data[id], 2, this.checked);\"></td>";
                      if ($data['nac']){
                         $sel = 'checked';
                      }
                      $tabla.="<td align='center'><input type=\"checkbox\" $sel onClick=\"cambioEstado($data[id], 1, this.checked);\"></td>";
                      $sel='';
                      if ($data['pre']){
                         $sel = 'checked';
                      }
                      $tabla.="<td align='center'><input type=\"checkbox\" $sel onClick=\"cambioEstado($data[id], 5, this.checked);\"></td>";
                      $sel='';
                      if ($data['lt']){
                         $sel = 'checked';
                      }
                      $tabla.="<td align='center'><input type=\"checkbox\" $sel onClick=\"cambioEstado($data[id], 6, this.checked);\"></td></tr>";
          }
          $tabla.='</tbody>
                  </table>
                  <style type="text/css">
                         #example { font-size: 75%; }
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
                  <script type="text/javascript">
                          $("#example").dataTable({
					                                    "sScrollY": "300px",
					                                    "bPaginate": false,
					                                    "bScrollCollapse": true,
					                                    "bJQueryUI": true,
					                                    "oLanguage": {
                                                                     "sLengthMenu": "Display _MENU_ records per page",
                                                                     "sZeroRecords": "Sin Registros para mostrar",
                                                                     "sInfo": "",
                                                                     "sInfoEmpty": "Showing 0 to 0 of 0 records",
                                                                     "sInfoFiltered": "(filtered from _MAX_ total records)"}
				                                       });
                          function cambioEstado(id, tipo, sel){
                                   if (sel){
                                      $.post("/modelo/rrhh/vtoxcond.php", { emple: id, tipovto: tipo, accion: "add"} );
                                   }
                                   else{
                                        $.post("/modelo/rrhh/vtoxcond.php", { emple: id, tipovto: tipo, accion: "del"} );
                                   }
                          }
                  </script>
                  ';
          mysql_free_result($result);
          cerrarconexcion($conn);
          print $tabla;
  }
  elseif($accion == 'add'){
                 insert("licenciasxconductor", "id, id_licencia, id_conductor", "$_POST[tipovto], $_POST[emple]");
  }
  elseif($accion == 'del'){
                 delete("licenciasxconductor", "id_licencia, id_conductor", "$_POST[tipovto], $_POST[emple]");
  }
?>

