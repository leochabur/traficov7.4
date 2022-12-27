<?php
/**
 *
 * @About:      API Interface
 * @File:       index.php
 * @Date:       $Date:$ Nov-2015
 * @Version:    $Rev:$ 1.0
 * @Developer:  Federico Guzman (federicoguzman@gmail.com)
 **/

/* Los headers permiten acceso desde otro dominio (CORS) a nuestro REST API o desde un cliente remoto via HTTP
 * Removiendo las lineas header() limitamos el acceso a nuestro RESTfull API a el mismo dominio
 * Nótese los métodos permitidos en Access-Control-Allow-Methods. Esto nos permite limitar los métodos de consulta a nuestro RESTfull API
 * Mas información: https://developer.mozilla.org/en-US/docs/Web/HTTP/Access_control_CORS
 **/
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: text/html; charset=utf-8');
header('P3P: CP="IDC DSP COR CURa ADMa OUR IND PHY ONL COM STA"'); 

//require_once ('../../libs/nusoap.php');


/* Puedes utilizar este file para conectar con base de datos incluido en este demo; 
 * si lo usas debes eliminar el include_once del file Config ya que le mismo está incluido en DBHandler 
 **/
//require_once '../include/DbHandler.php'; 

require '../libs/Slim/Slim.php'; 
\Slim\Slim::registerAutoloader(); 
$app = new \Slim\Slim();

 function distanceGPS($lat1, $lon1, $lat2, $lon2, $unit) {
  
   $theta = $lon1 - $lon2;
   $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
   $dist = acos($dist);
   $dist = rad2deg($dist);
   $miles = $dist * 60 * 1.1515;
   $unit = strtoupper($unit);
  
   if ($unit == "K") {
     return ($miles * 1.609344);
   } else if ($unit == "N") {
       return ($miles * 0.8684);
     } else {
         return $miles;
       }
 }

 function distancia($x1, $y1, $x2, $y2)
 {
      $moduloRaiz = pow(($x2 - $x1), 2) + pow(($y2 - $y1), 2);
      return sqrt($moduloRaiz);
 }


 function distanciaALaRecta($p1, $p2, $pos)
 {
      $x1 = $p1['x'];
      $y1 = $p1['y'];

      $x2 = $p2['x'];
      $y2 = $p2['y'];

      $px = $pos['x'];
      $py = $pos['y'];

      return distanceGPS($x1, $y1, $px, $py, 'K');
 }

function getPosInterno($interno)
{
    
   /* $oSoapSClient = new nusoap_client('https://app.urbetrack.com/App_services/Operation.asmx?wsdl', true);
    $params = array();
    $params['usuario'] = 'masterbus_trafico';
    $params['hash'] = '85CF3EC9C355539B74F36AB7D03BBC1C';
    $params['interno'] = "$interno";
    $resultado = $oSoapSClient->call('ApiGetLocationByVehicle', $params );
    $lati =$resultado['ApiGetLocationByVehicleResult']['Resultado']['Latitud'];
    $long =$resultado['ApiGetLocationByVehicleResult']['Resultado']['Longitud'];
   /* $int =$resultado['ApiGetLocationByVehicleResult']['Resultado']['Interno']." (".$resultado['ApiGetLocationByVehicleResult']['Resultado']['Patente'].")";
    $velocidad = $resultado['ApiGetLocationByVehicleResult']['Resultado']['Velocidad'];
    $fecha_hora =  $resultado['ApiGetLocationByVehicleResult']['Resultado']['Fecha'];*/
    return ['x' => $lati, 'y' => $long];
}

function procesarParadas($gpx, $pos)
{

   // return [0 => 'wwww', 1 => 'tyyyyy '.count($gpx->wpt)."   $pos[x]  $pos[y]  "];

     $listaParadas = [];
     $i = $auxi = 0;
     $dist = 9999999999999;
     foreach ($gpx->wpt as $wpt)
     {
          if ($i < (count($gpx->wpt) -1))
          {
               $px = round((float)$wpt['lat'], 5);
               $py = round((float)$wpt['lon'], 5);

               //return [0 => [$px, $py]];
               $listaParadas[$i] = ['name' => (String)$wpt->name, 'point' => ['x' => $px, 'y' => $py], 'recom' => 0, 'posrecta' => 999999, 'dist' => 999999];
               $auxdist = distancia($pos['x'], $pos['y'], $px, $py);
               if ($auxdist < $dist)
               {
                    $dist = $auxdist;
                    $auxi = $i;
               }
          }
          $i++;
     }
     $listaParadas[$auxi]['recom'] = 1;
     return [0 => $listaParadas, 1 => $listaParadas[$auxi]];
}


