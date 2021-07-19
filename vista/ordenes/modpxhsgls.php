<?php
     session_start();
     set_time_limit(0);
     error_reporting(0);
     include_once('../main.php');

     include_once('../paneles/viewpanel.php');
     define(RAIZ, '');


     encabezado('Menu Principal - Sistema de Administracion - Campana');
     


      if (isset($_POST['cliente'])){
         $cliente = $_POST['cliente'];
         $fechaVis=$_POST['fecha'];
      }
      else
         $fechaVis = date("Y-m-d");
         




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
      <script type="text/javascript"
      src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDv8b-qrzRkI9kvtJV6l3Lko-mCdrGh7oE">
    </script>
 <script>
    var element;
    var campo;
    var cmpstr;
	$(function() {

                  $("#dialog-ch-driver").dialog({autoOpen: false, width: 650, modal: true});
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
                 $('#plctrl').click(function(){
                                               var fec = $("#fecha").val();
                                               var cli = $("#cliente").val();
                                               var tno = $("#turno").val();
                                               window.open("/vista/ordenes/planilla_control.php?fec="+fec+"&cli="+cli+"&tno="+tno, "_blank");
                                               });
                 $('#cargar, #chepmtycli, #plctrl').button();
                 $.mask.definitions['~']='[012]';
                 $.mask.definitions['%']='[012345]';
                 $(".hora").mask("~9:%9",{completed:function(){}});
                 $(".ocultar").hide();
                 $('#fecha').datepicker({dateFormat:'yy-mm-dd'});
                 $('table input:button').click(function(){
                                                          var ord = $(this).attr('id');
                                                          var pax = $('#cantpax-'+ord).val();
                                                          var re = $('#reca-'+ord).val();

                                                          $.post('/modelo/ordenes/modpxhsgls.php', {orden: ord, cantpax: pax, reca: re},function(data){ $('#'+ord).val('Listo');});

                                                          });
                 $(".reca").keypress(function(e) {
                                                  if(e.which == 13) {
                                                                $(this).val(sumar($(this).val()));
                                                                $(this).next('input').focus();
                                                  }
                 });
                 
                 $(".pax").keypress(function(e) {
                                                  if(e.which == 13) {
                                                                $(this).val(sumar($(this).val()));
                                                                $(this).next('input').focus();
                                                  }
                 });



});
   function sumar(value){
            var total = value.split('+');
            var suma = 0;
            for (i in total){
                suma+=parseFloat(total[i]);
            }
            return suma;
   }

	</script>

<style type="text/css">
.tr_hover {
          background-color: #00ff00;
}



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

      

        


