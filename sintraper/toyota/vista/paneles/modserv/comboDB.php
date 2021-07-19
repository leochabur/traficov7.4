<?php
     session_start();
     define(RAIZ, '/nuevotrafico');
     //include ('/nuevotrafico/controlador/bdadmin.php');
     include ('../../../controlador/bdadmin.php');
     //include ('viewpanel.php');
     $accion = $_POST['accion'];
     
     if ($accion == 'lcr'){

        $conn = conexcion();

        $cliente = $_POST['cli'];
        $sql="SELECT c.id, upper(c.nombre) as nombre, km, date_format(tiempo_viaje, '%H:%i') as tiempo, upper(o.ciudad) as origen, upper(d.ciudad) as destino, upper(cs.clase) as clase
              FROM cronogramas c
              inner join ciudades o on ((c.ciudades_id_origen = o.id) and (c.ciudades_id_estructura_origen = o.id_estructura))
              inner join ciudades d on ((c.ciudades_id_destino = d.id) and (c.ciudades_id_estructura_destino = d.id_estructura))
              inner join claseservicio cs on ((c.claseServicio_id = cs.id) and (c.claseServicio_id_estructura = cs.id_estructura))
              WHERE (c.id_estructura = $_SESSION[structure]) and ((c.id_cliente = $cliente) and (c.id_estructura_cliente = $_SESSION[structure]))";

        $result = mysql_query($sql, $conn) or die (mysql_error($conn));
        $select = '<select id="servicios" name="servicios"  class="ui-widget-content  ui-corner-all"><option>SELECCIONE UN SERVICIO</option>';
        while ($data = mysql_fetch_array($result)){
              $select.="<option value=\"$data[id]\">$data[nombre]</option>";
        }
        mysql_free_result($result);
        mysql_close($conn);
        $select.= '</select>
                  <script>
                          $("#servicios").change(function(){
                                                            var cronos = $("#servicios option:selected").val();
                                                            $.post("../paneles/modserv/comboDB.php",{crono: cronos, accion: \'lhr\'},function(data){

                                                                                                                                            $("#hora-servi").html(data);
                                                                                                                                            $(\'#horarios\').selectmenu({width: 350});

                                                                                                                                            });
                                                            });
                  </script>';
        print $select;
     }elseif($accion == 'lhr'){
        $conn = conexcion();
        $crono = $_POST['crono'];
        $sql="SELECT km, DATE_FORMAT(tiempo_viaje, '%H:%i') as tiempo, o.ciudad as origen, d.ciudad as destino, cs.clase, c.activo
              FROM cronogramas c
              inner join ciudades o on (o.id = c.ciudades_id_origen) and (o.id_estructura =  c.ciudades_id_estructura_origen)
              inner join ciudades d on (d.id = c.ciudades_id_destino) and (d.id_estructura =  c.ciudades_id_estructura_destino)
              inner join claseservicio cs on (cs.id = c.claseServicio_id) and (cs.id_estructura = c.claseServicio_id_estructura)
              WHERE (c.id_estructura = $_SESSION[structure]) and (c.id = $crono)";
              
        $result = mysql_query($sql, $conn);
        if ($data = mysql_fetch_array($result)){
        $select = "<table border='0' align='center' width='75%'>
                   <tr>
                       <td WIDTH='20%'>Origen</td>
                       <td><input type='text' size='20' class='ui-widget-content ui-corner-all' value='$data[origen]'></td>
                       <td></td>
                   </tr>
                   <tr>
                       <td WIDTH='20%'>Destino</td>
                       <td><input type='text' size='20' class='ui-widget-content ui-corner-all' value='$data[destino]'></td>
                       <td></td>
                   </tr>
                   <tr>
                       <td WIDTH='20%'>Km recorrido</td>
                       <td><input type='text' size='20' class='ui-widget-content ui-corner-all' value='$data[km]'></td>
                       <td></td>
                   </tr>
                   <tr>
                       <td WIDTH='20%'>Duracion</td>
                       <td><input type='text' size='20' class='ui-widget-content ui-corner-all' value='$data[tiempo]'></td>
                       <td></td>
                   </tr>
                   <tr>
                   <td WIDTH='20%'>Clase</td>
                       <td><input type='text' size='20' class='ui-widget-content ui-corner-all' value='$data[clase]'></td>
                       <td></td>
                   </tr>
                   <tr>
                   <td WIDTH='20%'>Servicios</td>
                   <td colspan='2' align='left'>";
        }
        $sql="SELECT s.id, date_format(hcitacion, '%H:%i') as citacion, date_format(hsalida, '%H:%i') as salida, date_format(hllegada, '%H:%i') as llegada, date_format(hfinserv, '%H:%i') as hfin, upper(tipo) as tipo, upper(turno) as turno, if (i_v = 'i', 'IDA', 'VUELTA') as i_v, s.activo as activo
              FROM servicios s
              inner join turnos t on (t.id = s.id_turno) and (t.id_estructura = s.id_estructura_turno)
              inner join tiposervicio ts on (ts.id = s.id_TipoServicio) and (ts.id_estructura = s.id_estructura_TipoServicio)
              WHERE (s.id_estructura = $_SESSION[structure]) and ((s.id_cronograma = $crono) and (s.id_estructura_cronograma = $_SESSION[structure]))
              order by hcitacion";
              
        $result = mysql_query($sql, $conn);

        $select.= '<table id="tabla" class="ui-widget ui-widget-content" align="left">
                          <thead>
                                 <tr class="ui-widget-header">
                                     <th>H. Citacion</th>
                                     <th>H. Salida</th>
                                     <th>H. Llegada</th>
                                     <th>F. Fin Serv.</th>
                                     <th>Turno</th>
                                     <th>Tipo Servicio</th>
                                     <th>Ida Vuelta</th>
                                     <th>Estado</th>
                                 </tr>
                          </thead>
                          <tbody>';
        while ($data = mysql_fetch_array($result)){
              $select.="<tr id='$data[id]'>
                            <td><div class='hora' id='hcitacion-$data[id]'>$data[citacion]</div></td>
                            <td><div class='hora' id='hsalida-$data[id]'>$data[salida]</div></td>
                            <td><div class='hora' id='hllegada-$data[id]'>$data[llegada]</div></td>
                            <td><div class='hora' id='hfinserv-$data[id]'>$data[hfin]</div></td>
                            <td>".htmlentities($data['turno'])."</td>
                            <td>$data[tipo]</td>
                            <td>$data[i_v]</td>";
              if ($data['activo']){
                 $select.="<td><input class=\"boton\" type=\"button\" value=\"Desactivar\" id=\"$data[id]\"></td>
                           </tr>";
              }
              else{
                   $select.="<td><input class=\"boton\" type=\"button\" value=\"Activar\" id=\"$data[id]\"></td>
                             </tr>";
              }
        }
        mysql_free_result($result);
        mysql_close($conn);
        $select.= '</tbody>
                  </table>
                  </td>
                  </tr>
                  </table>
                   <script>
                           $(function(){
                           $(":button").button();
                           $.mask.definitions["~"]="[012]";
                           $.mask.definitions["%"]="[012345]";
                           $(".hora").mask("~9:%9");
                           $.editable.addInputType("masked", {
                                           element : function(settings, original){
                                                                                  var input = $("<input/>").mask(settings.mask);
                                                                                  $(this).append(input);
                                                                                  return(input);
                                                                                  }
                                                   });
                           $("select").selectmenu({width: 250});

                           $(".hora").editable("/nuevotrafico/modelo/procesa/servicios/upd-serv-crono.php", {type:"masked", mask: "~9:%9"});
                           });
                  </script>
                  <style>
#tabla th{
 padding:13px;
}
</style>';
        print $select;
     }elseif($accion == 'cghr'){
        $conn = conexcion();
        $sql="SELECT concat(date_format(hcitacion, '%H:%i,'), date_format(hsalida, '%H:%i,'), date_format(hllegada, '%H:%i,'), date_format(hfinserv, '%H:%i')) as horarios
              FROM servicios s
              WHERE (s.id = $_POST[hour]) and (s.id_estructura = $_SESSION[structure])";
        $result = mysql_query($sql, $conn);
        if (mysql_num_rows($result)){
           $data = mysql_fetch_array($result);
           $horario = $data['horarios'];
        }
        else{
             $horario = '0';
        }
        mysql_free_result($result);
        mysql_close($conn);
        print ($horario);
     }
     
     
     
     


     

?>

