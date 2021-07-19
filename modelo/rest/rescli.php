<?php
  session_start();
     set_time_limit(0);
     error_reporting(0);
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
    include ('../../controlador/bdadmin.php');
    include ('../../vista/paneles/viewpanel.php');
    include ('../../controlador/ejecutar_sql.php');
    $accion = $_POST['accion'];
    
    $sql_rest_gen = "select *
            from (SELECT r.id, upper(tu.tipo) as tipo, id_cliente, id_estructuracliente, antiguedad
                 FROM (select * from restclientetipounidad where id_tipounidad is not null) r
            inner join tipounidad tu on tu.id = r.id_tipounidad and tu.id_estructura = r.id_estructura_tipounidad
            union all
            SELECT r.id, upper(tv.nombre), id_cliente, id_estructuracliente, antiguedad
            FROM (select * from restclientetipounidad where id_tipovto is not null) r
            inner join tipovencimiento tv on tv.id = r.id_tipovto and tv.id_estructura = r.id_estructuratipovto) r
            where id_cliente = $_POST[cli] and id_estructuracliente = $_POST[str]";//filtra los tipos de unidad que no forman parte de la restriccion
    

    if ($accion == 'load'){
       $cronos = "cronogramas";
       if (($_POST['last'])){
          $cronos = "(select c.id, c.id_cliente, c.id_estructura, c.nombre, c.ciudades_id_origen, c.ciudades_id_destino, c.activo, precio_unitario
                     from ordenes o
                     inner join (select * from servicios where id_estructura = $_POST[str]) s on s.id = o.id_servicio
                     inner join (select * from cronogramas where id_estructura = $_POST[str]) c on c.id = s.id_cronograma
                     where fservicio between date_sub(date(now()), interval 60 day) and date(now()) and o.id_estructura = $_POST[str]
                     group by c.id)";
       }

       $sql = "select c.id, upper(nombre), upper(o.ciudad), upper(d.ciudad), precio_unitario
               from $cronos c
               inner join (select id as id_o, ciudad from ciudades where id_estructura = $_POST[str]) o on o.id_o = c.ciudades_id_origen
               inner join (select id as id_d, ciudad from ciudades where id_estructura = $_POST[str]) d on d.id_d = c.ciudades_id_destino
               where id_estructura = $_POST[str] and id_cliente = $_POST[cli] and activo
               order by nombre";
     //  die($sql);
       $conn = conexcion();
       $result = mysql_query($sql, $conn);
       $tabla='<table id="example-advanced" name="example-advanced" border="1">
                    <thead>
                           <tr class="ui-widget-header">
                               <th>Nombre Servicio</th>
                               <th>Origen</th>
                               <th>Destino</th>
                               <th>Importe</th>
                               <th>Guardar/Actualizar</th>
                           </tr>
                    </thead>
                    <tbody>';
       while ($data = mysql_fetch_array($result)){
             $tabla.="<tr>
                          <td>$data[1]</td>
                          <td>$data[2]</td>
                          <td>$data[3]</td>
                          <td><input type='text' size='7' id='data-$data[0]' value='$data[precio_unitario]'></td>
                          <td><input type='button' value='Guardar' id='$data[0]'></td>
                      </tr>";
       }
       $tabla.="</tbody></thead><script>
                                        $('#example-advanced').treetable();
                                        $('#example-advanced tr input:button').button().click(function(){
                                                                                                             var id = $(this).attr('id');
                                                                                                             var value = $('#data-'+id).val();
                                                                                                             if ($.isNumeric(value)){
                                                                                                                $.post('/modelo/servicios/valcro.php', {id_cron: id, price: value, accion:'updvalue', str: $_POST[str]});



                                                                                                             }
                                                                                                             else{
                                                                                                                  alert('El valor ingresado es invalido!');
                                                                                                                  $('#data-'+id).select();
                                                                                                             }
                                                                                                         });
