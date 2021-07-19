<?php
     session_start();
     define(RAIZ, '');
     //include ('/nuevotrafico/controlador/bdadmin.php');
     include ($_SERVER['DOCUMENT_ROOT'].'/controlador/bdadmin.php');
     $accion = $_POST['accion'];
     if ($accion == 'lcr'){
        $conn = conexcion();
        $cliente = $_POST['cli'];
        $sql="SELECT c.id, if(cs.id = 7,upper(concat(nombre,' (',cs.clase,')')), if(cs.id = 4,upper(concat(nombre,' (',cs.clase,')')), upper(nombre))) as nombre, km, date_format(tiempo_viaje, '%H:%i') as tiempo, upper(o.ciudad) as origen, upper(d.ciudad) as destino, upper(cs.clase) as clase
              FROM cronogramas c
              inner join ciudades o on ((c.ciudades_id_origen = o.id) and (c.ciudades_id_estructura_origen = o.id_estructura))
              inner join ciudades d on ((c.ciudades_id_destino = d.id) and (c.ciudades_id_estructura_destino = d.id_estructura))
              inner join claseservicio cs on ((c.claseServicio_id = cs.id) and (c.claseServicio_id_estructura = cs.id_estructura))
              WHERE (c.id_estructura = $_SESSION[structure]) and (activo) and ((c.id_cliente = $cliente) and (c.id_estructura_cliente = $_SESSION[structure]))
              ORDER BY c.nombre";
        $result = mysql_query($sql, $conn);
        $select = '<select id="servicios"  ><option>SELECCIONE UN SERVICIO</option>';
        while ($data = mysql_fetch_array($result)){
              $select.="<option value=\"$data[id]\">".htmlentities($data['nombre'])."</option>";
        }
        mysql_free_result($result);
        mysql_close($conn);
        $select.= '</select>
                  <script>
                          $("#servicios").change(function(){
                                                            var cronos = $("#servicios option:selected").val();
                                                            $.post("../paneles/comboDB.php",{crono: cronos, accion: \'lhr\'},function(data){
                                                                                                                                            $("#hora-servi").html(data);
                                                                                                                                            $(\'#horarios\').selectmenu({width: 350});
                                                                                                                                            });
                                                            });
                  </script>';
        print $select;
     }elseif($accion == 'lhr'){
        $conn = conexcion();
        $crono = $_POST['crono'];
        $sql="SELECT s.id, date_format(hcitacion, '%H:%i') as citacion, date_format(hsalida, '%H:%i') as salida
              FROM servicios s
              WHERE (s.id_estructura = $_SESSION[structure]) and ((s.id_cronograma = $crono) and (s.id_estructura_cronograma = $_SESSION[structure])) and (s.activo)
              order by hcitacion";
        $result = mysql_query($sql, $conn);
        $select = '<select id="horarios" name="horario"  class="ui-widget-content  ui-corner-all"><option>SELECCIONE UN HORARIO</option>';
        while ($data = mysql_fetch_array($result)){
              $select.="<option value=\"$data[id]\"><b>H. Citacion: $data[citacion]  H. Salida: $data[salida]</b></option>";
        }
        mysql_free_result($result);
        mysql_close($conn);
        $select.= '</select>
                   <script>
                          $("#horarios").change(function(){
                                                            var svr = $("#horarios option:selected").val();
                                                            $.post("../paneles/comboDB.php",{hour: svr, accion: \'cghr\'},function(data){
                                                                                                                                         if (data == 0){
                                                                                                                                            $(".hora").val(\'\');
                                                                                                                                         }
                                                                                                                                         else{
                                                                                                                                              var horarios = data.split(\',\');
                                                                                                                                              $("#hcitacion").val(horarios[0]);
                                                                                                                                              $("#hsalida").val(horarios[1]);
                                                                                                                                              $("#hllegada").val(horarios[2]);
                                                                                                                                              $("#hfinserv").val(horarios[3]);
                                                                                                                                              $("#km").val(horarios[4].trim());
                                                                                                                                         }
                                                                                                                                        });
                                                            });
                  </script>';
        print $select;
     }elseif($accion == 'cghr'){
        $conn = conexcion();
        $sql="SELECT concat(date_format(hcitacion, '%H:%i,'), date_format(hsalida, '%H:%i,'), date_format(hllegada, '%H:%i,'), date_format(hfinserv, '%H:%i,'),c.km,'') as horarios, c.km
              FROM servicios s
              INNER JOIN cronogramas c on ((c.id = s.id_cronograma)and(c.id_estructura = s.id_estructura_cronograma))
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

