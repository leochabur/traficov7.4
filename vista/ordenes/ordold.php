<?php
     //require( '../../php_speedy/php_speedy.php' );
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
                                                                       {accion:'updral', cmp:$(this).attr('id'), data: $(this).val()},
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

                 $("#dialog-ch-driver, #dialog-ch-interno").dialog({autoOpen: false, width: 650, modal: true});
                 $("#comentario_orden").dialog({autoOpen: false, 
                                                width: 650, 
                                                modal: true,
                                                close: function(event, ui) {$('#observa').val('');}
                                              });
                 $('#upcmt').submit(function(event){
                                                                event.preventDefault();                                                                
                                                                var form = $(this);                                                                
                                                                $.post('/modelo/procesa/mod_ordenes_coment.php',
                                                                       form.serialize(),
                                                                       function(data){
                                                                                      var response = $.parseJSON(data);
                                                                                      if (response.status){
                                                                                          var orden = form.find(":hidden[name='orden']").val();
                                                                                          var text = $('#observa');  
                                                                                          $('#comentario-'+orden).html(text.val());
                                                                                          $( "#comentario_orden").dialog('close');
                                                                                      }
                                                                                      else
                                                                                        alert(response.msge);
                                                                       });
                 });

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
                 $("#ordenCmt").submit(function(event){
                                                        event.preventDefault();
                                                        alert($(this).serialize());
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

  function abrirDialogoComentario(order){
          $('#ordenCmt').val(order);
          $( "#comentario_orden").dialog( "open" );
          var tr = $('#comentario-'+order);
          var legend=$("#id_legend");
          legend.text(tr.data('nombre'));
          $('#observa').val(tr.html());
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
                                 $sql = "SELECT o.id, finalizada, date_format(o.hcitacion, '%H:%i') as hcitacion, date_format(o.hsalida, '%H:%i') as hsalida, 
                                                date_format(o.hfinservicio, '%H:%i') as hfinserv, o.nombre, 
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
                             //    die($sql);
                                 $query = mysql_query($sql, $con) or die(mysql_error($con));
                                 $body = "";
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
                                            $horarioSTD="<td $tdclass id='std1-$id'></td>
                                                            <td $tdclass id='std2-$id'></td>
                                                            <td $tdclass id='std3-$id'></td>";
                                            if ($row[i_v] == 'i'){
                                               $hstd = $row[hllegada];
                                               $horarioSTD="<td $tdclass align='center' id='std1-$id'></td>
                                                            <td $tdclass><div align='center' id='std2-$id'>$hstd</div></td>
                                                            <td $tdclass align='center' id='std3-$id'><input type='text' name='hreal' size='5' value='$row[hllegadaReal]' id='hllegadaplantareal-$id' alt='Hora de llegada Real'></td>";
                                            }
                                            elseif ($row[i_v] == 'v'){
                                                   $hstd = $row[hsalida];
                                                   $horarioSTD="<td $tdclass align='center' id='std1-$id'><input type='text' name='hreal' size='5' value='$row[hsalidaReal]' id='hsalidaplantareal-$id' alt='Hora de salida Real'></td>
                                                                <td $tdclass><div align='center' id='std2-$id'>$hstd</div></td>
                                                                <td $tdclass align='center' id='std3-$id'></td>";
                                            }

					                        $body.="<tr data-id='$id' style='height: 20px;' name='$name'>
                                                       <td $tdclass id='hc1-$id'><div class='".($row[is_orden]?'horareal':'')."' id='hcitacionreal-$id'>$row[hcitacionreal]</div></td>
                                                       <td $tdclass id='hs1-$id'><div id='hs2-$id'>$row[hsalida]</div></td>
                                                       <td $tdclass id='name1-$id'><div id='name2-$id'>".$row['nombre']."</div></td>
                                                       <td $tdclass id='id_micro1-$id'><div id='id_micro-$id'><a href='/vista/segvial/posint.php?int=$row[interno]' target='_blank' onClick=\"window.open(this.href, this.target, 'width=900,height=650'); return false;\"><div style='color:#$row[color];'>$row[interno]</div></a></div></td>
                                                       <td $tdclass id='id_chofer_11-$id'><div id='id_chofer_1-$id' style='cursor: pointer;' onclick='mostrarDiag($row[id_empleado]);'>".($row['chofer1'])."</div></td>
                                                       <td $tdclass id='id_chofer_21-$id'><div id='id_chofer_2-$id' style='cursor: pointer;' onclick='mostrarDiag($row[id_empleado2]);'>".($row['chofer2'])."</div></td>";
                                         /* if ($cantTripulacion > 2){
                                               $body.="<td $tdclass><div class='$divclass' id='id_chofer_3-$id'>".($row['chofer3'])."</div></td>";
                                            }*/
                                            $isOrden="<td $tdclass id='pax-$id'></td>";
                                            if ($row['is_orden'] && !$row['cliVac']){
                                                $isOrden = "<td $tdclass id='pax-$id'><input type='text' name='hreal' size='2' value='$row[cantpax]' id='cantpax-$id' alt='Cant. Pax.'></td>";
                                            }
                                            $body.="<td $tdclass id='cli1-$id'><div class='$divclass' id='razon_social-$id'>".($row['razon_social'])."</div></td>
                                                       <td $tdclass id='cmt1-$id'><div id='comentario-$id' data-nombre='$row[nombre]'>$row[comentario]</div></td>
                                                       $horarioSTD
                                                       $isOrden
                                                       <td $tdclass id='hfin-$id'><div id='hfinservicioreal-$id' class='".($row[is_orden]?'horareal':'')."'>$row[hfinservicioreal]</div></td>
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
         
         <div id="comentario_orden" title="Modificar Observacion">
            <fieldset class="ui-widget ui-widget-content ui-corner-all">
                        <legend class="ui-widget ui-widget-header ui-corner-all" id="id_legend"></legend>
                        <form id="upcmt">
                                    <textarea class="ui-widget ui-widget-content  ui-corner-all" name='observa' id='observa' rows="7" cols="70"></textarea>
                                    <br>
          				                  <input type="submit" value="Guardar Cambios">
                                    <input type="hidden" name="accion" value="upComment"/>
                                    <input type="hidden" name="orden" id="ordenCmt"/>
                        </form>
            </fieldset>
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
                else if (action == 'vacio'){
                   element = elem;
                   abrirDialogo();
                }
                else if (action == 'dupli'){

                   duplicarOrden(elem);
                }
                else if (action == 'chdriver1'){
                    element = elem;
                    campo = 'id_chofer_1';
                    cmpstr = 'iech1';
                    chdriver(elem);
                }
                else if (action == 'chdriver2'){
                    element = elem;
                    campo = 'id_chofer_2';
                    cmpstr = 'iech2';
                    chdriver(elem);
                }
                else if (action == 'delorder'){
                    element = elem;
                    alert('Esta seguro de eliminar la orden '+element);
                    $.post("/modelo/procesa/procesar_ordenes.php", {accion: 'deor', order: element}, function(data) {
                                                                                                                                  $("#load").submit();
                                                                                                                                 });
                }
                else if (action == 'chinterno'){
                    element = elem;
                    campo = 'id_micro';
                    chinterno(elem);
                }
                
                else if (action == 'chclivac'){
                    element = elem;
                    campo = 'nombre';
                    chemptycli(elem);
                }
                else if (action == 'openord'){
                   abirOrdenCompleta(elem);
                }
                else if (action = 'updmt'){                  
                  abrirDialogoComentario(elem);
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
                "updmt": {name: "Cargar/Modificar Observacion"}
            }
        });
    });
</script>

</HTML>
<?php
//$compressor->finish();
?>
