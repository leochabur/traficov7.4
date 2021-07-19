<?php
//session_start();
include_once ('call.php');
include_once('manager.php');

function clientesOptions($estructura = 0)
{        $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT c FROM Cliente c WHERE c.activo = :activo and c.estructura = :str ORDER BY c.razonSocial");
         $q->setParameter('activo', true);
         if ($estructura)
            $str = $estructura;
         else
            $str = $_SESSION['structure'];         
         $q->setParameter('str',  $str);
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
           $options.="<option value='".$c->getId()."'>".$c->getCanal()."</option>";
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

function listaPresupuestos()
{        
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT p FROM Presupuesto p");
         $presupuestos = $q->getResult();
         return $presupuestos;

}

function movimientosDeCuenta($id, $desde, $hasta)
{
         $cliente = find('Cliente', $id);

         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT m FROM MovimientoCuenta m WHERE (m.cliente = :cliente) AND (m.fecha between :desde AND :hasta) ORDER BY m.fecha");
         $q->setParameter('cliente', $cliente);
         $q->setParameter('desde', $desde);
         $q->setParameter('hasta', $hasta);         
         $movimientos = $q->getResult();
         return $movimientos;
}

function listaEstructuras()
{        
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT e FROM Estructura e");
         $estructuras = $q->getResult();
         $options="";
         foreach($estructuras as $e){
           $options.="<option value='".$e->getId()."'>$e</option>";
         }
         return $options;
}

function listaTiposVehiculos($estructura, $generateOptions = 1)
{        
         $em = $GLOBALS['entityManager'];
         $str = find('Estructura', $estructura);         
         $q = $em->createQuery("SELECT t FROM TipoVehiculo t WHERE t.estructura = :str");
         $q->setParameter('str', $str);         
         $tipos = $q->getResult();
         if ($generateOptions){
            $options="";
            foreach($tipos as $t){
              $options.="<option value='".$t->getId()."'>$t</option>";
            }
            return $options;
         }
         else{
            return $tipos;
         }
}

function listadoCronogramas($cliente)
{
         $em = $GLOBALS['entityManager'];
         $cli = find('Cliente', $cliente);
         $q = $em->createQuery("SELECT c FROM Cronograma c WHERE c.cliente = :cliente AND c.activo = :activo AND c.vacio = :vacio ORDER BY c.nombre");
         $q->setParameter('cliente', $cli);
         $q->setParameter('activo', true);   
         $q->setParameter('vacio', false);                     
         $cronogramas = $q->getResult();
         return $cronogramas;
}

function facturacionCliente($cliente)
{
         $em = $GLOBALS['entityManager'];
         $cli = find('Cliente', $cliente);
         $q = $em->createQuery("SELECT f FROM FacturacionCliente f WHERE f.cliente = :cliente");
         $q->setParameter('cliente', $cli);               
         $facturacion = $q->getResult();
         return $facturacion[0];
}

function mediosPagosOptions()
{        $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT m FROM MedioPago m ORDER BY m.medioPago");
         $mediosPago = $q->getResult();
         $options="";
         foreach($mediosPago as $m){
           $options.="<option value='".$m->getId()."'>$m</option>";
         }
         return ($options);
}


function diasSemana($exclude = 0)
{
         $em = $GLOBALS['entityManager'];
         if ($exclude){
            $dql = "SELECT d FROM DiaSemana d WHERE d.id NOT IN ($exclude)";
         }
         else{
            $dql = "SELECT d FROM DiaSemana d";
         }

         $q = $em->createQuery($dql);             
         $diasSemana = $q->getResult();
         return $diasSemana;
}

function articulosClientesOption($cliente)
{
         $em = $GLOBALS['entityManager'];
         $cli = find('Cliente', $cliente);
         $q = $em->createQuery("SELECT a FROM ArticuloCliente a WHERE a.cliente = :cliente");
         $q->setParameter('cliente', $cli);               
         $articulos = $q->getResult();
         $options="";
         foreach($articulos as $a){
           $options.="<option value='".$a->getId()."'>$a</option>";
         }  
         return $options;
}

function diaSemana($dia)
{
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT d FROM DiaSemana d WHERE d.numero_dia = :dia");
         $q->setParameter('dia', $dia);               
         $facturacion = $q->getResult();
         return $facturacion[0];
}

function tipoVehiculo($estructura, $tipo)
{        
         $em = $GLOBALS['entityManager'];
         $str = find('Estructura', $estructura);         
         $q = $em->createQuery("SELECT t FROM TipoVehiculo t WHERE t.estructura = :str AND t.id = :id");
         $q->setParameter('str', $str);      
         $q->setParameter('id', $tipo);      
         $tipo = $q->getResult();
         return $tipo[0];
}

function cronograma($estructura, $crono)
{        
         $em = $GLOBALS['entityManager'];
         $str = find('Estructura', $estructura);         
         $q = $em->createQuery("SELECT c FROM Cronograma c WHERE c.estructura = :str AND c.id = :id");
         $q->setParameter('str', $str);      
         $q->setParameter('id', $crono);      
         $tipo = $q->getResult();
         return $tipo[0];
}




?>

