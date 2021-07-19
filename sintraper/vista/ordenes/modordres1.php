<?php
     session_start();
     include ('../../controlador/bdadmin.php');
     include('../paneles/viewpanel.php');
     include('../main.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);

     encabezado('Menu Principal - Sistema de Administracion - Campana');



     
     if (isset($_POST['fecha'])){
        $fecha = $_POST['fecha'];
        $fec = date_create($_POST['fecha']);
     }
     else{
         $fecha = date("Y-m-d");
         $fec = $fecha;
     }

     $diasemana = date("w", $fec);
     $dias = array('DOMINGO', 'LUNES', 'MARTES', 'MIERCOLES', 'JUEVES', 'VIERNES', 'SABADO');

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
 <script>
    var micros=new Array();
    micros.push(new Array(0,'A/D'));
    var choferes=new Array();
    choferes.push(new Array(0,'A Designar'));
    <?php
      $con = conexcion();

      $result = mysql_query('SELECT id, interno FROM unidades WHERE (id_estructura = '.STRUCTURED.') and (activo) ORDER BY interno', $con);
      $i=0;

      while ($data = mysql_fetch_array($result)){?>
            micros.push(new Array(<?php echo $data['id'];?>,  <?php echo $data['interno'];?>));
      <?php
           $i++;
      }
      $result = mysql_query("SELECT id_empleado, if(em.id = 1,upper(concat(legajo,' - ',e.apellido, ', ',e.nombre)), upper(concat(legajo,' - (',em.razon_social,') ', e.apellido, ', ',e.nombre)))as name FROM empleados e inner join empleadores em on em.id = e.id_empleador WHERE (e.id_estructura = $_SESSION[structure]) and (e.activo) and (id_cargo = 1) and (not borrado) ORDER BY apellido", $con);
      $i=0;
      while ($data = mysql_fetch_array($result)){?>
            choferes.push(new Array(<?php echo $data['id_empleado'];?>,  '<?php echo  htmlentities("$data[name]");?>'));
      <?php
           $i++;
      }
      
      mysql_close($con);

?>
    var element;
    var campo;
	$(function() {

                 $("#table").tablesorter({widgets: ['zebra']});
                 $(".fin").css("background-color", "#FF8080");

                 $('#cargar').button();
                 $.mask.definitions['~']='[012]';
                 $.mask.definitions['%']='[012345]';
                 $(".hora").mask("~9:%9",{completed:function(){}});
                 $('#fecha').datepicker({dateFormat:'yy-mm-dd'});
                 $('#send-order').datepicker({dateFormat:'yy-mm-dd', showOn: 'both'});
                 $('input:submit').button();
                 $('#sendorderatdate').button();
                 $(".modkm").keypress(function(event){
                                                      if ( event.which == 13 ){
                                                                                var key = $(this).attr('id');
                                                                                var val = $(this).val();
                                                                                alert(val);
                                                                                $.post("/modelo/ordenes/modordres.php",{id: key, value: val}, function(data){alert(data);});
                                                                               }
                                                      });

	});
	
     function cargarComboMicros(obj){
               var i;
               if (obj.options.length < 5)
               for (i = 1; i <= micros.length; i++){
                   var elOptNew = document.createElement('option');
                   elOptNew.text = micros[i-1][1];
                   elOptNew.value = micros[i-1][0];
                   obj.options.add(elOptNew, i);
               }
      }
      
      function cargarComboChoferes(obj){
               var i;
               if (obj.options.length < 5)
               for (i = 1; i <= choferes.length; i++){
                   var elOptNew = document.createElement('option');
                   elOptNew.text = choferes[i-1][1];
                   elOptNew.value = choferes[i-1][0];
                   obj.options.add(elOptNew, i);
               }
      }
      
      function change(obj){
               var val = $('#'+obj+' option:selected').val();
               $.post("/modelo/procesa/modordres.php",{id:obj, value:val});
      }
      
      function delend(val, obj){
               $.post("/modelo/procesa/modordres.php",{id:obj, value:val});
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

.Estilo2 {	color: #000000;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-weight: bold;

}

.Estilo1 {
	font-size: 18px;
	font-weight: bold;
	font-family:Verdana, Arial, Helvetica, sans-serif;
	background-color:#B0B0B0;
}
.Estilo3 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #333333;
	font-weight: bold;
	font-size:11px;
	background-color:#FFFFC0;
}
.Estilo4 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #333333;
	background-color:#FFFFC0;
	font-size:11px;
}

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
              <div align="center"><input id="fecha" name="fecha" value="<?php echo $fecha;?>" type="text" size="30"><input type="submit" id="cargar" name="cargar" class="button" value="Cargar Ordenes"></div>
         </form>
         </div>
         <hr align="tr">

         <div id="tablaordenes">
                            <?php
                                 echo "d: $diasemana";
                                 $con = conexcion();
                                 $query = mysql_query("SELECT o.id, finalizada, o.borrada, o.km, date_format(o.hcitacion, '%H:%i') as hcitacion, date_format(o.hsalida, '%H:%i') as hsalida, o.nombre, concat(ch1.apellido, ', ',ch1.nombre) as chofer1, upper(c.razon_social) as razon_social, concat(ch2.apellido, ', ',ch2.nombre) as chofer2, comentario, interno, c.id as id_cli, ori.ciudad, ori.id as id_city, m.id as id_micro, ch1.id_empleado as id_emple_1
                                                       FROM ordenes o
                                                       left join servicios s on (s.id = o.id_servicio) and (s.id_estructura = o.id_estructura_servicio)
                                                       left join cronogramas cr on (cr.id = s.id_cronograma) and (cr.id_estructura = s.id_estructura_cronograma)
                                                       left join ciudades ori on ((cr.ciudades_id_origen = ori.id) and (cr.ciudades_id_estructura_origen = ori.id_estructura))
                                                       left join ciudades d on ((cr.ciudades_id_destino = d.id) and (cr.ciudades_id_estructura_destino = d.id_estructura))
                                                       LEFT JOIN empleados ch1 ON ((ch1.id_empleado = o.id_chofer_1) and (ch1.id_estructura = o.id_estructura_chofer1))
                                                       LEFT JOIN empleados ch2 ON ((ch2.id_empleado = o.id_chofer_2) and (ch2.id_estructura = o.id_estructura_chofer2))
                                                       LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                                                       LEFT JOIN unidades m ON (m.id = o.id_micro)
                                                       WHERE (fservicio = '$fecha') and  (o.id_estructura = $_SESSION[structure]) and (not borrada)
                                                       ORDER BY c.razon_social, ori.ciudad, o.nombre, o.hcitacion", $con);
                                 $row = mysql_fetch_array($query);
                                 print "<table border='0' width='100%'>";
                                 $stcliente = "Estilo1";
                                 $stdestino = "Estilo2";
                                 $encabe_serv = "Estilo3";
                                 $servi = "Estilo4";
                                 while($row){
                                             $cliente = $row['id_cli'];
                                             print "<tr><td colspan='9' class='$stcliente' align='center'>$row[razon_social]</td></tr>";
                                             while (($row) &&($cliente == $row['id_cli'])){
                                                   $destino = $row['id_city'];
                                                   print "<tr><td colspan='9' class='$stdestino' align='center'>".htmlentities($row['ciudad'])."</td></tr>";
                                                   print "<tr>
                                                              <td class=\"$encabe_serv\">Orden</td>
                                                              <td class=\"$encabe_serv\">Servicio</td>
                                                              <td class=\"$encabe_serv\">H. Citacion</td>
                                                              <td class=\"$encabe_serv\">H. Salida</td>
                                                              <td class=\"$encabe_serv\">Km</td>
                                                              <td class=\"$encabe_serv\">Interno</td>
                                                              <td class=\"$encabe_serv\">Chofer</td>
                                                              <td class=\"$encabe_serv\">Desactivar</td>
                                                          </tr>";
                                                   while (($row) &&($cliente == $row['id_cli'])&&($destino == $row['id_city'])){
					                                     $id = $row['id'];
					                                     $check = '';
					                                     if ($row['borrada'])
                                                            $check = 'checked';
					                                     print "<tr>
                                                                    <td class=\"$servi\"><div class=\"$servi\" id=\"id-$id\">$id</div></td>
                                                                    <td class=\"$servi\"><div class=\"$servi\" id=\"nombre-$id\">".htmlentities($row['nombre'])."</div></td>
                                                                    <td class=\"$servi\"><div class=\"$servi $hora\" id=\"hcitacion-$id\">$row[hcitacion]</div></td>
                                                                    <td class=\"$servi\"><div class=\"$servi $hora\" id=\"hsalida-$id\">$row[hsalida]</div></td>
                                                                    <td class=\"$servi\"><input type='text' class='modkm' id=\"km-$id\" value='$row[km]' size='4' title='presione enter para modificar'></td>
                                                                    <td class=\"$servi\">
                                                                        <select id=\"id_micro-$id\" align=\"center\" onFocus=\"cargarComboMicros(this)\" onchange=\"change('id_micro-$id')\">
				                                                                <option value=\"$row[id_micro]\"><div align=\"center\">$row[interno]</div></option>
                                                                        </select>
                                                                    </td>
                                                                    <td class=\"$servi\">
                                                                        <select id=\"id_chofer_1-$id\" onFocus=\"cargarComboChoferes(this)\" onchange=\"change('id_chofer_1-$id')\">
				                                                                <option value=\"$row[id_emple_1]\"><div align=\"center\">$row[chofer1]</div></option>
                                                                        </select>
                                                                    </td>
                                                                    <td class=\"$servi\"><input type=\"checkbox\" $check onchange=\"{var val=0;if(this.checked){val=1;};delend(val, 'borrada-$id');}\"></td>
                                                                </tr>";
                                                         $row = mysql_fetch_array($query);
                                                   }
                                             }
                                 }
                                 print "</table>";
                                 mysql_free_result($query);
                                 mysql_close($con);
                            ?>
         </div>
</BODY>
</HTML>
