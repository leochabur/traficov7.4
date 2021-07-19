<?php
     set_time_limit(0);
     error_reporting(0);
     session_start();


     include_once('../main.php');

     include_once('../paneles/viewpanel.php');
     define(RAIZ, '');


     encabezado('Menu Principal - Sistema de Administracion - Campana');
     
    $todas="";
	$notodas="";

	if (isset($_POST['mostrar'])){
       $_SESSION['todas'] = $_POST['mostrar'];
    }

     
     if (isset($_POST['fecha'])){
        $fec = $_POST['fecha'];
        $fecha = explode("/", $fec);
        $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
        $fechaVis = $_POST['fecha'];
     }
     elseif(isset($_GET['fecha'])){
        $fec = $_GET['fecha'];
        $fecha = explode("/", $fec);
        $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
        $fechaVis = $_GET['fecha'];
     }
     else{
         $fecha = date("Y-m-d");
         $fechaVis = date("d/m/Y");
     }
     
     $orden="hcitacion";
     if (isset($_POST['order']) && ($_POST['order'] != '')){
        $orden = $_POST['order'];
     }

     $tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));//date("Y")."-".date("m")."-".(date("d")+1);
     $maniana= date("Y-m-d", $tomorrow);
     //$maniana= date("Y")."-".date("m")."-".(date("d")+1);