$app->post('/predictivo/calcular/:orden', function ($orden) use ($app) {

        function prueba($data){
            return $data;
        }

        $result = ejecutarSQLPDO("SELECT hsalidaplantareal as horaSalida,
                                           fservicio as fechaServicio,
                                           ord.id as iOrdenTrabajo,
                                           interno,
                                           s.id_cronograma as idExterno,
                                           if (hllegadaplantareal < hsalidaplantareal,
                                                                                    ADDDATE(CONCAT(fservicio,' ', hllegadaplantareal), INTERVAL 1 DAY),
                                                                                    CONCAT(fservicio,' ', hllegadaplantareal)) as horaLlegada,

                                           if (hsalidaplantareal < hcitacionreal,
                                                                                    ADDDATE(CONCAT(fservicio,' ', hcitacionreal), INTERVAL 1 DAY),
                                                                                    CONCAT(fservicio,' ', hcitacionreal)) as horasalida,

                                            gpx_file
                                    FROM (SELECT borrada, id_micro, nombre, id , hcitacionreal, hsalidaplantareal, fservicio, id_servicio, id_estructura_servicio, hllegadaplantareal
                                          FROM ordenes
                                          WHERE id = $orden) ord

                                    JOIN servicios s ON s.id = ord.id_servicio AND s.id_estructura = ord.id_estructura_servicio
                                    JOIN cronogramas_gpx cgpx ON cgpx.id_cronograma = s.id_cronograma
                                    JOIN unidades u ON u.id = ord.id_micro");

        $row = mysqli_fetch_array($result);

        if ($row)
        {
            $request = $app->request();
            $body = $request->getBody();
            $input = json_decode($body, true); 


            $llegada = DateTime::createFromFormat('Y-m-d H:i:s', $row['horaLlegada']);
            $salida = DateTime::createFromFormat('Y-m-d H:i:s', $row['horasalida']);
            $now = new DateTime();
            
            if ($now > $llegada)
            {
                echoResponse(200, ['status' => 301, 'message' => 'El servicio ya ha finalizado']);
            }
            elseif ($salida > $now)
            {
                //el servicio aun no ha iniciado, solo deberia devolver la parada mas cercana al usuario
               // echoResponse(200, ['status' => 301, 'message' => 'Todavia no salio']);

                //$posUnidad = getPosInterno($row['interno']);

                $gpx = simplexml_load_file($_SERVER['DOCUMENT_ROOT']."/gpx/files/$row[gpx_file]");

                $paradas = procesarParadas($gpx, ['x' => $input['posicionPasajero']['latitud'], 'y' => $input['posicionPasajero']['longitud']]);  
                return echoResponse(200, $paradas[1]);

                
            }
            else
            {

                $orden = array(
                                'iOrdenTrabajo' => $row['iOrdenTrabajo'],
                                 'fechaServicio' => $row['fechaServicio'],
                                 'horaSalida' => $row['horaSalida'],
                                 'interno' => $row['interno'],
                                 'idExterno' => $row['idExterno'],
                                 'horaLlegada' => $row['horaLlegada'],
                                 'gpx' => $row['gpx_file']
                             );

                $response = ['status' => 200, 'data' => $orden];   
                echoResponse(200, $response);
            }
        }
        else
        {
            $response = ['status' => 300, 'message' => 'No se encuentra la orden de trabajo con numero '];


            $request = $app->request();
            $body = $request->getBody();


            $input = json_decode($body, true); 

            echoResponse(200, $response);
        }





});


$app->get('/orden/:ord', function ($ord) use ($app) {
    try
    {


        $result = ejecutarSQLPDO("SELECT hsalidaplantareal as horaSalida,
                                           fservicio as fechaServicio,
                                           ord.id as iOrdenTrabajo,
                                           interno,
                                           s.id_cronograma as idExterno,
                                           if (hllegadaplantareal < hsalidaplantareal,
                                                                                    ADDDATE(CONCAT(fservicio,' ', hllegadaplantareal), INTERVAL 1 DAY),
                                                                                    CONCAT(fservicio,' ', hllegadaplantareal)) as horaLlegada
                                    FROM (SELECT borrada, id_micro, nombre, id , hsalidaplantareal, fservicio, id_servicio, id_estructura_servicio, hllegadaplantareal
                                          FROM ordenes
                                          WHERE id = $ord) ord

                                    JOIN servicios s ON s.id = ord.id_servicio AND s.id_estructura = ord.id_estructura_servicio
                                    JOIN unidades u ON u.id = ord.id_micro");

        $row = mysqli_fetch_array($result);

        $response = array();

        if ($row)
        {
            $orden = array(
                            'iOrdenTrabajo' => $row['iOrdenTrabajo'],
                             'fechaServicio' => $row['fechaServicio'],
                             'horaSalida' => $row['horaSalida'],
                             'interno' => $row['interno'],
                             'idExterno' => $row['idExterno'],
                             'horaLlegada' => $row['horaLlegada']
                         );

            $response = ['ok' => true, 'data' => $orden];   
        }
        else
        {
            $response = ['ok' => false, 'message' => 'No se encuentra la orden de trabajo con numero '];
        }

        echoResponse(200, $response);

    }
    catch (Exception $e){
                            echoResponse(200, array('ok' => false, 'message' => 'ERROR AL EJECUTAR LA CONSULTA'));
                        }
});

$app->get('/gpx/:cron', function ($cron) use ($app) {
    try
    {


        $result = ejecutarSQLPDO("SELECT * FROM cronogramas_gpx WHERE id_cronograma = $cron");

        $row = mysqli_fetch_array($result);

        $response = array();

        if ($row)
        {
            $image = file_get_contents("../../gpx/files/$row[gpx_file]");

            $base64 = base64_encode($image);

            $response = ['ok' => true, 'file' => $row['gpx_file'], 'gpx' => $base64];   
        }
        else
        {
            $response = ['ok' => false, 'message' => 'No se encuentra el archivo gpx'];
        }

        echoResponse(200, $response);

    }
    catch (Exception $e){
                            echoResponse(200, array('ok' => false, 'message' => 'ERROR AL EJECUTAR LA CONSULTA'));
                        }
});

$app->get('/servicios/proximos', function () use ($app) {
    try
    {
        $sql = "SELECT concat(ord.nombre, ' - ', time_format(hsalidaplantareal, '%H:%i'))  as servicio,
                       ord.id as iOrdenTrabajo,
                       hcitacionreal as hcitacion,
                       hsalidaplantareal as hsalida,
                       hllegadaplantareal as hllegada,
                       hfinservicioreal as hfinalizacion,
                       CONCAT(apellido,', ', emp.nombre) as conductor,
                       interno,
                       o.ciudad as origen,
                       d.ciudad as destino,
                       s.id_cronograma as idExterno
                FROM (SELECT id_chofer_1, hcitacionreal, vacio, borrada, id_ciudad_origen, id_ciudad_destino, id_servicio, id_micro, nombre, id, hllegadaplantareal , hsalidaplantareal,
                             hfinservicioreal, id_estructura, id_cliente, fservicio, id_estructura_servicio
                     FROM ordenes
                     WHERE id_estructura = 1 and not borrada and fservicio between DATE_SUB(DATE(NOW()), INTERVAL 1 DAY) AND DATE_ADD(DATE(NOW()), INTERVAL 1 DAY)) ord
                JOIN ciudades o on ord.id_ciudad_origen = o.id
                JOIN ciudades d on d.id = ord.id_ciudad_destino
                LEFT JOIN empleados emp ON emp.id_empleado = ord.id_chofer_1
                JOIN (SELECT i_v, id, id_estructura, id_cronograma from servicios where id_estructura = 1) s ON s.id = ord.id_servicio AND s.id_estructura = ord.id_estructura_servicio
                JOIN unidades u ON u.id = ord.id_micro
                WHERE s.i_v = 'i' AND NOW() BETWEEN DATE_SUB(CONCAT(fservicio,' ', ord.hsalidaplantareal), INTERVAL 120 MINUTE) AND
                      DATE_ADD(CONCAT(fservicio,' ', ord.hfinservicioreal), INTERVAL 180 MINUTE) AND
                      (id_cliente = 10 OR ord.nombre like '%rondin%') AND vacio = 0 AND borrada = 0 AND
                      ord.id_estructura = 1
                ORDER BY ord.nombre";

        $conn = mysqli_connect('mariadb-masterbus-trafico.planisys.net', 'c0mbexpuser', 'Mb2013Exp', 'c0mbexport');

        $result = mysqli_query($conn, $sql);
        $ordenes = [];

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $ordenes[] = $row;
        }

        echoResponse(200, $ordenes);

    }
    catch (Exception $e){
                            echoResponse(500, array('msge' => 'ERROR AL EJECUTAR LA CONSULTA'));
                        }
});


$app->get('/recorridos/next/:str/:int', function ($str, $int) use ($app) {
    try
    {
        $sql = "SELECT o.id, o.hsalidaplantareal, fservicio, o.nombre
                from ordenes o
                inner join servicios s on s.id = id_servicio and s.id_estructura = id_estructura_servicio
                inner join cronogramas c on c.id = id_cronograma and c.id_estructura = id_estructura_cronograma
                inner join unidades u on u.id = o.id_micro
                where not o.borrada and not o.suspendida and o.id_estructura = $str and interno = $int and concat(fservicio,' ', o.hsalidaplantareal) >= now() and tipoServicio = 'charter' and isDinamic
                order by concat(fservicio,' ', o.hsalidaplantareal) ASC
                LIMIT 1";
        $result = ejecutarSQLPDO($sql);
        $row = mysqli_fetch_array($result);
        if ($row)
        {
            echoResponse(200, array('idServicio' => $row['id'], 'horaSalida' => $row['hsalidaplantareal'], 'nombre' => $row['nombre'], 'fechaServicio' => $row['fservicio']));
        }
        else
        {
            echoResponse(404, array('msge' => 'No se encontro el servicio!!'));
        }
    }
    catch (Exception $e){
                            echoResponse(404, array('msge' => 'ERROR AL EJECUTAR LA CONSULTA'));
                        }
});


$app->post('/wpt/register', function () use ($app) {



    try
    {

            $request = $app->request();
            $body = $request->getBody();


            $input = json_decode($body, true); 

            if (!(json_last_error() == JSON_ERROR_NONE)) 
            {
                echoResponse(200, array('ok' => 'JSON MAL FORMADO'));
            }
            else
            {
               // echoResponse(200, array('ok' => $input['busID']));
              //  verifyRequiredParams(array('busID', 'tripID', 'wptID', 'lat', 'lon', 'arrival', 'duration'));

                if (!($input['busID'] && $input['tripID'] && $input['wptID'] &&  is_numeric($input['lat']) && is_numeric($input['lon']) && $input['arrival'] && $input['duration']))
                {
                    echoResponse(400, array('ok' => 'false', 'message' => 'Todos los campos son requeridos'));
                }
                else
                {
                    $sql = "INSERT INTO ap_registro_paradas (interno, servicio, parada, latitud, longitud, arrival, duration)
                            VALUES ('$input[busID]', '$input[tripID]', '$input[wptID]', $input[lat], $input[lon], $input[arrival], $input[duration])";

                    ejecutarSQLPDO($sql);
                    echoResponse(200, array('ok' => 'true'));
                }

            }
    }
    catch(\Exception $e){
                        echoResponse(500, array('ok' => 'ERROR' ));
    }
});

$app->post('/sending', function () use ($app) {

    $request = $app->request();
    $body = $request->getBody();
    $input = json_decode($body, true); 
    $array = $input['destinatarios'];
     $mail = new PHPMailer(true);
     try
     {
        $correo_emisor="soportetrafico@masterbus.net";
        $contrasena="Mas#Ter%21";
            $mail->isSMTP();                                           
         //   $mail->Host       = 'mail.airepampa.com.ar';                    
            $mail->SMTPAuth   = true;                                   
            $mail->Username   = $correo_emisor;//'avisos@airepampa.com.ar';                   
            $mail->Password   = $contrasena;//'leo181979';                                     
         //   $mail->Port       = 25;                                   
            $mail->FromName = 'Sistema Aire Pampa';
            //$mail->addAddress('avisos@airepampa.com.ar', 'Sistema');
           // $mail->addAddress('leochabur@gmail.com', 'Sistema');
            $mail->addAddress('info@airepampa.com.ar', '');
            foreach ($array as $val)
            {
                if (validateEMAIL($val['mail']))
                {
                    $mail->addAddress($val['mail'], ''); 
                }
            }
            $mail->isHTML(true);                          
            $mail->Subject = $input['subject'];
            $mail->Body    = $input['body'];
            $mail->send();
            echoResponse(200, array('message' => 'Correps enviados exitosamente'));

    }
    catch(\Exception $e){echoResponse(400, array('ok' => 'ERROR '.$e->getMessage()));}
});

function validateEMAIL($EMAIL) {

    return filter_var($EMAIL, FILTER_VALIDATE_EMAIL);
    /*$v = "/[a-zA-Z0-9_-.+]+@[a-zA-Z0-9-]+.[a-zA-Z]+/";

    return (bool)preg_match($v, $EMAIL);*/
}

/* Usando GET para consultar los autos */

//$app->post('/close', 'authenticate', function() use ($app){
$app->post('/close', function() use ($app){

    verifyRequiredParams(array('fecha', 'bus'));

    $response = array();
    //capturamos los parametros recibidos y los almacxenamos como un nuevo array


    $bus  = $app->request->params('bus');
    $fechaEvento = $app->request->params('fecha');
    $fechaEvento = str_replace('T', ' ', $fechaEvento);
    $fechaEvento = DateTime::createFromFormat('Y-m-d H:i:s', $fechaEvento);
    if (!$fechaEvento)
    {
        $response['message'] = 'Formato de fecha invalido!';
        echoResponse(201, $response);        
        $app->stop();
    }
    $fechaEvento->sub(new DateInterval('PT3H'));

    $conn = conexcion(true);
    $sql = "SELECT idOrden, razon_social, hllegadaplantareal, o.nombre, fservicio
            from (select id_micro, id as idOrden, id_servicio, id_estructura_servicio,
                         if(hllegadaplantareal < hcitacionreal, concat(DATE_ADD(fservicio, INTERVAL 1 DAY),' ', hllegadaplantareal), concat(fservicio,' ', hllegadaplantareal)) as llegadaPlanta, hllegadaplantareal, nombre, id_cliente, fservicio from ordenes) o 
            inner join unidades u on u.id = o.id_micro 
            inner join clientes c on c.id = o.id_cliente
            inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
            where llegadaPlanta between subtime('".$fechaEvento->format('Y-m-d H:i:s')."','00:20:00') AND addtime('".$fechaEvento->format('Y-m-d H:i:s')."', '00:20:00') and interno = $bus and s.i_v = 'i'";

    $result = mysqli_query($conn, $sql);
    $rows = mysqli_num_rows($result); //total de resultados de la consulta
    if ($rows > 1) //hay mas de un resultado, no puede ejecutar el cierre
    {
        $dtEvento = $fechaEvento->format('Y-m-d H:i:s');
        $insert = "INSERT INTO comunicacionesUrbeTrack (fechaHoraEvento, fechaHoraRecepcion, interno, accion, numeroRegistros) 
                    VALUES ('$dtEvento', now(), $bus, 'Se detectaron mas de una orden, no es posible aplicar el cierre automatico', $rows)";

        mysqli_query($conn, $insert);
    }
    elseif($rows == 0)
    {
        $dtEvento = $fechaEvento->format('Y-m-d H:i:s');
        $insert = "INSERT INTO comunicacionesUrbeTrack (fechaHoraEvento, fechaHoraRecepcion, interno, accion, numeroRegistros) 
                    VALUES ('$dtEvento', now(), $bus, 'No existen ordenes que puedan ser cerradas en el rango horario', $rows)";

        mysqli_query($conn, $insert);
    }
    else
    {
        $data = mysqli_fetch_array($result);

        $dtEvento = $fechaEvento->format('Y-m-d H:i:s');
        $insert = "INSERT INTO comunicacionesUrbeTrack (fechaHoraEvento, fechaHoraRecepcion, interno, cliente, servicio, fechaOrden, horaLlegadaDiagrama, numeroOrden, accion, numeroRegistros) VALUES ('$dtEvento', now(), $bus, '$data[razon_social]', '$data[nombre]', '$data[fservicio]', '$data[hllegadaplantareal]', $data[idOrden], 'La orden se cerrara automaticamente', $rows)";

        mysqli_query($conn, $insert);

      /*  $response["error"] = false;
        $response["message"] = "La orden se cerrara de manera automatica";
        $response["numeroOrden"] = $data['idOrden'];
        $response["nombre_orden"] = $data['nombre'];
        $response["cliente"] = $data['razon_social'];
        $response["hora_llegada_diagrama"] = $data['hllegadaplantareal'];
        $response["interno"] = $bus;*/
    }
    $response["error"] = false;
    $response["message"] = "La informacion se proceso de manera exitosa";
    mysqli_free_result($result);
    mysqli_close($conn);
    echoResponse(200, $response);
	
});

$app->get('/auto', 'authenticate', function() {
    
    $response = array();
    //$db = new DbHandler();

    /* Array de autos para ejemplo response
     * Puesdes usar el resultado de un query a la base de datos mediante un metodo en DBHandler
     **/
    $autos = array( 
                    array('make'=>'Toyota', 'model'=>'Corolla', 'year'=>'2006', 'MSRP'=>'18,000'),
                    array('make'=>'Nissan', 'model'=>'Sentra', 'year'=>'2010', 'MSRP'=>'22,000')
            );
    
    $response["error"] = false;
    $response["message"] = "Autos cargados: " . count($autos); //podemos usar count() para conocer el total de valores de un array
    $response["autos"] = $autos;

    echoResponse(200, $response);
});

/* Usando POST para crear un auto */

$app->post('/auto', 'authenticate', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('make', 'model', 'year', 'msrp'));

    $response = array();
    //capturamos los parametros recibidos y los almacxenamos como un nuevo array
    $param = array();
    $param['make']  = $app->request->params('make');
    $param['model'] = $app->request->params('model');
    $param['year']  = $app->request->params('year');
    $param['msrp']  = $app->request->params('msrp');
    
    /* Podemos inicializar la conexion a la base de datos si queremos hacer uso de esta para procesar los parametros con DB */
    //$db = new DbHandler();

    /* Podemos crear un metodo que almacene el nuevo auto, por ejemplo: */
    //$auto = $db->createAuto($param);

    if ( is_array($param) ) {
        $response["error"] = false;
        $response["message"] = "Auto creado satisfactoriamente!";
        $response["auto"] = $param;
    } else {
        $response["error"] = true;
        $response["message"] = "Error al crear auto. Por favor intenta nuevamente.";
    }
    echoResponse(201, $response);
});

