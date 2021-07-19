<?php
// bootstrap.php
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once "vendor/autoload.php";

$isDevMode = true;
//$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/src"), $isDevMode);
$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
$conn = array(
    'dbname' => 'c0mbexport',
    'user' => 'root',
    'password' => 'leo1979',
    'host' => '127.0.0.1',
    'driver' => 'pdo_mysql',
    'charset'  => 'utf8',
);  

$entityManager = EntityManager::create($conn, $config);