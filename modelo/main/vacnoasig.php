<?
  session_start();
  include_once ('../../controlador/bdadmin.php');
  include_once ('../../modelo/utils/dateutils.php');
  
  $accion= $_POST['accion'];

  if($accion == 'loscva'){
       $conn = conexcion();
       $fecha="(date(now()) = fservicio)";
       if (isset($_POST['fecha'])){
          $fec = dateToMysql($_POST['fecha'], '/');
          $fecha="('$fec' = fservicio)";
       }
       $sql = "SELECT o.id, date_format(hcitacion, '%H:%i') as hcitacion, date_format(hsalida, '%H:%i') as hsalida, date_format(hfinservicio, '%H:%i') as hfinserv, o.nombre, upper(c.razon_social) as razon_social
               FROM ordenes o
               LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
               where $fecha and (o.id_estructura = $_SESSION[structure]) and (vacio) and (id_cliente_vacio is null)";

       $result = mysql_query($sql, $conn);
       $asignadas = "<fieldset>
                    <legend>Vacios sin asignar cliente del </legend>
                    <table id='ordasig' class='tablesorter' width='75%'>
                            <thead>
                            <tr>
                                <th>Orden</th>
                                <th>H. Citacion</th>
                                <th>H. Salida</th>
                                <th>Servicios</th>
                                <th>Cliente</th>
                            </tr>
                            </thead>
                            <tbody>";
       while ($data = mysql_fetch_array($result)){
             $asignadas.="<tr id='$data[id]'>
                              <td>$data[id]</td>
                              <td>$data[hcitacion]</td>
                              <td>$data[hsalida]</td>
                              <td>$data[nombre]</td>
                              <td>$data[razon_social]</td>
                          </tr>";
       }
       $asignadas.="</tbody>
                    </table>
                    </fieldset>
                    <script type='text/javascript'>
                            $('tbody tr').click(function(){
                                                                    var id_orden = $(this).attr('id');
                                                                    var dialogo = $('<div style=\"display:none\" id=\"dialogo\" class=\"loading\" align=\"center\"></div>').appendTo('body');
                                                                    dialogo.dialog({
                                                                                   close: function(event, ui) {dialog.remove();},
                                                                                   title: 'Asignar Cliente al Vacio',
                                                                                   width:650,
                                                                                   height:100,
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
                                                                                   dialogo.load('/vista/ordenes/asvacord.php',{orden:id_orden},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});
                                                            });
                    </script>
                    <style type='text/css'>
                           #ordasig tbody tr {cursor: pointer}
                    </style> ";
       mysql_free_result($result);
       mysql_close($conn);
       print $asignadas;
  }
  
?>

