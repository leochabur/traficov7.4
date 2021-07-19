<?php
     set_time_limit(0);
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }

  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');



                     $export = conexcion();//mysql_connect('200.80.61.27', 'mbexpuser', 'Mb2013Exp');
                     //mysql_select_db('mbexport', $export);
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');

$sql="SELECT ts.tipo, interno, if (anio is null, '', anio) as anio, if ((nueva_patente is null or nueva_patente=''), patente, nueva_patente) as patente, cantasientos
FROM ordenes o
inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
inner join tiposervicio ts on ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
inner join unidades u on u.id = o.id_micro
where o.fservicio between '$desde' and '$hasta' and o.id_estructura = $_POST[str] and id_cliente = $_POST[cli] and ts.id = $_POST[tpo] and not borrada and not suspendida
group by s.id_TipoServicio, id_micro
order by tipo, interno";

//die($sql);

$tabla='<table width="65%" id="example" name="example" class="ui-widget ui-widget-content" align="center">
         <thead>
                    <tr class="ui-widget-header">
                        <th>Interno</th>
                        <th>Dominio</th>
                        <th>Cant. Asientos</th>
                        <th>Modelo</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tbody>';

$result = mysql_query($sql, $export);

while ($data = mysql_fetch_array($result)){
               $tabla.="<tr>
                            <td align='center'>$data[1]</td>
                            <td align='right'>$data[3]</td>
                            <td align='center'>$data[4]</td>
                            <td align='right'>$data[2]</td>
                            </tr>";
}
     $tabla.='</tbody>
              </table>
              <script>
                      $(function(){

                                   });
              </script>
                  <style>
                         #example th{
                                padding:13px;
                                font-size: 82.5%;
                                }
                         #example tr{
                                padding:13px;
                                font-size: 80.5%;
                                }
                         .pad{
                                padding:10px;
                                font-size: 85%;
                                }
                         #example tbody tr:hover {

                                        background-color: #FF8080;

}
                  </style>';


print $tabla;

?>