?>
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
 <link href="<?php echo RAIZ;?>/vista/css/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.tablesorter.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.contextMenu.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script>
    var element;
    var campo;
    var cmpstr;
	$(function() {
                  $("#back").button({icons: {primary: "ui-icon-circle-triangle-w"},text: false}).click(function(){
                                                                                                                  var date1 = $('#fecha').datepicker('getDate');
                                                                                                                  var date = new Date( Date.parse( date1 ) );
                                                                                                                  date.setDate( date.getDate() - 1 );
                                                                                                                  var newDate = date.toDateString();
                                                                                                                  newDate = new Date( Date.parse( newDate ) );
                                                                                                                  $( '#fecha' ).datepicker('setDate', newDate );
                                                                                                                  });
                  $("#next").button({icons: {primary: "ui-icon-circle-triangle-e"},text: false}).click(function(){
                                                                                                                  var date1 = $('#fecha').datepicker('getDate');
                                                                                                                  var date = new Date( Date.parse( date1 ) );
                                                                                                                  date.setDate( date.getDate() + 1 );
                                                                                                                  var newDate = date.toDateString();
                                                                                                                  newDate = new Date( Date.parse( newDate ) );
                                                                                                                  $( '#fecha' ).datepicker('setDate', newDate );
                                                                                                                  });

                 $("#table").tablesorter({widgets: ['zebra']});
                 $(".fin").css("background-color", "#EEF834");
                 $(".check").css("background-color", "#C0FFFF");
                 $(".susp").css("background-color", "#D0D0D0");
                 $(".scas").css("background-color", "#FFC0C0");
                 $("#table tbody tr div").mouseover(function() {
                                                                     $(this).addClass("tr_hover");
                                                                    });
                 $("#table tbody tr div").mouseout(function() {
                                                                     $(this).removeClass("tr_hover");
                                                                     });


                 $.editable.addInputType('masked', {
                                                     element : function(settings, original) {
                                                                                            var input = $('<input />').mask(settings.mask);
                                                                                            $(this).append(input);
                                                                                            return(input);
                                                                                            }
                                                   });
                 $('#cargar, #chepmtycli').button();
                 $.mask.definitions['~']='[012]';
                 $.mask.definitions['%']='[012345]';
                 $(".hora").mask("~9:%9",{completed:function(){}});
                 $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                 $('#send-order').datepicker({dateFormat:'dd/mm/yy', showOn: 'both'});
                 $('input:submit').button();
                 $('#sendorderatdate').button();
                 $('.hora').editable('/modelo/procesa/upd_ordenes.php', {type:"masked", mask: "~9:%9"});
                 $('.txt').editable('/modelo/procesa/upd_ordenes.php',  {type      : 'textarea',
                                                                                      cancel    : 'Cancelar',
                                                                                      submit    : 'Guardar'});
                 $("#dialog-ch-driver").dialog({autoOpen: false, width: 650, modal: true});
                 $("#dialog-ch-interno").dialog({autoOpen: false, width: 450, modal: true});
                 $("#send-another-date").dialog({modal: true, autoOpen: false, width: 450});
                 $("#ch-empty-cli").dialog({modal: true,
                                            autoOpen: false,
                                            width: 450,
                                            open: function(event, ui) {
                                                                      $.post('/modelo/procesa/mod_ordenes.php', {accion: "ldo", orden: element}, function(data){
                                                                                                                                                                var arg = data.split('-');
                                                                                                                                                                if (arg[9] == 0){
                                                                                                                                                                   alert('La orden no corresponde a un servicio vacio');
                                                                                                                                                                   $( "#ch-empty-cli").dialog( "close" );
                                                                                                                                                                }
                                                                                                                                                                });
                                                                      }
                                            });

                 $( "#dialog-form" ).dialog({
                                    autoOpen: false,
                                    height: 450,
                                    width: 750,
                                    modal: true,
                                    open: function(event, ui) {
                                                                 $.post('/modelo/procesa/mod_ordenes.php', {accion: "ldo", orden: element}, function(data) {
                                                                                                                                                                         var arg = data.split('-');

                                                                                                                                                                         if (arg[9] == 0){
                                                                                                                                                                            $("#origen option[value="+arg[6]+"]").attr("selected",true);
                                                                                                                                                                            $("#destino option[value="+arg[5]+"]").attr("selected",true);
                                                                                                                                                                            $("#corresponde option[value="+arg[7]+"]").attr("selected",true);
                                                                                                                                                                            $("#conductor option[value="+arg[8]+"]").attr("selected",true);
                                                                                                                                                                            $("#hcitacion").val(arg[0]);
                                                                                                                                                                            $("#hsalida").val(arg[1]);
                                                                                                                                                                            $("#hllegada").val(arg[2]);
                                                                                                                                                                            $("#hfinserv").val(arg[3]);
                                                                                                                                                                            $("#km").val(arg[10]);
                                                                                                                                                                            $("#nombre").val('Vacio - '+$("#nombre-"+element).html());

                                                                                                                                                                            $('#fservicio').val($('#fecha').val());
                                                                                                                                                                         }
                                                                                                                                                                         else{
                                                                                                                                                                              $( "#dialog-form").dialog( "close" );
                                                                                                                                                                              alert('La orden corresponde a un servicio vacio');

                                                                                                                                                                         }

                                                                                                            });
                                                               }
                                    });


                 $('#sendorderatdate').click(function(){
                                                        var now = $.datepicker.parseDate('dd/mm/yy', $('#fecha').val());
                                                        var other  = $.datepicker.parseDate('dd/mm/yy', $('#send-order').val());
                                                        if (other < now){
                                                           alert('Esta intentando enviar la orden a una fecha pasada');
                                                        }
                                                        else{
                                                             other = other.getFullYear()+"-"+(other.getMonth()+1)+"-"+other.getDate();
                                                             $.post("/modelo/procesa/procesar_ordenes.php", {accion:'sendodate', order: element, fecha: other}, function(data) {$( "#send-another-date").dialog( "close" ); $("#load").submit();});
                                                        }
                                                        });
                                                        
                 $('#chepmtycli').click(function(){
                                                   var cliente = $("#empty-cli option:selected").val();
                                                   $.post("/modelo/procesa/procesar_ordenes.php", {accion:'chemcli', order: element, cli: cliente}, function(data) {
                                                                                                                                                                    $("#"+campo+"-"+element).html(data);
                                                                                                                                                                    $( "#ch-empty-cli").dialog( "close" );
                                                                                                                                                                    });
                                                   });

                 $(".menu").contextMenu({
                                          menu: 'myMenu'
                                         },
                                         function(action, el, pos) {
                                                                    var elem = ($(el).attr('id')).split('-');
                                                                    if (action == 'cerrar'){
                                                                       cerrarOrden(elem[1]);
                                                                    }
                                                                    if (action == 'susp'){
                                                                       suspenderOrden(elem[1]);
                                                                    }
                                                                    if (action == 'check'){
                                                                       checkOrden(elem[1]);
                                                                    }
                                                                    if (action == 'vacio'){
                                                                       element = elem[1];
                                                                       abrirDialogo();
                                                                    }
                                                                    if (action == 'dupli'){

                                                                       duplicarOrden(elem[1]);
                                                                    }
                                                                    if (action == 'chdriver1'){
                                                                        element = elem[1];
                                                                        campo = 'id_chofer_1';
                                                                        cmpstr = 'iech1';
                                                                        chdriver(elem[1]);
                                                                    }
                                                                    if (action == 'chdriver2'){
                                                                        element = elem[1];
                                                                        campo = 'id_chofer_2';
                                                                        cmpstr = 'iech2';
                                                                        chdriver(elem[1]);
                                                                    }
                                                                    if (action == 'sendorder'){
                                                                        element = elem[1];
                                                                        $("#send-another-date").dialog( "option", "title", "Enviar la orden "+element+" a otra fecha");
                                                                        $("#send-another-date").dialog("open");
                                                                    }
                                                                    if (action == 'delorder'){
                                                                        element = elem[1];
                                                                        alert('Esta seguro de eliminar la orden '+element);
                                                                        $.post("/modelo/procesa/procesar_ordenes.php", {accion: 'deor', order: element}, function(data) {
                                                                                                                                                                                      $("#load").submit();
                                                                                                                                                                                     });
                                                                    }
                                                                    if (action == 'chinterno'){
                                                                        element = elem[1];
                                                                        campo = 'id_micro';
                                                                        chinterno(elem[1]);
                                                                    }
                                                                    
                                                                    if (action == 'chclivac'){
                                                                        element = elem[1];
                                                                        campo = 'nombre';
                                                                        chemptycli(elem[1]);
                                                                    }
                                                                    if (action == 'openord'){
                                                                       abirOrdenCompleta(elem[1]);
                                                                    }
                                                                    }
                                         );
                 $("#upcontact").validate({
                                           submitHandler: function(){
                                                                     var data = $("#upcontact").serialize();
                                                                     $.post("/modelo/procesa/procesar_ordenes.php", data, function(dat) {
                                                                                                                                        $('#mjw').html(dat);
                                                                                                                                        $( "#dialog-form").dialog( "close" );
                                                                                                                                        $("#load").submit();
                                                                                                                                        });
                                                                     }
                                           });
                 $("#chcond").button().click(function(){
                                                      var data = $("#conductores option:selected").val();
                                                      $.post("/modelo/procesa/mod_ordenes.php", {accion: 'update',
                                                                                                                   cmp: campo,
                                                                                                                   valor: data,
                                                                                                                   clave: element,
                                                                                                                   cmpstruct: cmpstr},
                                                                                                                   function(data){
                                                                                                                                  if (data){
                                                                                                                                     $("#"+campo+"-"+element).html($("#conductores option:selected").text());
                                                                                                                                     $("#dialog-ch-driver").dialog( "close" );
                                                                                                                                  }
                                                                                                                                 });
                                               });
                 $("#chint").button().click(function(){
                                                      var data = $("#internos option:selected").val();
                                                      $.post("/modelo/procesa/mod_ordenes.php", {accion: 'update',
                                                                                                                   cmp: campo,
                                                                                                                   valor: data,
                                                                                                                   clave: element,
                                                                                                                   cmpstruct:'iem'},
                                                                                                                   function(data){
                                                                                                                                  if (data){
                                                                                                                                     $("#"+campo+"-"+element).html($("#internos option:selected").text());
                                                                                                                                     $("#dialog-ch-interno").dialog( "close" );
                                                                                                                                  }
                                                                                                                                 });
                                               });
                 $(document).bind("keydown", disable_fresh);
                 $( "body" ).mousemove(function( event ){
                                                         var cy = event.pageY;
                                                         $('#posy').val(cy);
                                                        });
                 <?php
                      if (isset($_POST['posy'])){
                         print "$('body').animate({
                                                   scrollTop: '$_POST[posy]px'
                                                   },
                                                   0);";
                                    };
                 ?>
                 $('#table thead tr th').click(function(e){
                                                           $('#order').val($(this).attr('id'));
                                                           });
});

  function disable_fresh(e)
{
  if (e.which == 116){ e.preventDefault();
  $("#load").submit();
  }
};
	
	function cerrarOrden(id){
          if(confirm('Seguro cerrar la orden?'))
                             $.post('/modelo/procesa/mod_ordenes.php', {accion: "cls", orden: id}, function(data) {
                                                                                                                   $('[id$='+id+']').css("background-color", "#EEF834");
                                                                                                                  });
	}
	
	function suspenderOrden(id){
          if(confirm('Seguro suspender la orden?'))
                             $.post('/modelo/procesa/mod_ordenes.php', {accion: "susp", orden: id}, function(data) {
                                                                                                                   $('[id$='+id+']').css("background-color", "#D0D0D0");
                                                                                                                  });
	}

	function checkOrden(id){
          if(confirm('Seguro chequear la orden?'))
                             $.post('/modelo/procesa/mod_ordenes.php', {accion: "check", orden: id}, function(data) {
                                                                                                                   $('[id$='+id+']').css("background-color", "#C0FFFF");
                                                                                                                  });
	}
	
	function duplicarOrden(id){
          if(confirm('Seguro duplicar la orden?'))
                             $.post('/modelo/procesa/mod_ordenes.php', {accion: "dupl", orden: id}, function(data){

                                                                                                                    $("#load").submit();
                                                                                                                   });
	}

	function abrirDialogo(){
          $( "#dialog-form").dialog( "open" );
	}
	
	function chdriver(id){
          $.post('cargar_combo_conductores.php', {orden: id}, function(data) {
                                                                               $("#conductores").html(data);
                                                                             });
          $( "#dialog-ch-driver" ).dialog( "option", "title", "Cambiar conductor. Orden <? echo htmlentities('N°');?> "+element );
          $("#dialog-ch-driver").dialog("open");
	}
	
	function chinterno(id){
          $.post('cargar_combo_internos.php', {orden: id}, function(data) {
                                                                               $("#internos").html(data);
                                                                             });
          $( "#dialog-ch-interno" ).dialog( "option", "title", "Cambiar Interno. Orden <? echo htmlentities('N°');?> "+element );
          $("#dialog-ch-interno").dialog("open");
	}
	
	function chemptycli(id){
          $("#ch-empty-cli").dialog( "option", "title", "Cambiar Cliente Vacio de la Orden <? echo htmlentities('N°');?> "+element );
          $("#ch-empty-cli").dialog("open");
	}
	
	function mostrarDiag(c) {
             var dialog = $('<div style="display:none" class="loading" align="center"></div>').appendTo('body');
             dialog.dialog({
                            close: function(event, ui) {dialog.remove();},
                            title: 'Diagrama de Trabajo',
                            width:918,
                            height:450
                            });
             dialog.load('/modelo/informes/trafico/diagdia.php',{desde:'<?echo date("Y-m-d");?>', hasta: '<?echo $maniana;?>', cond:c},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});
     }
     
     function abirOrdenCompleta (id_orden){
              var dialog = $('<div style="display:none" id="dialog" class="loading" align="center"></div>').appendTo('body');
              dialog.dialog({
                             close: function(event, ui) {dialog.remove();},
                             title: 'Modificar orden',
                             width:850,
                             height:600,
                             modal:true,
                             show: {
                                   effect: 'blind',
                                   duration: 1000
                             },
                             hide: {
                                   effect: 'blind',
                                   duration: 1000
                             }
              });
              dialog.load('/vista/ordenes/modord.php',{orden:id_orden},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});
     }
	</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