</script>";
       mysql_close($conn);
       
       print $tabla;
}
elseif($accion=='loadres'){    // global $sql_rest_gen;
                         //  die($sql_rest_gen);
    $conn=conexcion();

                                   //<li><a href="#tabs-2">Parametros Restriccion Conductores</a></li>
    $tabla = '<div id="tabs">
                   <ul>
                       <li><a href="#tabs-1">Parametros Restriccion Unidades</a></li>
                       <li><a href="#tabs-2">Parametros Restriccion Conductores</a></li>
                   </ul>
                   <div id="tabs-1">
                        <div id="tabcoches">
                             <ul>
                                 <li><a href="#tabs1">Parametros Restriccion Unidades</a></li>
                                 <li><a href="#tabs2">Unidades Habilitadas Por El Cliente</a></li>
                             </ul>
                             <div id = "tabs1">'.
                                  getTablaRestGral($conn, $_POST[cli], $_POST[str]).getTablaRestTipo($conn, $_POST[cli], $_POST[str]).
                             '</div>
                             <div id="tabs2">'.getCochesHabilitados($_POST[cli], $_POST[str]).'
                             </div>
                        </div>
                   </div>
                   <div id="tabs-2">
                        <div id="tabcond">
                             <ul>
                                 <li><a href="#tabs1rc">Parametros Restriccion Vencimientos</a></li>
                                 <li><a href="#tabs2rc">Conductores Habilitadas Para El Cliente</a></li>
                             </ul>
                             <div id="tabs1rc">';
                                  $conn = conexcion();
                                  $tabla.=getTablaRestTipoLicencia($conn, $_POST[cli], $_POST[str]).'
                             </div>
                             <div id="tabs2rc">
                                  '.getConductoresHabilitados($_POST[cli], $_POST[str]).'
                             </div>
                        </div>
                   </div>

                   </div>'.getScriptStyle();
         /*   $tabla_aux.='<div id="tabs-2">
                <table id="rtpo" width="40%"  class="ui-widget ui-widget-content tabla" align="center">
                    <thead>
                           <tr class="ui-widget-header">
                               <th colspan="3">Vencimientos Requeridos por el Cliente</th>
                           </tr>
                           <tr class="ui-widget-header">
                               <th>Nro</th>
                               <th>Tipo Vencimiento</th>
                               <th>Quitar</th>
                           </tr>
                </div>
                </div>';  */
    @mysql_close($conn);
    
    print $tabla;
    
}
elseif($accion=='addrstpo'){
     $conn = conexcion();
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     if ($_POST[tpors] == 't'){
        if (!is_numeric($_POST['anti'])){
             $response[status] = false;
             $response[msge] = "Debe cargar un valor a la antiguedad maxima para el tipo de vehiculo seleccionado!!!";
             print json_encode($response);
             exit;          
        }
        if ($_POST[tipo] != "null"){
           $campos ="id, id_estructura, id_cliente, id_estructuracliente, id_tipounidad, id_estructura_tipounidad, antiguedad";
           $values="$_POST[estr], $_POST[clien], $_POST[estr], $_POST[tipo], $_POST[estr], $_POST[anti]";
        }
        else{
             $response[status] = false;
             $response[msge] = "Se ha producido un error al intentar almacenar la restriccion!!!";
             print json_encode($response);
             exit;
        }
     }
     else{
        $campos ="id, id_estructura, id_cliente, id_estructuracliente, id_tipovto, id_estructuratipovto";
        $values="$_POST[estr], $_POST[clien], $_POST[estr], $_POST[vto], $_POST[estr]";
     }
     try{
         begin($conn);
         insert("restclientetipounidad", $campos, $values, $conn);
         commit($conn);
         $response[msge] = "Restriccion generada con exito!!";
         cerrarconexcion($conn);
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = "Se ha producido un error al intentar almacenar la restriccion!!!";
                           $response[sql] = $e->getMessage();
                           cerrarconexcion($conn);
                           print json_encode($response);
                          };
}
elseif($accion=='delrstu'){
     $conn = conexcion();
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     $campos ="id";
     $values="$_POST[rst]";
     $valores="";
     try{
         begin($conn);
         delete("restclientetipounidad", $campos, $values, $conn);
         commit($conn);
         $response[msge] = "Restriccion eliminada con exito!!";
         cerrarconexcion($conn);
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = "Se ha producido un error al intentar eliminar la restriccion!!!";
                           $response[sql] = $e->getMessage();
                           cerrarconexcion($conn);
                           print json_encode($response);
                          };
}
elseif($accion=='incche'){
     $conn = conexcion();
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     $campos ="id_coche, id_cliente, id_estructuracliente, permitido";
     $values="$_POST[che], $_POST[cli], $_POST[str], $_POST[sino]";
     try{
         $sql = "INSERT INTO restcochesclientes ($campos) VALUES ($values) ON DUPLICATE KEY UPDATE permitido = $_POST[sino]";
         mysql_query($sql, $conn);
         $response[msge] = "Restriccion modificada con exito!!";
         cerrarconexcion($conn);
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = "Se ha producido un error al intentar eliminar la restriccion!!!";
                           $response[sql] = $e->getMessage();
                           cerrarconexcion($conn);
                           print json_encode($response);
                          };
}
elseif($accion=='incccond'){
     $conn = conexcion();
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     $campos ="id_cliente, id_estructuracliente, id_empleado, permitido";
     $values=" $_POST[cli], $_POST[str], $_POST[emp], $_POST[sino]";
     try{
         $sql = "INSERT INTO conductoresxcliente ($campos) VALUES ($values) ON DUPLICATE KEY UPDATE permitido = $_POST[sino]";
         mysql_query($sql, $conn);
         $response[msge] = "Restriccion modificada con exito!! ".$sql;
         cerrarconexcion($conn);
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = "Se ha producido un error al intentar modificar la restriccion!!!";
                           $response[sql] = $e->getMessage();
                           cerrarconexcion($conn);
                           print json_encode($response);
                          };
}
elseif($accion=='addrstgral'){
     $conn = conexcion();
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     $campos ="susp_neum, susp_elas, motor_tras, motor_del, id_cliente, id_estructuraCliente, filtralistaunidades, admitefletero";
     $values="$_POST[susne], $_POST[susela], $_POST[mottras], $_POST[motdel], $_POST[cli], $_POST[str], $_POST[flista], $_POST[afl]";
     try{
         $sql = "INSERT INTO restricciongralcliente ($campos) VALUES ($values)
                 ON DUPLICATE KEY UPDATE susp_neum=$_POST[susne], susp_elas=$_POST[susela], motor_tras=$_POST[mottras], motor_del=$_POST[motdel], filtralistaunidades=$_POST[flista], admitefletero = $_POST[afl]";
         mysql_query($sql, $conn);
         $response[msge] = "Restriccion modificada con exito!!";
         cerrarconexcion($conn);
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = "Se ha producido un error al intentar eliminar la restriccion!!!";
                           $response[sql] = $e->getMessage();
                           cerrarconexcion($conn);
                           print json_encode($response);
                          };
}
elseif($accion=='adrslic'){
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     $campos ="id, id_licencia, id_estructura_licencia, id_cliente, id_estructura_cliente";
     $values="$_POST[tipo], $_SESSION[structure], $_POST[cliente], $_SESSION[structure]";
     try{
         $insert = insert("restxtipolicenciacliente", $campos, $values);
         $response[msge] = "Restriccion modificada con exito!!";
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = "Se ha producido un error al intentar eliminar la restriccion!!!";
                           $response[sql] = $e->getMessage();
                           print json_encode($response);
                          };
}
elseif($accion=='delrslic'){
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     $campos ="id";
     $values="$_POST[rst]";
     try{
         delete("restxtipolicenciacliente", $campos, $values);
         $response[msge] = "Restriccion eliminada con exito!!";
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = "Se ha producido un error al intentar eliminar la restriccion!!!";
                           $response[sql] = $e->getMessage();
                           print json_encode($response);
                          };
}
elseif($accion=='allint'){
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     $cch = explode(",", $_POST[cch]);
     $sino = $_POST[sn];
     $con = conexcion();
     try{
         foreach ($cch as $id_coche){
                 $campos ="id_coche, id_cliente, id_estructuracliente, permitido";
                 $values="$id_coche, $_POST[cli], $_POST[str], $sino";
                 $sql = "INSERT INTO restcochesclientes ($campos) VALUES ($values) ON DUPLICATE KEY UPDATE permitido = $sino";
                 ejecutarSQL($sql, $con);
         }
         if ($sino){
            $mje = "Se han asignado todos los coches con exito al cliente!";
         }
         else{
              $mje = "Se han eliminado todos los coches asignados al cliente!";
         }
         $response[msge] = $mje;
         cerrarconexcion($conn);
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = "Se ha producido un error al intentar realizar la accion solicitada";
                           $response[sql] = $e->getMessage();
                           cerrarconexcion($conn);
                           print json_encode($response);
                          };
}
elseif($accion=='sverstcnd'){
     $response = array();     ///respuesta al cliente de la accion requerida
     $response[status] = true;
     $sino = $_POST[sn];
     $con = conexcion();
     try{
         $campos ="id_cliente, id_estructura_cliente, filtralista";
         $values="$_POST[cli], $_POST[str], $sino";
         $sql = "INSERT INTO restgralconductorcliente ($campos) VALUES ($values) ON DUPLICATE KEY UPDATE filtralista = $sino";
         ejecutarSQL($sql, $con);
         $response[msge] = "Restriccion almacenada con exito!";
         cerrarconexcion($con);
         print json_encode($response);
     }
     catch (Exception $e) {
                           $response[status] = false;
                           $response[msge] = "Se ha producido un error al intentar realizar la accion solicitada";
                           $response[sql] = $e->getMessage();
                           cerrarconexcion($conn);
                           print json_encode($response);
                          };
}



