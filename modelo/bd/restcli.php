<?php
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include_once('../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];

  if($accion == 'load'){
          $sql="SELECT upper(razon_social), cr.id, c.id
                  from clientes c
                  left join clienterestriccion cr on cr.id_cliente = c.id and cr.id_estructuracliente = c.id_estructura
                  where activo and c.id_estructura = $_SESSION[structure]
                  order by razon_social";
          $conn = conexcion();
          $result = mysql_query($sql, $conn);
          $tabla = "<table id='example' width='50%'>
                     <thead>
            	            <tr>
                                <th>Razon Social</th>
                                <th>Si/No</th>
                            </tr>
                     </thead>
                     <tbody>";
          while($data = mysql_fetch_array($result)){
                      $tabla.="<tr>
                                   <td>$data[0]</td>";
                      $sel='';
                      if ($data['1']){
                         $sel = 'checked';
                      }
                      $tabla.="<td align='center'><input type=\"checkbox\" $sel onClick=\"cambioEstado($data[2], this.checked);\"></td>
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
                          function cambioEstado(cliente, sel){
                                   if (sel){
                                      $.post("/modelo/bd/restcli.php", { cli: cliente, accion: "add"});
                                   }
                                   else{
                                        $.post("/modelo/bd/restcli.php", { cli: cliente, accion: "del"});
                                   }
                          }
                  </script>';
          mysql_free_result($result);
          cerrarconexcion($conn);
          print $tabla;
  }
  elseif($accion == 'add'){
                 insert("clienterestriccion", "id, id_cliente, id_estructuracliente", "$_POST[cli], $_SESSION[structure]");
  }
  elseif($accion == 'del'){
                 delete("clienterestriccion", "id_cliente, id_estructuracliente", "$_POST[cli], $_SESSION[structure]");
  }
?>

