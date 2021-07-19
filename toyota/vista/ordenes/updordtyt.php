<?php
     session_start();

     include_once('../main.php');

     include_once('../paneles/viewpanel.php');
     define(RAIZ, '');


     encabezado('Menu Principal - Sistema de Administracion - Campana');


  /*   $qcond = "SELECT id_empleado, upper(concat(apellido,', ', nombre)) as apenom
               FROM empleados
               where (id_estructura = $_SESSION[structure]) and (activo)
               order by apellido, nombre";
     $result = mysql_query($qcond, $con);
     while ($data = mysql_fetch_array($result)){
           $cond["$data[id_empleado]"] =  "$data[apenom]";
     }

     mysql_free_result($result);       */
     
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
                 $.mask.definitions['~']='[012]';
                 $.mask.definitions['%']='[012345]';
                 $(".hora").mask("~9:%9",{completed:function(){}});
                 $(".pass").mask("99")
                 $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                 $('#table :button').button().click(function(data){
                                                                   var ord = $(this).attr("id");
                                                                   var i_v = $(this).attr("title");
                                                                   var pasa = $('#pax-'+ord).val();
                                                                   var hor = $('#hor-'+ord).val();
                                                                   $.post("/toyota/modelo/modord.php", {orden: ord, horarios: hor, pax: pasa, iv:i_v}, function(){});
                                                                   $("#bt-"+ord).html('');
                                                                   });



	});

	</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
.small.button, .small.button:visited {
font-size: 11px ;
}

#table { font-size: 80.5%; }
#upcontact div{padding: 2px;}
#table thead tr th{padding: 7px;}
#table tbody tr td{padding: 5px;}
#cargar {font-size: 72.5%;}
.tr_hover {background-color: #ffccee}

option.0{background-color: #;}
option.1{background-color: ;}
#upcontact .error{
	font-size:0.8em;
	color:#ff0000;
}

.par{background:#f5f5f5}
.impar{background:#B0B0B0}
.encab{background:#C0FFFF;font-weight:bold;}
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
         </form>
         </div>
         <div id='mjw'></div>
         <hr align="tr">
         <table width=100%>
                <tr>
                    <td class="fin">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td>&Oacute;rdenes finalizadas</td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td></td>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td></td> <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td></td>
                    </tr>
         </table>
         <div id="tablaordenes">
              <table id='table' align="center" border="0" width="100%">
                     <tbody>
                            <?php
                                 $sql="SELECT o.id, tipo, turno, s.i_v, o.id, finalizada, date_format(o.hcitacion, '%H:%i') as hcitacion, date_format(o.hsalida, '%H:%i') as hsalida,
                                              date_format(o.hllegada, '%H:%i') as hllegada, o.nombre,
                                              if(em1.id = 1,concat(ch1.apellido, ', ',ch1.nombre), concat('(',em1.razon_social,') ', ch1.apellido, ', ',ch1.nombre)) as chofer1
                                              ,interno, ch1.id_empleado, if (i_v = 'i', date_format(s.hllegada, '%H:%i'), date_format(s.hsalida, '%H:%i')) as horario, finalizada, cantpax, upper(ciudad) as city
                                      FROM ordenes o
                                      LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
                                      LEFT JOIN servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                                      LEFT JOIN turnos t on t.id = s.id_turno and t.id_estructura = s.id_estructura_turno
                                      left join tiposervicio ts on ts.id = s.id_tiposervicio and ts.id_estructura = s.id_estructura_tiposervicio
                                      LEFT JOIN empleadores em1 ON (em1.id = ch1.id_empleador)
                                      LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
                                      left join ciudades dest on dest.id = o.id_ciudad_destino and dest.id_estructura = o.id_estructura_ciudad_destino
                                      LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                                      LEFT JOIN unidades m ON (m.id = o.id_micro)
                                      WHERE (fservicio = '$fecha') and (not borrada) and (o.id_estructura = $_SESSION[structure])and ((o.id_cliente in ($_SESSION[modaf])) or (o.id_cliente_vacio in ($_SESSION[modaf])))
                                      order by horario";
                                 $con = conexcion();

                                 $query = mysql_query($sql, $con) or die(mysql_error($con));
                                 $row = mysql_fetch_array($query);
                                 $i=0;
				                 while($row){
                                             $tipo = $row['tipo'];
                                             while (($tipo == $row['tipo']) && ($row)){
                                                   $turno = $row['turno'];
                                                   print "<tr><td align='center' colspan='9'><hr><h2>".htmlentities("$row[tipo] - Turno $turno")."</h2></td></tr>";
                                                   while (($tipo == $row['tipo']) && ($row) && ($turno == $row['turno'])){
                                                      $i_v = $row['i_v'];
                                                      if ($i_v == 'i')
                                                         $mje = 'Llegada';
                                                      else
                                                          $mje = 'Salida';
                                                      print "<tr><td align='center' colspan='9'><h3>$mje</h3><hr></td></tr>
                                                                     <tr class='encab'>
                                                                     <td>Nro. Orden</td>
                                                                     <td>Destino</td>
                                                                     <td>Servicio</td>
                                                                     <td>Horario</td>
                                                                     <td>Interno</td>
                                                                     <td>Conductor</td>
                                                                     <td>Horario</td>
                                                                     <td>Pasajeros</td>
                                                                     <td>Finalizar</td>
                                                                     </tr>";
                                                      while (($tipo == $row['tipo']) && ($row) && ($turno == $row['turno']) && ($i_v == $row['i_v'])){
                                                         $class="impar";
                                                         if(($i%2) == 0)
                                                                   $class="par";
                                                         print "<tr class='$class'>
                                                                    <td>$row[id]</td>
                                                                    <td>".htmlentities($row[city])."</td>
                                                                    <td>".htmlentities($row[nombre])."</td>
                                                                    <td>$row[horario]</td>
                                                                    <td align='right'>$row[interno]</td>
                                                                    <td>".htmlentities($row[chofer1])."</td>";
                                                         if ($row['finalizada']){
                                                            print "<td>$row[horario]</td>
                                                                    <td>$row[cantpax]</td>
                                                                    <td></td>";
                                                         }
                                                         else{
                                                              $pax="$row[cantpax]";
                                                              if ($row['cantpax'] < 10){
                                                                 $pax="0$row[cantpax]";
                                                              }
                                                              print "<td><input type='text' size='5' class='hora' value='$row[horario]' id='hor-$row[id]'></td>
                                                                    <td><input type='text' size='5' value='$pax' id='pax-$row[id]' class='pass'></td>
                                                                    <td id='bt-$row[id]'><input type='button' value='Finalizar' id='$row[id]' title='$row[i_v]'></td>";
                                                              }
                                                         print "</tr>";
                                                         $row = mysql_fetch_array($query);
                                                         $i++;
                                                      }
                                                   }
                                             }
                                 }
                                 mysql_free_result($query);
                                 mysql_close($con);
                            ?>
                     </tbody>
              </table>
         </div>
</BODY>
</HTML>
