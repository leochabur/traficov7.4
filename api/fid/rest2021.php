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
header("Content-Type: application/json");
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

//$app->post('/close', 'authenticate', function() use ($app){
$app->post('/search', function () use ($app) {
    try
    {
        $conn = conexcion(true);
        $request = $app->request();
        $body = $request->getBody();
        $input = json_decode($body); 
        $dni = $input->dni;
        $dni = str_replace('.', '', $dni);
        if (!is_numeric($dni))
        {
            echoResponse(400, array('error' => 'Numero de documento invalido'));
        }
        else
        {
            $sql = "SELECT  id_empleado, upper(concat(apellido,', ', nombre)) as nombre, legajo
                    FROM empleados e
                    where (replace(nrodoc, '.', '') = '$dni') AND (activo) ";
            $result = mysqli_query($conn, $sql);
            $resultado = array();
            if ($row = mysqli_fetch_array($result))
            {
                $resultado = array(
                                "id" => $row["id_empleado"],
                                "dni" => $dni,
                                "nombre" => $row["nombre"],
                                "legajo" => $row['legajo']
                                );
            }
            mysqli_free_result($result);
            mysqli_close($conn);

            echoResponse(200, $resultado);
        }
    }
    catch(\Exception $e){echoResponse(400, array('ok' => 'ERROR '.$e->getMessage()));}
});

$app->post('/embeddings', function () use ($app) 
{
    try
    {
        $request = $app->request();
        $body = $request->getBody();
        $input = json_decode($body, true); 

        $id = $input['user']['id'];
        $conn = conexcion(true);

        $sql = "INSERT INTO embeddings (id_empleado, embedding, imagen, stamp, version, modelo) VALUES ($id, '$input[embedding]', '$input[imagen]',  UNIX_TIMESTAMP(), '$input[version]', '$input[modelo]')";

        mysqli_query($conn, $sql);
        

        if (!mysqli_errno($conn))
        {
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "http://iotdevices.masterbus.net/api/login",
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS =>array(
                                                    'user' => 'Leo',
                                                    'pass' => 'leoMB'
                                                ),
              CURLOPT_RETURNTRANSFER => 1, 
              CURLOPT_HTTPHEADER => array(
                                          'content-type' => 'application/json'
                                          ),
            ));
            $response = curl_exec($curl);    
            curl_close($curl);
            $body = json_decode($response, true);


            $url = "http://iotdevices.masterbus.net/api/fcm/push";
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $headers = array(
               "access-token: $body[token]",
               "Content-Type: application/json",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);


           // $data['msg']['data'] = ['backend' => 'faceid', 'desc' => 'Sistema Face ID', 'action' => 'sync'];  
            $data = '{"msg":
                            {"data":
                                    {"backend":"faceid",
                                    "desc":"SistemaFaceID",
                                    "action":"sync"
                                    }
                            }
                    }';


            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            $resp = curl_exec($curl);
            curl_close($curl);

            $insert = "INSERT INTO embeddingsSent (response, stamp) VALUES ('$resp', now())";
            mysqli_query($conn, $insert);

            echoResponse(200, array('error' => false));
        }
        else
        {
            echoResponse(200, array('error' => true, 'message' => $sql.'  '.mysqli_error($conn)));
        }

        mysqli_close($conn);


    }
    catch(Exception $e){echoResponse(200, array('error' => true, 'message' => $e->getMessage()));}
});

