<?php
     session_start();
   /*  include('../../modelo/provincia.php');
     include('../../modelo/ciudades.php');*/
     include('../../controlador/ejecutar_sql.php');
     include_once('../paneles/viewpanel.php');
     include_once('../main.php');

     //define('RAIZ', '');
     define('STRUCTURED', $_SESSION['structure']);

     encabezado('Menu Principal - Sistema de Administracion - Campana');
     
//id, id_empleado, acumulado, substro, siniestro_numero, fecha_siniestro, hora_siniestro, estado_clima, cod_ubicacion, calle1, calle2, id_localidad, tipo_lesion, resp_estimada, tipo_maniobra, norma_no_respetada, cobertura_afectada, id_coche, id_cliente, compania_seguro, numero_poliza, indemnizacion_a_terceros, numero_estudio, usr_alta, fecha_alta, usr_edit, fecha_edit, usr_baja, fecha_baja, borrada, id_estructura,
     $lat=-34.1633346;
     $lon=-58.95926429999997;
     if(isset($_GET['nro'])){
          $edit=true;
          $sql = "select *, date_format(fecha, '%d/%m/%Y') as fecha_f, time_format(hora, '%H:%i') as hora_f
                  from hechos_vandalicos
                  where id = $_GET[nro]";
          $result = ejecutarSQL($sql);
          if ($row = mysql_fetch_array($result)){
             $lat=$row[latitud]?$row[latitud]:-34.1633346;
             $lon=$row[longitud]?$row[longitud]:-58.95926429999997;
          }
          $desde = $_GET[des];
          $hasta = $_GET[has];
          $cond = $_GET[con];
          $unid = $_GET[cch];
     }
     
     
     
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBRxC6Y4f-j6nECyHWigtBATtJyXyha-XU&libraries=adsense&sensor=true&language=es"></script>
 <script>
var map;

