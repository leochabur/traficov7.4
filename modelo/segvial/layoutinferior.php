<?php
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');


  echo json_encode(array('status' => 1));
                                                                                                                                                                                                 "<p><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                                                 "<strong>Se ha modificado con exito la unidad en la Base de Datos!</strong></p>"+
                                                                                                                                                                                                 "</div>"+
                                                                                                                                                                                                 "<div>";
                                                                                                                                                                                       $("#data").html(mje);

                                                                                                                                                                             }
                                                                                                                                                                             });
                                                                                                        }
                                                                              });
                          });

         </script>';
  }
  elseif($accion == 'upduda'){
          $video = $_POST['video'] ? 1 : 0;
          $banio = $_POST['banio'] ? 1 : 0;
          $bar = $_POST['bar'] ? 1 : 0;
          $activo = $_POST['activo'] ? 1 : 0;
          
          $campos = "marca_motor, anio, patente, nueva_patente, marca, modelo, cantasientos, video, bar, banio, activo, consumo, id_propietario, id_estructura_propietario";
          $values = "'$_POST[motor]', '$_POST[anio]', '$_POST[dominio]', '$_POST[n_dominio]', '$_POST[marca]', '$_POST[modelo]', '$_POST[cantas]', $video, $bar, $banio, $activo, '$_POST[consumo]', $_POST[propietario], $_SESSION[structure]";
          
          if ($_POST['calidad']){
             $campos.=", id_calidadcoche, id_estructura_calidadcoche";
             $values.=", $_POST[calidad], $_SESSION[structure]";
          }
          if ($_POST['tipo']){
             $campos.=", id_tipounidad, id_estructura_tipounidad";
             $values.=", $_POST[tipo], $_SESSION[structure]";
          }
          print update("unidades", $campos, $values, "(id = $_POST[id_unidad])and(id_estructura = $_SESSION[structure])");

  }
  elseif($accion == 'list'){
      if ($_POST['propietario']){
          $sql="SELECT upper(e.nombre) as estr, u.id as id_unidad, interno, patente, upper(nueva_patente) as nueva_patente, marca, modelo, marca_motor, anio, cantasientos, if(u.activo, 'checked', '') as activo, if(video, 'checked', '') as video, if(bar, 'checked', '') as bar, if(banio, 'checked', '') as banio, id_calidadcoche, id_tipounidad, consumo, calidad,  tipo
                FROM (SELECT * FROM unidades WHERE (activo = $_POST[state]) and (id_propietario = $_POST[propietario]) and (id_estructura_propietario = $_SESSION[structure])) u
                LEFT JOIN calidadcoche cc ON (cc.id = u.id_calidadcoche) and (cc.id_estructura = u.id_estructura_calidadcoche)
                LEFT JOIN tipounidad tu ON (tu.id = u.id_tipounidad) and (tu.id_estructura = u.id_estructura_tipounidad)
                left join estructuras e on e.id = u.id_estructura
                ORDER BY u.interno";
          }
          else{
          $sql="SELECT upper(e.nombre) as estr, u.id as id_unidad, interno, patente, upper(nueva_patente) as nueva_patente, marca, modelo, marca_motor, anio, cantasientos, if(u.activo, 'checked', '') as activo, if(video, 'checked', '') as video, if(bar, 'checked', '') as bar, if(banio, 'checked', '') as banio, id_calidadcoche, id_tipounidad, consumo, calidad,  tipo,
                upper(razon_social) as razon_social
                FROM unidades u
                LEFT JOIN calidadcoche cc ON (cc.id = u.id_calidadcoche) and (cc.id_estructura = u.id_estructura_calidadcoche)
                LEFT JOIN tipounidad tu ON (tu.id = u.id_tipounidad) and (tu.id_estructura = u.id_estructura_tipounidad)
                left join estructuras e on e.id = u.id_estructura
                inner join empleadores emp on emp.id = u.id_propietario and emp.id_estructura = id_estructura_propietario
                where (u.activo = $_POST[state]) and u.id_estructura = $_SESSION[structure] 
                ORDER BY razon_social, u.interno";
          }
          $conn = conexcion();
          $result = mysql_query($sql, $conn) or die (mysql_error($conn));
          $tabla = "<fieldset class='ui-widget ui-widget-content ui-corner-all'>
                    <legend class='ui-widget ui-widget-header ui-corner-all'>Unidades</legend>
                    <table id='example' align='center' border='0' width='100%'>
                     <thead>
            	            <tr>";
                                  if (!$_POST['propietario'])
                                     $tabla.="<th>Propietario</th>";
                                $tabla.="<th>Interno</th>
                                <th>Dominio</th>
                                <th>Nuevo Dominio</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Cant. As.</th>
                                <th>Consumo</th>
                                <th>Tipo</th>
                                <th>Calidad</th>
                                <th>Afectado a Estructura</th>
                                <th>Activo</th>
                            </tr>
                     </thead>
                     <tbody>";
          while($data = mysql_fetch_array($result)){
                      $tabla.="<tr>";
                                  if (!$_POST['propietario'])
                                     $tabla.="<td>$data[razon_social]</td>";
                                   $tabla.="<td><a href='../../vista/segvial/moduda.php?int=$data[id_unidad]'>$data[interno]</a></td>
                                   <td>$data[patente]</td>
                                   <td>$data[nueva_patente]</td>
                                   <td>$data[marca]</td>
                                   <td>$data[modelo]</td>
                                   <td>$data[cantasientos]</td>
                                   <td>$data[consumo]</td>
                                   <td>$data[tipo]</td>
                                   <td>$data[calidad]</td>
                                   <td>$data[estr]</td>
                                   <td><input type='checkbox' $data[activo] readonly='readonly' onClick='cambioEstado($data[id_unidad],this.checked);'></td>
                               </tr>";
          }
          $tabla.="</tbody>
                  </table>
                  </fieldset>
                  <style type='text/css'>
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
                  <script type='text/javascript'>
                          		$('#example').dataTable({
                                                         'sScrollY': '350px',
					                                    'bPaginate': false,
					                                    'bScrollCollapse': true,
					                                    'bJQueryUI': true,
					                                    'oLanguage': {
                                                                     'sLengthMenu': '',
                                                                     'sZeroRecords': 'Sin Registros para mostrar',
                                                                     'sInfo': '',
                                                                     'sInfoEmpty': '',
                                                                     'sInfoFiltered': ''}
				                                       });
                          
                          
                          
                          
                          function cambioEstado(id, state){
                                   if (state){
                                      $.post('/modelo/segvial/altauda.php',{coche: id, accion:'change', st:'up'});
                                   }
                                   else{
                                        $.post('/modelo/segvial/altauda.php',{coche: id, accion:'change', st:'down'});
                                   }
                          }
                  </script>
                  ";
          mysql_free_result($result);
          cerrarconexcion($conn);
          print $tabla;
  }
  elseif($accion == 'change'){

                 if ($_POST['st'] == 'up'){
                    update("unidades", "activo", "1", "(id = $_POST[coche])");
                 }
                 elseif ($_POST['st'] == 'down'){
                    update("unidades", "activo", "0", "(id = $_POST[coche])");
                 }
  }
?>

