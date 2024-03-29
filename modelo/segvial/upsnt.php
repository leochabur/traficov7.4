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
     $fecha = dateToMysql($_POST['fsin'],'/');
     $campos ="id,
               id_empleado,
               siniestro_numero,
               fecha_siniestro,
               hora_siniestro,
               estado_clima,
               cod_ubicacion,
               calle1,
               calle2,
               id_localidad,
               tipo_lesion,
               resp_estimada,
               cobertura_afectada,
               id_coche,
               id_cliente,
               compania_seguro,
               numero_poliza,
               costos_administrativos,
               id_estructura,
               usr_alta,
               fecha_alta,
               latitud,
               longitud,
               norma_no_respetada,
               tipo_maniobra,
               costos_reparacion_unidades,
               num_sin_asignado,
               id_estado,
               afecta_estadistica,
               potencial_grave,
               resp_propia";
     $values = $_POST[condsin] ? $_POST[condsin]:"NULL";
     $values.=",'$_POST[nsin]'";
     $values.=",'$fecha'";
     $values.=(','. ($_POST['hsin'] ? "'$_POST[hsin]'":"NULL"));
     $values.=",". ($_POST['climasin'] ? $_POST['climasin']:"NULL");
     $values.=",". ($_POST[ubicacionsin] ? $_POST[ubicacionsin]:"NULL");
     $values.=",". ($_POST[calle1sin] ? "'".str_replace(',', ' ', $_POST['calle1sin'])."'":"NULL");
     $values.=",". ($_POST[calle2sin] ? "'".str_replace(',', ' ', $_POST['calle2sin'])."'":"NULL");
     $values.=",". ($_POST[ciudadsin] ? $_POST[ciudadsin]:"NULL");
     $values.=",". ($_POST[lesionsin] ? $_POST[lesionsin]:"NULL");
     $values.=",". ($_POST[respsin] ? "'$_POST[respsin]'":"NULL");
     $values.=",". ($_POST[cobafecsin] ? $_POST[cobafecsin]:"NULL");
     $values.=",". ($_POST[internosin] ? $_POST[internosin]:"NULL");
     $values.=",". ($_POST[clientesin] ? $_POST[clientesin]:"NULL");
     $values.=",". ($_POST[compaseg] ? $_POST[compaseg]:"NULL");
     $values.=",". ($_POST[poliza] ? "'$_POST[poliza]'":"NULL");
     $values.=",". ($_POST[costadmin] ? $_POST[costadmin]:"null");
     $values.=",". ($_POST[estr] ? $_POST[estr]:"null");
     $values.=",$_SESSION[userid]";
     $values.=",NOW()";
     $values.=",". ($_POST[lat] ? $_POST[lat]:"0");
     $values.=",". ($_POST[long] ? $_POST[long]:"null");
     $values.=",". ($_POST[normanrsin] ? $_POST[normanrsin]:"null");
     $values.=",". ($_POST[tipomaniobrasin] ? $_POST[tipomaniobrasin]:"null");
     $values.=",". ($_POST[costrepa] ? $_POST[costrepa]:"null");
     $values.=", '$_POST[nsinasig]'";
     $values.=", ".($_POST[estadosin] ? $_POST[estadosin]:"null");
     $values.=", ".(isset($_POST[afes])?"1":"0");
     $values.=", ".(isset($_POST[potencial])?"1":"0");
     $values.=", ".(isset($_POST[respropia])?"1":"0");
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     try{
         insert("siniestros", $campos, $values);
         $response[msge] = "Se ha almacenado con exito el siniestro en la BD";
         //cerrarconexcion($conn);
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = $e->getMessage()." - Se ha producido un error al intentar guardar el siniestro!!!";
                           print json_encode($response);
                          };
  }
  elseif($accion == 'update'){
     $campos ="id_empleado,
               fecha_siniestro,
               hora_siniestro,
               estado_clima,
               cod_ubicacion,
               calle1,
               calle2,
               id_localidad,
               tipo_lesion,
               resp_estimada,
               cobertura_afectada,
               id_coche,
               id_cliente,
               compania_seguro,
               numero_poliza,
               costos_administrativos,
               id_estructura,
               usr_edit,
               fecha_edit,
               latitud,
               longitud,
               norma_no_respetada,
               tipo_maniobra,
               costos_reparacion_unidades,
               num_sin_asignado,
               id_estado,
               id_accionCorrectiva,
               fecha_accion_correctiva,
               obs_accion_correctiva,
               afecta_estadistica,
               potencial_grave,
               resp_propia";
               
     $cond = "id = $_POST[nsin]";
     $fecha = dateToMysql($_POST['fsin'],'/');
     $values = $_POST[condsin] ? $_POST[condsin]:"NULL";
     $values.=",'$fecha'";
     $values.=(','. ($_POST['hsin'] ? "'$_POST[hsin]'":"NULL"));
     $values.=",". ($_POST['climasin'] ? $_POST['climasin']:"NULL");
     $values.=",". ($_POST[ubicacionsin] ? $_POST[ubicacionsin]:"NULL");
     $values.=",". ($_POST[calle1sin] ? "'".str_replace(',', ' ', $_POST['calle1sin'])."'":"NULL");
     $values.=",". ($_POST[calle2sin] ? "'".str_replace(',', ' ', $_POST['calle2sin'])."'":"NULL");
     $values.=",". ($_POST[ciudadsin] ? $_POST[ciudadsin]:"NULL");
     $values.=",". ($_POST[lesionsin] ? $_POST[lesionsin]:"NULL");
     $values.=",". ($_POST[respsin] ? "'$_POST[respsin]'":"NULL");
     $values.=",". ($_POST[cobafecsin] ? $_POST[cobafecsin]:"NULL");
     $values.=",". ($_POST[internosin] ? $_POST[internosin]:"NULL");
     $values.=",". ($_POST[clientesin] ? $_POST[clientesin]:"NULL");
     $values.=",". ($_POST[compaseg] ? $_POST[compaseg]:"NULL");
     $values.=",". ($_POST[poliza] ? "'$_POST[poliza]'":"NULL");
     $values.=",". ($_POST[costadmin] ? $_POST[costadmin]:"null");
     $values.=",". ($_POST[estr] ? $_POST[estr]:"null");
     $values.=",$_SESSION[userid]";
     $values.=",NOW()";
     $values.=",". ($_POST[lat] ? $_POST[lat]:"0");
     $values.=",". ($_POST[long] ? $_POST[long]:"null");
     $values.=",". ($_POST[normanrsin] ? $_POST[normanrsin]:"null");
     $values.=",". ($_POST[tipomaniobrasin] ? $_POST[tipomaniobrasin]:"null");
     $values.=",". ($_POST[costrepa] ? $_POST[costrepa]:"null");
     $values.=", '$_POST[nsinasig]'";
     $values.=", ".($_POST[estadosin] ? $_POST[estadosin]:"null");
     $values.=",". ($_POST[acccor] ? $_POST[acccor]:"NULL");
     $values.=",". ($_POST[feacc] ? ("'".dateToMysql($_POST['feacc'],'/')."'"):"NULL");
     $values.=", '".str_replace(",",";",$_POST[detresin])."'";
     $values.=", ".(isset($_POST[afes])?1:0);
     $values.=", ".(isset($_POST[potencial])?"1":"0");
     $values.=", ".(isset($_POST[respropia])?"1":"0");
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     $response[mtasn]=false;
     $conn=conexcion();
     try {
          if ($_POST[acccor]){   //selecciono una fecha de accion correctiva

             if ($_POST[feacc]){ //si selecciono una fecha de accion correctiva debe registrarse la fecha de la misma

                $fechaacc = dateToMysql($_POST['feacc'],'/');
                $sql="SELECT imprimeFormulario FROM accionesCorrectivasSiniestros where id = $_POST[acccor]";

                $result=mysql_query($sql, $conn);
                if ($row = mysql_fetch_array($result)){

                   if ($row[0]){
                      $sql="select upper(apenom) from usuarios u where u.id = $_POST[solacccor]";//obtiene el sector del usuario logeado

                      $result=mysql_query($sql, $conn);
                      $convocante = "";
                      if ($row = mysql_fetch_array($result)){
                         $convocante=$row[0];
                      }
                      $sql = "select concat('SINIESTRO NUMERO ', s.id, ' OCURRIDO EL: ', date_format(fecha_siniestro, '%d/%m/%Y'),' - TIPO DE MANIOBRA: ', upper(maniobra),' - NORMA NO RESPETADA: ', upper(norma)) as detalle
                              from siniestros s
                              left join normas_seg_vial n on n.id = s.norma_no_respetada
                              left join tipo_maniobra_siniestro t on t.id = tipo_maniobra
                              where s.id = $_POST[nsin]";
                      $result = mysql_query($sql, $conn);
                      $detalle = '';
                      if ($row = mysql_fetch_array($result)){
                         $detalle=$row[0];
                      }
                      $impl=($_POST[feimacc] ? ("'".dateToMysql($_POST['feimacc'],'/')."'"):"NULL");
                      $veri=($_POST[feveracc] ? ("'".dateToMysql($_POST['feveracc'],'/')."'"):"NULL");
                      $resp=($_POST[solacccor]?$_POST[solacccor]:"NULL");
                      $tipoAccion = ($_POST[tipoacccor]?$_POST[tipoacccor]:"NULL");
                      $campos_minuta="id_siniestro, id_empleado, id_responsable, sector_convocante, fecha, temas_a_tratar, medidas_adoptadas, fecha_alta, id_user, fecha_implementacion, fecha_verificacion, tipo_accion_correctiva";
                      $valores_minuta="$_POST[nsin], $_POST[condsin], $resp, '".str_replace(",",";",$convocante)."', '$fechaacc', '$detalle', '".str_replace(",",";",$_POST[detresin])."', now(), $_SESSION[userid], $impl, $veri, $tipoAccion";

                      $existe_mta = mysql_query("SELECT * FROM minuta_de_reunion WHERE id_siniestro = $_POST[nsin]", $conn);
                      if ($rowmin = mysql_fetch_array($existe_mta)){
                         $mta=$rowmin[id];
                         try{
                                     update('minuta_de_reunion', $campos_minuta, $valores_minuta, "id = $mta", $conn);
                              }
                              catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = $e->getMessage()." - Se ha producido un error al intentar guardar la minuta!!!";
                           die (json_encode($response));
                          };
                      }
                      else{
                           $mta=insert('minuta_de_reunion', "id,".$campos_minuta, $valores_minuta, $conn);
                      }

                      $response[mtasn]=true;
                      $response[mtanro]=$mta;
                   }
                }
             }
             else{
                  $response[status] = false;
                  $response[msge] = "Debe seleccionar una fecha para la accion correctiva!!!!!!";
                  print json_encode($response);
                  exit();
             }
          }
         update("siniestros", $campos, $values, $cond, $conn) ;
         mysql_close($conn);
         $response[msge] = "Se ha almacenado con exito el siniestro en la BD";
         //cerrarconexcion($conn);
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = $e->getMessage()." - Se ha producido un error al intentar guardar el siniestro!!!";
                           print json_encode($response);
                          };
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
  elseif($accion == 'savevto'){
          $fecha = dateToMysql($_POST['fec'],'/');
          $campos = "id, id_micro, vencimiento, fechaAlta, usuarioAlta, id_tipovtv, id_estructuratipovtv";
          $values = "$_POST[mic], '$fecha', now(), $_SESSION[userid], $_POST[vtv], $_SESSION[structure]";
          print json_encode(insert("vtosinternos", $campos, $values));
  }
  elseif($accion == 'vervtos'){
          $sql="SELECT v.id, upper(nombre) as vtv, date_format(v.vencimiento, '%d/%m/%Y') as vto, upper(apenom) as usr, date_format(fechaAlta, '%d/%m/%Y - %H:%i') as fup
                FROM vtosinternos v
                inner join tipovencimiento tv on tv.id = v.id_tipovtv
                inner join usuarios u on u.id = usuarioAlta
                where id_micro = $_POST[mic]
                order by v.vencimiento desc
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
                      $tabla.="<tr id='fila-$data[id]'>
                                   <td>$data[vtv]</td>
                                   <td>$data[vto]</td>
                                   <td>$data[usr]</td>
                                   <td>$data[fup]</td>
                                   <td align='center'><img src='../../eliminar.png' width='21' height='22' border='0' id='$data[id]'></td>
                               </tr>";
          }
          $tabla.="</tbody>
                  </table>
                  </fieldset>
                  <script>
                          $('img').click(function(){
                                                    var id = $(this).attr('id');
                                                    if (confirm('Seguro eliminar el vencimiento?')){
                                                       $.post('/modelo/segvial/upvtouda.php', {accion:'delvto', id_nov: id}, function(data){
                                                                                                                                         if (data == 1){
                                                                                                                                            $('#fila-'+id).remove();
                                                                                                                                         }
                                                                                                                                         else{
                                                                                                                                              alert('No se ha podido eliminar el vencimiento');
                                                                                                                                         };
                                                                                                                                         });
                                                    }
                                                    });
                  </script>";
          mysql_free_result($result);
          cerrarconexcion($conn);
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
  elseif($accion == 'proxnrosin'){
           $sql = "SELECT (max(id)+1) as prox FROM siniestros";
           $result = ejecutarSQL($sql);
           if ($data = mysql_fetch_array($result)){
              print $data[0];
           }
  }
?>