?>
    <br><br>
    <div id="result"></div>
    <fieldset class="ui-widget ui-widget-content ui-corner-all">

         <legend class="ui-widget ui-widget-header ui-corner-all">Modificar Ordenes de Trabajo</legend>
         <hr align="tr">
         <div>
         <form id="load" method="post">

               <table align="center">
               <tr>
                   <td>
                       <select id="cliente" name="cliente" title="Please select something!" >
                       <?php
                           armarSelect('clientes', 'razon_social', 'id', 'razon_social', "(id_estructura = $_SESSION[structure])");
                       ?>
                       </select>
                   </td>
                   <td>
                       <input id="fecha" name="fecha" value="<?php echo $fechaVis;?>" type="text" size="20">
                   </td>
                   <td>
                       Incluir Vacios<input type="checkbox" name="empty" id="empty" <?php echo isset($_POST['empty'])?"checked":""; ?>>
                   </td>
                   <td>
                       <input type="submit" id="cargar" name="cargar" class="button" value="Cargar Ordenes">
                   </td>
               </tr>
               <tr>

                   <td>
                       <select id="turno" name="turno" title="Please select something!" >
                       <?php
                           armarSelect('turnos', 'turno', 'id', 'turno', "(id_estructura = $_SESSION[structure])");
                       ?>
                       </select>
                   </td>
                   <td></td>
                   <td></td>
                   <td><input id="plctrl" type="button" class="button" value="Imprimir Planilla Control"></td>
               </tr>
               </table>

              <input type="hidden" id="posx" name="posx">
              <input type="hidden" id="posy" name="posy" <?php if (isset($_POST['posy'])) echo "value='$_POST[posy]'";?>>
              <input type="hidden" id="order" name="order" <?php if (isset($_POST['order']) && ($_POST['order'] != '')) echo "value='$_POST[order]'";?>>
              
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
                                <th id="chofer2">H.Salida Std.</th>
                                <th id="nombre">Servicio</th>
                                <th id="interno">Interno</th>
                                <th id="chofer1">Conductor 1</th>
                                <th id="chofer2">H.Llegada Std.</th>
                                <th id="cantasientos">Cant. Asientos</th>
                                <th id="hfinservicio">Pasajeros</th>
                                <th id="observaciones">Recaudacion</th>
                                <th id="accion">Accion</th>
                            </tr>
                     </thead>
                     <tbody>
                            <?php
                                 if (isset($_POST['cliente'])){
                                          $con = conexcion();

                                          $sql="select concat(substr(e.apellido,1,4),substr(e.nombre,1,4),'-',e.id_empleado),
                                                       if (e.id_empleador = (SELECT valor FROM opciones where (id_estructura = 1) and (opcion = 'empleador-master')),
                                                       upper(concat(e.apellido,', ',e.nombre)),
                                                       upper(concat(e.apellido,', ',e.nombre,' (', em.razon_social,')'))) as emple
                                                from empleados e
                                                inner join empleadores em on em.id = e.id_empleador
                                                where (e.id_estructura = $_SESSION[structure]) and (id_cargo = 1) and (not e.borrado) and (e.activo)
                                                order by apellido";

                                          $result = ejecutarSQL($sql, $con);
                                          $conductores["aaaaaaaa-null"] = "";
                                          while ($data = mysql_fetch_array($result)){
                                                $conductores[$data[0]] = $data[1];
                                          };
                                          
                                          $sql="select concat(u.interno,'-',u.id), if (u.id_propietario = (SELECT valor FROM opciones where (id_estructura = 1) and (opcion = 'empleador-master')),
                                                       interno, concat(interno,' (',razon_social,')'))
                                                       from unidades u
                                                       left join empleadores p on p.id = u.id_propietario
                                                       where u.activo and p.activo and u.id_estructura = 1
                                                       order by interno";
                                          $result = ejecutarSQL($sql, $con);
                                          $coches["null"] = "";
                                          while ($data = mysql_fetch_array($result)){
                                                $coches[$data[0]] = $data[1];
                                          };

                                 if (isset($_POST['empty'])){
                                    $sql= "SELECT valor FROM opciones WHERE opcion = 'cliente-vacio' and id_estructura = $_SESSION[structure]";
                                    $result = ejecutarSQL($sql, $con);
                                    if ($row = mysql_fetch_array($result)){
                                       $id_empty = $row[valor];
                                       $empty = ", $row[valor]";
                                    }
                                 }
                                 $sql = "SELECT o.id as id_orden,
                                         o.finalizada, date_format(o.hllegadaplantareal, '%H:%i') as hllegada,
                                         date_format(o.hsalidaplantareal, '%H:%i') as hsalida,
                                         date_format(o.hfinservicioreal, '%H:%i') as hfinserv,
       o.nombre,
       if(em1.id = 1,concat(ch1.apellido, ', ',ch1.nombre), concat('(',em1.razon_social,') ', ch1.apellido, ', ',ch1.nombre)) as chofer1,
       upper(c.razon_social) as razon_social,
       concat(ch2.apellido, ', ',ch2.nombre) as chofer2,
       os.comentario as comentario,
       interno,
       o.suspendida,
       o.checkeada,
       emp.color,
       o.cantpax,
       if (o.id_servicio is null, (select i_v from tipotnoordenes t where id_orden = o.id), i_v) as i_v,
       cantasientos,
       date_format(o.hllegada, '%H:%i') as hllegadastd,
       date_format(o.hsalida, '%H:%i') as hsalidastd,
       c.id as id_cliente,
       if (cho.id is null, 0, 1) as chequeo,
       ch1.id_empleado as emp1,
       ch2.id_empleado as emp2,
       date_format(o.hcitacion, '%H:%i') as hcitacionstd,
       o.finalizada,
       if(rxo.monto is null,0, round(rxo.monto,2)) as recaudacion
