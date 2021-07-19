<?php
       session_start();
       set_time_limit(0);
     include( 'GoogChart.class.php' );
     include( 'bdadmin.php' );
     include( 'dateutils.php' );
     include('./main.php');
     include('./core_chart.php');
     //define(RAIZ, '');
   //  define(STRUCTURED, 1);
?>

<?php

     encabezado('Menu Principal - Sistema de Administracion - Campana');
       $origen=0;
       $destino=0;
       $servicio=0;
        if (isset($_POST['city_origen'])){
           $origen = $_POST['fcity_origen'];
        }
        if (isset($_POST['city_destino'])){
           $destino = $_POST['fcity_destino'];
        }
        if (isset($_POST['servicios'])){
           $servicio = $_POST['fservicios'];
        }
        if (isset($_POST['cargar'])){

        $avanzada = $_POST['avanzada']?'1':'0';
        $fil_city = isset($_POST['city_origen'])?'1':'0';
        $city_origen = $_POST['fcity_origen'];
        $i_v = $_POST['mostrar'];
        $fil_srv = isset($_POST['servicios'])?'1':'0';
        $servicio = $_POST['fservicios'];
        $title = "Graficos de Confiabilidad";
        $desde = dateToMysql($_POST['desde'], '/');
        $hasta = dateToMysql($_POST['hasta'], '/');
        }

?>

   <link type="text/css" href="../vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="../vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="../vista/css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="../vista/css/jquery.dataTables.css" rel="stylesheet" />
    
<script type="text/javascript" src="../vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="../vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="../vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="../vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="../vista/js/jquery.ui.datepicker-es.js"></script>

  <script type="text/javascript" src="../vista/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>


<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }

.small.button, .small.button:visited {
font-size: 11px ;
}

input.text { margin-bottom:12px; width:95%; padding: .4em; }
#newuda .error{
	font-size:0.8em;
	color:#ff0000;
}

</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#desde,#hasta').datepicker({dateFormat:'dd/mm/yy'});
                                                       $('#cargar').button();
                                                       $( "#center, #vuelta" ).tabs();

                                                       <?php
                                                            if ($origen){
                                                               print "\$('#fcity_origen option[value=$origen]').attr('selected', 'selected');
                                                                       \$('#city_origen').prop( 'checked', true );";
                                                            }
                                                            else{
                                                                 print "\$('#city_origen').prop( 'checked', false );
                                                                        \$('#fcity_origen').attr('disabled', 'disabled');";
                                                            }
                                                            if ($destino){
                                                               print "\$('#fcity_destino option[value=$destino]').attr('selected', 'selected');
                                                                       \$('#city_destino').prop( 'checked', true );";
                                                            }else{
                                                                 print "\$('#city_destino').prop( 'checked', false );
                                                                        \$('#fcity_destino').attr('disabled', 'disabled');";
                                                            }

                                                            if (isset($_POST['cargar'])){   ?>
                                                       
                                                             $("#tipo option[value='<?php echo $_POST['tipo'];?>']").attr('selected', 'selected');
                                                             $('#tipo, #turno, #clis, #fcity_destino, #fcity_origen').selectmenu({width: 350});
                                                       
                                                       <?php  } else {


                                                            //  print "\$('#tipo option[value=$_POST[tipo]]').attr('selected', 'selected');";

                                                              print "\$('#tipo, #turno, #clis, #fcity_destino, #fcity_origen').selectmenu({width: 350});";

                                                       } ?>
                                                       
   $.post('./load_data.php',{accion:'lcd', trn:$('#turno').val(), tpo: $('#tipo').val()},function(data){$('#cronos').html(data);}).done(function() {
    <?php
                                                            if ($servicio){
                                                               print "\$('#fservicios option[value=$servicio]').attr('selected', 'selected');
                                                                      \$('#servicios').prop( 'checked', true );
                                                                      \$('#fservicios').selectmenu({width: 350});";
                                                            }
                                                            else{
                                                                 print "\$('#servicios').prop( 'checked', false );
                                                                        \$('#fservicios').attr('disabled', 'disabled');
                                                                        \$('#fservicios').selectmenu({width: 350});";
                                                            }
    ?>
  });

  $('.city').click(function(){
                                  var id = $(this).attr('id');
                                  if( $(this).attr('checked')){
                                      $("#f"+id).removeAttr('disabled');
                                  }
                                  else{
                                      $("#f"+id).prop('disabled', 'disabled');
                                  }
                                  $(".selec").selectmenu({width: 350});
                                  });

                                  
  $('#servicios').click(function(){
                                    if( $(this).attr('checked')){
                                        $("#fservicios").removeAttr('disabled');
                                        $("#fcity_origen, #fcity_destino").prop('disabled', 'disabled');
                                    }
                                    else{
                                         $("#fservicios").prop('disabled', 'disabled');
                                    }
                                    $("#fservicios").selectmenu({width: 350});
                                    });

  $('#tipo').change(function(){
                               var check = $("#servicios").prop( "checked");
                               $.post('./load_data.php', {accion:'ltno', tis:$(this).val(), cliente: $("#clis").val()},
                                                                                            function(data){
                                                                                                           $('#divturno').html(data);
                                                                                                           $("#fturnos").selectmenu({width: 350});
                                                                                                           }).done(function(){ loadSrv();});
                               });
  $('#fcity_origen').change(function(){loadSrv();});
  $.post('./load_data.php', {cliente:$("#clis").val(), tis: $("#tipo").val(), accion:'ltno'}, function(data){
                                                                                                              $("#divturno").html(data);
                                                                                                              $("#fturnos").selectmenu({width: 350});
                                                                                                              $('#fturnos').change(function(){
                                                                                                                                              loadSrv();
                                                                                                                                              });
                                                                                                              });

   $("input:checkbox").on("click", function(){loadSrv();});
   $("input:radio[name=mostrar]").click(function(){loadSrv();});
   <?php if (!$avanzada) print '$("#tablaavanzada").toggle();';   ?>
   $("#bavanzada").button().click(function(){
                                             $("#tablaavanzada").toggle(function(){
                                                                                   if ($("#tablaavanzada").is(":visible") == true){
                                                                                      $("#bavanzada").val('Busqueda Estandar');
                                                                                      $("#avanzada").val('1');
                                                                                   }
                                                                                   else{
                                                                                       $("#bavanzada").val('Busqueda Avanzada');
                                                                                       $("#avanzada").val('0');
                                                                                   }
                                                                                   });
                                             $("#br").html("<br>");
                                             });


});

