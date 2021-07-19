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
	if (isset($_POST['mostrar']))
  {
       $_SESSION['todas'] = $_POST['mostrar'];
  }
  
  if (isset($_POST['fecha']))
  {
        $fec = $_POST['fecha'];
        $fecha = explode("/", $fec);
        $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
        $fechaVis = $_POST['fecha'];
  }
  elseif(isset($_GET['fecha']))
  {
        $fec = $_GET['fecha'];
        $fecha = explode("/", $fec);
        $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
        $fechaVis = $_GET['fecha'];
  }
  else
  {
         $fecha = date("Y-m-d");
         $fechaVis = date("d/m/Y");
  }


  $orden="hcitacion";
  if (isset($_POST['order']) && ($_POST['order'] != ''))
  {
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

                 $("#table tbody").mouseover(function() {
                                                                     $(this).addClass("tr_hover");
                                                                    });
                 $("#table tbody tr").mouseout(function() {
                                                                     $(this).removeClass("tr_hover");
                                                                     });



                 $('#cargar').button();

                 $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                    



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

?>
    <br><br>
    <div id="result"></div>
    <fieldset class="ui-widget ui-widget-content ui-corner-all">

         <legend class="ui-widget ui-widget-header ui-corner-all">Ordenes de Trabajo</legend>
         <hr align="tr">
         <div>
         <form id="load" method="post">
              <div align="center"><button id="back">Un dia atras.</button><input id="fecha" name="fecha" value="<?php echo $fechaVis;?>" type="text" size="20"><button id="next">Un dia adelante.</button><input type="submit" id="cargar" name="cargar" class="button" value="Cargar Ordenes"></div>
              <input type="hidden" id="posx" name="posx">
              <input type="hidden" id="posy" name="posy" <?php if (isset($_POST['posy'])) print "value='$_POST[posy]'";?>>
              <input type="hidden" id="order" name="order" <?php if (isset($_POST['order']) && ($_POST['order'] != '')) print "value='$_POST[order]'";?>>
              
         </form>
         </div>

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
                                <th id="razon_social">Cliente</th>

                            </tr>
                     </thead>
                     <tbody>
                            <?php
                                 $sql = "SELECT o.id, finalizada, time_format(o.hcitacion, '%H:%i') as hcitacion, time_format(o.hsalida, '%H:%i') as hsalida, 
                                                date_format(o.hfinservicio, '%H:%i') as hfinserv, o.nombre as nombre, 
                                                if(em1.id = 1,concat(ch1.apellido, ', ',ch1.nombre), concat('(',em1.razon_social,') ', ch1.apellido, ', ',ch1.nombre)) as chofer1, 
                                                upper(c.razon_social) as razon_social, concat(ch2.apellido, ', ',ch2.nombre) as chofer2, interno, 
                                                ch1.id_empleado, ch2.id_empleado as id_empleado2, suspendida, checkeada, emp.color,
                                                date_format(o.hsalidaplantareal, '%H:%i') as hsalidaReal,
                                                date_format(o.hcitacionreal, '%H:%i') as hcitacionreal
                                                       FROM ordenes o
                                                       LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
                                                       LEFT JOIN empleadores em1 ON (em1.id = ch1.id_empleador)
                                                       LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
                                                       LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                                                       LEFT JOIN unidades m ON (m.id = o.id_micro)
                                                       LEFT JOIN empleadores emp ON (emp.id = m.id_propietario) and (emp.id_estructura = m.id_estructura_propietario)
                                                       WHERE (fservicio = '$fecha') and (not borrada) and (not suspendida) and (o.id_estructura = $_SESSION[structure]) and (finalizada in ($_SESSION[todas]))
                                            order by $orden";
         
                                 $query = mysql_query($sql, $con) or die(mysql_error($con));
                                 $body = "";

				                 while($row = mysql_fetch_array($query)){
					                        $body.="<tr style='height: 20px;'>
                                                       <td>$row[hcitacionreal]</td>
                                                       <td >$row[hsalidaReal]</td>
                                                       <td>$row[nombre]</td>
                                                       <td><a href='/vista/segvial/posint.php?int=$row[interno]' target='_blank' onClick=\"window.open(this.href, this.target, 'width=900,height=650'); return false;\"><div style='color:#$row[color];'>$row[interno]</div></a></td>
                                                       <td>$row[chofer1]</td>
                                                       <td>$row[chofer2]</td>
                                                       <td>$row[razon_social])</td>
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

		

         
         
         <div id='rrr'></div>
</BODY>

</HTML>
