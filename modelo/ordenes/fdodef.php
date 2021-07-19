<?php
     session_start();
     include ('../../controlador/bdadmin.php');
     include ('../../controlador/ejecutar_sql.php');
     include ('../../modelo/utils/dateutils.php');

     $accion = $_POST['accion'];
     
     if ($accion == 'getyears')
     {
        $conn = conexcion(true);
        $sql="SELECT year(fecha) as anio
              FROM feriados
              group by year(fecha)
              order by anio DESC";

        $result = mysqli_query($conn, $sql);
        $options = '';
        $count = 0;
        while ($row = mysqli_fetch_array($result))
        {
            $options.="<option value='$row[0]'>$row[0]</option>";
            $count++;
        }

        if ((!$count) || (isset($_POST['initial'])))
        {
            $data = "<table border='0' align='center' width='50%''>
                        <tr>
                          <td WIDTH='20%''>Anio</td>
                          <td>
                              <select id='years'>
                                  $options
                              </select>
                          </td>
                          <td>
                              <input type='button' value='Cargar feriados' id='load'/>
                          </td>
                        </tr>
                    </table>
                    <div id='views'>

                    </div>
                    <script>
                        $('#years').selectmenu({width: 100});
                        $('#load').button().click(function(){
                                                            $('#views').html(\"<div align='center'><img  alt='cargando' src='../../vista/ajax-loader.gif' /></div>\");
                                                            $.post('/modelo/ordenes/fdodef.php',
                                                                   {accion : 'views', year : $('#years').val()},
                                                                   function(data){
                                                                                    $('#views').html(data);
                                                                   });
                          });
                          $('#load').trigger('click');
                    </script>";
        }
        else
        {
          $data = "<script>
                        $('#load').trigger('click');
                    </script>";
        }
        mysqli_free_result($result);
        mysqli_close($conn);
        print $data;
      }
      elseif ($accion == 'views')
      {
            $conn = conexcion(true);
            $sql="SELECT f.id, date_format(fecha, '%d/%m/%Y') as fecha, upper(descripcion) as descrip,
                        date_format(fecha_carga, '%d/%m/%Y %H:%i') as fecha_carga, upper(u.apenom) as alta, eliminado,
                        date_format(fecha_baja, '%d/%m/%Y %H:%i') as fecha_baja, upper(ub.apenom) as baja
                  FROM feriados f
                  inner join usuarios u on u.id = id_user
                  left join usuarios ub on ub.id = id_user_baja
                  where year(fecha) = $_POST[year]
                  order by f.fecha";

            $result = mysqli_query($conn, $sql);
            $tabla = '<hr>
                      <table class="table table-zebra">
                            <thead>
                                 <tr> 
                                    <th>Fecha</th>
                                    <th>Descripcion</th>
                                    <th>Fecha carga</th>
                                    <th>Usuario carga</th>
                                    <th>Estado</th>
                                    <th>Fecha baja</th>
                                    <th>Usuario baja</th>
                                    <th></th>
                                  </tr>
                              </thead>
                              <tbody>';
            while ($row = mysqli_fetch_array($result))
            {
                $btn = ($row['eliminado']?'':"<input type='button' value='Eliminar' data-fecha='$row[1]' data-id='$row[id]' class='delete'/>");
                $state = ($row['eliminado']?'Eliminado':'Activo');
                $tabla.="<tr>
                            <td>$row[1]</td>
                            <td>$row[2]</td>
                            <td>$row[3]</td>
                            <td>$row[4]</td>
                            <td>$state</td>
                            <td>$row[6]</td>
                            <td>$row[7]</td>
                            <td>$btn</td>
                        </tr>";
            }
            $tabla.="</tbody></table>
            <script>
                    $('.delete').button().click(function(){
                                                            var btn = $(this);
                                                            if (confirm('Seguro eliminar el feriado de fecha '+btn.data('fecha')+'?'))
                                                            {
                                                                $.post('/modelo/ordenes/fdodef.php',
                                                                       {accion : 'delete', id : btn.data('id')},
                                                                       function(data){
                                                                                        $('#load').trigger('click');
                                                                       });
                                                            }

                      });
            </script>";
            print $tabla;
      }
      elseif($accion == 'save')
      {
          $fecha = dateToMysql($_POST['fecha'], "/");
          $campos = "fecha, descripcion, id_user, fecha_carga, id_estructura";
          $descripcion = str_replace(',',' ', $_POST['descripcion']);
          $values = "'$fecha', '$descripcion', $_SESSION[userid], now(), $_POST[str]";
          $conn = conexcion(true);
          mysqli_query($conn, "INSERT INTO feriados ($campos) VALUES ($values)") or die(mysqli_error($conn));
          mysqli_close($conn);
      }
      elseif($accion == 'delete')
      {
          $campos = "eliminado = 1, id_user_baja = $_SESSION[userid], fecha_baja = now()";
          $sql = "UPDATE feriados SET $campos WHERE id = $_POST[id]";
          $conn = conexcion(true);
          mysqli_query($conn, $sql) or die(mysqli_error($conn));
          mysqli_close($conn);
      }
        /*
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
     }*/
?>

