<?
set_time_limit(0);
  session_start();
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  if($accion == 'res'){
     $rango = "$_POST[desde]  hasta  $_POST[hasta]";
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');

     $sql = "select legajo, concat(apellido,', ',nombre), nrodoc, domicilio, date_format(inicio_relacion_laboral, '%d/%m/%Y')
from empleados
where (inicio_relacion_laboral <= '$desde') and (id_empleado <> 249) and (activo) and (afectado_a_estructura = $_POST[str]) and  (not borrado) and (id_empleador = 1) and (id_cargo = $_POST[cgo]) and id_empleado not in (
SELECT id_empleado
FROM novedades n
inner join cod_novedades cn on cn.id = n.id_novedad
where (afecta_incentivo) and (n.activa) and ((desde between '$desde' and '$hasta') or (hasta between '$desde' and '$hasta'))
union
SELECT id_empleado
FROM siniestros
where fecha_siniestro between '$desde' and '$hasta' and not borrada
union
SELECT id_conductor_1
FROM infracciones
where fecha between '$desde' and '$hasta' and not eliminada and id_conductor_1 is not null
union
SELECT id_conductor_2
FROM infracciones
where fecha between '$desde' and '$hasta' and not eliminada and id_conductor_2 is not null
union
SELECT id_conductor_3
FROM infracciones
where fecha between '$desde' and '$hasta' and not eliminada and id_conductor_3 is not null
union
SELECT id_empleado
FROM descargos d
left join resolucionSiniestro rd on rd.id = d.resolucion
where fecha_emision between '$desde' and '$hasta' and not eliminado and rd.afecta_incentivo)
order by apellido, nombre";
  //   die($sql);

     $conn = conexcion();
     
     $result = mysql_query($sql, $conn) or die (mysql_error($conn));
     $tabla_in='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>
                           <tr class="ui-widget-header">
                               <th>Legajo</th>
                               <th>Apellido, Nombre</th>
                               <th>DNI</th>
                               <th>Direccion</th>
                           </tr>
                    </thead>
                    <tbody>';
     $data = mysql_fetch_array($result);
     while ($data){
                             $tabla_in.="<tr>
                                      <td>$data[0]</td>
                                      <td>$data[1]</td>
                                      <td>$data[2]</td>
                                      <td>$data[3]</td>
                                      </tr>";

                       $data = mysql_fetch_array($result);
     }
     $tabla_in.='</tbody>
              </table>';
              
    ///////////////out
    
     $sql = "SELECT *
             FROM (
          select legajo, concat(apellido,', ',nombre) as apenom, nrodoc, domicilio, id_empleado, date_format(inicio_relacion_laboral, '%d/%m/%Y'), activo, borrado, id_cargo, id_empleador, afectado_a_estructura
from empleados
where id_empleado not in (
select id_empleado
from empleados
where (inicio_relacion_laboral <= '$desde') and (id_empleado <> 249) and (activo) and (afectado_a_estructura = $_POST[str]) and  (not borrado) and (id_empleador = 1) and (id_cargo = $_POST[cgo]) and id_empleado not in (
SELECT id_empleado
FROM novedades n
inner join cod_novedades cn on cn.id = n.id_novedad
where (afecta_incentivo) and (n.activa) and ((desde between '$desde' and '$hasta') or (hasta between '$desde' and '$hasta'))
union
SELECT id_empleado
FROM siniestros
where fecha_siniestro between '$desde' and '$hasta' and not borrada
union
SELECT id_conductor_1
FROM infracciones
where fecha between '$desde' and '$hasta' and not eliminada and id_conductor_1 is not null
union
SELECT id_conductor_2
FROM infracciones
where fecha between '$desde' and '$hasta' and not eliminada and id_conductor_2 is not null
union
SELECT id_conductor_3
FROM infracciones
where fecha between '$desde' and '$hasta' and not eliminada and id_conductor_3 is not null
union
SELECT id_empleado
FROM descargos d
left join resolucionSiniestro rd on rd.id = d.resolucion
where fecha_emision between '$desde' and '$hasta' and not eliminado and rd.afecta_incentivo))) o
where (id_empleado <> 249) and (activo) and (afectado_a_estructura = $_POST[str]) and  (not borrado) and (id_empleador = 1) and (id_cargo = $_POST[cgo])
order by apenom";
 //    die($sql);


     $result = mysql_query($sql, $conn) or die (mysql_error($conn));
     $tabla_out='<table id="example_out" name="example_out" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>
                           <tr class="ui-widget-header">
                               <th>Legajo</th>
                               <th>Apellido, Nombre</th>
                               <th>DNI</th>
                               <th>Direccion</th>
                               <th>Fecha Ingreso</th>
                           </tr>
                    </thead>
                    <tbody>';
     $data = mysql_fetch_array($result);
     while ($data){
                             $tabla_out.="<tr id='$data[4]' class='exc'>
                                      <td>$data[0]</td>
                                      <td>$data[1]</td>
                                      <td>$data[2]</td>
                                      <td>$data[3]</td>
                                      <td>$data[5]</td>
                                      </tr>";

                       $data = mysql_fetch_array($result);
     }
     $tabla_out.='</tbody>
              </table>';
              
     $detalle = "
                $('.exc').click(function(){
                                           var nom;
                                           $(this).each(function(index, element){
                                                                                   nom = $(element).find('td').eq(1).html();
                                                                                 });
                                           var conductor = $(this).attr('id');
                                           var dialog = $('<div style=\"display:none\" id=\"dialog\" class=\"loading\" align=\"center\"></div>').appendTo('body');
                                           dialog.dialog({
                                                                                   close: function(event, ui) {dialog.remove();},
                                                                                   title: 'Detalle de exclusion',
                                                                                   width:850,
                                                                                   height:450,
                                                                                   modal:true,
                                                                                         show: {
                                                                                                effect: 'blind',
                                                                                                duration: 300
                                                                                         },
                                                                                         hide: {
                                                                                               effect: 'blind',
                                                                                               duration: 300
                                                                                               }
                                                                                   });
                                                                                   dialog.load('/modelo/rrhh/ictvo/geninc.php',{emp:conductor, accion:'dexc', desde:$('#desde').val(), hasta:$('#hasta').val(), nomc:nom},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});

                                             });";
    
    
    ////////////////////// end out




















     $output='<div id="tabs">
  <ul>
    <li><a href="#tabs-2">Dentro del programa</a></li>
    <li><a href="#tabs-1">Fuera del programa</a></li>

  </ul>
  <div id="tabs-1">
     '.$tabla_out.'
  </div>
  <div id="tabs-2">
  '.$tabla_in.'
  </div>
