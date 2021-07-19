<?php
     set_time_limit(0);
     error_reporting(0);
     session_start();
     include ('../../controlador/bdadmin.php');
     include('../paneles/viewpanel.php');
     include('../main.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);

     encabezado('Menu Principal - Sistema de Administracion - Campana');



     
     if (isset($_POST['fecha'])){
        $fecha = $_POST['fecha'];
      //  $fec = date_create($fecha);
      //  @date('w',$fec);
        $cargar=1;
     }
     else{
         $fecha = "";//date("Y-m-d");
         $diasemana = "";//date('w');
         $cargar=0;
     }
     
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
      
      $res_cc = mysql_query("SELECT cant_cond FROM estructuras WHERE id = $_SESSION[structure]", $con);
      if ($data_cc = mysql_fetch_array($res_cc)){
         $cantTripulacion = $data_cc[0];
      }
      ////// si no esta finalizado el diagrama setea una variable de session para no enviar el mail
      $sql = "SELECT * FROM estadoDiagramasDiarios e where fecha = '$fecha' and id_estado = 1";
      $result = mysql_query($sql, $con);
      if ($data = mysql_fetch_array($result)){
         $_SESSION['senmail'] = 1;
      }
      else{
          $_SESSION['senmail'] = 0;
      }
      //////////////////////////////////////////////////////////////////////////////////////////
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
            choferes.push(new Array(<?php echo $data['id_empleado'];?>,  '<?php echo  htmlentities($data[name]);?>'));
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
                 $('#delall').button().click(function(){
                                                        if (confirm("Seguro eliminar todas las ordenes del "+$('#fecha').val()+"?")) {
                                                           if (confirm("Las ordenes eliminadas no podran ser recuperadas. Eliminar de todas maneras?")) {
                                                              $.post("/modelo/ordenes/delall.php",{fecha: $('#fecha').val()}, function(data){$('#load').submit();});
                                                           }
                                                        }
                                                        });

                 $('.selcnd').on('change', function(){
                                                      var sel = $(this);
                                                      var nroCh = sel.data('nroch'); /// 1 chofer_1, 2 chofer_2, 3 tripulacion por orden
                                                      var orden = sel.data('orden');
                                                      var trip = sel.data('trip'); /// id tripulacion por oroden
                                                      var cond = sel.val();
                                                      change(nroCh, orden, trip, cond);
                                                    });

                 $('.selcch').on('change', function(){
                                                      var sel = $(this);
                                                      var orden = sel.data('orden');
                                                      var cche = sel.val();
                                                      changeOmnibus(orden, cche);
                                                    });  
                 $('.km').on('keypress', function(e){
                                                    if(e.which == 13) {
                                                          var inp = $(this);
                                                          $.post("/modelo/procesa/modordressur.php",
                                                                  {accion:'ckm', id:inp.data('orden'), km:inp.val()},
                                                                  function(data){
                                                                                  var result = $.parseJSON(data);
                                                                                  if (!result.ok)
                                                                                    alert('Se han producido errores');
                                                                  });
                                                    }   
                                                    }); 
                  $('.active').on('click', function(data){
                                                            var chk = $(this);
                                                            var ok = 0;
                                                            if (chk.is(':checked'))
                                                              ok = 1;
                                                            $.post("/modelo/procesa/modordressur.php",

                                                                  {accion:'stt', id:chk.data('orden'), st:ok},
                                                                  function(data){
                                                                                  var result = $.parseJSON(data);
                                                                                  if (!result.ok)
                                                                                    alert('Se han producido errores');
                                                                  });                                                            

                  });           

	});
      function change(nroCh, orden, trip, cond){

               $.post("/modelo/procesa/modordressur.php",{accion:'cnd', id:orden, nc:nroCh, txo:trip, ch:cond}, function(data){
                                                                                                                                var result = $.parseJSON(data);
                                                                                                                                if (!result.ok)
                                                                                                                                {  
                                                                                                                                  alert('Se han producido errores');
                                                                                                                                  alert(result.msge);
                                                                                                                                }
                                                                                                                              });
      }

      function changeOmnibus(orden, interno){

               $.post("/modelo/procesa/modordressur.php",{accion:'cche', id:orden, int:interno}, function(data){

                                                                                                                var result = $.parseJSON(data);
                                                                                                                if (!result.ok)
                                                                                                                  alert('Se han producido errores');
                                                                                                                });
      }      

	
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

     /*$ordenes = "SELECT o.id as orden, o.id_chofer_1 as id_chofer,if (date(citacion) < '$fecha', '00:00:00', citacion) as cita,
                        if (date(salida) < '$fecha', '00:00:00', salida) as sale,
                        if (date(finalizacion) > '$fecha', '23:59:00', finalizacion) as fina,
                        if (date(llegada) > '$fecha', '23:59:00', llegada) as llega, id_cliente, id_estructura_cliente, id_micro, nombre as nomOrden,
                        id_ciudad_destino, id_estructura_ciudad_destino, 1 as conOrd, 0 as trip, o.km, o.borrada,

                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(finalizacion)) and (not suspendida) and (o.id_estructura = 2)
                  UNION
                  SELECT o.id, o.id_chofer_2,if (date(citacion) < '$fecha', '00:00:00', citacion),
                        if (date(salida) < '$fecha', '00:00:00', salida),
                        if (date(finalizacion) > '$fecha', '23:59:00', finalizacion),
                        if (date(llegada) > '$fecha', '23:59:00', llegada), id_cliente, id_estructura_cliente, id_micro, nombre,
                        id_ciudad_destino, id_estructura_ciudad_destino, 2 as conOrd, 0 as trip, o.km, o.borrada
                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(finalizacion)) and (not suspendida) and (o.id_estructura = 2)
                  union
                  SELECT o.id, id_empleado,if (date(citacion) < '$fecha', '00:00:00', citacion),
                        if (date(salida) < '$fecha', '00:00:00', salida),
                        if (date(finalizacion) > '$fecha', '23:59:00', finalizacion),
                        if (date(llegada) > '$fecha', '23:59:00', llegada), id_cliente, id_estructura_cliente, id_micro, nombre,
                        id_ciudad_destino, id_estructura_ciudad_destino, 3 as conOrd, txo.id as trip, o.km, o.borrada
                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  INNER JOIN tripulacionXOrdenes txo ON txo.id_orden = o.id AND txo.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(finalizacion)) and (not suspendida) and (o.id_estructura = 2)";*/     

     $ordenes = "SELECT o.id as orden, o.id_chofer_1 as id_chofer, id_cliente, id_estructura_cliente, id_micro, nombre as nomOrden,
                        id_ciudad_destino, id_estructura_ciudad_destino, 1 as conOrd, 0 as trip, o.km, o.borrada,
                        date_format(salida, '%d/%m/%Y %H:%i') as sale, date_format(llegada, '%d/%m/%Y %H:%i') as llega

                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(finalizacion)) and (not suspendida) and (o.id_estructura = 2)
                  UNION
                  SELECT o.id, o.id_chofer_2, id_cliente, id_estructura_cliente, id_micro, nombre,
                        id_ciudad_destino, id_estructura_ciudad_destino, 2 as conOrd, 0 as trip, o.km, o.borrada,
                        date_format(salida, '%d/%m/%Y %H:%i') as sale, date_format(llegada, '%d/%m/%Y %H:%i') as llega
                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(finalizacion)) and (not suspendida) and (o.id_estructura = 2)
                  union
                  SELECT o.id, id_empleado, id_cliente, id_estructura_cliente, id_micro, nombre,
                        id_ciudad_destino, id_estructura_ciudad_destino, 3 as conOrd, txo.id as trip, o.km, o.borrada,
                        date_format(salida, '%d/%m/%Y %H:%i') as sale, date_format(llegada, '%d/%m/%Y %H:%i') as llega
                  FROM ordenes o
                  INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id and hhs.id_estructura_orden = o.id_estructura
                  INNER JOIN tripulacionXOrdenes txo ON txo.id_orden = o.id AND txo.id_estructura_orden = o.id_estructura
                  WHERE ('$fecha' between date(citacion) and date(finalizacion)) and (not suspendida) and (o.id_estructura = 2)";  

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
                            if ($cargar)
                            {
                                 $ordenes = "SELECT llega, orden, nomOrden, sale, 
                                                    interno, razon_social, concat(apellido,', ',nombre) as chofer, id_empleado,
                                                    c.id as id_cli, des.id as id_city, des.ciudad, o.orden as id, o.id_micro, ch.id_empleado, o.conOrd, o.trip, o.km, o.borrada
                                             FROM ($ordenes) o
                                             LEFT JOIN ciudades des ON des.id = o.id_ciudad_destino and des.id_estructura = o.id_estructura_ciudad_destino
                                             LEFT JOIN empleados ch ON (ch.id_empleado = o.id_chofer)
                                             LEFT JOIN unidades m ON (m.id = o.id_micro)
                                             LEFT JOIN obsSupervisores os ON os.id_orden = o.orden
                                             LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                                             order by orden, conOrd";
                               //  die($ordenes);
                                 $con = conexcion();
                                 $query = mysql_query($ordenes, $con)or die(mysql_error($con));
                                 $row = mysql_fetch_array($query);
                                 if ($row){
                                    echo "<div align='right'><h2><input id='delall' type='button' class='button' value='Eliminar Diagrama Completo'></h2></div>";
                                    echo "<div align='center'><h2>".$dias[$row['diasem']]."</h2></div>";
                                 }
                                 print "<table border='0' width='100%'>";
                                 $stcliente = "Estilo1";
                                 $stdestino = "Estilo2";
                                 $encabe_serv = "Estilo3";
                                 $servi = "";


                                 $td = "<td class='$encabe_serv'>Servicio</td>
                                        <td class='$encabe_serv'>H. Salida</td>
                                        <td class='$encabe_serv'>H. llegada</td>
                                        <td class='$encabe_serv'>Km</td>
                                        <td class='$encabe_serv'>Interno</td>";
                                  for ($i=1; $i <= $cantTripulacion; $i++) { 
                                        $td.="<td class='$encabe_serv'>Conductor $i</td>";
                                  }
                                  $td.="<td class='$encabe_serv'>Desactivada?</td>";
                                 while($row)
                                 {
                                             $cliente = $row['id_cli'];
                                             print "<tr><td colspan='10' class='$stcliente' align='center'>$row[razon_social]</td></tr>";
                                             while (($row) &&($cliente == $row['id_cli']))
                                             {
                                                   $destino = $row['id_city'];
                                                   print "<tr><td colspan='9' class='$stdestino' align='center'>".utf8_decode($row['ciudad'])."</td></tr>";
                                                   print "<tr>
                                                          $td";
                                                   while (($row) &&($cliente == $row['id_cli'])&&($destino == $row['id_city']))
                                                   {
                                                        $id = $row['id'];
                                                        $check = '';
                                                        if ($row['borrada'] == 1)
                                                              $check = 'checked';
                                                        print "<tr class='celda'>
                                                               <td class='$servi'><div class='$servi' id='nombre-$id'>".utf8_decode($row['nomOrden'])."</div></td>
                                                                <td class='$servi'><div class='$servi $hora' id='hcitacion-$id'>$row[sale]</div></td>
                                                                <td class='$servi'><div class='$servi $hora' id='hsalida-$id'>$row[llega]</div></td>
                                                                <td class='$servi'><input type='text' data-orden='$id' class='km' value='$row[km]' size='4' title='presione enter para modificar'></td>
                                                                <td class='$servi'>
                                                                    <select class='selcch' align='center' onFocus='cargarComboMicros(this)' data-orden='$id'>
                                                                    <option value='$row[id_micro]'><div align='center'>$row[interno]</div></option>
                                                                    </select>
                                                                </td>";      
                                                        $j=0;                                                  
                                                        while (($row) && ($cliente == $row['id_cli'])&&($destino == $row['id_city'])&&($id == $row['id']))
                                                        {
                                                              print "<td class='$servi'>
                                                                        <select class='selcnd' onFocus='cargarComboChoferes(this)' data-orden='$id' 
                                                                        data-nroch='$row[conOrd]' data-trip='$row[trip]'>
                                                                            <option value='$row[id_empleado]'>".htmlentities($row[chofer])."</option>
                                                                        </select>
                                                                    </td>";
                                                              $j++;
                                                          $row = mysql_fetch_array($query);
                                                        }

                                                        for ($i = $j; $i < $cantTripulacion; $i++){
                                                          print "<td class='$servi'>
                                                                        <select class='selcnd' onFocus='cargarComboChoferes(this)' data-orden='$id' data-nroch='3' data-trip='0'>                                                        
                                                                        </select>
                                                                    </td>";
                                                        }
                                                         print "<td class='$servi'><input class='active' type='checkbox' $check data-orden='$id'/></td>
                                                                </tr>";
                                                                                                                 
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