function verificarOrden($orden){

}

function getTablaRestGral($conn, $cli, $str){
    $sql = "SELECT * FROM restricciongralcliente where id_cliente = $cli and id_estructuraCliente = $_POST[str]";
    $result = mysql_query($sql, $conn);

    if ($data = mysql_fetch_array($result)){
       $mot_del = $data[motor_del];
       $mot_trac = $data[motor_tras];
       $susp_neum = $data[susp_neum];
       $susp_elas = $data[susp_elas];
       $ant_max = $data[ant_max];
       $ad_flet = $data[admitefletero];
       $filtra_lista = $data[filtralistaunidades];
    }

//                                <tr>
//                                    <td>Antiguedad Maxima Parque (Ej. 5 a&ntilde;os)</td>
//                                    <td align="right"><input type="text" name="antmax" size="5" class="ui-widget ui-widget-content  ui-corner-all" value="'.$ant_max.'"></td>
//                                </tr>    
         return '<form id="rest_gral"> <table border="0" align="center" width="40%" name="tabla">
                                <tr>
                                    <td>Motor Delantero</td>
                                    <td align="right">SI<input type="radio" '.($mot_del?'checked="checked"':'').' name="motdel" value="1">NO<input type="radio" '.(!$mot_del?'checked="checked"':'').'name="motdel" value="0"></td>
                                </tr>
                                <tr>
                                    <td>Motor Tracero</td>
                                    <td align="right">SI<input type="radio" '.($mot_trac?'checked="checked"':'').' name="mottras" value="1">NO<input type="radio" '.(!$mot_trac?'checked="checked"':'').' name="mottras" value="0"></td>
                                </tr>
                                <tr>
                                    <td>Suspencion neumatica</td>
                                    <td align="right">SI<input type="radio" '.($susp_neum?'checked="checked"':'').' name="susne" value="1">NO<input type="radio" '.(!$susp_neum?'checked="checked"':'').' name="susne" value="0"></td>
                                </tr>
                                <tr>
                                    <td>Suspencion elastico</td>
                                    <td align="right">SI<input type="radio" '.($susp_elas?'checked="checked"':'').' name="susela" value="1">NO<input type="radio" '.(!$susp_elas?'checked="checked"':'').' name="susela" value="0"></td>
                                </tr>
                                <tr>
                                <td>Considera lista unidades</td>
                                    <td align="right">SI<input type="radio" '.($filtra_lista?'checked="checked"':'').' name="flista" value="1">NO<input type="radio" '.(!$filtra_lista?'checked="checked"':'').' name="flista" value="0"></td>
                                </tr>
                                <td>Admite fletero</td>
                                    <td align="right">SI<input type="radio" '.($ad_flet?'checked="checked"':'').' name="afl" value="1">NO<input type="radio" '.(!$ad_flet?'checked="checked"':'').' name="afl" value="0"></td>
                                </tr>
                   </table>
                   </form>';
}

