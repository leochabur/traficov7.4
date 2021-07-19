<div id="dialog-form" title="Agregar servicios al presupuesto">
	<form id="addsrv">
       <table>
              <tr>
                  <td>Origen</td>
                  <td colspan="2">
                      <select class='select1' name="origen"><?php print ciudadesOptions();?></select>
                  </td>
                  <td>Lugar Salida</td>
                  <td colspan="2"><input type="text" name="lsalida" id="lsalida" class="text ui-widget-content ui-corner-all required"></td>
              </tr>
              <tr>
                  <td>Destino</td>
                  <td colspan="2">
                      <select class='select1' name="destino"><?php print ciudadesOptions();?></select>
                  </td>
                  <td>Lugar Llegada</td>
                  <td colspan="2"><input type="text" name="lllegada" id="lllegada" class="text ui-widget-content ui-corner-all"></td>
              </tr>
              <tr>
                  <td>Distancia</td>
                  <td><input type="text" name="km" id="km" size="5" class="text ui-widget-content ui-corner-all required"></td>
              </tr>
              <tr>
                  <td>Pasajeros</td>
                  <td><input type="text" name="pax" id="pax" size="5" class="text ui-widget-content ui-corner-all required"></td>
              </tr>
              <tr>
                  <td colspan="6" class="ui-widget ui-widget-header ui-corner-all">Servicio de IDA</td>
              </tr>

              <tr class="top">
                  <td><label for="name">Fecha Salida</label></td>
                  <td><input type="text" name="fida" id="fida" class="text ui-widget-content ui-corner-all fecha required"></td>
                  <td>Hora Salida</td>
                  <td><input type="text" name="hsida" id="hsida" size="5" class="text ui-widget-content ui-corner-all hora required"></td>
              <tr>
              <tr class="top">
                  <td><label for="name">Fecha Llegada</label></td>
                  <td><input type="text" name="fllegadaida" id="fllegadaida" class="text ui-widget-content ui-corner-all fecha required"></td>
                  <td>Hora Llegada</td>
                  <td><input type="text" name="hllida" id="hllida" size="5" class="text ui-widget-content ui-corner-all hora required"></td>
              <tr>                
              <tr>
                  <td colspan="6" class="ui-widget ui-widget-header ui-corner-all">Servicio de VUELTA</td>
              </tr>                
              <tr>
                  <td><label for="name">Fecha Salida</label></td>
                  <td><input type="text" name="fregreso" id="fregreso" class="text ui-widget-content ui-corner-all fecha"></td>
                  <td>Hora Salida</td>
                  <td><input type="text" name="hsreg" id="hsreg" size="5" class="text ui-widget-content ui-corner-all hora"></td>
              <tr>
              <tr>
                  <td><label for="name">Fecha Llegada</label></td>
                  <td><input type="text" name="fllegadaregreso" id="fllegadaregreso" class="text ui-widget-content ui-corner-all fecha"></td>
                  <td>Hora Llegada</td>
                  <td><input type="text" name="hllreg" id="hllreg" size="5" class="text ui-widget-content ui-corner-all hora"></td>
              <tr>                
                  <?php
                       $servicios = serviciosViajeList();
                       foreach($servicios as $s){
                                          print "<tr>
                                                     <td>$s</td>
                                                     <td><input type='checkbox' name='srv-".$s->getId()."'></td>
                                                 </tr>";     //
                       }
                  ?>
              <tr>
                  <td>Observaciones</td>
                  <td colspan="5"><textarea rows="5" cols="50" class="text ui-widget-content ui-corner-all" name="obs"></textarea></td>
              </tr>


       </table>
			<input type="submit" tabindex="-1" style="position:absolute; top:-1000px">
			<input type="hidden" name="accion" value="addsrv">
      <input type="hidden" name="clisrv"/>
	</form>
</div>

<script>
        $('#addsrv').validate({
                                  submitHandler: function(){
                                                            var datos = form.serialize();
                                                        
                                                            $.post("/modelo/turismo/nwprs.php", datos, function(data) {
                                                                                                                      var response = $.parseJSON(data);
                                                                                                                     
                                                                                                                      if ( response.ok ) {
                                                                                                                         <?php if ($edit) print "viajes[response.id] = 0;"; else print "viajes.push(response.id);";?>
        				                                                                                                         $( "#vjelst tbody" ).append( "<tr id='tr"+response.id+"'>" +
                                                                                                                                                             "<td>" + response.fsalida + "</td>" +
        					                                                                                                                                 "<td>" + response.origen + "</td>" +
        					                                                                                                                                 "<td>" + response.destino + "</td>" +
        					                                                                                                                                 "<td>" + response.hsalida + "</td>" +
        					                                                                                                                                 "<td>" + response.pax + "</td>" +
        					                                                                                                                                 "<td><img src='../../eliminar.png' width='20' height='20' border='0' onclick='remove("+response.id+")'></td>" +
        				                                                                                                                                     "</tr>"+
                                                                                                                                                             "<input type='hidden' name='vjpr-"+response.id+"'>" );
        				                                                                                                         
			                                                                                                               }
                                                                                                                     else{
                                                                                                                           alert(response.message);
                                                                                                                     }


                                                                                                                                  });

                                                           }
                                  });
        $('#pagoanti').change(function(){
                                          if ($(this).is(':checked')){
                                            $('#flimite').addClass( "required" );
                                          }
                                          else{
                                            $('#flimite').removeClass( "required" );
                                          }
        });        
       function remove(id){
                           var i = viajes.indexOf( id );
                           viajes.splice( i, 1 );
                           $('#tr'+id).remove();
                           };
</script>
