<?php
     session_start();
     set_time_limit(0);
     error_reporting(0);
     include ('../../../controlador/bdadmin.php');
  //   include ('../../../controlador/ejecutar_sql.php');
     include ('../../../modelo/utils/dateutils.php');
     $accion = $_POST['accion'];
     
     if ($accion == 'lemp'){
         $cargo="(id_cargo = 1) and ";
        if ($_SESSION['modaf'] == 1){
           $cargo="";
        }
        $empleador = $_POST['emplor'];
        if ($empleador)
           $empleador = "(id_empleador = $empleador) and";
        $conn = conexcion();

        $sql="SELECT id_empleado, concat(apellido,', ', nombre) as apenom
              FROM empleados
              WHERE $empleador $cargo (activo) and (id_estructura in (SELECT uxe.id_estructura
                                                                                                       FROM usuarios u
                                                                                                       inner join usuariosxestructuras uxe on uxe.id_usuario = u.id
                                                                                                       where u.id = $_SESSION[userid]))
              ORDER BY apellido, nombre";
        $result = mysql_query($sql, $conn) or die (mysql_error($conn));
        $tabla= '<select id="emples" name="emples">
                 <option value="0">Todos</option>';
        while ($data = mysql_fetch_array($result)){
              $tabla.="<option value='$data[id_empleado]'>".htmlentities($data['apenom'])."</option>";
        }
        mysql_free_result($result);
        mysql_close($conn);
        $setemple='';
        if (isset($_POST['emple']))
           $setemple = "$('#emples option[value=$_POST[emple]]').attr('selected', 'selected'); ";
        $tabla.="</select>
                  <script>
                          $setemple
                          $('#emples').selectmenu({width: 350});
                  </script>";
        print $tabla;
     }
     elseif($accion == 'lnov'){
        $desde = dateToMysql($_POST['desde'],'/');
        $hasta = dateToMysql($_POST['hasta'], '/');
        $conn = conexcion();
        $empleado="";
        $nofranco="";
        if ($_SESSION['modaf'] == 1){
           $nofranco="and (cn.id <> 15)";
        }
        if ($_POST['emple']){
           $empleado = "and (n.id_empleado = $_POST[emple])";
        }
              $sql = "SELECT ca.descripcion, legajo, n.id_empleado, n.id, upper(concat(em.apellido, ', ',em.nombre)) as empleado, date_format(desde, '%d/%m/%Y') as desde, date_format(hasta, '%d/%m/%Y') as hasta, cn.nov_text, e.nombre, u.apenom
                      FROM novedades n
                      inner join cod_novedades cn on cn.id = n.id_novedad
                      inner join estructuras e on e.id = n.id_estructura
                      inner join usuarios u on u.id = n.usuario
                      inner join empleados em on em.id_empleado = n.id_empleado
                      left join cargo ca on ca.id = em.id_cargo
                      where  (n.id_estructura in (SELECT uxe.id_estructura
                                                                                     FROM usuarios u
                                                                                     inner join usuariosxestructuras uxe on uxe.id_usuario = u.id
                                                                                     where u.id = $_SESSION[userid]))
                             and ((desde between '$desde' and '$hasta') or (hasta between '$desde' and '$hasta') or ('$desde' between  desde and hasta)or ('$hasta' = hasta) or ('$desde' = desde) or ('$hasta' between desde and hasta)) and (n.activa) $nofranco $empleado
                      order by nombre, desde, nov_text";
         //     die($sql);
              $result = mysql_query($sql, $conn) or die ("error al conectar ".mysql_error($conn));
            //  die("registros ".mysql_num_rows($result));
              $tabla ='<fieldset class="ui-widget ui-widget-content ui-corner-all">
                      <legend class="ui-widget ui-widget-header ui-corner-all">Listado de Novedades</legend>
                      <table id="example" name="example">
	                      <thead>
		                         <tr>
                                     <th>Legajo</th>
                                     <th>Apellido, Nombre</th>
                                     <th>Puesto</th>
                                     <th>Fecha Desde</th>
			                         <th>Fecha Hasta</th>
			                         <th>Novedad</th>
			                         <th>Afectado a estructura:</th>
			                         <th>Usuario Alta</th>
		                          </tr>
                          </thead>
                          <tbody>';
              while ($data = mysql_fetch_array($result)){
                    $tabla.="<tr id='$data[id]' title='$data[id_empleado]'>
                                 <td>$data[legajo]</td>
                                 <td>".($data['empleado'])."</td>
                                 <td>$data[descripcion]</td>
                                 <td>$data[desde]</td>
                                 <td>$data[hasta]</td>
                                 <td>$data[nov_text]</td>
                                 <td>$data[nombre]</td>
                                 <td>".($data[apenom])."</td>
                             </tr>";
              }


        $tabla.='</table>
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
                         #example tbody tr {cursor: pointer}
                  </style>
                  <script>
                          		$("#example").dataTable({
					                                    "sScrollY": "400px",

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
                                $("#example tr").click(function(){
                                                                  var nov = $(this).attr("id");
                                                                  var emp = $(this).attr("title");
                                                                  document.location.href="./modnvda.php?dri="+emp+"&nov="+nov+"&ds='.$desde.'&hs='.$hasta.'";
                                                                  });
                  </script>';
        mysql_free_result($result);
        mysql_close($conn);
        print $tabla;
     }

?>