function loadSrv(){
         var cl = $("#clis").val();
         var ts = $("#tipo").val();
         var cco=0;
         if ($("#city_origen").prop('checked'))
            cco=1;
         var co = $("#fcity_origen").val();
         var iv = $("input:radio[name=mostrar]:checked").val();
         var tn = $('#fturnos').val();
         $.post('./load_data.php', {accion:'lcf', cli:cl, t_s:ts, tno:tn, cho: cco, cor: co, i_v:iv}, function(data){
                                                                                                                      var check = $("#servicios").prop( "checked");
                                                                                                                      $("#cronos").html(data);
                                                                                                                      if (!check)
                                                                                                                         $("#fservicios").prop('disabled', 'disabled');
                                                                                                                      $("#fservicios").selectmenu({width: 350});
                                                                                                                      });
}
</script>



<body>
<?php
     menu();

     $chcnf = "";
     $chefc = "checked";
     if (isset($_POST['cargar'])){

        $avanzada = $_POST['avanzada']?'1':'0';
        $fil_city = isset($_POST['city_origen'])?'1':'0';
      //  die("filtro $fil_city");
        $city_origen = $_POST['fcity_origen'];
        $i_v = $_POST['mostrar'];
        $chi="";
        $chv="";
        $chiv="";
        if ($i_v == 'i')
           $chi="checked";
        elseif($i_v == 'v')
           $chv="checked";
        else
            $chiv="checked";
        $fil_srv = isset($_POST['servicios'])?'1':'0';
        $servicio = $_POST['fservicios'];
        $title = "Graficos de Confiabilidad";
        $desde = dateToMysql($_POST['desde'], '/');
        $hasta = dateToMysql($_POST['hasta'], '/');
        $t_serv = $_POST['tipo'];
        $turno =  $_POST['fturnos'];
        $tg = "ADMINISTRACION";
        if ($t_serv == 2)
           $tg = "PRODUCCION";
        if ($turno == 1)
           $tg.=" TURNO MAÑANA";
        elseif ($turno == 2){
               $tg.=" TURNO TARDE";
        }
        else
            $tg.=" TURNO NOCHE";
           
        if ($_POST['cnf'] == 'cnf'){
           $title = "Graficos de Confiabilidad";
           $chcnf = "checked";
           $chefc = "";
           $titulo = titulo($servicio,$city_origen,$city_origen, $avanzada,$i_v, $fil_srv, $fil_city, $t_serv, $turno);
          // die("$avanzada");
           $idvta = $i_v;
           if ($i_v == 'iv')
              $idvta='i';
           if ($avanzada){    //se activa la busqueda avanzada
              if ($fil_srv){  //solo se filtra un servicio en particular por ende solo se genera un solo grafico
                 $soloEntrada = true;
                 $datosIda = graphConfiabilidad($_POST['desde'], $_POST['hasta'], $origen, $destino, $servicio, $t_serv, $turno, $idvta,$avanzada, $fil_city, $city_origen, $i_v, $fil_srv, $servicio);
                 $tablaEntrada = $datosIda[1];
                 $datos = $datosIda[0];
              }
              else{
                   if ($i_v == 'iv'){
                     // die("avanzada IV");
                      $soloEntrada = false;
                      $datosIda = graphConfiabilidad($_POST['desde'], $_POST['hasta'], $origen, $destino, $servicio, $t_serv, $turno, 'i',$avanzada, $fil_city, $city_origen, 'i', $fil_srv, $servicio);
                      $tablaEntrada = $datosIda[1];
                      $datos = $datosIda[0];
                      
                      $datosVta = graphConfiabilidad($_POST['desde'], $_POST['hasta'], $origen, $destino, $servicio, $t_serv, $turno, 'v', $avanzada, $fil_city, $city_origen, 'v', $fil_srv, $servicio);
                      $tablaSalida = $datosVta[1];
                      $datosvta = $datosVta[0];
                   }
                   else{
                       $soloEntrada = true;
                      $datosIda = graphConfiabilidad($_POST['desde'], $_POST['hasta'], $origen, $destino, $servicio, $t_serv, $turno, $i_v,$avanzada, $fil_city, $city_origen, $i_v, $fil_srv, $servicio);
                      $tablaEntrada = $datosIda[1];
                      $datos = $datosIda[0];
                   }
              }
           }
           else{
                      $datosIda = graphConfiabilidad($_POST['desde'], $_POST['hasta'], $origen, $destino, $servicio, $t_serv, $turno, 'i',$avanzada, $fil_city, $city_origen, 'i', $fil_srv, $servicio);
                      $tablaEntrada = $datosIda[1];
                      $datos = $datosIda[0];

                      $datosVta = graphConfiabilidad($_POST['desde'], $_POST['hasta'], $origen, $destino, $servicio, $t_serv, $turno, 'v', $avanzada, $fil_city, $city_origen, 'v', $fil_srv, $servicio);
                      $tablaSalida = $datosVta[1];
                      $datosvta = $datosVta[0];
           }

            //flag para indicar si se debe graficar la vuelta tambien
           if (($avanzada && $iv == 'iv') || (!$avanzada)){ //se grafican los graficos de ida y vuelta



              $tg = $tg;
           }
           else{
                $tg = $titulo;
           }
           
           $chartIda = createFunctionChart("drawChartVta", $datosvta, "egr", "Salida de Planta");
           $chartVta = createFunctionChart("drawChart", $datos, "ing", "");
           print "<script type=\"text/javascript\">
                          google.load(\"visualization\", \"1\", {packages:[\"corechart\"]});
                          google.setOnLoadCallback(drawChart);
                          google.setOnLoadCallback(drawChartVta);
                          $chartIda
                          $chartVta
          </script>";
        }
        else{
           $chcnf = "";
           $chefc = "checked";
           $title = "Graficos de Eficiencia";
           
           $titulo = titulo($servicio,$city_origen,$city_origen, $avanzada,$i_v, $fil_srv, $fil_city, $t_serv, $turno);
           
           if ($avanzada){    //se activa la busqueda avanzada
              if ($fil_srv){  //solo se filtra un servicio en particular por ende solo se genera un solo grafico
                  $soloEntrada = true;
                  $datosIda = graphEficiencia($_POST['desde'], $_POST['hasta'], $origen, $destino, $servicio, $t_serv, $turno, $i_v,$avanzada, $fil_city, $city_origen, $i_v, $fil_srv, $servicio);
                  $tablaEntrada = $datosIda[1];
                  $datos = $datosIda[0];
              }
              else{
                   if ($i_v == 'iv'){
                     // die("avanzada IV");
                      $soloEntrada = false;
                      $datosIda = graphEficiencia($_POST['desde'], $_POST['hasta'], $origen, $destino, $servicio, $t_serv, $turno, 'i',$avanzada, $fil_city, $city_origen, 'i', $fil_srv, $servicio);
                      $tablaEntrada = $datosIda[1];
                      $datos = $datosIda[0];

                      $datosVta = graphEficiencia($_POST['desde'], $_POST['hasta'], $origen, $destino, $servicio, $t_serv, $turno, 'v',$avanzada, $fil_city, $city_origen, 'v', $fil_srv, $servicio);
                      $tablaSalida = $datosVta[1];
                      $datosvta = $datosVta[0];
                   }
                   else{
                       $soloEntrada = true;
                       $datosIda = graphEficiencia($_POST['desde'], $_POST['hasta'], $origen, $destino, $servicio, $t_serv, $turno, $i_v,$avanzada, $fil_city, $city_origen, $i_v, $fil_srv, $servicio);
                       $tablaEntrada = $datosIda[1];
                       $datos = $datosIda[0];
                   }
              }
           }
           else{
                  $datosIda = graphEficiencia($_POST['desde'], $_POST['hasta'], $origen, $destino, $servicio, $t_serv, $turno, 'i',$avanzada, $fil_city, $city_origen, 'i', $fil_srv, $servicio);
                  $tablaEntrada = $datosIda[1];
                  $datos = $datosIda[0];

                  $datosVta = graphEficiencia($_POST['desde'], $_POST['hasta'], $origen, $destino, $servicio, $t_serv, $turno, 'v',$avanzada, $fil_city, $city_origen, 'v', $fil_srv, $servicio);
                  $tablaSalida = $datosVta[1];
                  $datosvta = $datosVta[0];
           }
           
           
           
           
           
        //   $soloEntrada = true; //flag para indicar si se debe graficar la vuelta tambien
           if ((!$servicio) && (!$origen) && (!$destino)){ //se grafican los graficos de ida y vuelta



              $tg = htmlentities($tg);
           }
           else{
                $tg = $titulo;
           }
           $chartIda = createFunctionChart("drawChartVta", $datosvta, "egr", "Salida de Planta","");
           $chartVta = createFunctionChart("drawChart", $datos, "ing", "");
           print "<script type=\"text/javascript\">
                          google.load(\"visualization\", \"1\", {packages:[\"corechart\"]});
                          google.setOnLoadCallback(drawChart);
                          google.setOnLoadCallback(drawChartVta);
                          $chartIda
                          $chartVta
          </script>";
        }
     }


?>
    <br><br>
         <form id="upuda" method="POST">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Graficas de Confiablidad y Eficiencia</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar Datos</legend>
		                 <table>
		                 <tr>
		                 <td>
                         <table border="0" align="left" width="50%" name="tabla" CELLPADDING=5>
                                <tr>
                                    <td>Cliente</td>
                                    <td>
                                        <select id="clis" name="clis" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option value="10">TOYOTA</option>
                                        </select>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Tipo de Servicio</td>
                                    <td colspan="3">
                                        <select id="tipo" name="tipo" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option value="2">PRODUCCION</option>
                                                <option value="1">ADMINISTRACION</option>
                                                <option value="16">MANTENIMIENTO</option>
                                        </select>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td>Turno</td>
                                    <td id="divturno" colspan="3">
                                    </td>
                                    </tr>
                                    <tr>
                                    <td>Desde</td>
                                    <td><input id="desde" name="desde"  type="text" size="20" value="<?php if (isset($_POST['cargar'])) echo $_POST['desde'];?>"></td>
                                    <td>Hasta</td>
                                    <td><input id="hasta" name="hasta" type="text" size="20" value="<?php if (isset($_POST['cargar'])) echo $_POST['hasta'];?>"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                    </tr>
                                    <tr>
                                    <td>Confiabilidad</td>
                                    <td><input id="cnf" type="radio" name="cnf" <?php echo $chcnf; ?> value="cnf"></td>
                                    <td>Eficiencia</td>
                                    <td><input id="efc" type="radio" name="cnf" value="efc" <?php echo $chefc; ?>></td>
                                    </tr>
                                                                        <tr>
                                        <td colspan="4"><input type="button" id="bavanzada" value="Busqueda Avanzada"></td>
                                    </tr>
                                </table>
                                </td>
                                </tr>
                                <tr>
                                <td>
                                <hr align="tr">
                                <table border="0" align="left" width="50%" id="tablaavanzada" name="tablaavanzada" CELLPADDING=5 >
                                    <tr>
                                        <td align="left">Origen</td>
                                        <td>
                                            <select name="fcity_origen" id="fcity_origen" class="selec">

                                            <?php
                                            try{
                                                 $conn = conexcion();
                                                 $sql = "SELECT id, upper(ciudad)
                                                         FROM ciudades c
                                                         WHERE c.id in (select ciudades_id_destino from cronogramas where id_cliente = 10 group by ciudades_id_destino)
                                                         order by ciudad";
                                                 $result = mysql_query($sql, $conn);
                                                 $option = "";
                                                 while ($city = mysql_fetch_array($result)){
                                                       $option.="<option value='$city[0]'>$city[1]</option>";
                                                 }
                                                 print $option;
                                                 } catch (Exception $e) {print $e->getMessage();}
                                            ?>
                                            </select>
                                        </td>
                                        <td colspan='2'><input type="checkbox" name="city_origen"  id="city_origen" value="origen" class="city">Filtar</td>
                                    </tr>
                                    <tr>
                                        <td>Mostrar</td>
                                        <td colspan="3">
                                        <input type="radio" name="mostrar" value="i" <?php print $chi;?>>Entrada a Planta
                                        <input type="radio" name="mostrar" value="v" <?php print $chv;?>>Salida de Planta
                                        <input type="radio" name="mostrar" value="iv" <?php print $chiv;?>>Ambos

                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Servicios</td>
                                        <td>
                                        <div id="cronos">
                                        
                                        </div>
                                        </td>
                                        <td colspan='2'><input type="checkbox" id="servicios" name="servicios">Filtrar</td>
                                    </tr>
                                    </table>
                                    </td>
                                    <tr><td><hr align="tr"></td></tr>
                                    </tr>
                                    <tr>
                                    <td>
                         <table border="0" align="left" width="50%" id="ejecutar" name="ejecutar">
                                    <tr>
                                    <td colspan="4" align="left">
                                        <input type="submit" value="Cargar Ordenes" id="cargar" name="cargar">
                                    </td>
                                </tr>
                         </table>
                         </td>
                         </tr>
                         </table>
                         </div>
                         </fieldset>
                         <br>
                         <?php if (isset($_POST['cargar'])){ ?>
                         <h2><p align="center"><?php echo ($tg);?></p></h2> <br>
                         <div id="center"></h2>
                              <ul>
                                  <li><a href="#tabs-1">Grafico <?php if (!$soloEntrada) echo "(Entrada)";?></a></li>
                                  <li><a href="#tabs-2">Fuente de Datos</a></li>
                              </ul>
                              <div id="tabs-1" align="center">
                                   <?php echo $chart; ?>
                                  <div id="ing" style="width: 1200px; height: 400px;"></div>
                              </div>
                              <div id="tabs-2" align="center">
                                   <?php echo $tablaEntrada; ?>
                              </div>
                         </div>
                         <hr align="tr">
                         <?php  if (!$soloEntrada){   ?>
                         <div id="vuelta"></h2>
                              <ul>
                                  <li><a href="#tabs-3">Grafico (Salida)</a></li>
                                  <li><a href="#tabs-4">Fuente de Datos</a></li>
                              </ul>
                              <div id="tabs-3" align="center">
                                   <?php echo $chartv; ?>
                                   <div id="egr" style="width: 1200px; height: 400px;"></div>
                              </div>
                              <div id="tabs-4" align="center">
                                   <?php echo $tablaSalida; ?>
                              </div>
                         </div>
                         <?php }  ?>
                         <?php } ?>
            </fieldset>
            <input type="hidden" name="accion" id="accion" value="list">
            <input type="hidden" name="order" id="order">
            <input type="hidden" name="avanzada" id="avanzada" <?php print "value='$avanzada'";?>>
         </form>
         
         <?php
         
              print "<script>
                       $('.show').click(function(){
                       var par = $(this).attr('id');
                                            var dialog = $('<div style=\"display:none\" id=\"dialog\" class=\"loading\" align=\"center\"></div>').appendTo('body');
                                            dialog.dialog({
                                                           close: function(event, ui) {dialog.remove();},
                                                           title: 'Fuentes de grafico',
                                                           width:1050,
                                                           height:350,
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
                                                           dialog.load('./fontdraw.php',{params:par},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});
                       });
              </script>";
         
         ?>


</body>
</html>
