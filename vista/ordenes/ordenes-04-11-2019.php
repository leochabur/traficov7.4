<?php
   //  require( '../../php_speedy/php_speedy.php' );
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
?>

 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>

 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.tablesorter.js"></script>

  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script>
    var element;
    var campo;
    var cmpstr;
	$(function() {
                   $("input[name='hreal']").keypress(function(e) {
                                                    var comp = $(this);
                                                     if(e.which == 13) {
                                                                $.post('/modelo/procesa/mod_ordenes.php',
                                                                       {accion:'updral', cmp: comp.attr('id'), data: comp.val()},
                                                                       function(data){
                                                                                      var response = $.parseJSON(data);
                                                                                      if (response.status){
                                                                                         comp.css('background-color', '#48FF48');
                                                                                      }
                                                                                      else{
                                                                                           comp.css('background-color', '#FF0000');
                                                                                      }
                                                                                      });
                                                     }
                                                    });

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
                 $("input[name='hora']").mask("~9:%9");
                 $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                 $(".pax").mask("99");
                 $('input:submit').button();
                 $("div.horareal").editable('/modelo/procesa/upd_ordenes.php', {type:"masked", mask: "~9:%9"});

                 $("div.observa").editable('/modelo/procesa/mod_ordenes.php',  {type      : 'textarea',
                                                                        accion: 'updcmt',
                                                                        id: $(this).attr('id'),
                                                                        value: $(this).val(),
                                                                                      cancel    : 'Cancelar',
                                                                                      submit    : 'Guardar'});
                 $("#dialog-ch-driver").dialog({autoOpen: false, width: 650, modal: true});
                 $("#dialog-ch-interno").dialog({autoOpen: false, width: 450, modal: true});
                    

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

function showMenu(event){
  alert(event.button);
}

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
     //     $( "#dialog-form").dialog( "open" );
	}
	
	function chdriver(id){
          $.post('cargar_combo_conductores.php', {orden: id}, function(data) {
                                                                               $("#conductores").html(data);
                                                                             });
          $( "#dialog-ch-driver" ).dialog( "option", "title", "Cambiar conductor. Orden <? echo htmlentities('N�');?> "+element );
          $("#dialog-ch-driver").dialog("open");
	}
	
	function chinterno(id){
          $.post('cargar_combo_internos.php', {orden: id}, function(data) {
                                                                               $("#internos").html(data);
                                                                             });
          $( "#dialog-ch-interno" ).dialog( "option", "title", "Cambiar Interno. Orden <? echo htmlentities('N�');?> "+element );
          $("#dialog-ch-interno").dialog("open");
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
                                   //  if (($_SESSION['userid'] == 25) || ($_SESSION['userid'] == 18)|| ($_SESSION['userid'] == 37))
                                    //    print '<th id="hfinservicio">H. Llegada</th>';
                                ?>
                                <th>H. Salida Real</th>
                                <th>Hora Std.</th>
                                <th>H. Llegada Real</th>
                                <th>Pax</th>
                                <th>H. Fin</th>
                                <!--th>C.1</th>
                                <th>C.2</th-->

                                <?php if ($cantTripulacion > 2){
                                        print "<th>C.3</th>";
                                     }
                                ?>
                            </tr>
                     </thead>
                     <tbody>
                            <?php
                                 $sql = "SELECT o.id, finalizada, time_format(o.hcitacion, '%H:%i') as hcitacion, time_format(o.hsalida, '%H:%i') as hsalida, 
                                                date_format(o.hfinservicio, '%H:%i') as hfinserv, o.nombre as nombre, 
                                                if(em1.id = 1,concat(ch1.apellido, ', ',ch1.nombre), concat('(',em1.razon_social,') ', ch1.apellido, ', ',ch1.nombre)) as chofer1, 
                                                upper(c.razon_social) as razon_social, concat(ch2.apellido, ', ',ch2.nombre) as chofer2, os.comentario as comentario, interno, 
                                                ch1.id_empleado, ch2.id_empleado as id_empleado2, suspendida, checkeada, emp.color, date_format(o.hllegada, '%H:%i') as hllegada, 
                                                ot.id as or_tur, cantpax, i_v, 1 as is_orden, date_format(o.hllegadaplantareal, '%H:%i') as hllegadaReal, 
                                                date_format(o.hsalidaplantareal, '%H:%i') as hsalidaReal, op.id as cliVac, date_format(o.hfinservicioreal, '%H:%i') as hfinservicioreal,
                                                date_format(o.hcitacionreal, '%H:%i') as hcitacionreal
                                                       FROM ordenes o
                                                       LEFT JOIN servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                                                       LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
                                                       LEFT JOIN empleadores em1 ON (em1.id = ch1.id_empleador)
                                                       LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
                                                       $tripulacion
                                                       LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                                                       LEFT JOIN unidades m ON (m.id = o.id_micro)
                                                       LEFT JOIN empleadores emp ON (emp.id = m.id_propietario) and (emp.id_estructura = m.id_estructura_propietario)
                                                       LEFT JOIN ordenes_turismo ot on ot.id_orden = o.id and ot.id_estructura_orden = o.id_estructura
                                                       LEFT JOIN obsSupervisores os ON os.id_orden = o.id
                                                       LEFT JOIN (SELECT * FROM opciones WHERE opcion = 'cliente-vacio') op ON op.id_estructura = o.id_estructura and op.valor = o.id_cliente
                                                       WHERE (fservicio = '$fecha') and (not borrada) and (not suspendida) and (o.id_estructura = $_SESSION[structure]) and (finalizada in ($_SESSION[todas]))
                                         UNION
                                         SELECT n.id as id, 0 as finalizada, '00:00' as hcitacion, '00:00' as hsalida, '00:00' as hfinserv, cn.nov_text as nombre, concat(e.apellido, ', ',e.nombre) as chofer1, '' as razon_social, '' as chofer2, '' as comentario, '' as interno, e.id_empleado as id_empleado, 0 as id_empleado2, 0, 0, '' as color, '00:00' as hllegada, 0 as or_tur, '' as cantpax, 'n' as i_v, 0 as is_orden, '00:00' as llegadareal, '00:00' as salildareal, 0 as cliVac, '', ''
                                         FROM novedades n
                                         inner join cod_novedades cn on cn.id = n.id_novedad
                                         inner join empleados e on e.id_empleado = n.id_empleado
                                         where ('$fecha' between desde and hasta) and (e.id_estructura = $_SESSION[structure]) and (e.id_cargo = 1) and (n.activa)
                                         order by $orden";
         
                                 $query = mysql_query($sql, $con) or die(mysql_error($con));
                                 $body = "";
                                 
                                 $sqlDiag = "SELECT * FROM estadoDiagramasDiarios where fecha = '$fecha' and id_estado = 1 and id_estructura = $_SESSION[structure]";
                                 $resultDiag = mysql_query($sqlDiag, $con);  
                                 $diagramaFinalizado = false; 
                                 if ( mysql_num_rows($resultDiag)){
                                      $diagramaFinalizado = true;

                                 }
				                 while($row = mysql_fetch_array($query)){
                                            $tdclass="";
                                            $name='menu';
                                            $id = $row['id'];
                                            if ($row['finalizada']){
                                               $tdclass='style="background-color:#EEF834;"';
                                               $name='';
                                            }
                                            elseif ($row['checkeada']){
                                                   $tdclass='style="background-color:#C0FFFF;"';
                                            }
                                            elseif ($row['suspendida']){
                                               $tdclass='style="background-color:#D0D0D0;"';
                                            }
                                            elseif ((!$row['id_empleado']) && (!$row['id_empleado2'])){
                                                   $tdclass='style="background-color:#FFC0C0;"';
                                            }

                                            $hstd = "";
                                            $horarioSTD="<td $tdclass id='tdBaseWhite-$id'></td>
                                                            <td $tdclass id='tdBaseBlack-$id'></td>
                                                            <td $tdclass id='tdLlegadaPlantaReal-$id'></td>";
                                            if ($diagramaFinalizado){
                                                $llega = $row['hllegadaReal'];
                                                $cita = $row['hcitacionreal'];
                                                $sale = $row['hsalidaReal'];
                                                $fina = $row['hfinservicioreal'];
                                                $tdSalida = "<div $tdclass id='hsalidaReal-$id'>$sale</div>";
                                            }
                                            else
                                            {
                                                $llega = $row['hllegada'];
                                                $cita = $row['hcitacion'];
                                                $sale = $row['hsalida'];
                                                $fina = $row['hfinserv'];  
                                                $tdSalida = "<div $tdclass id='hsalida-$id'>$sale</div>";                                           
                                            }
                                            if ($row[i_v] == 'i'){

                                               $hstd = $llega;
                                               $horarioSTD="<td $tdclass align='center' id='tdBaseWhite-$id'></td>
                                                            <td $tdclass id='tdBaseBlack-$id'><div align='center' id='tdBaseSubBlack-$id'>$hstd</div></td>
                                                            <td $tdclass id='tdLlegadaPlantaReal-$id' align='center'><input type='text' name='hreal' size='5' value='$llega' id='hllegadaplantareal-$id' alt='Hora de llegada Real'></td>";
                                            }
                                            elseif ($row[i_v] == 'v'){
                                                   $hstd = $sale;
                                                   $horarioSTD="<td $tdclass align='center' id='tdBaseWhite-$id'><input type='text' name='hreal' size='5' value='$sale' id='hsalidaplantareal-$id' alt='Hora de salida Real'></td>
                                                                <td $tdclass id='tdBaseBlack-$id'><div align='center' id='tdBaseSubBlack-$id'>$hstd</div></td>
                                                                <td $tdclass id='tdLlegadaPlantaReal-$id' align='center'></td>";
                                            }
					                        $body.="<tr data-id='$id' style='height: 20px;' name='$name'>
                                                       <td $tdclass id='tdBaseHCitacion-$id'><div class='".($row[is_orden]?'horareal':'')."' id='hcitacionreal-$id'>$cita</div></td>
                                                       <td $tdclass id='tdBaseHSalida-$id'>$tdSalida</td>
                                                       <td $tdclass id='tdBaseNombre-$id'>".$row['nombre']."</td>
                                                       <td $tdclass id='tdBaseMicro-$id'><a href='/vista/segvial/posint.php?int=$row[interno]' target='_blank' onClick=\"window.open(this.href, this.target, 'width=900,height=650'); return false;\"><div style='color:#$row[color];' id='id_micro-$id'>$row[interno]</div></a></td>
                                                       <td $tdclass id='tdBaseCh1-$id'><div style='cursor: pointer;' onclick='mostrarDiag($row[id_empleado]);' id='id_chofer_1-$id'>".($row['chofer1'])."</div></td>
                                                       <td $tdclass id='tdBaseCh2-$id'><div style='cursor: pointer;' onclick='mostrarDiag($row[id_empleado2]);' id='id_chofer_2-$id'>".($row['chofer2'])."</div></td>";

                                            $isOrden="<td $tdclass id='tdBaseIsOrden-$id'></td>";
                                            $observa = '';
                                            if ($row['is_orden'] && !$row['cliVac']){
                                                $isOrden = "<td $tdclass id='tdBaseIsOrden-$id'><input type='text' name='hreal' size='2' value='$row[cantpax]' id='cantpax-$id' alt='Cant. Pax.'></td>";
                                                $observa='class="observa"';                                          
                                            }
                                            $body.="<td $tdclass id='tdBaseCliente-$id'><div id='razon_social-$id'>".($row['razon_social'])."</div></td>
                                                       <td $tdclass id='tdBase-$id'><div $observa id='cmt-$id-updcmt'>$row[comentario]</div></td>
                                                       $horarioSTD
                                                       $isOrden
                                                       <td $tdclass id='tdBaseHFinServReal-$id'><div id='hfinservicioreal-$id' class='".($row[is_orden]?'horareal':'')."'>$fina</div></td>
                                                   </tr>";
                                 }
                                 print $body;
                                 mysql_free_result($query);
                                 mysql_close($con);
                            ?>
                     </tbody>
              </table>
         </div>
	</fieldset>

		
		
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
         <div id='rrr'></div>
</BODY>
<script type="text/javascript" src="/vista/js/MDB-Free_4/js/jquery-3.4.1.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.contextMenu.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-contextmenu/2.7.1/jquery.ui.position.js"></script>
<script type="text/javascript">
    var v34 = $.noConflict(true);
    v34(function(){
        v34.contextMenu({
            selector: '#table tbody tr[name=menu]', 
            callback: function(action, options) {
                var elem = v34(this).data('id');
                if (action == 'cerrar'){
                  cerrarOrden(elem);
                }
                else if (action == 'susp'){
                   suspenderOrden(elem);
                }
                else if (action == 'check'){
                   checkOrden(elem);
                }
                if (action == 'vacio'){
                   element = elem;
                   abrirDialogo();
                }
                if (action == 'dupli'){

                   duplicarOrden(elem);
                }
                if (action == 'chdriver1'){
                    element = elem;
                    campo = 'id_chofer_1';
                    cmpstr = 'iech1';
                    chdriver(elem);
                }
                if (action == 'chdriver2'){
                    element = elem;
                    campo = 'id_chofer_2';
                    cmpstr = 'iech2';
                    chdriver(elem);
                }
                if (action == 'delorder'){
                    element = elem;
                    alert('Esta seguro de eliminar la orden '+element);
                    $.post("/modelo/procesa/procesar_ordenes.php", {accion: 'deor', order: element}, function(data) {
                                                                                                                                  $("#load").submit();
                                                                                                                                 });
                }
                if (action == 'chinterno'){
                    element = elem;
                    campo = 'id_micro';
                    chinterno(elem);
                }
                
                if (action == 'chclivac'){
                    element = elem;
                    campo = 'nombre';
                    chemptycli(elem);
                }
                if (action == 'openord'){
                   abirOrdenCompleta(elem);
                }
            },
            items: {
                "cerrar": {name: "Cerrar Orden"},
                "check": {name: "Chequear orden"},
                "susp": {name: "Suspender orden"},
                "vacio": {name: "Crear Vacio"},
                "delorder": {name: "Eliminar Orden"},
                <?php
                          if ($_SESSION['permisos'][2] > 1){
                             print '"dupli": {name: "Duplicar Orden"},';
                          }
                ?>
                "chdriver1": {name: "Cambiar Conductor 1"},
                "chdriver2": {name: "Cambiar Conductor 2"},
                "chinterno": {name: "Cambiar Interno"},
                "sep1": "---------",
                "quit": {name: "Cerrar", icon: function($element, key, item){ return 'context-menu-icon context-menu-icon-quit'; }}
            }
        });
    });
</script>

</HTML>
<?php
$compressor->finish();
?>
