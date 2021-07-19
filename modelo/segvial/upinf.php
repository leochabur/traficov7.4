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
     $fin = dateToMysql($_POST['fsin'],'/');
     $fen = ($_POST[fen]?"'".dateToMysql($_POST[fen],'/')."'":'NULL');
     $fve = ($_POST[fve]?"'".dateToMysql($_POST[fve],'/')."'":'NULL');
     $fcp = ($_POST[fcp]?"'".dateToMysql($_POST[fcp],'/')."'":'NULL');
     $frp = ($_POST[frp]?"'".dateToMysql($_POST[frp],'/')."'":'NULL');


     $campos ="id,
               id_coche,
               fecha,
               id_conductor_1,
               id_conductor_2,
               id_ciudad,
               id_tipo_infraccion,
               importe,
               id_resolucion,
               fecha_entrega,
               fecha_pago_real,
               compromiso_pago,
               latitud,
               longitud,
               id_user,
               fecha_mod,
               id_conductor_3,
               detalle_resolucion,
               fecha_vencimiento,
               id_estructura";

     $values=($_POST[interno] ? $_POST[interno]:"NULL");
     $values.=",'$fin'";
     $values.=",". ($_POST[e1] ? $_POST[e1]:"NULL");
     $values.=",". ($_POST[e2] ? $_POST[e2]:"NULL");
     $values.=",". ($_POST[ciudadsin] ? $_POST[ciudadsin]:"NULL");
     $values.=",". ($_POST[tipo] ? $_POST[tipo]:"NULL");
     $values.=",". ($_POST[importe] ? $_POST[importe]:"NULL");
     $values.=",". ($_POST[reso] ? $_POST[reso]:"NULL");
     $values.=",$fen";
     $values.=",$frp";
     $values.=",$fcp";
     $values.=",".($_POST[lat] ? $_POST[lat]:"NULL");
     $values.=",".($_POST[long] ? $_POST[long]:"NULL");
     $values.=",$_SESSION[userid]";
     $values.=",NOW()";
     $values.=",". ($_POST[e3] ? $_POST[e3]:"NULL");
     $values.=",". ($_POST[detaller] ? "'".cambiarStr($_POST[detaller])."'":"NULL");
     $values.=",$fve";
     $values.=",".($_POST[estr] ? $_POST[estr]:"NULL");


     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     try{
         insert("infracciones", $campos, $values);
         $response[msge] = "La infraccion ha sido dada de alta con exito en la BD!";
         print (json_encode($response));
     }catch (Exception $e) {
                            $response[status] = false;
                            $response[msge]="No se ha podido realizar la accion solicitada!!!";
                            $response[msge]=$e->getMessage();
                            print (json_encode($response));
                            };
  }
  elseif($accion == 'update'){
     $fin = dateToMysql($_POST['fsin'],'/');
     $fen = ($_POST[fen]?"'".dateToMysql($_POST[fen],'/')."'":'NULL');
     $fve = ($_POST[fve]?"'".dateToMysql($_POST[fve],'/')."'":'NULL');
     $fcp = ($_POST[fcp]?"'".dateToMysql($_POST[fcp],'/')."'":'NULL');
     $frp = ($_POST[frp]?"'".dateToMysql($_POST[frp],'/')."'":'NULL');


     $campos ="id_coche,
               fecha,
               id_conductor_1,
               id_conductor_2,
               id_ciudad,
               id_tipo_infraccion,
               importe,
               id_resolucion,
               fecha_entrega,
               fecha_pago_real,
               compromiso_pago,
               latitud,
               longitud,
               id_user,
               fecha_mod,
               id_conductor_3,
               detalle_resolucion,
               fecha_vencimiento,
               id_estructura";

     $values=($_POST[interno] ? $_POST[interno]:"NULL");
     $values.=",'$fin'";
     $values.=",". ($_POST[e1] ? $_POST[e1]:"NULL");
     $values.=",". ($_POST[e2] ? $_POST[e2]:"NULL");
     $values.=",". ($_POST[ciudadsin] ? $_POST[ciudadsin]:"NULL");
     $values.=",". ($_POST[tipo] ? $_POST[tipo]:"NULL");
     $values.=",". ($_POST[importe] ? $_POST[importe]:"NULL");
     $values.=",". ($_POST[reso] ? $_POST[reso]:"NULL");
     $values.=",$fen";
     $values.=",$frp";
     $values.=",$fcp";
     $values.=",".($_POST[lat] ? $_POST[lat]:"NULL");
     $values.=",".($_POST[long] ? $_POST[long]:"NULL");
     $values.=",$_SESSION[userid]";
     $values.=",NOW()";
     $values.=",". ($_POST[e3] ? $_POST[e3]:"NULL");
     $values.=",". ($_POST[detaller] ? "'".cambiarStr($_POST[detaller])."'":"NULL");
     $values.=",$fve";
     $values.=",".($_POST[estr] ? $_POST[estr]:"NULL");
     
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     $conn = conexcion();
     try{
         begin($conn);
         backup('infracciones', 'infracciones_modificadas', "id = $_POST[ninf]", $conn);
         update('infracciones', $campos, $values, "id = $_POST[ninf]", $conn);
         commit($conn);
         cerrarconexcion($conn);
         $response[msge] = "La infraccion ha sido modificada con exito en la BD!";
         print (json_encode($response));
     }catch (Exception $e) {
                            $response[status] = false;
                            $response[msge]="No se ha podido realizar la accion solicitada!!!";
                            $response[sql]=$e->getMessage();
                            rollback($conn);
                            cerrarconexcion($conn);
                            print (json_encode($response));
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
  elseif($accion == 'ordcnd'){
           $fecha = dateToMysql($_POST['fecha'],'/');
           $sql = "select o.id, upper(concat(nombre,'(',razon_social,'). H salida:',hsalida)) as servicio
                   from ordenes o
                   inner join clientes c on c.id = o.id_cliente
                   where fservicio = '$fecha' and $_POST[cond] in (id_chofer_1, id_chofer_2) and o.id_estructura = $_POST[estr]";
           $result = ejecutarSQL($sql);
           $select="<select id='ortra' name='ortra'><option value=0></option>";
           while ($data = mysql_fetch_array($result)){
                 $select.="<option value=$data[0]>$data[1]</option>";
           }
           $select.="</select>";
           print $select;
  }
  
  function cambiarStr($str){
           return str_replace(",",";",$str);
  }
  
  

  
  
?>

