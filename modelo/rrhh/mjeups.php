<?php
     session_start();
     include ('../../controlador/bdadmin.php');
     include ('../../controlador/ejecutar_sql.php');
     include ('../../modelo/utils/dateutils.php');
     $accion = $_POST['accion'];
     
     if ($accion == 'lcr'){
        $conn = conexcion();
        $cliente = $_POST['cli'];
        $sql="SELECT upper(es.nombre) as str, e.id_empleado, legajo, upper(concat(apellido, ', ',e.nombre)) as apenom, nrodoc, if(e.activo, 'checked', '') as activo, upper(codigo) as codigo
                FROM empleados e
                left join empleadores em on (em.id = e.id_empleador) and (em.id_estructura = e.id_estructura_empleador)
                left join cargo c on c.id = e.id_cargo and c.id_estructura = e.id_estructura_cargo
                inner join estructuras es on es.id = e.id_estructura
                where (e.id_empleador = $_POST[cli]) and (e.id_estructura in (SELECT uxe.id_estructura
                                                                                                 FROM usuarios u
                                                                                                 inner join usuariosxestructuras uxe on uxe.id_usuario = u.id
                                                                                                 where u.id = $_SESSION[userid]))
                order by apellido";

        $result = mysql_query($sql, $conn) or die (mysql_error($conn));
        $tabla = '<br>Listado de personal<br><table id="example" name="example" width="100%">
	                      <thead>
		                         <tr>
                                     <th>Legajo</th>
			                         <th>Apellido, Nombre</th>
			                         <th>DNI</th>
			                         <th>Puesto</th>
			                         <th>Afectado a estructura:</th>
			                         <th>Sel.</th>
		                          </tr>
                          </thead>
                          <tbody>';
        while ($data = mysql_fetch_array($result)){
              $tabla.="<tr>
                              <td class='sel'>$data[legajo]</td>
                              <td class='sel' id='td$data[id_empleado]'>".htmlentities($data['apenom'])."</td>
                              <td class='sel'>".htmlentities($data['dni'])."</td>
                              <td class='sel'>".htmlentities($data['codigo'])."</td>
                              <td class='sel'>".htmlentities($data['str'])."</td>
                              <td><input type='checkbox' id='$data[id_empleado]' onclick=\"cargarCheck(this);\"></td>
                       </tr>";
        }
        mysql_free_result($result);

        $tabla.= '</table>';
        

        
        $tabla.='<input type="button" value="Crear Mensaje" id="create">
                  <form id="newnov">
                      <fieldset class="ui-widget ui-widget-content ui-corner-all">
                      <legend class="ui-widget ui-widget-header ui-corner-all">Crear Mensaje</legend>
                      <table border="0" id="mjeup" name="mjeup">
                      <tr>
                          <td>Mostrar a partir de:</td>
                          <td><input id="apartir" name="apartir" type="text" size="12" class="required ui-widget ui-widget-content  ui-corner-all"></td>
                      </tr>
                      <tr>
                          <td>Mensaje:</td>
                          <td><textarea id="mjetxt" name="mjetxt" rows="6" cols="35" class="required ui-widget ui-widget-content  ui-corner-all"></textarea></td>
                      </tr>
                      <tr>
                          <td><input type="button" id="addmje" value="Enviar Mensaje"></td>
                          <td><input type="button" id="cancel" value="Cancelar"></td>
                      </tr>
                      </table>
	                  </fieldset>
                  </form>';
        $sql="SELECT mensaje, upper(apenom), m.fecha_alta, m.vigencia_desde, upper(concat(apellido, ', ', nombre))
FROM mensajes m
inner join usuarios u on u.id = id_usuarioalta
inner join empleados e on e.id_empleado = m.id_empleado
order by m.fecha_alta DESC";

        $result = mysql_query($sql, $conn) or die (mysql_error($conn));
        $tabla.= '<br>Ultimos mensajes enviados<br><table id="mensajes" name="mensajes" width="100%">
	                      <thead>
		                         <tr>
                                     <th>Mensaje dirigido a:</th>
			                         <th>Fecha Alta</th>
			                         <th>Usuario Alta</th>
			                         <th>Mensaje</th>
			                         <th>Mostrar a partir del</th>
		                          </tr>
                          </thead>
                          <tbody>';
        while ($data = mysql_fetch_array($result)){
              $tabla.="<tr>
                              <td class='sel'>".htmlentities($data[4])."</td>
                              <td class='sel'>".htmlentities($data[2])."</td>
                              <td class='sel'>".htmlentities($data[1])."</td>
                              <td class='sel'>".htmlentities($data[0])."</td>
                              <td>$data[3]</td>
                       </tr>";
        }
        mysql_free_result($result);
        mysql_close($conn);
        $tabla.= '</tbody></table><br>';
        $tabla.='<style type="text/css">
                         #mensajes { font-size: 75%; }
                  </style>
                  <script>
                          var conductores = new Array();
                          if (!Array.indexOf) {
                                Array.prototype.indexOf = function (obj, start) {
                                for (var i = (start || 0); i < this.length; i++) {
                                    if (this[i] == obj) {
                                       return i;
                                    }
                                }
                                return -1;
                                }
                          }

                         function cargarCheck(orden){
                                  if (orden.checked){
                                     conductores.push(orden.id);
                                  }
                                   else{
                                         var a = conductores.indexOf(orden.id);
                                         conductores.splice(a,1);
                                         }
                         }
                                 $("#create").click(function(){$("#newnov").toggle();});
                                 $("#newnov").toggle();
                          		$("#example").dataTable({
					                                    "sScrollY": "200px",
					                                    "sScrollX": "100%",
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
                          		$("#mensajes").dataTable({
					                                    "sScrollY": "200px",
					                                    "sScrollX": "100%",
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
                                $("#apartir").datepicker({dateFormat:"dd/mm/yy"});
                                $(":button, #addmje, #cancel").button();
                                $("#addmje").click(function(){
                                                                  $.post("/modelo/rrhh/mjeups.php", {accion: "svemje", mje: $("#mjetxt").val() ,fecha: $("#apartir").val(), conduc: conductores.join(",")}).done(function(){
                                                                                                                                                                                                                             $("#mjetxt").val("");
                                                                                                                                                                                                                             $("#apartir").val("");
                                                                                                                                                                                                                             conductores = new Array();
                                                                                                                                                                                                                             $("#newnov").toggle();
                                                                                                                                                                                                                            });

                                                                  });
                  </script>';
        print $tabla;
     }
     elseif ($accion == 'svemje'){
            $desde = dateToMysql($_POST['fecha'], "/");
            $conduct = explode(',', $_POST['conduc']);
            for ($i = 0; $i < count($conduct); $i++){
                $campos = "id, id_empleado, mensaje, visto, usuario_alta, fecha_alta, usuario_baja, fecha_baja, vigencia_desde, id_usuarioalta, id_usuariobaja";
                $values = "$conduct[$i], '$_POST[mje]', 0, $_SESSION[userid], now(), null, null, '$desde', $_SESSION[userid], null";
                $ok = insert('mensajes', $campos, $values);
                print $ok;
            }
     }
?>