function getTablaRestTipo($conn, $cli, $str){
    global $sql_rest_gen;
    $result = mysql_query($sql_rest_gen, $conn);
    $tabla='<br>
             <table id="rtpo" width="40%"  class="ui-widget ui-widget-content tabla" align="center">
                    <thead>
                           <tr class="ui-widget-header">
                               <th colspan="4">Tipo Unidades/Venciminentos Adminitidas Por El Cliente</th>
                           </tr>
                           <tr class="ui-widget-header">
                               <th>Nro</th>
                               <th>Tipo Unidad/Vencimiento</th>
                               <th>Ant.Max.</th>
                               <th>Quitar</th>
                           </tr>';
    $i=1;
    while ($data = mysql_fetch_array($result)) {
          $tabla.="<tr>
                       <td>".$i++."</td>
                       <td>$data[1]</td>
                       <td align='right'>$data[antiguedad]</td>                       
                       <td align='center' id='tr$data[0]'><img src='../../vista/menos.png' width='15' height='15' border='0' id='$data[0]' class='cursor'></td>
                   </tr>";

    }
    $filtro_tipos = "id not in(SELECT if (id_tipounidad is null, 0, id_tipounidad)
                     FROM restclientetipounidad
                     where id_estructura = $str and id_cliente = $cli and id_estructuracliente = $str) and (id_estructura = $str)";
    $tabla.='</table>
             <br>
             <table id="addrtpo" width="40%"  class="ui-widget ui-widget-content tabla" align="center">
                    <thead>
                           <tr class="ui-widget-header">
                               <th colspan="3">Agregar Nuevo Tipo Unidad/Vencimiento</th>
                           </tr>
                    </thead>
                    <tr>
                        <td>Tipo Unidad</td>
                        <td><select id="tipos">'.armarSelect('tipounidad', 'tipo', 'id', 'tipo', $filtro_tipos, 1).'</select></td>
                        <td><input type="text" id="antmaxunidad" size="5" class="ui-widget ui-widget-content  ui-corner-all" placeholder="Ant. Max."></td>
                        <td align="center"><img src="../../vista/add.png" width="15" height="15" border="0" id="t"></td>
                    </tr>
                    <tr>
                        <td>Vencimiento</td>
                        <td><select id="vtos">'.armarSelect('tipovencimiento', 'nombre', 'id', 'nombre', "id_estructura = $_POST[str]", 1).'</select></td>
                        <td></td>
                        <td align="center"><img src="../../vista/add.png" width="15" height="15" border="0" id="v"></td>
                    </tr>
                    <tr><td colspan="3"></td></tr>
             </table>
             <br>
             <table id="addrtpo" width="40%"  class="ui-widget ui-widget-content tabla" align="center" border="0">
                    <tr>
                        <td colspan="3" align="right"><input type="button" value="Guadar Restricciones" id="sveres"></td>
                    </tr>
             </table>';
    return $tabla;
}

