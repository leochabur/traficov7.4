<?php
set_time_limit(0);
include ('controlador/bdadmin.php');


$conn = conexcion();

$sql = "select s.id as idServicio,
               ord.id as idOrden,
               c.id as idCronograma,
               c.nombre as Cronograma,
               cl.id as idCliente,
               cl.razon_social as Cliente,
               o.ciudad as Origen, 
               d.ciudad as Destino,
               ord.fservicio as Fecha_Servicio,
               u.interno as interno
from (select * from ordenes where fservicio between '2020-04-21' and '2020-04-21' and id_servicio is not null) ord
inner join servicios s on s.id = ord.id_servicio and s.id_estructura = ord.id_estructura_servicio
inner join cronogramas c on s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura
inner join ciudades o on o.id = ciudades_id_origen and o.id_estructura = ciudades_id_estructura_origen
inner join ciudades d on d.id = ciudades_id_destino and d.id_estructura = ciudades_id_estructura_destino
inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
left join unidades u on u.id = ord.id_micro
where c.activo and s.activo and cl.activo and not c.vacio and c.id_estructura = 1
LIMIT 10";

$result = mysql_query($sql, $conn) or die(mysql_error($conn));

$ordenes = array();
while ($row = mysql_fetch_array($result))
{
  $ordenes[] = array('idServicio' => $row['idServicio'],
                     'idOrden' => $row['idOrden'],
                     'idCronograma' => $row['idCronograma'],
                     'Cronograma' => $row['Cronograma'],
                      'idCliente' => $row['idCliente'],
                      'Cliente' => $row['Cliente'],
                      'Origen' => $row['Origen'],
                      'Destino' => $row['Destino'],
                      'Fecha_Servicio' => $row['Fecha_Servicio'],
                      'interno' => $row['interno']);

}

$payload = json_encode($ordenes);


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://paxtracker.mspivak.com/api/integrations/traffic/trips",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS =>"{'trips':[$payload]}",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer d8Ypl7DMuQsHjjW/INIHxRXjiV1BSezxrmbTV8EWZvk=",
    "Content-Type: text/plain"
  ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;

//die(var_dump($payload));

/*$curl = curl_init();
 
 curl_setopt_array($curl, array(
  CURLOPT_URL => "http://paxtracker.mspivak.com/api/integrations/traffic/trips",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_HTTPHEADER => array(
    "Authorization: Bearer d8Ypl7DMuQsHjjW/INIHxRXjiV1BSezxrmbTV8EWZvk="
  ),
));
 


curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);

//set the content type to application/json
curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

//return response instead of outputting
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

//execute the POST request
$result = curl_exec($curl);

$response = curl_exec($curl);
$err = curl_error($curl);
 


print $response;

 
if ($err) {
  echo "cURL Error #:" . $err;
} else {
$info = curl_getinfo($curl);
  echo 'Se tardó ', $info['total_time'], ' segundos en enviar una petición a ', $info['url'], "\n";
}
curl_close($curl);*/