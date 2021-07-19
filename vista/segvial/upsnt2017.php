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
          $sql = "SELECT date_format(fecha_siniestro, '%d/%m/%Y') as fecha, time_format(hora_siniestro, '%H:%i') as hora,
                         latitud, longitud, id_estructura, id, id_empleado, id_cliente, estado_clima, calle1, calle2, cod_ubicacion,
                         tipo_lesion, resp_estimada, id_coche, cobertura_afectada, compania_seguro, tipo_maniobra, norma_no_respetada,
                         numero_poliza, id_localidad, costos_reparacion_unidades, costos_administrativos, num_sin_asignado, id_estado
                  FROM siniestros
                  WHERE id = $_GET[nro]";
          $result = ejecutarSQL($sql);
          if ($row = mysql_fetch_array($result)){
             $lat=$row[latitud]?$row[latitud]:-34.1633346;
             $lon=$row[longitud]?$row[longitud]:-58.95926429999997;
          }
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
          echo $edit?"$('#clientesin option[value=$row[id_cliente]]').attr('selected','selected');":"";
          echo $edit?"$('#climasin option[value=$row[estado_clima]]').attr('selected','selected');":"";
          echo $edit?"$('#ubicacionsin option[value=$row[cod_ubicacion]]').attr('selected','selected');":"";
          echo $edit?"$('#lesionsin option[value=$row[tipo_lesion]]').attr('selected','selected');":"";
          echo $edit?"$('#respsin option[value=$row[resp_estimada]]').attr('selected','selected');":"";
          echo $edit?"$('#internosin option[value=$row[id_coche]]').attr('selected','selected');":"";
          echo $edit?"$('#cobafecsin option[value=$row[cobertura_afectada]]').attr('selected','selected');":"";
          echo $edit?"$('#compaseg option[value=$row[compania_seguro]]').attr('selected','selected');":"";
          echo $edit?"$('#tipomaniobrasin option[value=$row[tipo_maniobra]]').attr('selected','selected');":"";
          echo $edit?"$('#normanrsin option[value=$row[norma_no_respetada]]').attr('selected','selected');":"";
          echo $edit?"$('#ciudadsin option[value=$row[id_localidad]]').attr('selected','selected');":"";
          echo $edit?"$('#estadosin option[value=$row[id_estado]]').attr('selected','selected');":"";




       ?>
        $('#origen, #destino, #cliente, #turnos, #tipos, #clase, #cobafec, #compaseg').selectmenu({width: 350});
        $('#interno, #cs').selectmenu({width: 250});
        $('select').selectmenu({width: 450});
        

        
        
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
                                                            $.post("/modelo/segvial/upsnt.php", datos, function(data) {
                                                                                                                      <?php if ($edit){   ?>
                                                                                                                            if (data == 1)
                                                                                                                               history.back();
                                                                                                                      <?php }?>
                                                                                                                           $('#commentForm')[0].reset();
                                                                                                                           $.post("/modelo/segvial/upsnt.php", {accion: 'proxnrosin'}, function(data){$('#nsin').val(data);});
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
		                 <legend class="ui-widget ui-widget-header ui-corner-all"><?php echo $edit?"Modifica Siniestro":"Ingresar Siniestro";?></legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%">
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
                                    <td WIDTH="20%"><label for="fservicio">Fecha Siniestro</label></td>
                                    <td><input id="fsin" name="fsin" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2" value="<?php echo $edit?$row['fecha']:"";?>"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Siniestro</td>
                                    <td colspan="2"><input id="hsin" maxlength="5" size="4" name="hsin" class="required hora ui-widget-content ui-corner-all" value="<?php echo $edit?$row['hora']:"";?>"/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Num. Siniestro</td>
                                    <td colspan="2"><input id="nsin" size="8" name="nsin" readonly class="required ui-widget-content ui-corner-all" value="<?php echo $edit?$row['id']:"";?>"/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Num. Sin. Asignado</td>
                                    <td colspan="2"><input id="nsinasig" size="8" name="nsinasig" class="ui-widget-content ui-corner-all" value="<?php echo $edit?$row['num_sin_asignado']:"";?>"/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Conductor</td>
                                    <td><select id="condsin" name="condsin" class="ui-widget-content  ui-corner-all">
                                                <?php
                                                     armarSelect ('empleados', 'apellido', "id_empleado", "concat(apellido,', ',nombre)", "")

                                              //  armarSelectCond($_SESSION['structure']);?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Cliente</td>
                                    <td><select id="clientesin" name="clientesin" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <option value="0">NO DETALLA</option>
                                                <?php
                                                     armarSelect('clientes', 'razon_social', 'id', 'razon_social', "(id_estructura = $_SESSION[structure])");
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Estado clima</td>
                                    <td><select id="climasin" name="climasin" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('estadoClima', 'estado', 'id', 'estado', "");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Cod. Ubicacion</td>
                                    <td><select id="ubicacionsin" name="ubicacionsin" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('codUbicacionSiniestro', 'codigo', 'id', 'codigo', "");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Ciudad Siniestro</td>
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
                                    <td WIDTH="20%">Calle 1</td>
                                    <td>
                                        <input id="calle1sin"  size="30" name="calle1sin" class="required ui-widget-content ui-corner-all" value="<?php echo $edit?$row['calle1']:"";?>"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Calle 2</td>
                                    <td>
                                        <input id="calle2sin" size="30" name="calle2sin" class="required ui-widget-content ui-corner-all" value="<?php echo $edit?$row['calle2']:"";?>"/>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Tipo Lesion</td>
                                    <td><select id="lesionsin" name="lesionsin"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('tipoLesionSiniestro', 'tipo', 'id', 'tipo', "");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Responsabilidad</td>
                                    <td><select id="respsin" name="respsin"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('resp_estimada_siniestro', 'responsabilidad', 'id', 'responsabilidad', "");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>

                                <tr>
                                    <td WIDTH="20%">Interno</td>
                                    <td><select id="internosin" name="internosin" class="ui-widget-content  ui-corner-all">
                                                <option value="0">NO DETALLA</option>
                                                <?php
                                                     armarSelect('unidades', 'CAST(interno as UNSIGNED)', 'id', 'interno', "");
                                                ?>
                                        </select>
                                    </td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Cobertura Afectada</td>
                                    <td><select id="cobafecsin" name="cobafecsin" class="ui-widget-content  ui-corner-all">
                                                <option value="0">NO DETALLA</option>
                                                <?php
                                                     armarSelect('coberturaAfectadaSiniestro', 'cobertura', 'id', 'cobertura', "");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Tipo Maniobra</td>
                                    <td><select id="tipomaniobrasin" name="tipomaniobrasin" class="ui-widget-content  ui-corner-all">
                                                <option value="0">NO DETALLA</option>
                                                <?php
                                                     armarSelect('tipo_maniobra_siniestro', 'maniobra', 'id', 'maniobra', "");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Norma No Respetada</td>
                                    <td><select id="normanrsin" name="normanrsin" class="ui-widget-content  ui-corner-all">
                                                <option value="0">NO DETALLA</option>
                                                <?php
                                                     armarSelect('normas_seg_vial', 'norma', 'id', 'norma', "");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Compa&ntilde;ia Aseguradora</td>
                                    <td><select id="compaseg" name="compaseg" class="ui-widget-content  ui-corner-all">
                                                <option value="0">NO DETALLA</option>
                                                <?php
                                                     armarSelect('companiasAseguradoras', 'compania', 'id', 'compania', "");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Estado Siniestro</td>
                                    <td><select id="estadosin" name="estadosin" class="ui-widget-content  ui-corner-all">
                                                <option value="0">NO DETALLA</option>
                                                <?php
                                                     armarSelect('estadosiniestro', 'estado', 'id', 'estado', "");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Numero poliza</td>
                                    <td colspan="2"><input id="poliza" size="15" name="poliza" class="required ui-widget-content number  ui-corner-all" value="<?php echo $edit?$row['numero_poliza']:"";?>"/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Costos Administrativos</td>
                                    <td colspan="2"><input id="costadmin" size="7" name="costadmin" class="ui-widget-content number  ui-corner-all" value="<?php echo $edit?$row['costos_administrativos']:"";?>"/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Costos Reparacion Unidades</td>
                                    <td colspan="2"><input id="costrepa" size="7" name="costrepa" class="ui-widget-content number  ui-corner-all" value="<?php echo $edit?$row['costos_reparacion_unidades']:"";?>"/></td>
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
                                        Latitud <input type="text" size="20" name="lat" id="lat" value="<?php echo $lat;?>" class="required ui-widget-content number  ui-corner-all">

                                        Longitud <input type="text" size="20" name="long" id="long" value="<?php echo $lon;?>" class="required ui-widget-content number  ui-corner-all">
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="3" align="right"><input id="envioFormulario" class="boton" type="submit" value="Guardar Siniestro" name="envioFormulario"></td>
                                </tr>
                                
                         </table>
	</fieldset>
	<input type="hidden" name="accion" id="accion" value="<?php echo $edit?"update":"sve"; ?>"/>
</form>
</div>

         

</BODY>
</HTML>
