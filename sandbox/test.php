<?php
require_once(dirname(__FILE__) . '/lib/Doctrine.php');
spl_autoload_register(array('Doctrine', 'autoload'));
$conn = Doctrine_Manager::connection('mysql://root:leo1979@localhost/mbexport', 'doctrine');
$conn->setCharset('utf8');
//Doctrine_Core::generateModelsFromDb('models', array('doctrine'), array('generateTableClasses' => true));
Doctrine_Core::loadModels('models');

