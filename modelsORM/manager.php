<?php

include_once $_SERVER["DOCUMENT_ROOT"]."/controlador/parameter.php";

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\ClassLoader,
    Doctrine\ORM\ConfiguraDoctrine\ORM\Tools\Setuption,
    Doctrine\DBAL\Types\Type,
    Doctrine\Common\Cache\ArrayCache,
    Doctrine\DBAL\Logging\EchoSqlLogger;
//use DoctrineExtensions\Query\Mysql\TimeDiff;

use Symfony\Component\Validator\Validation;



require_once "vendor/autoload.php";
date_default_timezone_set('America/Argentina/Buenos_Aires');

$isDevMode = false;


$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml",  __DIR__."/config/xml/lavadero", __DIR__."/config/xml/rrhh", __DIR__."/config/xml/turismo", __DIR__."/config/xml/facturacion", __DIR__."/config/xml/trafico", __DIR__."/config/xml/segVial"), $isDevMode);

/*$conn = array(
    'dbname' => 'c0mbexport',
    'user' => 'root',
    'password' => 'leo1979',
    'host' => '127.0.0.1',
    'driver' => 'pdo_mysql',
    'charset'  => 'utf8',
); 
*/
    

/*function getValidator()
{
    return Validation::createValidatorBuilder();
}*/

$config->setAutoGenerateProxyClasses(true);
$conn = array(
    'dbname' => DBNAME,
    'user' => USERNAME,
    'password' => PASSWORD,
    'host' => HOSTNAME,
    'driver' => 'pdo_mysql',
    'charset'  => 'utf8',
);

// obtaining the entity manager
//try{
$entityManager = EntityManager::create($conn, $config);

//$classLoader = new \Doctrine\Common\ClassLoader('DoctrineExtensions', '/path/to/extensions');
///$classLoader->register();
//$entityManager->getConfiguration()->addCustomStringFunction('TIMEDIFF', TimeDiff::class);
//} catch(\InvalidArgumentException $e){ die($e);}


//die('de aca '.print_r($entityManager));
