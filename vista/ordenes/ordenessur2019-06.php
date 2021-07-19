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
 <!--link type="text/css" href="<?php //echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/-->

 <link href="/vista/css/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.tablesorter.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.contextMenu.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>


 <link href="<?php echo RAIZ;?>/vista/css/jquery.contextMenu.css" rel="stylesheet" type="text/css" />



 <script>
    var element;
    var campo;
    var cmpstr;
	$(function() {


                   $(".ocultar").hide();

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
                 $(".pax").mask("99");
                 $('#send-order').datepicker({dateFormat:'dd/mm/yy', showOn: 'both'});
                 $('input:submit').button();
                 $('#sendorderatdate').button();
                 $('.hora').editable('/modelo/procesa/upd_ordenes.php', {type:"masked", mask: "~9:%9"});

                 $('.txt').editable('/modelo/procesa/mod_ordenes.php',  {type      : 'textarea',
                                                                        accion: 'updcmt',
                                                                        id: $(this).attr('id'),
                                                                        value: $(this).val(),
                                                                                      cancel    : 'Cancelar',
                                                                                      submit    : 'Guardar'});
                 $("#dialog-ch-driver").dialog({autoOpen: false, width: 650, modal: true});
                 $("#dialog-ch-interno").dialog({autoOpen: false, width: 450, modal: true});

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

	function chdriver(id){
          $.post('cargar_combo_conductores.php', {orden: id}, function(data) {
                                                                               $("#conductores").html(data);
                                                                             });
          $( "#dialog-ch-driver" ).dialog( "option", "title", "Cambiar conductor. Orden <?php echo htmlentities('N°');?> "+element );
          $("#dialog-ch-driver").dialog("open");
	}
	
	function chinterno(id){
          $.post('cargar_combo_internos.php', {orden: id}, function(data) {
                                                                               $("#internos").html(data);
                                                                             });
          $( "#dialog-ch-interno" ).dialog( "option", "title", "Cambiar Interno. Orden <?php echo htmlentities('N°');?> "+element );
          $("#dialog-ch-interno").dialog("open");
	}
	
	function chemptycli(id){
          $("#ch-empty-cli").dialog( "option", "title", "Cambiar Cliente Vacio de la Orden <?php echo htmlentities('N°');?> "+element );
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
             dialog.load('/modelo/informes/trafico/diagdia.php',{desde:'<?php echo date("Y-m-d");?>', hasta: '<?php echo $maniana;?>', cond:c},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});
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
.tsmo {cursor: pointer}
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

.headt td {
  height: 20px;
}
</style>
<BODY>
<?php
     menu();
     $con = conexcion();
     $ordenes = "SELECT o.id as orden, o.id_chofer_1 as id_chofer,if (date(citacion) < '$fecha', '00:00:00', citacion) as cita,
                        if (date(salida) < '$fecha', '00:00:00', salida) as sale,
                        if (date(finalizacion) > '$fecha', '23:59:59', finalizacion) as fina,
                        if (date(llegada) > '$fecha', '23:59:59', llegada) as llega, id_cliente, id_estructura_cliente, id_micro, nombre as nomOrden
                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(finalizacion))  and (not borrada) and (not suspendida) and (o.id_estructura = 2)
                  UNION
                  SELECT o.id, o.id_chofer_2,if (date(citacion) < '$fecha', '00:00:00', citacion),
                        if (date(salida) < '$fecha', '00:00:00', salida),
                        if (date(finalizacion) > '$fecha', '23:59:59', finalizacion),
                        if (date(llegada) > '$fecha', '23:59:59', llegada), id_cliente, id_estructura_cliente, id_micro, nombre
                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(finalizacion))  and (not borrada) and (not suspendida) and (o.id_estructura = 2)
                  union
                  SELECT o.id, id_empleado,if (date(citacion) < '$fecha', '00:00:00', citacion),
                        if (date(salida) < '$fecha', '00:00:00', salida),
                        if (date(finalizacion) > '$fecha', '23:59:59', finalizacion),
                        if (date(llegada) > '$fecha', '23:59:59', llegada), id_cliente, id_estructura_cliente, id_micro, nombre
                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  INNER JOIN tripulacionXOrdenes txo ON txo.id_orden = o.id AND txo.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(finalizacion))  and (not borrada) and (not suspendida) and (o.id_estructura = 2)";


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
                    <td><?php print "Mostrar Todas las ordenes<input type=\"Radio\" name=\"mostrar\" value=\"0,1\">";?></td>
                    <td></td>
                    <td><?php print "Mostrar Ordenes sin finalizar<input type=\"Radio\" name=\"mostrar\" value=\"0\">";?></td>
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
                                <th id="hcitacion">H. Salida</th>
                                <th id="hsalida">H. Llegada</th>
                                <th id="nombre">Servicio</th>
                                <th id="interno">Interno</th>
                                <?php
                                     for ($i = 1; $i <= $cantTripulacion; $i++){
                                        print "<th id='chofer$i'>Conductor $i</th>";
                                     }
                                ?>
                                <th id="razon_social">Cliente</th>
                            </tr>
                     </thead>
                     <tbody>
                            <?php
                                 $ordenes = "SELECT time_format(o.llega, '%H:%i') as llega, orden, nomOrden, time_format(o.cita, '%H:%i') as cita, time_format(o.sale, '%H:%i') as sale, interno, razon_social, concat(apellido,', ',nombre) as chofer, id_empleado
                                             FROM ($ordenes) o
                                             LEFT JOIN empleados ch ON (ch.id_empleado = o.id_chofer)
                                             LEFT JOIN unidades m ON (m.id = o.id_micro)
                                             LEFT JOIN obsSupervisores os ON os.id_orden = o.orden
                                             LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                                             order by orden
                                             ";
                              //    die($ordenes);

                                 $sql = "UNION
                                         SELECT *
                                         FROM novedades n
                                         inner join cod_novedades cn on cn.id = n.id_novedad
                                         inner join empleados e on e.id_empleado = n.id_empleado
                                         where ('$fecha' between desde and hasta) and (e.id_estructura = $_SESSION[structure]) and (e.id_cargo = 1) and (n.activa)";
                                 $query = mysql_query($ordenes, $con) or die(mysql_error($con));
				                 $row = mysql_fetch_array($query);
                         while($row)
                         {                  $id = $row['orden'];
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
                                            $hstd = "";
                                            $salereal = "ocultar";
                                            $llegareal = "ocultar";
                                            if ($row[i_v] == 'i'){
                                               $hstd = $row[hllegada];
                                               $salereal = "ocultar";
                                               $llegareal = "";
                                            }
                                            elseif ($row[i_v] == 'v'){
                                                   $hstd = $row[hsalida];
                                                   $salereal = "";
                                                   $llegareal = "ocultar";
                                            }
                                            
                                            $cargapax = "ocultar";
                                            if ($row[is_orden]){
                                               $cargapax = "";
                                               if ($row[cliVac]){
                                                  $salereal = "ocultar";
                                                  $llegareal = "ocultar";
                                                  $cargapax = "ocultar";
                                               }
                                            }
                                            else{
                                                 $hora="";
                                                 $obs="";
                                            }

                                            $orden = $row['orden'];
                                            $fila = "<tr class='headt'>
                                                       <td class='$tdclass'><div class='$hora' id='hcitacion-$id'>$row[sale]</div></td>
                                                       <td class='$tdclass'><div class='$hora' id='hsalida-$id'>$row[llega]</div></td>
                                                       <td class='$tdclass'><div class='$divclass $turi' id='nombre-$id'>".utf8_decode($row['nomOrden'])."</div></td>
                                                       <td class='$tdclass'><div class='$divclass' id='id_micro-$id'>$row[interno]</div></td>";
                                            $j = 0;
                                            while ($orden == $row['orden'])
                                            {
                                                if ($row['id_empleado'])
                                                {
                                                  $fila.="<td class='$tdclass'><div class='$divclass' id='id_chofer_1-$id' style='cursor: pointer;' onclick='mostrarDiag($row[id_empleado]);'>".($row['chofer'])."</div></td>";
                                                }
                                                else{
                                                   $fila.="<td class='$tdclass'><div class='$divclass' id='id_chofer_2-$id' style='cursor: pointer;'></div></td>";
                                               }
                                                $j++;
                                                $ant = $row;
                                                $row = mysql_fetch_array($query);
                                                $last = $row;
                                            }
                                            $row = $ant;
                                            for ($i=$j; $i < $cantTripulacion; $i++) { 
                                                $p = $i + 1;
                                                $fila.="<td class='$tdclass'><div class='$divclass' id='id_chofer_$p-$id' style='cursor: pointer;'></div></td>";
                                            }

                                            $fila.= "<td class=\"$tdclass\"><div class=\"$divclass\" id=\"razon_social-$id\">".htmlentities($row['razon_social'])."</div></td>
                                                   </tr>";
                                            print $fila;
                                            $row = $last;
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
			<li class="copy"><a href="#delorder">Eliminar Orden</a></li>
			<li class="paste"><a href="#sendorder">Enviar a otra fecha...</a></li>
  <?php
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
         <div id='rrr'></div>
</BODY>

 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.tablesorter.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.contextMenu.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>

</HTML>