function getScriptStyle(){
         return '<script>
                    $( "#tabs" ).tabs();
                    $( "#tabcoches, #tabcond" ).tabs();
                    $("#tipos, #vtos, #tipolic").selectmenu({width: 300});
                    $("#proc").hide();
                    $("#addrtpo img").click(function(){
                                                 var type = $(this).attr("id");
                                                 var cli = $("#clientes").val();
                                                 var tpo = $("#tipos").val();
                                                 var str = $("#str").val();
                                                 var venc = $("#vtos").val();
                                                 var ant = $("#antmaxunidad").val();
                                                 $.post("/modelo/rest/rescli.php", {accion:"addrstpo", estr:str, clien:cli, tipo:tpo, tpors:type, vto:venc,anti: ant}, 
                                                        function(data) {
                                                                      var response = $.parseJSON(data);
                                                                      if (!response.status){
                                                                         alert(response.msge);
                                                                      }
                                                                      else{
                                                                           $( "#load" ).trigger( "click" );
                                                                      }

                                                                });
                                                 });
                    $("#rtpo img").click(function(){
                                                    if (confirm("Seguro eliminar la restriccion")){
                                                        var id = $(this).attr("id");
                                                        $.post("/modelo/rest/rescli.php", {accion:"delrstu", rst:id}, function(data){
                                                                                                                                     var response = $.parseJSON(data);
                                                                                                                                     if (!response.status){
                                                                                                                                         alert(response.msge);
                                                                                                                                     }
                                                                                                                                     else{
                                                                                                                                          $( "#load" ).trigger( "click" );
                                                                                                                                     }
                                                                                                                                 });
                                                    }
                                                    });
                                                    
                    $("#cocheshab, #conductoreshab").dataTable({
					                                    "sScrollY": "250px",
					                                    "bPaginate": false,
					                                    "bScrollCollapse": true,
					                                    "bJQueryUI": true,
					                                    "oLanguage": {
                                                                     "sLengthMenu": "Display _MENU_ records per page",
                                                                     "sZeroRecords": "Sin Registros para mostrar",
                                                                     "sInfo": "",
                                                                     "sInfoEmpty": "Showing 0 to 0 of 0 records",
                                                                     "sInfoFiltered": "(filtered from _MAX_ total records)"}
				                                       });
                    $(".stch").click(function(){
                                                var coche = $(this).attr("id").split("-");
                                                var perm = 0;
                                                if ($(this).is(":checked"))
                                                   perm = 1;
                                                $.post("/modelo/rest/rescli.php", {accion:"incche", sino:perm, che:coche[1], cli:$("#clientes").val(), str:$("#str").val()}, function(data){});
                                                
                    });

                    $(".stchlic").click(function(){
                                                var emple = $(this).attr("id").split("-");
                                                var perm = 0;
                                                if ($(this).is(":checked"))
                                                   perm = 1;
                                                $.post("/modelo/rest/rescli.php", {accion:"incccond", sino:perm, emp:emple[1], cli:$("#clientes").val(), str:$("#str").val()}, function(data){});

                    });
                    
                    $("#sveres").button().click(function(){
                                                           var data = $("#rest_gral").serialize();
                                                           data=data+"&str="+$("#str").val()+"&cli="+$("#clientes").val()+"&accion=addrstgral";
                                                           $.post("/modelo/rest/rescli.php", data, function(data){
                                                                                                                  var response = $.parseJSON(data);
                                                                                                                  if (response.status){
                                                                                                                     alert("Restriccion generada con exito!!");
                                                                                                                  }
                                                                                                                  });
                                                           });
                    $("#addrcond img").click(function(){
                                                        var cli = $("#clientes").val();
                                                        var tpo = $("#tipolic").val();
                                                        $.post("/modelo/rest/rescli.php", {accion:"adrslic", cliente:cli, tipo:tpo}, function(data){
                                                                                                                                                    var response = $.parseJSON(data);
                                                                                                                                                    if (!response.status){
                                                                                                                                                       alert(response.msge);
                                                                                                                                                    }
                                                                                                                                                    else{
                                                                                                                                                         $( "#load" ).trigger( "click" );
                                                                                                                                                    }
                                                                                                                                                    });
                                                        });
                    $("#rtlic img").click(function(){
                                                    if (confirm("Seguro eliminar la restriccion")){
                                                        var id = $(this).attr("id");
                                                        $.post("/modelo/rest/rescli.php", {accion:"delrslic", rst:id}, function(data){
                                                                                                                                     var response = $.parseJSON(data);
                                                                                                                                     if (!response.status){
                                                                                                                                         alert(response.msge);
                                                                                                                                     }
                                                                                                                                     else{
                                                                                                                                          $( "#load" ).trigger( "click" );
                                                                                                                                     }
                                                                                                                                 });
                                                    }
                                                    });
                    $("#aplicar").button().click(function(){
                                                  var sino = 0;
                                                  if ($("#allInt").is(":checked")){
                                                     sino = 1;
                                                  }
                                                  $("#cocheshab input:checkbox").prop( "checked", $("#allInt").is(":checked"));
                                                  var coches = new Array();
                                                  $("#cocheshab input:checkbox").each(function(){
                                                                                                 coches.push($(this).attr("data_id"));
                                                                                                 });
                                                  $("#aplicar").hide();
                                                  $("#proc").show();
                                                  $.post("/modelo/rest/rescli.php", {accion:"allint", cch: coches.join(","), sn:sino, cli:$("#clientes").val(), str:$("#str").val()}, function(data){

                                                                                                                                                                                                     $("#aplicar").show();
                                                                                                                                                                                                     $("#proc").hide();

                                                                                                                                                                                                     });

                                                  });
                    $("#sverescnd").button().click(function(){

                                                              var sino = $("input:radio[name=lstacnd]:checked").val();
                                                              $.post("/modelo/rest/rescli.php", {accion:"sverstcnd", sn:sino, cli:$("#clientes").val(), str:$("#str").val()}, function(data){
                                                                                                                                                                                             var response = $.parseJSON(data);
                                                                                                                                                                                             alert(response.msge);
                                                                                                                                                                                             });
                                                              });
            </script>
            <style>
                         .tabla th{

                                font-size: 72.5%;
                                }
                         .cursor{cursor:pointer; cursor: hand}
                         
                         .pad th{

                                font-size: 82.5%;
                                }
                         .pad tr{
                                padding:13px;
                                font-size: 80.5%;
                                }
                         .pad{
                                padding:10px;
                                font-size: 85%;
                                }
                         .pad tbody tr:hover {

                                        background-color: #FF8080; }
                                        

                         .example tbody tr.even:hover,
                         .example tbody tr.even td.highlighted {background-color: #ECFFB3;}
                         .example tbody tr.odd:hover,
                         .example tbody tr.odd td.highlighted {background-color: #E6FF99;}
                         .example tr.even:hover {background-color: #ECFFB3;}
                         .example tr.even:hover td.sorting_1 {background-color: #DDFF75;}
                         .example tr.even:hover td.sorting_2 {background-color: #E7FF9E;}
                         .example tr.even:hover td.sorting_3 {background-color: #E2FF89;}
                         .example tr.odd:hover {background-color: #E6FF99;}
                         .example tr.odd:hover td.sorting_1 {background-color: #D6FF5C;}
                         .example tr.odd:hover td.sorting_2 {background-color: #E0FF84;}
                         .example tr.odd:hover td.sorting_3 {background-color: #DBFF70;}
            </style>';
}

function getConductoresHabilitados($cliente, $str){
    $tabla='<table id="conductoreshab" class="example">
                    <thead>
                           <tr>
                               <th>Apellido, Nombre</th>
                               <th>Empleador</th>
                               <th>Si/No</th>
                           </tr>
                    </thead>
                    <tbody>';
    $sql="select emp.id_empleado, upper(concat(apellido,', ', nombre)) as nombre, upper(razon_social) as empleado,
                 (select permitido from conductoresxcliente r where r.id_empleado = emp.id_empleado and id_cliente = $cliente) as perm
                 from empleados emp
                 inner join empleadores e on e.id = emp.id_empleador
                 where emp.activo and id_cargo = 1 and emp.id_estructura = $str
                 order by apellido";
        //  die($sql);
    $conn = conexcion();
    $result = mysql_query($sql, $conn);
    while ($data = mysql_fetch_array($result)){
          if ($data[perm] == 1)
             $perm = "checked";
          else
              $perm="";
          $tabla.="<tr>
                       <td>$data[1]</td>
                       <td>$data[2]</td>
                       <td align='center'><input type='checkbox' $perm id='cond-$data[id_empleado]' class='stchlic'></td>
                   </tr>
                   ";
    }
    $tabla.="</tbody></table>";
    return $tabla;
}


function getCochesHabilitados($cliente, $str){
    $tabla='Marcar/Desmarcar Todos<input type="checkbox" id="allInt"> <input type="button" value="Aplicar" id="aplicar"><span id="proc"><font color="#FF0000">Procesando...</font></span>
            <table id="cocheshab" class="example">
                    <thead>
                           <tr>
                               <th>Interno</th>
                               <th>Año</th>
                               <th>Propietario</th>
                               <th>Si/No</th>
                           </tr>
                    </thead>
                    <tbody>';
    $sql="select u.id, u.interno, razon_social, (select permitido from restcochesclientes r where u.id = r.id_coche and id_cliente = $cliente) as perm ,anio
          from unidades u
          inner join empleadores e on e.id = u.id_propietario and e.id_estructura =  u.id_estructura_propietario
          where u.activo
          order by interno";
        //  die($sql);
    $conn = conexcion();
    $result = mysql_query($sql, $conn);
    while ($data = mysql_fetch_array($result)){
          if ($data[perm] == 1)
             $perm = "checked";
          else
              $perm="";
          $tabla.="<tr>
                       <td>$data[1]</td>
                       <td>$data[4]</td>
                       <td>$data[2]</td>
                       <td align='center'><input type='checkbox' $perm id='coche-$data[id]' class='stch' data_id='$data[id]'></td>
                   </tr>
                   ";
    }
    $tabla.="</tbody></table>";
    return $tabla;
}

function getTablaRestTipoLicencia($conn, $cli, $str){
    $sql = "SELECT r.id, upper(licencia)
            FROM restxtipolicenciacliente r
            inner join licencias l on l.id = id_licencia and l.id_estructura = id_estructura_licencia
            where id_cliente = $cli and id_estructura_cliente = $str and id_estructura = $str
            order by licencia";

    $filtro = "SELECT r.filtralista FROM restgralconductorcliente r WHERE r.id_cliente = $cli and r.id_estructura_cliente = $str";

    $resfiltro = mysql_query($filtro, $conn);
    if ($row = mysql_fetch_array($resfiltro)){
       if ($row[0])
          $chksi="checked";
       else
           $chkno="checked";
    }
    $result = mysql_query($sql, $conn);
    $tabla='<br>
             <table id="rstgralcnd" width="40%"  class="ui-widget ui-widget-content tabla" align="center">
                    <tr>
                        <td>Considera lista de conductores</td>
                        <td align="right">SI<input type="radio" name="lstacnd" '.$chksi.' value="1">NO<input type="radio" name="lstacnd" '.$chkno.' value="0"></td>
                    </tr>
             </table>
             <table id="rtlic" width="40%"  class="ui-widget ui-widget-content tabla" align="center">
                    <thead>
                           <tr class="ui-widget-header">
                               <th colspan="3">Tipo Licencias Adminitidas Por El Cliente</th>
                           </tr>
                           <tr class="ui-widget-header">
                               <th>Nro</th>
                               <th>Tipo Licencia</th>
                               <th>Quitar</th>
                           </tr>';
    $i=1;
    while ($data = mysql_fetch_array($result)) {
          $tabla.="<tr>
                       <td>".$i++."</td>
                       <td>$data[1]</td>
                       <td align='center' id='tr$data[0]'><img src='../../vista/menos.png' width='15' height='15' border='0' id='$data[0]' class='cursor'></td>
                   </tr>";

    }
    $filtro_tipos = "id not in (SELECT id_licencia FROM restxtipolicenciacliente where id_cliente = $cli and id_estructura_cliente = $str) and id_estructura = $str";
    $tabla.='</table>
             <br>
             <table id="addrcond" width="40%"  class="ui-widget ui-widget-content tabla" align="center">
                    <thead>
                           <tr class="ui-widget-header">
                               <th colspan="3">Agregar Nuevo Tipo Vencimiento</th>
                           </tr>
                    </thead>
                    <tr>
                        <td>Tipo Licencia</td>
                        <td><select id="tipolic">'.armarSelect('licencias', 'licencia', 'id', 'licencia', $filtro_tipos, 1).'</select></td>
                        <td align="center"><img src="../../vista/add.png" width="15" height="15" border="0" id="t"></td>
                    </tr>
                    <tr><td colspan="3"></td></tr>
             </table>
             <br>
             <table id="addrtpo" width="40%"  class="ui-widget ui-widget-content tabla" align="center" border="0">
                    <tr>
                        <td colspan="3" align="right"><input type="button" value="Guadar Restricciones" id="sverescnd"></td>
                    </tr>
             </table>';
    return $tabla;
}
  
?>

