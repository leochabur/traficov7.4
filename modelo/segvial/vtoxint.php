<?
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include_once('../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];

  if($accion == 'load'){
          $sql="SELECT u.id, interno, upper(patente) as dominio, razon_social,
                       (SELECT idtipovencimiento FROM tipovencimientoporinterno where (idunidad = u.id) and (idtipovencimiento = 1)) as pcia,
                       (SELECT idtipovencimiento FROM tipovencimientoporinterno where (idunidad = u.id) and (idtipovencimiento = 2)) as nac
                FROM unidades u
                inner join estructuras e on e.id = u.id_estructura
                inner join empleadores em on em.id = u.id_propietario
                where (u.activo) and (em.activo)";
          $conn = conexcion();
          $result = mysql_query($sql, $conn);
          $tabla = "<table id='example'>
                     <thead>
            	            <tr>
                                <th>Interno</th>
                                <th>Dominio</th>
                                <th>Propietario</th>
                                <th>VTV Provincia</th>
                                <th>VTV Nacion</th>
                            </tr>
                     </thead>
                     <tbody>";
          while($data = mysql_fetch_array($result)){
                      $tabla.="<tr>
                                   <td>$data[interno]</td>
                                   <td>$data[dominio]</td>
                                   <td>$data[razon_social]</td>";
                      $sel='';
                      if ($data['pcia']){
                         $sel = 'checked';
                      }
                      $tabla.="<td align='center'><input type=\"checkbox\" $sel onClick=\"cambioEstado($data[id], 1, this.checked);\"></td>";
                      $sel='';
                      if ($data['nac']){
                         $sel = 'checked';
                      }
                      $tabla.="<td align='center'><input type=\"checkbox\" $sel onClick=\"cambioEstado($data[id], 2, this.checked);\"></td>
                               </tr>";
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
                          function cambioEstado(id_micro, vtv, sel){
                                   if (sel){
                                      $.post("/modelo/segvial/vtoxint.php", { interno: id_micro, tipovtv: vtv, accion: "add"} );
                                   }
                                   else{
                                        $.post("/modelo/segvial/vtoxint.php", { interno: id_micro, tipovtv: vtv, accion: "del"} );
                                   }
                          }
                  </script>
                  ';
          mysql_free_result($result);
          cerrarconexcion($conn);
          print $tabla;
  }
  elseif($accion == 'add'){
                 insert("tipovencimientoporinterno", "id, idunidad, idtipovencimiento", "$_POST[interno], $_POST[tipovtv]");
  }
  elseif($accion == 'del'){
                 delete("tipovencimientoporinterno", "idunidad, idtipovencimiento", "$_POST[interno], $_POST[tipovtv]");
  }
?>