FROM ordenes o
left join (select id, id_estructura, i_v from servicios) s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
LEFT JOIN empleadores em1 ON (em1.id = ch1.id_empleador)
LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
LEFT JOIN unidades m ON (m.id = o.id_micro)
LEFT JOIN empleadores emp ON (emp.id = m.id_propietario) and (emp.id_estructura = m.id_estructura_propietario)
LEFT JOIN obsSupervisores os ON os.id = o.id
LEFT JOIN recaudacionxorden rxo ON rxo.id_orden = o.id and o.id_estructura = rxo.id_estructura_orden
left join chequeo_ordenes cho on cho.id_orden = o.id and cho.id_estructura_orden = o.id_estructura
WHERE (o.fservicio = '$fechaVis') and (not o.borrada) and (not o.suspendida) and (o.id_estructura = $_SESSION[structure]) and (c.id in ($cliente $empty)) and (o.id <> 2355259)
order by o.hsalida";
//die($sql);                               //  die("$id_empty == $cliente");
                           //    die("cuasi".$con);
                                $conn=conexcion();
                                try{
                                 $result = ejecutarSQL($sql, $conn);// or die(mysql_error($con));
                                   }catch (Exception $e){print $e->getMessage();}
                              //   die ("khkhkj  $result".mysql_num_rows($result));
				                 while($row = mysql_fetch_array($result)){
                                            $id = $row['id_orden'];
                                            $tdclass="";
                                            $horastd=$row['hsalidastd'];
                                            if ($row['finalizada']){
                                               $tdclass="fin";
                                            }
                                            elseif ($row['checkeada']){
                                                   $tdclass="check";
                                            }
                                            elseif ($row['suspendida']){
                                               $tdclass="susp";
                                            }
                                            elseif ((!$row['emp1']) && (!$row['emp2'])){
                                                   $tdclass="scas";
                                            }
                                            if ($row['i_v'] == 'i'){
                                               $hiddeni = "";
                                               $hiddenv = "ocultar";
                                               $horastd=$row['hllegadastd'];
                                            }
                                            else{
                                               $hiddeni = "ocultar";
                                               $hiddenv = "";
                                            }
                                            if ($row[chequeo]){
                                               $im_ch = "check_si";
                                            }
                                            else{
                                                 $im_ch = "check_no";
                                            }
                                            $cierre="";
                                            if (!$row[finalizada]){
                                               $cierre = "<img class=\"$tdclass cloor\" src='../cierre.png' width='15' height='15' border='0' id='cls-$id'>";
                                            }
					                        echo "<tr id='trchk$row[id_orden]'>
                                                       <td class=\"$tdclass\" align='center'>$row[hcitacionstd]</td>
                                                       <td class=\"$tdclass\" align='center'>$row[hsalidastd]</td>
                                                       <td class=\"$tdclass\">".utf8_decode($row['nombre'])."</td>
                                                       <td class=\"$tdclass\"><div style='color:#$row[color];' class ='chint' id=\"chin-$row[id_orden]\">$row[interno]</div></td>
                                                       <td class=\"$tdclass\"><div id=\"chc1-$row[id_orden]-$row[emp1]\" class='chcnd'>".htmlentities($row['chofer1'])."</div></td>
                                                       <td class=\"$tdclass\" align='center'>$row[hllegadastd]</td>
                                                       <td class=\"$tdclass\" align='center'>$row[cantasientos]</td>";
                                             if ($row[id_cliente] == $cliente){
                                                       echo "<td class=\"$tdclass\"><input class='pax' type='text' size='5' value='$row[cantpax]' id='cantpax-$id'></td>";
                                             }
                                             else{
                                                  echo "<td class=\"$tdclass\"></td>";
                                             }
                                             echo "<td class=\"$tdclass\"><input class='reca' type='text' size='5' value='$row[recaudacion]' id='reca-$id'></td>
                                                       <td class=\"$tdclass\"><input id='$id' type='button' value='Guardar'></td>
                                                   </tr>";
                                 }
                               //  die ("khkhkj  siiiiiii $con".mysql_num_rows($query));
                                 mysql_free_result($result);
                                 mysql_close($conn);
                                 }
                            ?>
                     </tbody>
              </table>
         </div>
	</fieldset>
    <script>

                  $('select').selectmenu({width: 350});

                  




	</script>
         

         



</BODY>
</HTML>
