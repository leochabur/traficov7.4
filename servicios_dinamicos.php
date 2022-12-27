<?php

$conn = new mysqli('190.216.31.40', 'c0mbexpuser', 'Mb2013Exp', 'c0mbexport');

$pasajeros = mysqli_query($conn, "SELECT  concat(apellido,',' , nombre) as apenom, 
										  dni, 
										  latitud, 
										  longtud, direccion, ciudad, activo FROM dinamic_pasajeros WHERE activo LIMIT 15");

$detail = [];

$servicio[0] = [
			 'idServicio' => '4896196',
			 'nombreServicio' => 'Turno Central Sierras Bayas Entrada',
			 'cliente' => 'PLANTA L´AMALÍ',
			 'horaLlegada' => '08:00:00',
			 'destino' => [
			 				'latitud' => -37.0377782712,
			 				'longitud' => -60.2963741777
			 				],
			 'pasajeros' => []
			 ];

foreach ($pasajeros as $pax)
{

	$servicio[0]['pasajeros'][] = [
								'dni' => $pax['dni'],
								'nombreApellido' => $pax['apenom'],
								'lugarSubida' => [
													'latitud' => $pax['latitud'],
													'longitud' => $pax['longtud']
												 ]
							  ];

}

print json_encode($servicio);


?>