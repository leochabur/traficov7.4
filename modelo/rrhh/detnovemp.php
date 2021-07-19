<?
  session_start();
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/bdadmin.php');
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);

  //$accion = $_POST['accion'];
  if (true){ //
     $desde = explode("/", $_POST['desde']);
     $hasta = explode("/", $_POST['hasta']);
     $dd = 1;
     $dh = mktime( 0, 0, 0, $hasta[0], 1, $hasta[1]);
     $dh= date("t",$dh);
     $fdesde  = "$desde[1]-$desde[0]-$dd";
     $fhasta  = "$hasta[1]-$hasta[0]-$dh";

     $conn = mysql_connect("rrhh.masterbus.net", "masterbus", "master,07a");
     mysql_select_db("rrhh", $conn);

     $tabla ='<table id="detalle" name="detalle" width="100%" border="0">
                     <thead>
                            <tr>
                                <th>NOVEDAD</th>
                                <th>FECHA DESDE</th>
                                <th>FECHA HASTA</th>
                            </tr>
                     </thead>
                     <tbody>';
     $sql="select nov_text, date_format(desde, '%d/%m/%Y') as desde, date_format(hasta, '%d/%m/%Y') as hasta
           FROM novedades n
           inner join cod_novedades cn on cn.id = n.id_novedad
           where (('$fdesde' between desde and hasta) or ('$fhasta' between desde and hasta)) and (n.id_novedad in (SELECT id_novedad FROM novporincent)) and (id_empleado = $_POST[emple])
           order by desde";
     $nov = mysql_query($sql, $conn) or die(mysql_error($conn));
     while ($row = mysql_fetch_array($nov)){
           $tabla.="<tr>
                        <td>$row[nov_text]</td>
                        <td>$row[desde]</td>
                        <td>$row[hasta]</td>
                    </tr>";
     }
     $tabla.='</tbody>
                   </table>
                  <style type="text/css">

                         #detalle tbody{ font-size: 75%; }
                         #detalle thead { font-size: 45%; }
                         #detalle tbody tr.even:hover, #detalle tbody tr.even td.highlighted {background-color: #ECFFB3;}
                         #detalle tbody tr.odd:hover, #detalle tbody tr.odd td.highlighted {background-color: #E6FF99;}
                         #detalle tr.even:hover {background-color: #ECFFB3;}
                         #detalle tr.even:hover td.sorting_1 {background-color: #DDFF75;}
                         #detalle tr.even:hover td.sorting_2 {background-color: #E7FF9E;}
                         #detalle tr.even:hover td.sorting_3 {background-color: #E2FF89;}
                         #detalle tr.odd:hover {background-color: #E6FF99;}
                         #detalle tr.odd:hover td.sorting_1 {background-color: #D6FF5C;}
                         #detalle tr.odd:hover td.sorting_2 {background-color: #E0FF84;}
                         #detalle tr.odd:hover td.sorting_3 {background-color: #DBFF70;}
                  </style>
                  <script>
                          		$("#detalle").dataTable({
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

                  </script>';
            //  mysql_free_result($nov);
             // mysql_close($conn);

   print ($tabla);
  }

?>

