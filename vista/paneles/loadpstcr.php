<?php
     session_start();
     define(RAIZ, '/nuevotrafico');
     //include ('/nuevotrafico/controlador/bdadmin.php');
     include ('../../controlador/bdadmin.php');
     include ('viewpanel.php');

     function selectTurnos($value, $option){
              $print = "<select id=\"turno\" name=\"turno\"  class=\"ui-widget-content  ui-corner-all\">";
              $print.=armarSelect('turnos', 'turno', 'id', 'turno', "(id_estructura = $_SESSION[structure])", 1);
              return $print.'</select>';
     }

     function selectTipo($value, $option){
              $print = "<select id=\"tipo\" name=\"tipo\"  class=\"ui-widget-content  ui-corner-all\">";
              $print.=armarSelect('tiposervicio', 'tipo', 'id', 'tipo', "(id_estructura = $_SESSION[structure])",1);
              return $print.'</select>';
     }
     
     function selectPostaHorario($value, $option){
              $print = "<select id=\"postah\" name=\"postah\"  class=\"ui-widget-content  ui-corner-all\">";
              $print.=armarSelect('postasHorarios', 'descripcion', 'id', 'descripcion', "", 1);
              return $print.'</select>';
     }

     function selectIV($value, $option){
              return "<select id=\"iv\" name=\"iv\"  class=\"ui-widget-content  ui-corner-all\">
                             <option value=\"i\">IDA</option>
                             <option value=\"v\">VUELTA</option>
                     </select>";
     }
     
     
     $accion = $_POST['accion'];
     
     if ($accion == 'lcr'){

        $conn = conexcion();

        $cliente = $_POST['cli'];
        $sql="SELECT c.id, upper(c.nombre) as nombre, km, date_format(tiempo_viaje, '%H:%i') as tiempo, upper(o.ciudad) as origen, upper(d.ciudad) as destino, upper(cs.clase) as clase
              FROM cronogramas c
              inner join ciudades o on ((c.ciudades_id_origen = o.id) and (c.ciudades_id_estructura_origen = o.id_estructura))
              inner join ciudades d on ((c.ciudades_id_destino = d.id) and (c.ciudades_id_estructura_destino = d.id_estructura))
              inner join claseservicio cs on ((c.claseServicio_id = cs.id) and (c.claseServicio_id_estructura = cs.id_estructura))
              WHERE (c.id_estructura = $_SESSION[structure]) and ((c.id_cliente = $cliente) and (c.id_estructura_cliente = $_SESSION[structure]))
              ORDER BY c.nombre";

        $result = mysql_query($sql, $conn) or die (mysql_error($conn));
        $select = '<select id="servicios" name="servicios"  ><option>SELECCIONE UN SERVICIO</option>';
        while ($data = mysql_fetch_array($result)){
              $select.="<option value=\"$data[id]\">$data[nombre]</option>";
        }
        mysql_free_result($result);
        mysql_close($conn);
        $select.= '</select>
                  <script>
                          $("#servicios").change(function(){
                                                            var cronos = $("#servicios option:selected").val();
                                                            $("#hora-servi").html("<div align=\'center\'><img src=\'../ajax-loader.gif\' /></div>");
                                                            $.post("../paneles/loadpstcr.php",{crono: cronos, accion: \'lhr\'},function(data){

                                                                                                                                            $("#hora-servi").html(data);
                                                                                                                                            $(\'#horarios\').selectmenu({width: 450});

                                                                                                                                            });
                                                            });
                  </script>';
        print $select;
     }elseif($accion == 'lhr'){

        $conn = conexcion();
        $crono = $_POST['crono'];
        $clases = array();
        $result = mysql_query("SELECT id, clase FROM claseservicio WHERE id_estructura = $_SESSION[structure]", $conn);
        while ($data = mysql_fetch_array($result)){
              $clases[$data[0]]=$data[1];
        }
        $result = mysql_query("SELECT id, ciudad FROM ciudades where id_estructura = $_SESSION[structure] order by ciudad", $conn);
        while ($data = mysql_fetch_array($result)){
              $ciudades[$data[0]]=$data[1];
        }
        
        $turnos[1]= htmlentities('Mañana');
        $turnos[2]= htmlentities('Tarde');
        $turnos[3]= htmlentities('Noche');
        
        $tipos[1]="ADMINISTACION";
        $tipos[2]="PRODUCCION";
        $tipos[3]="ESPECIAL";
        $tipos[16]="MANTENIMIENTO";
        
        $i_v['i']="IDA";
        $i_v['v']="VUELTA";
        
        $sql="SELECT km, DATE_FORMAT(tiempo_viaje, '%H:%i') as tiempo, o.ciudad as origen, d.ciudad as destino, cs.clase, c.activo, c.id, c.nombre as nombre
              FROM cronogramas c
              inner join ciudades o on (o.id = c.ciudades_id_origen) and (o.id_estructura =  c.ciudades_id_estructura_origen)
              inner join ciudades d on (d.id = c.ciudades_id_destino) and (d.id_estructura =  c.ciudades_id_estructura_destino)
              inner join claseservicio cs on (cs.id = c.claseServicio_id) and (cs.id_estructura = c.claseServicio_id_estructura)
              WHERE (c.id_estructura = $_SESSION[structure]) and (c.id = $crono)";
              
        $result = mysql_query($sql, $conn);
        if ($data = mysql_fetch_array($result)){
        $select = "<table border='0' align='center' width='75%'>
                   <tr>
                       <td WIDTH='20%'>Nombre</td>
                       <td><div id='nombre-$data[id]' class='updnombre'>".htmlentities($data['nombre'])."</div></td>
                       <td></td>
                   </tr>
                   <tr>
                       <td WIDTH='20%'>Origen</td>
                       <td><div id='ciudades_id_origen-$data[id]' class='updciudad'>".htmlentities($data['origen'])."</div></td>
                       <td></td>
                   </tr>
                   <tr>
                       <td WIDTH='20%'>Destino</td>
                       <td><div id='ciudades_id_destino-$data[id]' class='updciudad'>".htmlentities($data['destino'])."</div></td>
                       <td></td>
                   </tr>
                   <tr>
                       <td WIDTH='20%'>Km recorrido</td>
                       <td><div class='cronupd' id='km-$data[id]'>$data[km]</div></td>
                       <td></td>
                   </tr>
                   <tr>
                       <td WIDTH='20%'>Duracion</td>
                       <td><div class='cronupd' id='tiempo_viaje-$data[id]'>$data[tiempo]</div></td>
                       <td></td>
                   </tr>
                   <tr>
                   <td WIDTH='20%'>Clase</td>
                       <td><div id='claseServicio_id-$data[id]' class='updclase'>$data[clase]</div></td>
                       <td></td>
                   </tr>
                   <tr>
                   <td WIDTH='20%'>Control Horarios</td>
                   <td colspan='2' align='left'>";
        }
        $sql="SELECT upper(descripcion) as descrip, date_format(hora_relativa_llegada, '%H:%i') as llegada, date_format(hora_relativa_salida, '%H:%i') as salida, latitud, longitud, orden, pc.id
              FROM postasCronogramas pc
              inner join postasHorarios ph on ph.id = pc.id_postaHorario
              WHERE (pc.id_cronograma = $crono) and (pc.id_estructura_cronograma = $_SESSION[structure])
              order by orden";
        // die($sql);
        $result = mysql_query($sql, $conn);

        $select.= '<table id="tabla" class="ui-widget ui-widget-content" align="left">
                          <thead>
                                 <tr class="ui-widget-header">
                                     <th>Descripcion</th>
                                     <th>H. Relativa Llegada</th>
                                     <th>H. Relativa Salida</th>
                                     <th>Latitud</th>
                                     <th>Longitud</th>
                                     <th>Orden Aparicion</th>
                                     <th>Accion</th>
                                 </tr>
                          </thead>
                          <tbody>';
        while ($data = mysql_fetch_array($result)){
              $label = 'Activar';
              if ($data['activo']){
                 $label = 'Desac.';
              }
              $select.="<tr id='$data[id]'>
                            <td><div id='descripcion-$data[id]'>$data[descrip]</div></td>
                            <td><div class='hora' id='hora_relativa_llegada-$data[id]'>$data[llegada]</div></td>
                            <td><div class='hora' id='hora_relativa_salida-$data[id]'>$data[salida]</div></td>
                            <td><div id='latitud-$data[id]'>$data[latitud]</div></td>
                            <td><div id='longitud-$data[id]' >$data[longitud]</div></td>
                            <td><div id='orden-$data[id]' >$data[orden]</div></td>
                            <td><input type='button' id='boton$data[id]' value='$label' onClick='modSrv(this.value, $data[id]);'></td>";
        }
        mysql_free_result($result);
        mysql_close($conn);
        $select.= '</tbody>
                  </table>
                  </td>
                  </tr>
                  <tr>
                      <td></td>
                      <td colspan="2"><input type="button" id="addsrv" value="Agregar Marca Control Horario"></td>
                  </tr>
                  </table>
        <div id="dialog-form" title="Agregar Marcas de Horarios al Cronograma">
              <form id="upcontact">
	                <fieldset>
                              <div class="div">
		                      <label for="h_llegada">Hora Relativa Llegada</label>
		                      <input type="text" name="h_llegada" id="h_llegada" maxlength="5" size="5" class="hora ui-widget-content ui-corner-all required" onblur="valida(this.value, this.id);"/>
                              <span></span>
                              </div>
                              <div class="div">
                              <label for="h_salida">Hora Relativa Salida</label>
		                      <input type="text" name="h_salida" id="h_salida" maxlength="5" size="5" class="hora ui-widget-content ui-corner-all required" onblur="valida(this.value, this.id);"/>
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="h_fin">Orden de Aparicion</label>
		                      <input type="text" name="orden" id="orden" maxlength="5" size="5" class="ui-widget-content ui-corner-all required" onblur="valida(this.value, this.id);"/>
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="turno">Punto de control</label>
		                      <span></span>'.
                              selectPostaHorario(0,0).
                              '</div>
                    </fieldset>
           			<fieldset id="botonera" style="border:none; text-align: right;">
				              <input id="envioFormulario" class="boton" type="submit" value="Guardar Punto de Control" name="envioFormulario">
                    </fieldset>
                    <input type="hidden" id="cronogramas" name"cronogramas" value="3">
              </form>
         </div>
                   <script>
                           $(function(){
                                        $.editable.addInputType("masked", {
                                                     element : function(settings, original) {
                                                                                            var input = $("<input />").mask(settings.mask);
                                                                                            $(this).append(input);
                                                                                            return(input);
                                                                                            }
                                                                          });
                                        $("select").selectmenu({width: 350});
                                        $.mask.definitions["H"]="[012]";
                                        $.mask.definitions["N"]="[012345]";
                                        $(".hora").mask("H9:N9");
                                        $(".hora").editable("/modelo/procesa/servicios/upd-serv-crono.php", {type:"masked", mask: "H9:N9"});
                                        $(".cronupd").editable("/modelo/procesa/servicios/update-crono.php");
                                        $( "#dialog-form" ).dialog({autoOpen: false,
                                                                  height: 385,
                                                                  width: 620,
                                                                  modal: true,
                                                                  close: function() {$("#upcontact").each (function(){this.reset();})}
                                                                  });
                                        $("#addsrv, #envioFormulario").button().click(function() {

                                                                                                 $( "#dialog-form" ).dialog( "open" );
                                                                                                 });
                                        $(".updclase").editable("/modelo/procesa/servicios/upd-clase.php", {
                                                                                                   data   : '.json_encode($clases).',
                                                                                                   type   : "select",
                                                                                                   submit : "Cambiar"
                                                                                                    });
                                                                                                    
                                        $(".updciudad").editable("/modelo/procesa/servicios/upd-clase.php", {
                                                                                                   data   : '.json_encode($ciudades).',
                                                                                                   type   : "select",
                                                                                                   submit : "Cambiar"
                                                                                                    });
                                        $(".updtno").editable("/modelo/procesa/servicios/upd-clase.php", {
                                                                                                   data   : '.json_encode($turnos).',
                                                                                                   type   : "select",
                                                                                                   submit : "Cambiar"
                                                                                                    });
                                        $(".updtipo").editable("/modelo/procesa/servicios/upd-clase.php", {
                                                                                                   data   : '.json_encode($tipos).',
                                                                                                   type   : "select",
                                                                                                   submit : "Cambiar"
                                                                                                    });
                                        $(".updiv").editable("/modelo/procesa/servicios/upd-clase.php", {
                                                                                                   data   : '.json_encode($i_v).',
                                                                                                   type   : "select",
                                                                                                   submit : "Cambiar"
                                                                                                    });
                                        $(".updnombre").editable("/modelo/procesa/servicios/upd-clase.php", {submit : "Cambiar"});
                                        
                                        $("#upcontact").validate({
                                                                  submitHandler: function(e){
                                                                                             var hsal = $("#h_salida").val();
                                                                                             var hleg = $("#h_llegada").val();
                                                                                             var orden = $("#orden").val();
                                                                                             var posta = $("#postah").val();
                                                                                             var crono = $("#servicios").val();
                                                                                             $.post("../../modelo/servicios/addpstcron.php",{sale:hsal, lega:hleg, ord:orden, cron: crono, pst:posta, accion: \'addpst\'},function(data){
                                                                                                                                                                                                                                                                       $("#tabla tbody").append(data);
                                                                                                                                                                                                                                                                       $( "#dialog-form" ).dialog( "close" );
                                                                                                                                                                                                                                                                       });
                                                                                             }
                                                                  });
                           });
                           function modSrv(label, srvc){
                                    var st = 1;
                                    var val = "Desac.";
                                    if (label == "Desac."){
                                       st = 0;
                                       val = "Activar";
                                    }
                                     $.post("../../modelo/servicios/addsrvcron.php", {sta:st, srv:srvc, accion:"acdesrv"},function(data){
                                                                                                                                         $("#boton"+srvc).attr("value",val);
                                                                                                                                         });
                           }
                  </script>
                  <style>
                         #tabla th{
                                padding:13px;
                                font-size: 72.5%;
                                }
                         #tabla tr{
                                padding:13px;
                                font-size: 72.5%;
                                }
                  </style>';
        print $select;
     }
     
     
     
     


     

?>