function load_map() {



    var myLatlng = new google.maps.LatLng(<?php echo "$lat,$lon" ?>);
    var myOptions = {
        zoom: 9,
        center: myLatlng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map($("#map_canvas").get(0), myOptions);



  var marker = new google.maps.Marker({
    position: myLatlng,
    map: map
  });



}

$('#search').live('click', function() {

    var address = $('#address').val();

    var geocoder = new google.maps.Geocoder();

    geocoder.geocode({ 'address': address}, geocodeResult);
});

function geocodeResult(results, status) {

    if (status == 'OK'){

        var mapOptions = {
            center: results[0].geometry.location,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map($("#map_canvas").get(0), mapOptions);

        map.fitBounds(results[0].geometry.viewport);

        var markerOptions = { position: results[0].geometry.location }
        var marker = new google.maps.Marker(markerOptions);
        $("#lat").val(results[0].geometry.location.lat);
        $("#long").val(results[0].geometry.location.lng);
        marker.setMap(map);
    }
    else{
        alert("Geocoding no tuvo éxito debido a: " + status);
    }
}



 
 
 
 
 
	$(function() {
              load_map();

        $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
		$("input:button").button({icons: {
                                       primary: "ui-icon-disk"
                                       }});
        $('#envioFormulario').button();
        $(':button').button();
        $(':submit').button();
		$("#fsin").datepicker({
                                    dateFormat : 'dd/mm/yy'
                                   });
		$.mask.definitions['~']='[012]';
        $.mask.definitions['%']='[012345]';
        $(".hora").mask("~9:%9",{completed:function(){}});
        $('#km, #n_interno').mask("9999");
       <? echo $edit?"$('#estr option[value=$row[id_estructura]]').attr('selected','selected');":"";
          echo $edit?"$('#condsin option[value=$row[id_empleado]]').attr('selected','selected');":"";
          echo $edit?"$('#ciudadsin option[value=$row[lugar_incidente]]').attr('selected','selected');":"";
          echo $edit?"$('#internosin option[value=$row[id_micro]]').attr('selected','selected');":"";
          echo $edit?"$('#organismo option[value=$row[id_organismo_interviniente]]').attr('selected','selected');":"";


          echo $edit?'$.post("/modelo/segvial/upheva.php", {accion:"ordcnd", fecha:"'.$row[fecha_f].'", cond:'.$row[id_empleado].', estr:'.$row[id_estructura].'}, function(data) {
                                                                                                                                                             $("#ordenes").html(data);
                                                                                                                                                             $("#ortra option[value='.$row[id_orden].']").attr("selected","selected");
                                                                                                                                                             $("#ortra").selectmenu({width: 450});
                                                                                                                                                           });':'';



       ?>
        $('#origen, #destino, #cliente, #turnos, #tipos, #clase, #cobafec, #compaseg').selectmenu({width: 350});
        $('#interno, #cs').selectmenu({width: 250});
        $('select').selectmenu({width: 450});
        
        $('#condsin').change(function(){
                                        $.post("/modelo/segvial/upheva.php", {accion:'ordcnd', fecha:$("#fsin").val(), cond:$(this).val(), estr:$("#estr").val()}, function(data) {
                                                                                                                                                             $('#ordenes').html(data);
                                                                                                                                                             $('#ortra').selectmenu({width: 450});
                                                                                                                                                           });
                                        });
        
        
        $("#addinterno").button().click(function(){
                                                      $( "#udaform" ).dialog( "open" );
                                               });
        $("#addcond").button().click(function(){
                                                      $( "#driform" ).dialog( "open" );
                                               });
                                               

        $('#udaform').dialog({autoOpen: false,
                              height: 370,
                              width: 480,
                              modal: true,
                              close: function(){
                                                $('#upuda input:text').val('');
                                                }
                              });
        $('#driform').dialog({autoOpen: false,
                              height: 250,
                              width: 450,
                              modal: true,
                              close: function(){
                                                $('#upuda input:text').val('');
                                                }
                              });

        $('#commentForm').validate({
                                  submitHandler: function(){
                                                            var datos = $("#commentForm").serialize();
                                                            $("#envioFormulario").hide();
                                                            $.post("/modelo/segvial/upheva.php", datos, function(data) {
                                                                                                                       var response = $.parseJSON(data);
                                                                                                                       if (response.status){
                                                                                                                          alert(response.msge);
                                                                                                                          <?php
                                                                                                                               if ($edit){
                                                                                                                                  print '$(location).attr("href","/vista/segvial/readhv.php?ds='.$desde.'&hs='.$hasta.'&cn='.$cond.'&ud='.$unid.'");';
                                                                                                                               }
                                                                                                                               else{
                                                                                                                                    print "$('#commentForm')[0].reset();";
                                                                                                                               }
                                                                                                                          ?>
                                                                                                                       }
                                                                                                                       else{
                                                                                                                            alert(response.msge);
                                                                                                                       }
                                                                                                                       $("#envioFormulario").show();
                                                                                                                      });
                                                           }
                                  });

        $("#fservicio").focus();
        <?php if(!$edit){?>$.post("/modelo/segvial/upsnt.php", {accion: 'proxnrosin'}, function(data){$('#nsin').val(data);});<?php }?>
	});
	</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 150px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}
#commentForm td{padding: 2px;}
#commentForm #upuda .error{
	font-size:0.8em;
	color:#ff0000;
}
#upuda .error{
	font-size:0.8em;
	color:#ff0000;
}

#updri .error{
	font-size:0.8em;
	color:#ff0000;
}

#commentForm .error{
	font-size:0.8em;
	color:#ff0000;
}

</style>
<BODY>
<?php
     menu();

