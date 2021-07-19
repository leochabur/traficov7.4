<?php
require_once(dirname(__FILE__)  . '/lib/Doctrine.php');
spl_autoload_register(array('Doctrine',  'autoload'));
//die();
$conn  =  Doctrine_Manager::connection('mysql://root:leo1979@localhost/mbexport',  'doctrine');

Doctrine_Core::generateModelsFromDb('models',  array('doctrine'), array('generateTableClasses' => true));
die("<br>$conn");
