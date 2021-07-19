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



include_once '../include/Config.php';
include '../../controlador/bdadmin.php';

/* Puedes utilizar este file para conectar con base de datos incluido en este demo; 
 * si lo usas debes eliminar el include_once del file Config ya que le mismo está incluido en DBHandler 
 **/
//require_once '../include/DbHandler.php'; 

require '../libs/Slim/Slim.php'; 
\Slim\Slim::registerAutoloader(); 
$app = new \Slim\Slim();


/* Usando GET para consultar los autos */

$app->post('/close', 'authenticate', function() use ($app){
    

    verifyRequiredParams(array('fecha', 'bus'));

    $response = array();
    //capturamos los parametros recibidos y los almacxenamos como un nuevo array


    $bus  = $app->request->params('bus');
    $fechaEvento = $app->request->params('fecha');
    $fechaEvento = DateTime::createFromFormat('Y-m-d H:i:s', $fechaEvento);
    if (!$fechaEvento)
    {
        $response['message'] = 'Formato de fecha invalido!';
        echoResponse(201, $response);        
        $app->stop();
    }

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
        echoResponse(201, $response);
        
        $app->stop();
    }
}
 
/**
 * Validando parametro email si necesario; un Extra ;)
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Email address is not valid';
        echoResponse(400, $response);
        
        $app->stop();
    }
}
 
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