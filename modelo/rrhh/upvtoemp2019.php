<?php
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  include ('../../vista/paneles/viewpanel.php');
  include_once('../../modelo/utils/dateutils.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];

  if ($accion == 'sve'){ ///codigo para guardar ////
     $conn = conexcion();
     $sql = "SELECT * FROM unidades WHERE (interno = $_POST[interno]) and (id_estructura = $_SESSION[structure])";
     $result = mysql_query($sql, $conn);
     if (mysql_num_rows($result)){
        $ok = "iebd"; //codigo de error para indicar que ya existe el numero de interno que se esta intentando asignar
     }
     else{
          if ($_SESSION['permisos'][4] > 2){
             $campos = "id, interno, patente, marca, modelo, cantasientos, id_pase, video, bar, banio, activo, id_estructura, id_calidadcoche, id_estructura_calidadcoche, id_tipounidad, id_estructura_tipounidad, consumo, id_propietario, id_estructura_propietario";
             $video = $_POST['video'] ? 1 : 0;
             $banio = $_POST['banio'] ? 1 : 0;
             $bar = $_POST['bar'] ? 1 : 0;
             $values = "'$_POST[interno]', '$_POST[dominio]', '$_POST[marca]', '$_POST[modelo]', '$_POST[cantas]', '0', $video, $bar, $banio, 1, $_SESSION[structure], $_POST[calidad], $_SESSION[structure], $_POST[tipo], $_SESSION[structure], '$_POST[consumo]', $_POST[propietario], $_SESSION[structure]";
          }
          else{
               $campos = "id, interno, patente, id_estructura, id_propietario, id_estructura_propietario";
               $values = "'$_POST[interno]', '$_POST[dominio]', $_SESSION[structure], $_POST[propietario], $_SESSION[structure]";
          }
          $ok = insert('unidades', $campos, $values);
     }
     //cerrarconexcion($conn);
     print json_encode($ok);
  }
  elseif($accion == 'sveevt'){
     $conn = conexcion();
     $sql = "SELECT *
             FROM unidades
             WHERE (interno = $_POST[n_interno]) and (id_propietario = $_POST[propietario]) and (id_estructura_propietario,$_SESSION[structure])";
     $result = mysql_query($sql, $conn);
     cerrarconexcion($conn);
     if (mysql_fetch_array($result)){
        $ok = "0"; //codigo de error para indicar que ya existe el numero de interno que se esta intentando asignar
     }
     else{
          $ok = insert('unidades', 'id, interno, id_estructura, procesado, id_propietario, id_estructura_propietario', "$_POST[n_interno], $_SESSION[structure], 0, $_POST[propietario], $_SESSION[structure]"); //agrega una unidad pendiente de procesamiento por parte del sector  seg vial
     }
     print json_encode($ok);
  }
  elseif($accion == 'load'){
          $conn = conexcion();
          $sql = "SELECT id_empleado, upper(concat(apellido, ', ',nombre)), legajo, nrodoc, upper(razon_social) as emplor
                  FROM empleados e
                  inner join empleadores em on em.id = e.id_empleador
                  where (e.id_empleado = $_POST[emple])";

          $result = mysql_query($sql, $conn);
          $data = mysql_fetch_array($result);

          $tabla= '<fieldset class="ui-widget ui-widget-content ui-corner-all">
                <legend class="ui-widget ui-widget-header ui-corner-all">Datos del empleado</legend>
                <form id="modunidad">
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Empleados</td>
                                    <td>'.htmlentities($data['emplor']).'
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                <td WIDTH="20%">Legajo</td>
                                    <td><input id="dominio" name="dominio" readonly size="8" value="'.$data['legajo'].'" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">DNI</td>
                                    <td><input id="dominio" name="dominio" readonly size="8" value="'.$data['nrodoc'].'" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                         </table>
            </fieldset>
          </form>
          <fieldset class="ui-widget ui-widget-content ui-corner-all">
                <legend class="ui-widget ui-widget-header ui-corner-all">Clientes afectados al Empleado</legend>';
          $sql = "SELECT upper(c.razon_social)
                  FROM conductoresxcliente cxc
                  inner join clientes c on c.id = cxc.id_cliente and c.id_estructura = cxc.id_estructuracliente
                  where id_empleado = $_POST[emple]";
          $result = mysql_query($sql, $conn);
          $i = 1;
          while ($row = mysql_fetch_array($result)) {
            if ($i == 1)
              $tabla.=$row[0];
            else
              $tabla.=" | $row[0]";
            $i++;
          }
          $tabla.="</fieldset>";
          $tabla.='<fieldset class="ui-widget ui-widget-content ui-corner-all">
                <legend class="ui-widget ui-widget-header ui-corner-all">Vencimientos Asignados al Empleado</legend>
                <form id="modunidad">
                      <div id="result"></div>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Tipo Vencimiento</td>
                                    <td>
                                    <select id="tipovto">
                                    <option value="0">Seleccione Una Opcion</option>';
          $sql = "SELECT li.id, upper(li.licencia) as lic
                  FROM licenciasxconductor l
                  inner join licencias li on li.id = l.id_licencia
                  where id_conductor = $_POST[emple]
                  order by lic";
          $result = mysql_query($sql, $conn);
          cerrarconexcion($conn);
          while ($row = mysql_fetch_array($result)){
                $tabla.="<option value='$row[id]'>$row[lic]</option>";
          }
          $tabla.='</select></td>
                                    <td><input type="button" id="ultvto" value="Ver ultimos Vtos.">
                                    </td>
                                </tr>
                                <tr id="trupvto">
                                    <td>Fecha Vto.</td>
                                    <td><input id="fecvto" type="text" size="20" class="ui-widget ui-widget-content  ui-corner-all"></td>
                                    <td><input type="button" value="Guardar Vto" id="svevto"></td>
                                </tr>
                         </table>
            </fieldset>
          </form>
          <script type="text/javascript">
                          $("#tipovto").selectmenu({width: 300});
                          $("#fecvto").datepicker({ dateFormat: "dd/mm/yy" });
                          $("#trupvto").hide();
                          $("#svevto").button().click(function(data){
                                                                     var emple = $("#emple").val();
                                                                     var tivto = $("#tipovto").val();
                                                                     var fecha = $("#fecvto").val();
                                                                     $.post("/modelo/rrhh/upvtoemp.php", {emp: emple, vto: tivto, fec: fecha, accion: "savevto"}, function(data){
                                                                                                                                                                                    var obj = JSON.parse(data);
                                                                                                                                                                                    var mjetxt;
                                                                                                                                                                                    if (obj > 0){
                                                                                                                                                                                       $("#fecvto").val("");
                                                                                                                                                                                       var coche = $("#emple option:selected").text();
                                                                                                                                                                                       mjetxt = "Se ha grabado con exito el vencimiento correspondiente a "+coche;
                                                                                                                                                                                    }
                                                                                                                                                                                    var mje = "<div class=\"ui-widget\">"+
                                                                                                                                                                                               "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                                                               "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                                               "<strong>"+mjetxt+"!</strong></p>"+
                                                                                                                                                                                               "</div>"+
                                                                                                                                                                                               "<div>";
                                                                                                                                                                                       $("#result").html(mje);

                                                                                                                                                                                    });
                                                                     });
                         $("#ultvto").button().click(function(data){
                                                                    var micro = $("#emple").val();
                                                                    var coche = $("#emple option:selected").text();
                                                                    var dialog = $("<div style=\"display:none\" id=\"dialog\" class=\"loading\" align=\"center\"></div>").appendTo("body");
                                                                    dialog.dialog({
                                                                                   close: function(event, ui) {dialog.remove();},
                                                                                   title: "Ver ultimos vencimientos correspondiente a "+coche,
                                                                                   width:850,
                                                                                   height:300,
                                                                                   modal:true,
                                                                                         show: {
                                                                                                effect: "blind",
                                                                                                duration: 1000
                                                                                         },
                                                                                         hide: {
                                                                                               effect: "blind",
                                                                                               duration: 1000
                                                                                               }
                                                                                   });
                                                                                   dialog.load("/modelo/rrhh/upvtoemp.php",{mic:micro, accion: "vervtos"},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass("loading");});
                                                                    });
                         $("#tipovto").change(function(){
                                                         $("#result").html("");
                                                         if ($(this).val() == 0){
                                                            $("#trupvto").hide();
                                                            $("#fecvto").val("");
                                                         }
                                                         else{
                                                              $("#trupvto").show();
                                                         }
                                                         });
          </script>';
          print $tabla;
  }
  elseif($accion == 'savevto'){
          $fecha = dateToMysql($_POST['fec'],'/');
          $campos = "id, id_conductor, id_licencia, vigencia_hasta, id_usuario, fecha_alta";
          $values = "$_POST[emp], $_POST[vto], '$fecha', $_SESSION[userid], now()";
          print json_encode(insert("licenciaconductor", $campos, $values));
  }
  elseif($accion == 'vervtos'){
          $sql="SELECT l.id, upper(licencia) as vtv, date_format(l.vigencia_hasta, '%d/%m/%Y') as vto, upper(apenom) as usr, date_format(fecha_Alta, '%d/%m/%Y - %H:%i') as fup
                FROM licenciaconductor l
                inner join usuarios u on u.id = l.id_usuario
                inner join licencias li on li.id = l.id_licencia
                where id_conductor = $_POST[mic]
                order by l.vigencia_hasta desc
                limit 10";
          $conn = conexcion();
          $result = mysql_query($sql, $conn);
          $tabla = "<fieldset class='ui-widget ui-widget-content ui-corner-all'>
                    <legend class='ui-widget ui-widget-header ui-corner-all'></legend>
                    <table id='table' align='center' class='tablesorter' border='0' width='100%'>
                     <thead>
            	            <tr>
                                <th>Tipo Vencimiento</th>
                                <th>Fecha Vencimiento</th>
                                <th>Usuario Alta</th>
                                <th>Fecha - Hora Alta</th>
                                <th>Accion</th>
                            </tr>
                     </thead>
                     <tbody>";
          while($data = mysql_fetch_array($result)){
                      $tabla.="<tr id='$data[id]'>
                                   <td>$data[vtv]</td>
                                   <td>$data[vto]</td>
                                   <td>$data[usr]</td>
                                   <td>$data[fup]</td>
                                   <td><a href=''><img src='../../vista/css/images/eliminar.gif' width='16' height='16' border='0'></a></td>
                               </tr>";
          }
          $tabla.="</tbody>
                  </table>
                  </fieldset>
                  <script>
                          $('#table a').click(function(event) {
                                                        event.preventDefault();
                                                        var lic = $(this).parent().parent().attr('id');
                                                        if (confirm('Seguro Eliminar Vencimiento?')){
                                                           $.post('/modelo/rrhh/upvtoemp.php', {id_lic: lic, accion: 'delvto'}, function(data){
                                                                                                                                               $('#'+lic).remove();
                                                                                                                                               });
                                                        }
                                                        });
                  </script>";
          mysql_free_result($result);
          cerrarconexcion($conn);
          print $tabla;
  }
  elseif($accion == 'delvto'){
          $conn = conexcion();
          $sql = "DELETE FROM licenciaconductor WHERE id = $_POST[id_lic]";
          mysql_query($sql, $conn);
  }
?>

