<?php
     session_start();
     include ('../../controlador/bdadmin.php');
     include('../paneles/viewpanel.php');
     include('../main.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);

     encabezado('Menu Principal - Sistema de Administracion - Campana');

        $cargar=1;

     
     $dias = array(1 =>'DOMINGO', 2 =>'LUNES', 3 =>'MARTES', 4 =>'MIERCOLES', 5 =>'JUEVES', 6 =>'VIERNES', 7 =>'SABADO');

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
      
      ////// carga el combo con los turnos ////////////////////////////////////////////////////////
      $sql = "SELECT id, turno FROM turnos  where id_estructura = $_SESSION[structure]";
      $result = mysql_query($sql, $con);
      $turnos = "<select id='turnos'>";
      while ($data = mysql_fetch_array($result)){
            $turnos.="<option value='$data[0]'>$data[1]</option>";
      }
      $turnos.="</select>";
      //////////////////////////////////////////////////////////////////////////////////////////
      
      ////// carga el combo con los tipos de servicio ////////////////////////////////////////////////
      $result = mysql_query('SELECT id, tipo FROM tiposervicio WHERE id = $_SESSION[structure]', $con);
      $tipos = "<select id='tipos'>";
      while ($data = mysql_fetch_array($result)){
            $tipos.="<option value='$data[0]'>$data[1]</option>";
      }
      $tipos.="</select>";
      ///////////////////////////////////////////////////////////////////////////////////////////////////
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
                 $('#delall').button().click(function(){
                                                        if (confirm("Seguro eliminar todas las ordenes del "+$('#fecha').val()+"?")) {
                                                           if (confirm("Las ordenes eliminadas no podran ser recuperadas. Eliminar de todas maneras?")) {
                                                              $.post("/modelo/ordenes/delall.php",{fecha: $('#fecha').val()}, function(data){$('#load').submit();});
                                                           }
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
               $.post("/modelo/procesa/modordres.php",{id:obj, value:val}, function(data){
                                                                                          if (data == "1"){
                                                                                             if (val == 1){
                                                                                                var elem = obj.split("-");
                                                                                                $.post("/modelo/enviomail/sendordbja.php",{orden: elem[1]},function(data){});
                                                                                             }
                                                                                          }
                                                                                          });
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
.celda{
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #333333;
	background-color:#FFFFC0;
	font-size:11px;
}
.celda:hover{
   background-color:#B0B0B0;
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
               <?php if ($_SESSION['userid'] == 18) print "OJO DONDE METES LOS GARFIOS!!!!<br>";?>
              <div align="center"><input id="fecha" name="fecha" value="<?php echo $fecha;?>" type="text" size="30"><input type="submit" id="cargar" name="cargar" class="button" value="Cargar Ordenes"></div>
         </form>
         </div>
         <hr align="tr">

         <div id="tablaordenes">
                            <?php
                            if ($cargar){

                                 $con = conexcion();
                                 $query = mysql_query("select razon_social,
                                                              c.nombre,
                                                              date_format(hcitacion, '%H:%m') as hcitacion,
                                                              date_format(hsalida, '%H:%m') as hsalida,
                                                              date_format(hllegada, '%H:%m') as hllegada,
                                                              date_format(hfinserv, '%H:%m') as hfinserv,
                                                              i_v,
                                                              t.id as id_turno,
                                                              t.turno as turno,
                                                              ts.id as id_tiposrv,
                                                              ts.tipo as tipo
                                                       from (select * from servicios where id_estructura = $_SESSION[structure]) s
                                                       inner join (select * from cronogramas where id_estructura = $_SESSION[structure]) c on c.id = s.id_cronograma
                                                       inner join (select * from clientes where id_estructura = $_SESSION[structure]) cl on cl.id = c.id_cliente
                                                       left join (select * from turnos where id_estructura = $_SESSION[structure]) t on t.id = s.id_turno
                                                       left join (select * from tiposervicio where id_estructura = $_SESSION[structure]) ts on ts.id = s.id_tiposervicio
                                                       order by cl.razon_social, hcitacion", $con);
                                 $row = mysql_fetch_array($query);
          $tabla = "<table id='example' align='center' border='0' width='100%'>
                     <thead>
            	            <tr>
                                <th>Legjo</th>
                                <th>Apellido, Nombre</th>
                                <th>DNI</th>
                                <th>Empleador</th>
                                <th>Afectado a...</th>
                                <th>Puesto</th>
                            </tr>
                     </thead>
                     <tbody>";
                                 while($row){
                                             $cliente = $row['id_cli'];
                                             print "<tr><td colspan='9' class='$stcliente' align='center'>$row[razon_social]</td></tr>";
                                             while (($row) &&($cliente == $row['id_cli'])){
                                                   $destino = $row['id_city'];
                                                   print "<tr><td colspan='9' class='$stdestino' align='center'>".utf8_decode($row['ciudad'])."</td></tr>";
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
					                                     if ($row['borrada'] == 1)
                                                            $check = 'checked';
					                                     print "<tr class='celda'>
                                                                    <td class=\"$servi\"><div class=\"$servi\" id=\"id-$id\">$id</div></td>
                                                                    <td class=\"$servi\"><div class=\"$servi\" id=\"nombre-$id\">".utf8_decode($row['nombre'])."</div></td>
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
                                 }
                            ?>
         </div>
</BODY>
</HTML>
