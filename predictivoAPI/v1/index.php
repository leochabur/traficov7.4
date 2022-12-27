<?php
error_reporting(E_ALL);

require '../libs/Slim/Slim.php'; 
//\Slim\Slim::registerAutoloader(); 

try{

	$app = new \Slim\Slim();

$app->get('/gpx', function () use ($app) {

		echoResponse(200, ['prueba REST']);

	});
	


	/*$app->get('/gpx', function () use ($app) {

		echoResponse(200, ['prueba REST']);

	});
*/
	$app->run();

	print "pepepep";

}
catch (Exception $e) {
						print 'errorororor';
}
	



function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);
 
    // setting response content type to json
    $app->contentType('application/json');
 
    echo json_encode($response);
}


?>