/* corremos la aplicación */
$app->run();

/*********************** USEFULL FUNCTIONS **************************************/

/**
 * Verificando los parametros requeridos en el metodo o endpoint
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }
 
    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'El campo ' . substr($error_fields, 0, -2) . ' es requerido';
        echoResponse(400, $response);
        
        $app->stop();
    }
}
 
/**
 * Validando parametro email si necesario; un Extra ;)
 */
/*function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoResponse(400, $response);
        
        $app->stop();
    }
}*/
 
/**
 * Mostrando la respuesta en formato json al cliente o navegador
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);
 
    // setting response content type to json
    $app->contentType('application/json');
 
    echo json_encode($response);
}



/**
 * Agregando un leyer intermedio e autenticación para uno o todos los metodos, usar segun necesidad
 * Revisa si la consulta contiene un Header "Authorization" para validar
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = get_nginx_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
 
    // Verifying Authorization Header
    if (isset($headers['Authorization'])) {
        //$db = new DbHandler(); //utilizar para manejar autenticacion contra base de datos
 
        // get the api key
        $token = $headers['Authorization'];
        
        // validating api key
        if (!($token == API_KEY)) { //API_KEY declarada en Config.php
            
            // api key is not present in users table
            $response["error"] = true;
            $response["message"] = "Acceso denegado. Token inválido";
            echoResponse(201, $response);
            
            $app->stop(); //Detenemos la ejecución del programa al no validar
            
        } else {
            //procede utilizar el recurso o metodo del llamado
        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Falta token de autorización";
        echoResponse(201, $response);
        
        $app->stop();
    }
}

    function get_nginx_headers(){

        $all_headers=array();

            foreach($_SERVER as $name => $value){

                if(substr($name,0,5)=='HTTP_'){

                    $name=substr($name,5);
                    $name=str_replace('_',' ',$name);
                    $name=strtolower($name);
                    $name=ucwords($name);
                    $name=str_replace(' ', '-', $name);

                    $all_headers[$name] = $value; 
            }
        }


        return $all_headers;
}
?>