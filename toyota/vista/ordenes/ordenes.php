<?php
     session_start();

     include_once('../main.php');

     include_once('../paneles/viewpanel.php');
     include_once ('../../../controlador/bdadmin.php');
     define(RAIZ, '');


     encabezado('Menu Principal - Sistema de Administracion - Campana');

    $all="";
    $sinfin="";
	if (isset($_POST['mostrar'])){
       if ($_POST['mostrar'] == 't'){
          $all="checked";
          $_SESSION['todas'] = '0,1';
       }
       else{
           $sfin="checked";
           $_SESSION['todas'] = '0';
       }
    }
    else{
         $_SESSION['todas'] = "0,1";
         $all="checked";
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
     
     $maniana= date("Y")."-".date("m")."-".(date("d")+1);
     




  $estructura = $_SESSION['structure'];




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
    var cond = new Array();
 <?php
  $conn = conexcion();
  $sql = "select e.id_empleado, upper(if(em.id = 1,concat(e.apellido, ', ',e.nombre), concat(e.apellido, ', ',e.nombre,'(',em.razon_social,' )')))  as emple
          from empleados e
          inner join empleadores em on em.id = e.id_empleador
          where (e.activo) and (e.id_estructura = $estructura) and (id_cargo = 1) and (e.id_empleado not in (SELECT e.id_empleado
                                                                                                             from novedades nov
                                                                                                             inner join empleados e on e.id_empleado = nov.id_empleado
                                                                                                             where ('$fecha' between desde and hasta) and (e.activo) and (nov.id_estructura = $estructura) and (id_cargo = 1)
                                                                                                             group by e.id_empleado))
          order by e.apellido, e.nombre";
  $result = mysql_query($sql, $conn);
  
  $micros = "SELECT id, interno FROM unidades m where (activo) and (id_estructura = $_SESSION[structure]) order by interno";
  $resu = mysql_query($micros, $conn);


?> cond.sort();
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

                 $("#table").tablesorter();
                 $("#table tr:odd").css("background-color", "#ddd"); // filas impares
                 $("#tabletr:even").css("background-color", "#ccc"); // filas pares
                 $(".fin").css("background-color", "#EEF834");
                 $(".check").css("background-color", "#C0FFFF");
                 $(".susp").css("background-color", "#D0D0D0");
                 $("#table tbody tr").mouseover(function() {
                                                                     $(this).addClass("tr_hover");
                                                                    });
                 $("#table tbody tr").mouseout(function() {
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

                 $('input:submit').button();

                 $('.hora').editable('/toyota/modelo/procesa/upd_ordenes.php', {type:"masked", mask: "~9:%9"});
                 $('.txt').editable('/toyota/modelo/procesa/upd_ordenes.php',  {type      : 'textarea',
                                                                                      cancel    : 'Cancelar',
                                                                                      submit    : 'Guardar'});
                  $('.cond').editable('/toyota/modelo/procesa/upd_ordenes.php', {
                                                                              data   : {<?
                                                                                            while ($row = mysql_fetch_array($result)){
                                                                                                  echo "'$row[0]':'".htmlentities($row[1])."',";
                                                                                            }

                                                                                         ?>},
                                                                              type   : 'select',
                                                                              submit : 'Guardar'
                                                                              });
                  $('.mic').editable('/toyota/modelo/procesa/upd_ordenes.php', {
                                                                              data   : {<?
                                                                                            while ($row = mysql_fetch_array($resu)){
                                                                                                  echo "'$row[0]':'".htmlentities($row[1])."',";
                                                                                            }

                                                                                         ?>},
                                                                              type   : 'select',
                                                                              submit : 'Guardar'
                                                                              });
	});
	
	function cerrarOrden(id){
          if(confirm('Seguro cerrar la orden?'))
                             $.post('/modelo/procesa/mod_ordenes.php', {accion: "cls", orden: id}, function(data) {
                                                                                                                   $('[id$='+id+']').css("background-color", "#EEF834");
                                                                                                                  });
	}

	function checkOrden(id){

          if(confirm('Seguro chequear la orden?'))
                             $.post('/modelo/procesa/mod_ordenes.php', {accion: "check", orden: id}, function(data) {
                                                                                                                   $('#tr-'+id).css("background-color", "#C0FFFF");
                                                                                                                  });
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

#table thead tr th{padding: 7px;}
#table tbody tr td{padding: 0px;}
#cargar {font-size: 72.5%;}
.tr_hover {background-color: #ffccee}



#table thead {cursor: pointer}

</style>
<BODY>
<?php
     menu();
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
                    <td><?print "Mostrar Todas las ordenes<input type=\"Radio\" name=\"mostrar\" $all value='t'>";?></td>
                    <td></td>
                    <td><?print "Mostrar Ordenes sin finalizar<input type=\"Radio\" name=\"mostrar\" $sfin value='s'>";?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    </tr>
              </table>
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
                    <td></td> <td></td>
                    <td></td>
                    </tr>
         </table>
         <div id="tablaordenes">
              <table id='table' align="center" border="0" width="100%">
                     <thead>
            	            <tr class="ui-widget ui-widget-header ui-corner-all">
                                <th>H. Citacion</th>
                                <th>H. Salida</th>
                                <th>H. Fin</th>
                                <th>Servicio</th>
                                <th>Interno</th>
                                <th>Conductor 1</th>
                                <th>Cliente</th>
                                <th>Observaciones</th>
                                <th>C.1</th>
                                <th>Chequear</th>
                            </tr>
                     </thead>
                     <tbody>
                            <?php
                                 $con = conexcion();
                                 $sql = "SELECT o.id, finalizada, date_format(hcitacion, '%H:%i') as hcitacion, date_format(hsalida, '%H:%i') as hsalida, date_format(hfinservicio, '%H:%i') as hfinserv, o.nombre, if(em1.id = 1,concat(ch1.apellido, ', ',ch1.nombre), concat('(',em1.razon_social,') ', ch1.apellido, ', ',ch1.nombre)) as chofer1, upper(c.razon_social) as razon_social, concat(ch2.apellido, ', ',ch2.nombre) as chofer2, comentario, interno, ch1.id_empleado, ch2.id_empleado as id_empleado2, suspendida, checkeada
                                                       FROM ordenes o
                                                       LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
                                                       LEFT JOIN empleadores em1 ON (em1.id = ch1.id_empleador)
                                                       LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
                                                       LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                                                       LEFT JOIN unidades m ON (m.id = o.id_micro)
                                                       WHERE (fservicio = '$fecha') and (finalizada in ($_SESSION[todas])) and (not borrada) and (o.id_estructura = $_SESSION[structure])and ((o.id_cliente in ($_SESSION[modaf])) or (o.id_cliente_vacio in ($_SESSION[modaf])))
                                         order by hcitacion";

                                 $query = mysql_query($sql, $con) or die(mysql_error($con));
				                 while($row = mysql_fetch_array($query)){
                                            $tdclass="";$hora="hora";$txt="txt";$cond="cond"; $mic="mic";
                                            if ($row['finalizada']){
                                               $tdclass="fin";
                                               $hora="";
                                               $txt="";
                                               $cond="";
                                               $mic="";
                                            }
                                            elseif ($row['suspendida']){
                                                   $tdclass="susp";
                                                   $hora="";
                                                   $txt="";
                                                   $cond="";
                                                   $mic="";
                                            }
                                            elseif ($row['checkeada']){
                                               $tdclass="check";
                                            }


                                            $dri='';
                                            if ($row['chofer1'])
                                               $dri="<img title='Ver diagrama de ".htmlentities($row['chofer1'])."' src='../user.png' border='0' onclick='mostrarDiag($row[id_empleado]);' style='cursor:pointer'>";
                                            $id = $row[0];
					                        print "<tr id='tr-$id' class='$tdclass'>
                                                       <td class='$hora' id='hcitacion-$id'>$row[hcitacion]</td>
                                                       <td class='$hora' id='hsalida-$id'>$row[hsalida]</td>
                                                       <td class='$hora id='hfinserv-$id'>$row[hfinserv]</td>
                                                       <td>".htmlentities($row['nombre'])."</td>
                                                       <td class='$mic' id='id_micro-$id'><a href='/toyota/vista/segvial/posint.php?int=$row[interno]' target='_blank' onClick=\"window.open(this.href, this.target, 'width=900,height=650'); return false;\">$row[interno]</a></td>
                                                       <td class='$cond' id='id_chofer_1-$id'>".htmlentities($row['chofer1'])."</td>
                                                       <td>".htmlentities($row['razon_social'])."</td>
                                                       <td class='$txt'>$row[comentario]</td>
                                                       <td>$dri</td>
                                                       <td><input type='button' value='Chequear' onClick='checkOrden($row[id]);'></td>
                                                   </tr>";
                                 }
                                 mysql_free_result($query);
                                 mysql_close($con);
                            ?>
                     </tbody>
              </table>
         </div>
	</fieldset>

</BODY>
</HTML>