$app->get('/embeddings/:stamp/:type', function ($stamp, $type) use ($app) 
{
    try
    {
       /* $request = $app->request();
        $body = $request->getBody();
        $input = json_decode($body, true); 

        $type = "";
        if (is_array($input) && array_key_exists('type', $input))
        {
                $type = $input['type'];*/
                
                if ($type)
                {
                    $type = " AND id_cargo in (0,1)";
                }
                else
                {
                    $type = "";
                }
       
        $conn = conexcion(true);

        $sql = "SELECT upper(concat(apellido,',', nombre)) as nombre, replace(nrodoc,'.','') as nrodoc, e.id_empleado as id,
                        if (embedding is null, '', embedding) as embedding, if (e.activo is null, 1, e.activo) as activo, em.id as idEmbedding
                FROM empleados e 
                LEFT JOIN embeddings em ON em.id_empleado = e.id_empleado
                WHERE e.activo and em.stamp > $stamp";

        $sql = "SELECT upper(concat(apellido,',', nombre)) as nombre, replace(nrodoc,'.','') as nrodoc, e.id_empleado as id, 
                        if (embedding is null, '', embedding) as embedding, if (e.activo is null, 1, e.activo) as activo, em.id as idEmbedding
                FROM empleados e 
                LEFT JOIN embeddings em ON em.id_empleado = e.id_empleado 
                WHERE (changeStamp >= $stamp) or ((activo) and (stamp >= $stamp)) or ((fechaHoraBaja >= $stamp) and not activo) $type 
                ORDER BY e.id_empleado";


        $result = mysqli_query($conn, $sql) or die(echoResponse(200, mysqli_error($conn)." ".$sql));
        $data = mysqli_fetch_array($result);
        $embeddings = array();
        while ($data)
        {
            $empleado = $data['id'];
            $dni = $data['nrodoc'];
            $nombre = $data['nombre'];
            $activo = $data['activo'];
            $embedding = array();
            while (($data) && ($empleado == $data['id']))
            {
                if ($data['idEmbedding'])
                {
                    $embedding[] = ['idEmbedding'=> ($data['idEmbedding']?$data['idEmbedding']:''), 'embedding' => $data['embedding']];
                }
                
                $data = mysqli_fetch_array($result);
            }
            if (!count($embedding))
            {
            }
            $embeddings[] = array('id' => $empleado,
                                 'dni' => $dni,
                                 'status' => $activo,
                                 'nombre' => $nombre,
                                 'embeddings' => $embedding);
            unset($embedding);
        }
        mysqli_free_result($result);        
        mysqli_close($conn);
        echoResponse(200, $embeddings);
    }
    catch(Exception $e){echoResponse(200, array('error' => true, 'message' => $e->getMessage()));}
});

$app->get('/empleados', function () use ($app) 
{
    $conn = conexcion(true);
    $emples = "SELECT apellido, nombre, e.id_empleado
                FROM embeddings e
                inner join empleados em on em.id_empleado = e.id_empleado
                group by em.id_empleado
                order by apellido";

    $result = mysqli_query($conn, $emples);
    $data = [];
    while ($row = mysqli_fetch_array($result))
    {
        $data[] = ['id' => $row['id_empleado'], 'apellido' => $row['apellido'], 'nombre' => $row['nombre']];
    }

    mysqli_free_result($result);
    mysqli_close($conn);
    echoResponse(200, $data);
});  

$app->get('/images/:emple', function ($emple) use ($app) 
{
    $conn = conexcion(true);
    $sqlInternos = "SELECT version, modelo, imagen, stamp 
                    FROM embeddings e 
                    left join empleados em on em.id_empleado = e.id_empleado 
				    where em.id_empleado = $emple";
	$imagenes = mysqli_query($conn, $sqlInternos);

    $data = array();
    while ($row = mysqli_fetch_array($imagenes))
    {
        $data[] = ['version' => $row['version'], 'modelo' => $row['modelo'], 'imagen' => $row['imagen'], 'stamp' => $row['stamp']];
    }
    mysqli_free_result($imagenes);
    mysqli_close($conn);
    echoResponse(200, $data);
});

$app->post('/record', function () use ($app) 
{
    try
    {
        $request = $app->request();
        $body = $request->getBody();
        $input = json_decode($body, true); 

        if (!is_array($input))
        {
            echoResponse(401, array('error' => true, 'message' => 'Los parametros recibidos son incorrectos'));
        }
        else
        {
            if (array_key_exists('id', $input) && array_key_exists('stamp', $input) && array_key_exists('action', $input))
            {
                if (is_numeric($input['stamp']))
                {
                    $conn = conexcion(true);

                    $idEmbedding = (isset($input['idEmbedding'])?$input['idEmbedding']:'null');

                    $sql = "INSERT INTO accesosregistro (id_empleado, stamp, sentido, request, idEmbedding) 
                            VALUES ($input[id], $input[stamp], $input[action], '$body', $idEmbedding)";
                    mysqli_query($conn, $sql);
                    
                    if (!mysqli_errno($conn))
                    {
                        mysqli_close($conn);
                        echoResponse(200, array('error' => false));
                    }
                    else
                    {
                        $error = mysqli_error($conn);
                        mysqli_close($conn);
                        echoResponse(401, array('error' => true, 'message' => $error));
                    }
                }
                else
                {
                    echoResponse(401, array('error' => true, 'message' => 'El valor del campo stamp es incorrecto'));
                }
            }
            else
            {
                echoResponse(401, array('error' => true, 'message' => 'Todos los parametros (id, stamp y action) son requeridos'));
            }
        }   
    }
    catch(Exception $e){echoResponse(401, array('error' => true, 'message' => $e->getMessage()));}
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