</div>
<style>
                         #example { font-size: 85%; }
                         #example tbody tr:hover {

                                        background-color: #FF8080;
                                        }
                         #example_out { font-size: 85%; }
                         #example_out tbody tr:hover {

                                        background-color: #FF8080;
                                        }
                                        
                         #example tr:nth-child(odd) {
                                           background-color:#f2f2f2;
                                           }
                         #example tr:nth-child(even) {
                                            background-color:#fbfbfb;
                                            }
                         #example_out tr:nth-child(odd) {
                                           background-color:#f2f2f2;
                                           }
                         #example_out tr:nth-child(even) {
                                            background-color:#fbfbfb;
                                            }
                  </style>
                    <script>
                            $( function() {
                                          $( "#tabs" ).tabs();
                                          '.$detalle.'
                                          } );
                                          
                    </script>';
    print $output;
  }
elseif($accion == 'dexc'){
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
$sql="SELECT 'NOVEDAD' as causa, date_format(desde, '%d/%m/%Y') as desde, date_format(hasta, '%d/%m/%Y') as hasta, upper(nov_text) as detalle
FROM novedades n
inner join cod_novedades cn on cn.id = n.id_novedad
where (afecta_incentivo) and (n.activa) and ((desde between '$desde' and '$hasta') or (hasta between '$desde' and '$hasta')) and (id_empleado = $_POST[emp])
union
SELECT 'SINIESRTO' as causa, date_format(fecha_siniestro, '%d/%m/%Y') as desde, date_format(fecha_siniestro, '%d/%m/%Y') as hasta, cast(concat('SINIESTRO OCURRIDO EN: ', upper(ciudad), ' CON EL INTERNO: ', interno) as char) as detalle
FROM siniestros s
left join unidades u on u.id = s.id_coche
left join ciudades c on c.id = s.id_localidad
where fecha_siniestro between '$desde' and '$hasta' and not borrada and (id_empleado = $_POST[emp])
union
SELECT 'INFRACCION' as causa, date_format(fecha, '%d/%m/%Y') as desde, date_format(fecha, '%d/%m/%Y') as hasta, upper(infraccion) as detalle
FROM infracciones i
left join tipo_infraccion ti on ti.id = i.id_tipo_infraccion
where fecha between '$desde' and '$hasta' and not eliminada and ($_POST[emp] in (id_conductor_1, id_conductor_2, id_conductor_3))
union
SELECT 'PEDIDO DE EXPLICACION' as causa, date_format(fecha_emision, '%d/%m/%Y') as desde, date_format(fecha_emision, '%d/%m/%Y') as hasta, descripcion_hecho as detalle
FROM descargos d
left join resolucionSiniestro rs on rs.id = d.resolucion
where fecha_emision between '$desde' and '$hasta' and not eliminado and (id_empleado = $_POST[emp]) and rs.afecta_incentivo";
    //           die($sql);
     $conn = conexcion();

     $result = mysql_query($sql, $conn) or die (mysql_error($conn));
     $tabla_in='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>
                           <tr class="ui-widget-header" >
                               <th colspan="5">Detalle exclusiones de '.$_POST[nomc].'</th>
                           </tr>
                           <tr class="ui-widget-header" colspan="">
                               <th>Causa exclusion</th>
                               <th>Fecha desde</th>
                               <th>Fecha hasta</th>
                               <th>Detalle</th>
                               <th>Resolucion</th>
                           </tr>
                    </thead>
                    <tbody>';
     $data = mysql_fetch_array($result);
     while ($data){
                             $tabla_in.="<tr>
                                      <td>$data[0]</td>
                                      <td>$data[1]</td>
                                      <td>$data[2]</td>
                                      <td>$data[3]</td>
                                      <td></td>
                                      </tr>";

                       $data = mysql_fetch_array($result);
     }
     $tabla_in.='</tbody>
              </table>
              <style>
                         #example { font-size: 85%; }
                         #example tbody tr:hover {
                                                 background-color: #FF8080;
                                                 }
                         #example tr:nth-child(odd) {
                                           background-color:#f2f2f2;
                                           }
                         #example tr:nth-child(even) {
                                            background-color:#fbfbfb;
                                            }
              </style>';
     print $tabla_in;

}
  
?>

