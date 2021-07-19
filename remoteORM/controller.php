<?php
session_start();
include ('call.php');
include_once('manager.php');

function clientesOptions()
{        $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT c FROM Cliente c WHERE c.activo = :activo and c.estructura = :str ORDER BY c.razonSocial");
         $q->setParameter('activo', true);
         $q->setParameter('str',  $_SESSION['structure']);
         $clientes = $q->getResult();
         $options=array();
         foreach($clientes as $c){
           $options.="<option value='".$c->getId()."'>$c</option>";
         }
         return ($options);
}

function canalesPedidosOptions()
{        $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT c FROM CanalPedido c ORDER BY c.canal");
         $clientes = $q->getResult();
         $options=array();
         foreach($clientes as $c){
           $options.="<option value='".$c->getId()."'>$c</option>";
         }
         return ($options);
}

function ciudadesOptions()
{        $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT c FROM Ciudad c WHERE c.estructura = :str ORDER BY c.ciudad");
         $q->setParameter('str',  $_SESSION['structure']);
         $clientes = $q->getResult();
         $options="";
         foreach($clientes as $c){
           $options.="<option value='".$c->getId()."'>$c</option>";
         }
         return ($options);
}

function serviciosViajeList()
{        $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT s FROM ServicioViaje s ORDER BY s.servicio");
         $servicios = $q->getResult();
         return $servicios;
}






?>

