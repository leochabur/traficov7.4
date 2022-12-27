<?php
session_start();
set_time_limit(0);
error_reporting(E_ALL & ~E_NOTICE); 
include ('../../controlador/ejecutar_sql.php');



if (isset($_POST['accion']))  
{
    $accion = $_POST['accion'];
    if($accion == 'viewload')
    {
        if (!($_POST['desde'] && $_POST['hasta']))
        {
            print "<b>Los campos desde y hasta no pueden permanecer en blanco.</b>";
            exit;
        }				  

        $desde = DateTime::createFromFormat('d/m/Y', $_POST['desde']);
        $hasta = DateTime::createFromFormat('d/m/Y', $_POST['hasta']);
        $desde = $desde->format('Y-m-d');
        $hasta = $hasta->format('Y-m-d');

        $sql = "SELECT date_format(fecha_carga, '%d/%m/%Y - %H:%i:%s') as hora,
                       odometro_pump as odometro, litros, tipo_combustible as tipo, dominio, interno, trx_id, km, odometro_urbetrack, km_urbe
                FROM carga_combustible_pump 
                WHERE date(fecha_carga) between '$desde' AND '$hasta'
                ORDER BY fecha_carga";


        $result = ejecutarSQL($sql);

        $tabla="<table class='table table-zebra' width='100%'>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>ID Pump Control</th>
                        <th>Fecha Hora Carga</th>                        
                        <th>Interno</th>
                        <th>Dominio</th>
                        <th>Odometro Pump Control</th>
                        <th>Odometro UrbeTrack</th>
                        <th>Producto</th>                                          
                        <th>Litros</th>
                        <th>Km Recorridos Pump Control</th>
                        <th>Km Recorridos Urbetrack</th>
                        <th>Consumo Estimado</th>
                    </tr>
                </thead>
                <tbody>";

        $i=1;
        while ($carga = mysql_fetch_array($result)) 
        {
            $interno = $carga['interno'];

            if (is_numeric($interno))
            {
                $interno = $interno + 0;
            }

            $litros = intval($carga['litros']);
            $consumo = '';
            if ($carga['km'])
            {
                $consumo = (($litros / $carga['km'])*100);
            }

             $tabla.="<tr>   
                             <td>$i</td>
                             <td>$carga[trx_id]</td>
                             <td>$carga[hora]</td>
                             <td>$interno</td>                                                  
                             <td>$carga[dominio]</td>
                             <td>$carga[odometro]</td>
                              <td>$carga[odometro_urbetrack]</td>
                             <td>$carga[tipo]</td>
                             <td align='right'>$carga[litros]</td>
                             <td align='right'>$carga[km]</td>
                              <td align='right'>$carga[km_urbe]</td>
                             <td align='right'>$consumo</td>
                        </tr>";
            $i++;
        }

     $tabla.="</tbody>
     </table>";
     print $tabla;

 }
}

?>

