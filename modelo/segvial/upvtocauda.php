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
                <legend class="ui-widget ui-widget-header ui-corner-all">Polizas de Seguro Asignadas a la Unidad</legend>
                <form id="modunidad">
                      <div id="result"></div>
                         <table border="0" align="center" width="75%" name="tabla">
                                <tr>
                                    <td>Polizas
                                    <select id="polizas">
                                    <option value="0">Seleccione Una Opcion</option>';
          $sql = "select  id_poliza, concat (compania, ' // ', nro_poliza,'- (', date_format(vigente_desde, '%d/%m/%Y'),' - ', date_format(vigente_hasta, '%d/%m/%Y'), ')'), date_format(vigente_desde, '%d/%m/%Y') as desde, date_format(vigente_hasta, '%d/%m/%Y') as hasta
from(
    SELECT id_coche as id_micro, upper(compania) as compania, vigente_desde, vigente_hasta, v.id as id_poliza, nro_poliza
    FROM polizasSeguroCoches v
    inner join companiasAseguradoras ca on ca.id = v.id_companiaSeguro
    where activa) u
where id_micro = $_POST[unidad]";
          $result = mysql_query($sql, $conn);

          while ($row = mysql_fetch_array($result)){
                $tabla.="<option value='$row[0]'>$row[1]</option>";
          }
          $tabla.='</select></td><td WIDTH="20%"><input type="button" id="baja" value="Dar de Baja">
                                    </td>
                                </tr>
                                <tr id="trupvto">
                                    <td>Desde
                                    <input id="desde" type="text" size="20" class="ui-widget ui-widget-content  ui-corner-all">
                                    Hasta
                                    <input id="hasta" type="text" size="20" class="ui-widget ui-widget-content  ui-corner-all">
                                    Poliza<input id="numpoliza" type="text" size="20" class="ui-widget ui-widget-content  ui-corner-all">
                                    </td><td WIDTH="20%"><input type="button" value="Modificar Datos" id="svevto"></td>
                                </tr>
                         </table>
            </fieldset>
          </form>
          <fieldset class="ui-widget ui-widget-content ui-corner-all">
                <legend class="ui-widget ui-widget-header ui-corner-all">Cargar Nueva Poliza</legend>
                <form>
 <table border="0" align="center" width="65%" name="tabla">
                                <tr>
                                    <td>'.htmlentities("Compañias").' de Seguro</td>
                                    <td><select id="companias">';
          $sql = "SELECT * FROM companiasAseguradoras order by compania";
          $result = mysql_query($sql, $conn);
          cerrarconexcion($conn);
          while ($row = mysql_fetch_array($result)){
                $tabla.="<option value='$row[0]'>$row[1]</option>";
          }
          $tabla.='</select></td><td>Numero Poliza</td><td><input id="newnumpoliza" type="text" size="20" class="ui-widget ui-widget-content  ui-corner-all"></td>
                                </tr>
                                <tr id="trupvto">
                                    <td>Desde</td>
                                    <td><input id="newdesde" type="text" size="20" class="ui-widget ui-widget-content  ui-corner-all"></td>
                                    <td>Hasta</td>
                                    <td><input id="newhasta" type="text" size="20" class="ui-widget ui-widget-content  ui-corner-all"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" align="right"><input type="button" value="Cargar Poliza" id="newpol"></td>
                                </tr>
                         </table>
                
                </form>
           </fieldset>
          <script type="text/javascript">
                          $("#polizas").selectmenu({width: 500});
                          $("#companias").selectmenu({width: 300});
                          $("#desde, #hasta, #newdesde, #newhasta").datepicker({ dateFormat: "dd/mm/yy", changeMonth: true, changeYear: true });
                          $("#trupvto").hide();
                          $("#svevto").button().click(function(data){
                                                                     var poliza = $("#polizas").val();
                                                                     var hasta = $("#hasta").val();
                                                                     var desde = $("#desde").val();
                                                                     var numero = $("#numpoliza").val();
                                                                     $.post("/modelo/segvial/upvtocauda.php", {pol: poliza, des: desde, has: hasta, nro: numero, accion: "updpoliza"}, function(data){
                                                                                                                                                                                                      if (data == 1)
                                                                                                                                                                                                         alert("se ha dado modificado con exito la poliza!!");
                                                                                                                                                                                                      });
                                                                     });
                         $("#baja").button().click(function(data){
                                                                    var polizas = $("#polizas").val();
                                                                    var pol = $("#polizas option:selected").text();
                                                                    if (confirm("Seguro dar de baja la poliza "+pol)){
                                                                       $.post("/modelo/segvial/upvtocauda.php", {poliza:polizas, accion: "bjaPoliza"}, function(data){ if (data == 1)
                                                                                                                                                                          alert("se ha dado de baja con exito la poliza!!");});
                                                                    }


                                                                    });
                                                                    
                         $("#newpol").button().click(function(data){
                                                                    var coche = $("#unidad").val();
                                                                    var comp = $("#companias").val();
                                                                    var hasta = $("#newhasta").val();
                                                                    var desde = $("#newdesde").val();
                                                                    var numero = $("#newnumpoliza").val();
                                                                    if (confirm("Seguro guardar la poliza? ")){
                                                                       $.post("/modelo/segvial/upvtocauda.php", {ch:coche, cmp:comp, des: desde, has:hasta, nro:numero, accion: "newPoliza"}, function(data){ if (data == 1)
                                                                                                                                                                                                               alert("se ha dado de alta con exito la poliza!!");
                                                                                                                                                                                                               $("#result").html(data);
                                                                                                                                                                                                            });
                                                                    }


                                                                    });
                                                                    
                         $("#polizas").change(function(){
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
  elseif($accion == 'updpoliza'){
          $desde = dateToMysql($_POST['des'], "/");
          $hasta = dateToMysql($_POST['has'], "/");
          try{
              update("polizasSeguroCoches", "nro_poliza, vigente_desde, vigente_hasta, fecha_mod, usr_mod", "$_POST[nro], '$desde', '$hasta', now(), $_SESSION[userid]", "id = $_POST[pol]");
              print "1";
          }catch (Exception $e) {
                 print "$e";
          }
  }
  elseif($accion == 'bjaPoliza'){
          try{
              update("polizasSeguroCoches", "activa", 0, "id = $_POST[poliza]");
              print "1";
          }catch (Exception $e) {
                 print "0";
          }
  }
  elseif($accion == 'newPoliza'){
          $desde = dateToMysql($_POST['des'], "/");
          $hasta = dateToMysql($_POST['has'], "/");
          try{
              insert("polizasSeguroCoches", "id, id_coche, id_companiaSeguro, nro_poliza, vigente_desde, vigente_hasta, usr_alta, fecha_alta, activa",
                     "$_POST[ch], $_POST[cmp], '$_POST[nro]', '$desde', '$hasta', $_SESSION[userid], now(), 1");
              print "1";
          }catch (Exception $e) {
                 print "$_POST[ch], $_POST[cmp], '$_POST[nro]', '$desde', '$hasta', $_SESSION[userid], now(), 1)";
          }
  }
?>

