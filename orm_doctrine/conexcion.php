<?php

require_once(dirname(__FILE__)  .'/lib/Doctrine.php');
spl_autoload_register(array('Doctrine',  'autoload'));
$conn  =  Doctrine_Manager::connection('mysql://c0mbexpuser:Mb2013Exp@traficonuevo.masterbus.net/c0mbexport',  'doctrine');
$conn->setCharset('utf8');
Doctrine_Core::loadModels(dirname(__FILE__)  .'/models');