.small.button, .small.button:visited {
font-size: 11px ;
}

#table { font-size: 72.5%; }
#upcontact div{padding: 2px;}
#table thead tr th{padding: 7px;}
#table tbody tr td{padding: 0px;}
#cargar {font-size: 72.5%;}
.tr_hover {background-color: #ffccee}

option.0{background-color: #;}
option.1{background-color: ;}
#upcontact .error{
	font-size:0.8em;
	color:#ff0000;
}
</style>
<BODY>
<?php
     menu();
     $con = conexcion();
     $res_cc = mysql_query("SELECT cant_cond FROM estructuras WHERE id = $_SESSION[structure]", $con);
     if ($data_cc = mysql_fetch_array($res_cc)){
         $cantTripulacion = $data_cc[0];
     }

?>
    <br><br>
    <div id="result"></div>
    <fieldset class="ui-widget ui-widget-content ui-corner-all">

         <legend class="ui-widget ui-widget-header ui-corner-all">Ordenes de Trabajo</legend>
         <hr align="tr">
         <div>
         <form id="load" method="post">
              <div align="center"><button id="back">Un dia atras.</button><input id="fecha" name="fecha" value="<?php echo $fechaVis;?>" type="text" size="20"><button id="next">Un dia adelante.</button><input type="submit" id="cargar" name="cargar" class="button" value="Cargar Ordenes"></div>
              <table>
                   <tr>
                    <td>Mostrar ordenes</td>
                    <td><?print "Mostrar Todas las ordenes<input type=\"Radio\" name=\"mostrar\" value=\"0,1\">";?></td>
                    <td></td>
                    <td><?print "Mostrar Ordenes sin finalizar<input type=\"Radio\" name=\"mostrar\" value=\"0\">";?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    </tr>
              </table>
              <input type="hidden" id="posx" name="posx">
              <input type="hidden" id="posy" name="posy" <?php if (isset($_POST['posy'])) print "value='$_POST[posy]'";?>>
              <input type="hidden" id="order" name="order" <?php if (isset($_POST['order']) && ($_POST['order'] != '')) print "value='$_POST[order]'";?>>
              
         </form>
         </div>
         <div id='mjw'></div>
         <hr align="tr">
         <table width=100%>
                <tr>
                    <td class="fin">&Oacute;rdenes finalizadas</td>
                    <td>&nbsp;&nbsp;</td>
                    <td class="check">&Oacute;rdenes checkeadas</td>
                    <td>&nbsp;&nbsp;</td>
                    <td class="susp">&Oacute;rdenes suspendidas</td>
                    <td>&nbsp;&nbsp;</td>
                    <td class="scas">&Oacute;rdenes sin conductores</td>
                    </tr>
         </table>
         <div id="tablaordenes">
              <table id='table' align="center" class="tablesorter" border="0" width="100%">
                     <thead>
            	            <tr class="">
                                <th id="hcitacion">H. Citacion</th>
                                <th id="hsalida">H. Salida</th>
                                <th id="nombre">Servicio</th>
                                <th id="interno">Interno</th>
                                <th id="chofer1">Conductor 1</th>
                                <th id="chofer2">Conductor 2</th>
                                <?php
                                     if ($cantTripulacion > 2){
                                        print "<th>Conductor 3</th>";
                                     }
                                ?>
                                <th id="razon_social">Cliente</th>
                                <th id="comentario">Observaciones</th>
                                <?php
                                     if ($_SESSION['userid'] == 25)
                                        print '<th id="hfinservicio">H. Llegada</th>';
                                ?>
                                <th id="hfinservicio">H. Fin</th>
                                <th>C.1</th>
                                <th>C.2</th>
                                <?php if ($cantTripulacion > 2){
                                        print "<th>C.3</th>";
                                     }
                                ?>
                            </tr>
                     </thead>
                     <tbody>
                            <?php
                                 if ($cantTripulacion > 2){
                                    $tripulacion = "LEFT JOIN tripulacionXOrdenes txo ON txo.id_orden = o.id AND txo.id_estructura_orden = o.id_estructura
                                                    LEFT JOIN empleados ch3 ON (ch3.id_empleado = txo.id_empleado)";
                                    $camposTripulacion = ", concat(ch3.apellido, ', ',ch3.nombre) as chofer3, if (txo.id is null, 0, txo.id) as id_trip_x_orden, ch3.id_empleado as id_emple_3";
                                    $camposNovedades = ", '' as chofer3, 0 as txxx, 0 as id_emple_3";
                                 }

                                 $sql = "SELECT o.id, finalizada, date_format(hcitacion, '%H:%i') as hcitacion, date_format(hsalida, '%H:%i') as hsalida, date_format(hfinservicio, '%H:%i') as hfinserv, o.nombre, if(em1.id = 1,concat(ch1.apellido, ', ',ch1.nombre), concat('(',em1.razon_social,') ', ch1.apellido, ', ',ch1.nombre)) as chofer1, upper(c.razon_social) as razon_social, concat(ch2.apellido, ', ',ch2.nombre) as chofer2, comentario, interno, ch1.id_empleado, ch2.id_empleado as id_empleado2, suspendida, checkeada, emp.color, date_format(hllegada, '%H:%i') as hllegada $camposTripulacion
                                                       FROM ordenes o
                                                       LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
                                                       LEFT JOIN empleadores em1 ON (em1.id = ch1.id_empleador)
                                                       LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
                                                       $tripulacion
                                                       LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                                                       LEFT JOIN unidades m ON (m.id = o.id_micro)
                                                       LEFT JOIN empleadores emp ON (emp.id = m.id_propietario) and (emp.id_estructura = m.id_estructura_propietario)
                                                       WHERE (fservicio = '$fecha') and (not borrada) and (not suspendida) and (o.id_estructura = $_SESSION[structure]) and (finalizada in ($_SESSION[todas]))
                                         UNION
                                         SELECT n.id as id, 0 as finalizada, '00:00' as hcitacion, '00:00' as hsalida, '00:00' as hfinserv, cn.nov_text as nombre, concat(e.apellido, ', ',e.nombre) as chofer1, '' as razon_social, '' as chofer2, '' as comentario, '' as interno, e.id_empleado as id_empleado, 0 as id_empleado2, 0, 0, '' as color, '00:00' as hllegada $camposNovedades
                                         FROM novedades n
                                         inner join cod_novedades cn on cn.id = n.id_novedad
                                         inner join empleados e on e.id_empleado = n.id_empleado
                                         where ('$fecha' between desde and hasta) and (e.id_estructura = $_SESSION[structure]) and (e.id_cargo = 1) and (n.activa)
                                         order by $orden";
                             //    die($sql);
                                 $query = mysql_query($sql, $con) or die(mysql_error($con));
				                 while($row = mysql_fetch_array($query)){
                                            $obs=""; $hora=""; $cond=""; $tdclass="fin"; $divclass="fin"; //inicializamos todas las clases correspondientes a los estilos a aplicar
                                            $tdclass="";
                                            if ($row['finalizada']){
                                               $tdclass="fin";
                                            }
                                            elseif ($row['checkeada']){
                                                   $tdclass="check";
                                            }
                                            elseif ($row['suspendida']){
                                               $tdclass="susp";
                                               $divclass="";
                                               $hora="";
                                               $cond="";
                                            }
                                            elseif ((!$row['id_empleado']) && (!$row['id_empleado2'])){
                                                   $tdclass="scas";
                                            }
                                            if (!$row['finalizada']){
                                                //para no aplicar el color como finalizada
                                               $divclass="menu"; //como no esta finalizada puede mostrar el menu
                                               $hora="hora"; //para poder modificar los horarios
                                               $cond="cond"; //para poder modificar los conductores
                                               $obs="txt";
                                            }

                                            $img='';
                                            if ($row['chofer2'])
                                               $img = "<img title='Ver diagrama de ".htmlentities($row['chofer2'])."' src='../user.png' border='0' onclick='mostrarDiag($row[id_empleado2]);' style='cursor:pointer'>";
                                            $dri='';
                                            if ($row['chofer1'])
                                               $dri="<img title='Ver diagrama de ".htmlentities($row['chofer1'])."' src='../user.png' border='0' onclick='mostrarDiag($row[id_empleado]);' style='cursor:pointer'>";
                                            if ($row['chofer3'])
                                               $dri3="<img title='Ver diagrama de ".htmlentities($row['chofer3'])."' src='../user.png' border='0' onclick='mostrarDiag($row[id_emple_3]);' style='cursor:pointer'>";


                                            $id = $row['id'];
					                        $llegada='';
				                            if ($_SESSION['userid'] == 25)
                                               $hllegada = "<td class=\"$tdclass\"><div class=\"$hora\" id=\"hllegada-$id\">$row[hllegada]</div></td>";
					                        print "<tr>
                                                       <td class=\"$tdclass\"><div class=\"$hora\" id=\"hcitacion-$id\">$row[hcitacion]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$hora\" id=\"hsalida-$id\">$row[hsalida]</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$divclass\" id=\"nombre-$id\">".utf8_decode($row['nombre'])."</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$divclass\" id=\"id_micro-$id\"><a href='/vista/segvial/posint.php?int=$row[interno]' target='_blank' onClick=\"window.open(this.href, this.target, 'width=900,height=650'); return false;\"><div style='color:#$row[color];'>$row[interno]</div></a></div></td>
                                                       <td class=\"$tdclass\"><div class=\"$divclass\" id=\"id_chofer_1-$id\">".htmlentities($row['chofer1'])."</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$divclass\" id=\"id_chofer_2-$id\">".htmlentities($row['chofer2'])."</div></td>";
                                            if ($cantTripulacion > 2){
                                               print "<td class=\"$tdclass\"><div class=\"$divclass\" id=\"id_chofer_2-$id\">".htmlentities($row['chofer3'])."</div></td>";
                                            }
                                            print "<td class=\"$tdclass\"><div class=\"$divclass\" id=\"razon_social-$id\">".htmlentities($row['razon_social'])."</div></td>
                                                       <td class=\"$tdclass\"><div class=\"$obs $divclass\" id=\"comentario-$id\">$row[comentario]</div></td>
                                                       $hllegada
                                                       <td class=\"$tdclass\"><div class=\"$hora\" id=\"hfinservicio-$id\">$row[hfinserv]</div></td>
                                                       <td class=\"$tdclass\"><div align='center'>$dri</div></td>
                                                       <td class=\"$tdclass\"><div align='center'>$img</div></td>
                                                       <td class=\"$tdclass\"><div align='center'>$dri3</div></td>
                                                   </tr>";
                                 }
                                 mysql_free_result($query);
                                 mysql_close($con);
                            ?>
                     </tbody>
              </table>
         </div>
	</fieldset>
		<ul id="myMenu" class="contextMenu">
			<li class="edit"><a href="#cerrar" id="edit">Cerrar Orden</a></li>
			<li class="edit"><a href="#check" id="edit">Chequear Orden</a></li>
			<li class="edit"><a href="#susp" id="edit">Suspender Orden</a></li>
			<li class="cut separator"><a href="#vacio">Crear Vacio</a></li>
			<li class="copy"><a href="#delorder">Eliminar Orden</a></li>
			<li class="paste"><a href="#sendorder">Enviar a otra fecha...</a></li>
  <?
            if ($_SESSION['permisos'][2] > 1){
               print "<li class='copy'><a href='#dupli'>Duplicar Orden</a></li>";
            }
  ?>
			<li class="delete"><a href="#chdriver1">Cambiar Conductor 1</a>
			<li class="delete"><a href="#chdriver2">Cambiar Conductor 2</a>
			<li class="delete"><a href="#chinterno">Cambiar Interno</a>
			<li class="delete"><a href="#chclivac">Cambiar Cliente Vacio</a>
			<li class="delete"><a href="#openord">Abrir Orden</a>
            </li>
		</ul>
		
		
 <div id="dialog-form" title="Diagramar Servicio Vacio">
              <form id="upcontact">
	                <fieldset>
                              <div class="div">
		                      <label for="hcitacion">Fecha servicio</label>
                              <input id="fservicio" name="fservicio" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/>
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="hcitacion">Nombre Servicio</label>
                              <input id="nombre" name="nombre" size="35" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/>
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="h_cita">Cliente</label>
                              <input id="cliente" name="cliente" type="text" size="35" class="ui-widget ui-widget-content  ui-corner-all" value="Master Bus S.A." readonly="readonly">
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="turno">Correspondiente a:</label>
                              <select id="corresponde" name="corresponde" title="Please select something!" >
                                                <?php
                                                     armarSelect('clientes', 'razon_social', 'id', 'razon_social', "(id_estructura = $_SESSION[structure])");
                                                ?>
                              </select>
                              </div>
                              <div class="div">
		                      <label for="turno">Origen:</label>
                              <select id="origen" name="origen" title="Please select something!" >
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>
                              </select>
                              </div>
                              <div class="div">
		                      <label for="turno">Destino:</label>
                              <select id="destino" name="destino" title="Please select something!" >
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>
                              </select>
                              </div>
                              <div class="div">
		                      <label for="turno">Conductor:</label>
                              <select id="conductor" name="conductor" title="Please select something!" >
                                                <?php
                                                 armarSelectCondNov($_SESSION['structure'], $fechaVis);
                                                // armarSelectCond($_SESSION['structure']);
                                                ?>
                              </select>
                              </div>


                              <div class="div">
		                      <label for="hcitacion">Hora Citacion</label>
		                      <input type="text" name="hcitacion" id="hcitacion" maxlength="5" size="5" class="hora ui-widget-content ui-corner-all required" />
                              <span></span>
                              </div>
                              <div class="div">
                              <label for="hsalida">Hora Salida</label>
		                      <input type="text" name="hsalida" id="hsalida" maxlength="5" size="5" class="hora ui-widget-content ui-corner-all required" />
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="hllegada">Hora Llegada</label>
		                      <input type="text" name="hllegada" id="hllegada" maxlength="5" size="5" class="hora ui-widget-content ui-corner-all required" />
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="hfin">Hora Fin Servicio</label>
		                      <input type="text" name="hfinserv" id="hfinserv" maxlength="5" size="5" class="hora ui-widget-content ui-corner-all required"/>
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="hfin">Km</label>
		                      <input type="text" name="km" id="km" maxlength="5" size="5" class="ui-widget-content ui-corner-all {required:true}"/>
                              <span></span>
                              </div>
                    </fieldset>
           			<fieldset id="botonera" style="border:none; text-align: right;">
				              <input id="envioFormulario" class="boton" type="submit" value="Guardar Orden" name="envioFormulario">
                    </fieldset>
                    <input type="hidden" name="accion" id="accion" value="soes"/>
              </form>
         </div>
         
         <div id="dialog-ch-driver" title="Modificar conductor">
              <form id="upcontacts">
	                <fieldset>
                              <div class="div">
		                      <label for="turno">Conductor</label>
                              <select id="conductores" name="conductores" title="Please select something!" >
                                      <option>Selecciones uno</option>
                              </select>
                              </div>

                    </fieldset>
           			<fieldset id="botonera" style="border:none; text-align: right;">
				              <input id="chcond" class="boton" type="button" value="Guardar Cambios" name="envioFormulario">
                    </fieldset>
                    <input type="hidden" name="accion" id="accion" value="soes"/>
              </form>
         </div>
         
         <div id="dialog-ch-interno" title="Modificar Interno">
              <form id="upcontactss">
	                <fieldset>
                              <div class="div">
		                      <label for="turno">Internos</label>
                              <select id="internos" name="internos" title="Please select something!" >
                                      <option>Selecciones uno</option>
                              </select>
                              </div>

                    </fieldset>
           			<fieldset id="botonera" style="border:none; text-align: right;">
				              <input id="chint" class="boton" type="button" value="Guardar Cambios" name="chint">
                    </fieldset>
                    <input type="hidden" name="accionint" id="accionint" value="soes"/>
              </form>
         </div>
         
         <div id="send-another-date" title="Enviar orden a otra fecha">
              <form id="sendorder">
	                <fieldset>
                              <div class="div">
		                      <label for="turno">Nueva fecha</label>
		                      <input id="send-order" name="send-order" type="text" size="30">
                              </div>

                    </fieldset>
           			<fieldset id="botonera" style="border:none; text-align: right;">
				              <input id="sendorderatdate" class="boton" type="button" value="Guardar Cambios" name="envioFormulario">
                    </fieldset>
                    <input type="hidden" name="accion" id="accion" value="soes"/>
              </form>
         </div>
         <div id="ch-empty-cli" title="Cambiar Cliente Vacio">
              <form id="sendorder">
	                <fieldset>
                              <div class="div">
		                      <label for="turno">Afectar Vacio A:</label>
                              <select id="empty-cli" name="empty-cli" title="Please select something!" >
                                                <?php
                                                     armarSelect('clientes', 'razon_social', 'id', 'razon_social', "(id_estructura = $_SESSION[structure])");
                                                ?>
                              </select>
                              </div>

                    </fieldset>
           			<fieldset id="botonera" style="border:none; text-align: right;">
				              <input id="chepmtycli" class="boton" type="button" value="Guardar Cambios" name="chepmtycli">
                    </fieldset>
                    <input type="hidden" name="accion" id="accion" value="checl"/>
              </form>
         </div>
         <div id='rrr'></div>
</BODY>
</HTML>
