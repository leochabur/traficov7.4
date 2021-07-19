<?
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  include ('../../vista/paneles/viewpanel.php');
  include_once('../../modelo/utils/dateutils.php');
   if (!$_SESSION['auth']){
      session_destroy();
      print('<b><p align="center">Su sesion ha expirado!</p></b><meta http-equiv="Refresh" content="2;url=/">');
      exit;
   }
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];

  if ($accion == 'sve'){ ///codigo para guardar ////
     $emision = dateToMysql($_POST['emision'],'/');
     $entrega = $_POST['entrega']?"'".dateToMysql($_POST['entrega'],'/')."'": 'NULL';
     $rpta = $_POST['rpta']?"'".dateToMysql($_POST['rpta'],'/')."'":'NULL';
     $campos ="id, id_empleado, fecha_emision, fecha_entrega, id_solicitante, mediante, descripcion_hecho, fecha_respuesta, resolucion, detalle_resolucion, nro_descargo, id_siniestro, usr_alta, fecha_alta";
     $values = $_POST[destinatario] ? $_POST[destinatario]:"NULL";
     $values.=",'$emision'";
     $values.=",$entrega";
     $values.=",". ($_POST[solicitante] ? $_POST[solicitante]:"NULL");
     $values.=",". ($_POST[mediante] ? "'$_POST[mediante]'":"NULL");
     $values.=",". ($_POST[desc_hecho] ? "'".str_replace(",",";",$_POST[desc_hecho])."'":"NULL");
     $values.=",$rpta";
     $values.=",". ($_POST[resolucion] ? $_POST[resolucion]:"NULL");
     $values.=",". ($_POST[detalleresolucion] ? "'$_POST[detalleresolucion]'":"NULL");
     $values.=", $_POST[ndesc]";
     $values.=",". ($_POST[siniestro] ? $_POST[siniestro]:"NULL");

   //  die($values);
     try{
         insert("descargos", "$campos", $values.",$_SESSION[userid], now()");
         print "1";
     }
     catch (Exception $e) {print $e->getMessage();};
    // print $values;
     //print json_encode($ok);
  }
  elseif ($accion == 'upd'){ ///codigo para guardar ////
     $emision = dateToMysql($_POST['emision'],'/');
     $entrega = $_POST['entrega']?"'".dateToMysql($_POST['entrega'],'/')."'": 'NULL';
     $rpta = ($_POST['rpta']?"'".dateToMysql($_POST['rpta'],'/')."'":'NULL');
     $campos ="id_empleado, fecha_emision, fecha_entrega, id_solicitante, mediante, descripcion_hecho, fecha_respuesta, resolucion, detalle_resolucion, id_siniestro, usr_alta, fecha_alta";
     $values = $_POST[destinatario] ? $_POST[destinatario]:"NULL";
     $values.=",'$emision'";
     $values.=",$entrega";
     $values.=",". ($_POST[solicitante] ? $_POST[solicitante]:"NULL");
     $values.=",". ($_POST[mediante] ? "'$_POST[mediante]'":"NULL");
     $values.=",". ($_POST[desc_hecho] ? "'".str_replace(",",";",$_POST[desc_hecho])."'":"NULL");
     $values.=",$rpta";
     $values.=",". ($_POST[resolucion] ? $_POST[resolucion]:"NULL");
     $values.=",". ($_POST[detalleresolucion] ? "'".str_replace(",",";",$_POST[detalleresolucion])."'":"NULL");
     $values.=",". ($_POST[siniestro] ? $_POST[siniestro]:"NULL");

     //  die($values);
     $conn = conexcion();
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     try{
         begin($conn);
         backup("descargos", "descargos_modificados", "id = $_POST[dcgo]", $conn);
         update("descargos", "$campos", $values.",$_SESSION[userid], now()", "id = $_POST[dcgo]", $conn);
         commit($conn);
         $response[msge] = "Se ha actualizado con exito el descargo en la BD";
         cerrarconexcion($conn);
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = $e->getMessage()." - Se ha producido un error al intentar actualizar el descargo!!!";
                           cerrarconexcion($conn);
                           print json_encode($response);
                          };
    // print $values;
     //print json_encode($ok);
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
          $sql = "SELECT u.id as id_unidad, interno, patente, marca, modelo, marca_motor, anio, cantasientos, if(u.activo, 'checked', '') as activo, if(video, 'checked', '') as video, if(bar, 'checked', '') as bar, if(banio, 'checked', '') as banio, id_calidadcoche, id_tipounidad, consumo, id_propietario, calidad,  tipo, e.id as id_propietario, e.razon_social, nueva_patente
                  FROM (SELECT * FROM unidades WHERE (id = $_POST[unidad])) u
                  LEFT JOIN calidadcoche cc ON (cc.id = u.id_calidadcoche) and (cc.id_estructura = u.id_estructura_calidadcoche)
                  LEFT JOIN tipounidad tu ON (tu.id = u.id_tipounidad) and (tu.id_estructura = u.id_estructura_tipounidad)
                  LEFT JOIN empleadores e ON (e.id = u.id_propietario) and (e.id_estructura = u.id_estructura_propietario)";

          $result = mysql_query($sql, $conn);
          $data = mysql_fetch_array($result);

          $tabla= '<fieldset class="ui-widget ui-widget-content ui-corner-all">
                <legend class="ui-widget ui-widget-header ui-corner-all">Datos de la Unidad</legend>
                <form id="modunidad">
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Propietario</td>
                                    <td>'.$data['razon_social'].'
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Dominio</td>
                                    <td><input id="dominio" name="dominio" readonly size="8" value="'.$data['patente'].'" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Nuevo Dominio</td>
                                    <td><input id="dominio" name="dominio" readonly size="8" value="'.$data['nueva_patente'].'" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                         </table>
            </fieldset>
          </form>
          <fieldset class="ui-widget ui-widget-content ui-corner-all">
                <legend class="ui-widget ui-widget-header ui-corner-all">Tipo de Vencimientos Asignados a la Unidad</legend>
                <form id="modunidad">
                      <div id="result"></div>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Tipo Vencimiento</td>
                                    <td>
                                    <select id="tipovto">
                                    <option value="0">Seleccione Una Opcion</option>';
          $sql = "SELECT tv.id, upper(nombre) as vto FROM tipovencimientoporinterno t inner join tipovencimiento tv on tv.id = t.idtipovencimiento where (idunidad = $_POST[unidad])";
          $result = mysql_query($sql, $conn);
          cerrarconexcion($conn);
          while ($row = mysql_fetch_array($result)){
                $tabla.="<option value='$row[id]'>$row[vto]</option>";
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
                          $("#tipovto").selectmenu({width: 250});
                          $("#fecvto").datepicker({ dateFormat: "dd/mm/yy" });
                          $("#trupvto").hide();
                          $("#svevto").button().click(function(data){
                                                                     var micro = $("#unidad").val();
                                                                     var tivto = $("#tipovto").val();
                                                                     var fecha = $("#fecvto").val();
                                                                     $.post("/modelo/segvial/upvtouda.php", {mic: micro, vtv: tivto, fec: fecha, accion: "savevto"}, function(data){
                                                                                                                                                                                    var obj = JSON.parse(data);
                                                                                                                                                                                    var mjetxt;
                                                                                                                                                                                    if (obj > 0){
                                                                                                                                                                                       $("#fecvto").val("");
                                                                                                                                                                                       var coche = $("#unidad option:selected").text();
                                                                                                                                                                                       mjetxt = "Se ha grabado con exito el vencimiento correspondiente al interno "+coche;
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
                                                                    var micro = $("#unidad").val();
                                                                    var coche = $("#unidad option:selected").text();
                                                                    var dialog = $("<div style=\"display:none\" id=\"dialog\" class=\"loading\" align=\"center\"></div>").appendTo("body");
                                                                    dialog.dialog({
                                                                                   close: function(event, ui) {dialog.remove();},
                                                                                   title: "Ver ultimos vencimientos correspondiente al interno "+coche,
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
                                                                                   dialog.load("/modelo/segvial/upvtouda.php",{mic:micro, accion: "vervtos"},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass("loading");});
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

  elseif($accion == 'delvto'){
          try{
              delete("vtosinternos", "id","$_POST[id_nov]");
              print "1";
          }catch (Exception $e) {
                 print "0";
          }
  }
  elseif($accion == 'proxnrodcgo'){
           $sql = "SELECT (max(nro_descargo)+1) as prox FROM descargos";
           $result = ejecutarSQL($sql);
           if ($data = mysql_fetch_array($result)){
              print $data[0];
           }
  }
  elseif($accion == 'sincnd'){
       print "<option value='0'>NO DETALLA</option>".armarSelect('siniestros', 'id', 'id', "concat(id,' - ', date_format(fecha_siniestro, '%d/%m/%Y'))", "(id_empleado = $_POST[cnd])", 1);
  }
  elseif ($accion == 'deldcgo'){ ///codigo para eliminar ////

     $conn = conexcion();
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     try{
         $campos = "usr_alta, fecha_alta, eliminado";
         $values = "$_SESSION[userid], now(), 1";
         begin($conn);
         backup("descargos", "descargos_modificados", "id = $_POST[dcgo]", $conn);
         update("descargos", "$campos", $values.",$_SESSION[userid], now()", "id = $_POST[dcgo]", $conn);
         commit($conn);
         $response[msge] = "Se ha eliminado con exito el descargo en la BD";
         cerrarconexcion($conn);
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = "Se ha producido un error al intentar elimnar el descargo!!!";
                           $response[sql] = $e->getMessage();
                           cerrarconexcion($conn);
                           print json_encode($response);
                          };
    // print $values;
     //print json_encode($ok);
  }
?>