?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form class="cmxform" id="commentForm" method="get" action="" name="commentForm">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all"><?php echo $edit?"Modifica Siniestro":"Ingresar Hechos Vandalicos";?></legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%">
                         <?php
                              if ($edit){
                                print '<tr>
                                           <td WIDTH="20%"><label for="fservicio">Numero</label></td>
                                           <td><input id="nhv" name="nhv" readonly class="ui-widget ui-widget-content  ui-corner-all" value="'.$_GET[nro].'"/></td>
                                           <td></td>
                                    </tr>';
                              }
                         ?>
                                <tr>
                                    <td WIDTH="20%">Estructura</td>
                                    <td><select id="estr" name="estr" class="ui-widget-content  ui-corner-all">
                                                <?php
                                                     armarSelect ('estructuras', 'nombre', "id", "nombre", "")

                                              //  armarSelectCond($_SESSION['structure']);?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha Hecho</label></td>
                                    <td><input id="fsin" name="fsin" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2" value="<?php echo $edit?$row['fecha_f']:"";?>"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Hecho</td>
                                    <td colspan="2"><input id="hsin" maxlength="5" size="4" name="hsin" class="required hora ui-widget-content ui-corner-all" value="<?php echo $edit?$row['hora_f']:"";?>"/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Empleado</td>
                                    <td><select id="condsin" name="condsin" class="ui-widget-content  ui-corner-all">
                                                <?php
                                                     armarSelect ('empleados', 'apellido', "id_empleado", "concat(apellido,', ',nombre)", "activo")

                                              //  armarSelectCond($_SESSION['structure']);?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Servicio</td>
                                    <td id='ordenes'>
                                    </td>
                                    <td>
                                    </td>
                                </tr>

                                <tr>
                                    <td WIDTH="20%">Ciudad Hecho</td>
                                    <td><select id="ciudadsin" name="ciudadsin" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Ubicacion hecho</td>
                                    <td>
                                        <input id="ubicahecho"  size="30" name="ubicahecho" class="required ui-widget-content ui-corner-all" value="<?php echo $edit?$row['direccion_incidente']:"";?>"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Heridos</td>
                                    <td>
                                        <input id="heridos"  size="10" name="heridos" class="ui-widget-content ui-corner-all" value="<?php echo $edit?$row['calle1']:"";?>"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>

                                <tr>
                                    <td WIDTH="20%">Interno</td>
                                    <td><select id="internosin" name="internosin" class="ui-widget-content  ui-corner-all">
                                                <option value="0">NO DETALLA</option>
                                                <?php
                                                     armarSelect('unidades', 'CAST(interno as UNSIGNED)', 'id', 'interno', "(id_estructura = ".$_SESSION['structure'].") and (activo)");
                                                ?>
                                        </select>
                                    </td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Organismo Interviniente</td>
                                    <td><select id="organismo" name="organismo" class="ui-widget-content  ui-corner-all">
                                                <option value="0">NO DETALLA</option>
                                                <?php
                                                     armarSelect('organismos_intervinientes_h_v', 'organismo', 'id', 'organismo', "");
                                                ?>
                                        </select>
                                    </td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Descripcion del hecho</label></td>
                                    <td><textarea id="desc_hecho" name="desc_hecho" rows="5" cols="60" class="required ui-widget ui-widget-content  ui-corner-all" ><?php echo $edit?$row['descripcion_hecho']:"";?></textarea></td>
                                    <td></td>
                                </tr>
                                
<tr>
                                    <td colspan="3">
<div><input type="text" maxlength="100" id="address" placeholder="Dirección" /> <input type="button" id="search" value="Buscar Direccion" /></div><br/>
<div id='map_canvas' style="width:600px; height:400px;"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Datos geograficos
                                    </td>
                                    <td>
                                        Latitud <input type="text" size="20" name="lat" id="lat" value="<?php echo $lat;?>" class="ui-widget-content number  ui-corner-all">

                                        Longitud <input type="text" size="20" name="long" id="long" value="<?php echo $lon;?>" class="ui-widget-content number  ui-corner-all">
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3" align="right"><input id="envioFormulario" class="boton" type="submit" value="Guardar" name="envioFormulario"></td>
                                </tr>
                                
                         </table>
	</fieldset>
	<input type="hidden" name="accion" id="accion" value="<?php echo $edit?"update":"sve"; ?>"/>
    <?php //echo $edit?'<input type="hidden" name="nhv" id="nhv" value="'.$_GET[nro].'"/>':'';
    ?>
</form>
</div>

         

</BODY>
</HTML>
