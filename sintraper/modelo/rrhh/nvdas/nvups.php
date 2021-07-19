<?php
     session_start();
     include ('../../../controlador/bdadmin.php');
     include ('../../../controlador/ejecutar_sql.php');
     include ('../../../modelo/utils/dateutils.php');
     $accion = $_POST['accion'];
     
     if ($accion == 'lcr'){
        $cargo="(id_cargo = 1) and ";
        if ($_SESSION['modaf'] == 1){
           $cargo="";
        }
        $conn = conexcion();
        $cliente = $_POST['cli'];
        $sql="SELECT em.id as id_empleador, upper(es.nombre) as str, e.id_empleado, legajo, upper(concat(apellido, ', ',e.nombre)) as apenom, nrodoc, if(e.activo, 'checked', '') as activo, upper(codigo) as codigo
                FROM empleados e
                left join empleadores em on (em.id = e.id_empleador) and (em.id_estructura = e.id_estructura_empleador)
                left join cargo c on c.id = e.id_cargo
                inner join estructuras es on es.id = e.id_estructura
                where $cargo (e.id_empleador = $_POST[cli]) and (e.id_estructura in (SELECT uxe.id_estructura
                                                                                                 FROM usuarios u
                                                                                                 inner join usuariosxestructuras uxe on uxe.id_usuario = u.id
                                                                                                 where u.id = $_SESSION[userid]))
                order by apellido";

        $result = mysql_query($sql, $conn) or die (mysql_error($conn));
        $tabla = '<br>Listado de personal<br><table id="example" name="example">
	                      <thead>
		                         <tr>
                                     <th>Legajo</th>
			                         <th>Apellido, Nombre</th>
			                         <th>DNI</th>
			                         <th>Puesto</th>
			                         <th>Afectado a estructura:</th>
		                          </tr>
                          </thead>
                          <tbody>';
        while ($data = mysql_fetch_array($result)){
              $tabla.="<tr id='$data[id_empleado]' title='$data[id_empleador]'>
                              <td>$data[legajo]</td>
                              <td id='td$data[id_empleado]'>".htmlentities($data['apenom'])."</td>
                              <td>".htmlentities($data['dni'])."</td>
                              <td>".htmlentities($data['codigo'])."</td>
                              <td>".htmlentities($data['str'])."</td>
                       </tr>";
        }
        mysql_free_result($result);
        mysql_close($conn);
        $tabla.= '</table>
                  <style type="text/css">
                         table { font-size: 85%; }
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
					                                    "sScrollX": "750px",
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
                                                                  var id_emple = $(this).attr("id");
                                                                  var id_emplor = $(this).attr("title");
                                                                  var n_cron = $("#td"+id_emple).html();

                                                                  $("#srvcron").html(\'<div align="center"><img  alt="cargando" src="../../../vista/ajax-loader.gif" /></div>\');
                                                                  $.post("/modelo/rrhh/nvdas/nvups.php",{emple: id_emple, empledor:id_emplor,  nomcron: n_cron, accion: "lsrv"},function(data){
                                                                                                                                                           $("#srvcron").html(data);
                                                                                                                                                         });
                                                                  });
                  </script>';
        print $tabla;
     }
     elseif ($accion == 'lsrv'){
            $emple = $_POST['emple'];
            $emplor = $_POST['empledor'];
            $conn = conexcion();
            $cond = ""; //verifica que modulos esta habilitado a operar el usuario
            $modulos = "SELECT moduloAfectado FROM usuariosxestructuras where (id_usuario = $_SESSION[userid]) and (id_estructura = $_SESSION[structure])";
            $result_modulos = mysql_query($modulos, $conn);
            if ($row = mysql_fetch_array($result_modulos)){
               if ($row[moduloAfectado] == 2)
                  $cond = " and (id = 13)";
            }
            $sql = "SELECT upper(nov_text) as nov, id
                    FROM cod_novedades c
                    where (activa) $cond
                    order by nov_text";
            $result = mysql_query($sql, $conn) or die (mysql_error($conn));
            $tabla = '<form id="newnov">
                      <fieldset class="ui-widget ui-widget-content ui-corner-all">
                      <legend class="ui-widget ui-widget-header ui-corner-all">'.$_POST['nomcron'].'</legend>
                      <table border="0" id="nueva" name="nueva">
                             <tr>
                                 <td>Codigo de novedad</td>
                                 <td><select id="novedad" name="novedad" title="Please select something!" validate="required:true">';
            while ($data = mysql_fetch_array($result)){
              $tabla.="<option value='$data[id]'>".htmlentities($data['nov'])."</option>";
            }
            mysql_free_result($result);
            mysql_close($conn);
            $tabla.= '</td>
                      </tr>
                      <tr>
                          <td>Fecha desde</td>
                          <td><input id="desde" name="desde" type="text" size="12" class="required ui-widget ui-widget-content  ui-corner-all"></td>
                      </tr>
                      <tr>
                          <td>Fecha hasta</td>
                          <td><input id="hasta" name="hasta" type="text" size="12" class="required ui-widget ui-widget-content  ui-corner-all"></td>
                      </tr>
                      <tr>
                          <td><input type="submit" id="addnov" value="Agregar novedad"></td>
                          <td><input type="submit" id="vernov" value="Ver ultimas novedades de '.$_POST['nomcron'].'" onclick="$(location).attr(\'href\',\'/vista/rrhh/nvdas/nvlist.php?emp='.$emplor.'&emple='.$emple.'\');"></td>
                      </tr>
                      </table>
                      	</fieldset>
                       </form>
                      <br>
                      <br>
                      <br>
                      <br>
                      <style type="text/css">
                             #newnov .error{
	                                        font-size:0.8em;
	                                        color:#ff0000;
                                         }
                             </style>
                      <script>
                            $("select").selectmenu({width: 350});
                            $("#desde, #hasta").datepicker({dateFormat:"dd/mm/yy"});
                            $("#addnov, #vernov").button();
                            $("html, body").animate({scrollTop: $(document).height()},1500);
                            $("#newnov").validate({
                                                    submitHandler: function(e){
                                                                               var des = $("#desde").val();
                                                                               var has = $("#hasta").val();
                                                                               var noved = $("#novedad option:selected").val();
                                                                               $.post("/modelo/rrhh/nvdas/nvups.php",{emple:'.$emple.', desde: des, hasta: has, nov: noved, accion: "savenv"},function(data){
                                                                                                                                                                                                              var mje;
                                                                                                                                                                                                              if (isDigit(data)){
                                                                                                                                                                                                                   mje = "<div class=\"ui-widget\">"+
                                                                                                                                                                                                                         "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                                                                                         "<p><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                                                                         "<strong>Se ha grabado con exito la novedad en la Base de Datos!</strong></p>"+
                                                                                                                                                                                                                         "</div>"+
                                                                                                                                                                                                                         "<div>";
                                                                                                                                                                                                              }
                                                                                                                                                                                                              else{
                                                                                                                                                                                                                   mje = "<div class=\"ui-widget\">"+
                                                                                                                                                                                                                         "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                                                                                         "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                                                                         "<strong>Se han producido errores al intentar guardar la novedad</strong></p>"+
                                                                                                                                                                                                                         "</div>"+
                                                                                                                                                                                                                         "<div>";
                                                                                                                                                                                                                   }
                                                                                                                                                                                                              $("#srvcron").html(mje);
                                                                                                                                                                                                             });
                                                                               }
                                                    });

                            function isDigit(value){
                                                    return typeof value === \'number\' || !isNaN(Number(value.replace(/^\s*$/, \'a\')));
                            }
                      </script>';
            print $tabla;

     }
     elseif ($accion == 'savenv'){
            $desde = dateToMysql($_POST['desde'], "/");
            $hasta = dateToMysql($_POST['hasta'], "/");
            $campos = "id, id_empleado, desde, hasta, id_novedad, estado, activa, pendiente, usuario, fecha_alta, usertxt, id_estructura";
            $values = "$_POST[emple], '$desde', '$hasta', $_POST[nov], 'no_disp', 1, 0, $_SESSION[userid], now(), '', $_SESSION[structure]";
            $ok = insert('novedades', $campos, $values);
            $conn = conexcion();
            $sql = "SELECT id as id_orden
                    FROM (SELECT id, fservicio, hsalida
                          FROM ordenes o
                          WHERE (not finalizada) and (id_chofer_1 = $_POST[emple])
                          and (fservicio between '$desde' and '$hasta')
                         ) o";
            $result = mysql_query($sql, $conn);
            mysql_close($conn);
            $campo='id_chofer_1, id_estructura_chofer1, id_user, fecha_accion';
            while ($row = mysql_fetch_array($result)){
                  $value="null, null, $_SESSION[userid], now()";
                  backup('ordenes', 'ordenes_modificadas', "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])");
                  update('ordenes', $campo, $value, "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])");
            }
            
            $conn = conexcion();
            $sql = "SELECT id as id_orden
                    FROM (SELECT id, fservicio, hsalida
                          FROM ordenes o
                          WHERE (not finalizada) and (id_chofer_2 = $_POST[emple])
                          and (fservicio between '$desde' and '$hasta')
                         ) o";
            $result = mysql_query($sql, $conn);
            mysql_close($conn);
            $campo='id_chofer_2, id_estructura_chofer2, id_user, fecha_accion';
            while ($row = mysql_fetch_array($result)){
                  $value="null, null, $_SESSION[userid], now()";
                  backup('ordenes', 'ordenes_modificadas', "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])");
                  update('ordenes', $campo, $value, "(id = $row[id_orden]) and (id_estructura = $_SESSION[structure])");
            }
            print $ok;

     }
?>

