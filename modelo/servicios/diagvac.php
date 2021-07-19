<?php
  session_start();
       error_reporting(1);
  include ('../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);

  $conn = conexcion();
  $sql="select o.nombre, o.id, id_ciudad_origen as id_origen, ori.ciudad as ciudad_origen, id_ciudad_destino as id_destino, des.ciudad as destino,
      			id_ciudad as id_base, upper(bas.ciudad) as base, distancia, tiempo, hos.citacion, hos.salida, hos.llegada, hos.finalizacion, id_micro, interno
		from ordenes o
		inner join ciudades ori on ori.id = o.id_ciudad_origen and ori.id_estructura = o.id_estructura_ciudad_origen
		inner join ciudades des on des.id = o.id_ciudad_destino and des.id_estructura = o.id_estructura_ciudad_destino
		left join distanciasRecorridos dr on dr.id_origen = ori.id and dr.id_estructura_origen = ori.id_estructura and dr.id_destino = des.id and dr.id_estructura_destino = des.id_estructura
		inner join horarios_ordenes_sur hos on hos.id_orden = o.id and hos.id_estructura_orden = o.id_estructura
		inner join baseoperativaunidades bo on bo.id_unidad = id_micro
		inner join baseoperativaxestructura boxe on boxe.id = bo.id_baseoperativa and boxe.id_estructura = o.id_estructura
		inner join ciudades bas on bas.id = id_ciudad and bas.id_estructura = id_estructura_ciudad
		inner join unidades u on u.id = o.id_micro
		where o.id_estructura = 2 and year(fservicio) = 2018
		order by id_micro, hos.citacion";

  try{
  		  $table = "<table border='1'>";
		  $result = ejecutarSQL($sql, $conn);
		  $row = mysql_fetch_array($result);
		  while ($row){
		  		$micro = $row['id_micro'];
		  		$inicio = true;
		  		while (($row) && ($micro == $row['id_micro'])){
		  			if ($inicio){ ///inicia el recorrido
		  				if ($row['id_origen'] != $row['id_base']){///no sale de la base donde esta asignada la unidad, debe busvcar los datos del recorido
		  					$data = getKmTmpo($conn, $row);
		  					$saleServicio = DateTime::createFromFormat('Y-m-d H:i:s', $row['citacion']);
		  					$llegaServicio = DateTime::createFromFormat('Y-m-d H:i:s', $row['finalizacion']);
		  					$saleVacio = clone $saleServicio;
		  					$saleVacio->sub(new DateInterval("PT$data[1]M"));
		  					$table.= "<tr><td>VACIO - $row[base] - $row[ciudad_origen]</td><td>".$saleVacio->format('d/m/Y - H:i')."</td><td>".$saleServicio->format('d/m/Y - H:i')."</td><td>$row[interno]</td></tr>";
		  					
		  				}
		  				$inicio = false;
		  			}
		  			else{
		  				if ($ultorden['id_destino'] != $row['id_origen']){

		  					$data = getKmTmpo($conn, array('id_base' => $ultorden['id_destino'], 'id_origen' => $row[id_origen], 'base'=> $ultorden['destino'], 'ciudad_origen'=> $row['ciudad_origen']));
		  				}

		  			}
		  			$table.= "<tr><td>$row[nombre]</td><td>".$saleServicio->format('d/m/Y - H:i')."</td><td>".$llegaServicio->format('d/m/Y - H:i')."</td><td>$row[interno]</td></tr>";
		  			$ultorden = $row;
		  			$row = mysql_fetch_array($result);
		  		}
		  }
		  $table.="</table>";
		  print $table;
	}catch (Exception $e){
		print $e->getMessage();
	}




  function getKmTmpo($conn, $row){
  					$sql = "SELECT distancia, round(tiempo/60)
			                FROM distanciasRecorridos
			                where id_origen = $row[id_base] and id_estructura_origen = $_SESSION[structure] and id_destino = $row[id_origen] and id_estructura_destino = $_SESSION[structure]";
			        $kmtpo = ejecutarSQL($sql, $conn);
			        $rowKmTpo = mysql_fetch_array($kmtpo);
			        if (!$rowKmTpo){ ////no existen datos para el orien y el destinodados
			        	throw new Exception("No se pudo calcular la ruta para el recorrido $row[base] - $row[ciudad_origen] <a href='../../vista/ordenes/modkmtpo.php?des=$row[id_base]&has=$row[id_origen]' title='Crear' target='_blank'><b><h2>Click Aqui Para Agregar Recorrido Manualmente</h2></b></a>");
			        }
			        return $rowKmTpo;  	

  }