<?php

     @session_start();
     //set_time_limit(0);
error_reporting(E_ALL);      
include_once ('call.php');
include_once('manager.php');

function clientesOptions($estructura = 0)
{        $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT c 
                               FROM Cliente c 
                               JOIN c.estructura e
                               WHERE c.activo = :activo and e.id = :str ORDER BY c.razonSocial");
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

function getCiudad($id, $str)
{        $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT c 
                                FROM Ciudad c 
                                JOIN c.estructura e
                                WHERE c.id = :id AND e.id = :str");
         $q->setParameter('str',  $str);
         $q->setParameter('id',  $id);
         $ciudad = $q->getOneOrNullResult();
         return $ciudad;
}

function getServicio($id, $str)
{        $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT s 
                                FROM Servicio s
                                JOIN s.estructura e
                                WHERE s.id = :id AND e.id = :str");
         $q->setParameter('str',  $str);
         $q->setParameter('id',  $id);
         $ciudad = $q->getOneOrNullResult();
         return $ciudad;
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

function getCliente($id, $str){
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT c 
                                FROM Cliente c 
                                JOIN c.estructura e
                                WHERE c.id = :id AND e.id = :str");
         $q->setParameter('id', $id);
         $q->setParameter('str', $str);    
         return $q->getOneOrNullResult();      
}

function getCronogramas($cliente){
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT c
                                FROM Cronograma c 
                                JOIN c.cliente cli
                                JOIN cli.estructura es
                                WHERE cli.id = :cliente AND es.id = :str
                                ORDER BY c.nombre");
         $q->setParameter('cliente', $cliente->getId());
         $q->setParameter('str', $cliente->getEstructura()->getId());   
         return $q->getResult();      
}

function listadoCronogramas($cliente)
{
         $em = $GLOBALS['entityManager'];
        // $cli = find('Cliente', $cliente);
         $q = $em->createQuery("SELECT c 
                                FROM Cronograma c 
                                JOIN c.cliente cli
                                WHERE cli.id = :cliente AND c.activo = :activo AND c.vacio = :vacio 
                                ORDER BY c.nombre");
         $q->setParameter('cliente', $cliente->getId());
         $q->setParameter('activo', true);   
         $q->setParameter('vacio', false);                     
         $cronogramas = $q->getResult();
         return $cronogramas;
}

function facturacionCliente($cliente)
{
         $em = $GLOBALS['entityManager'];
        //$cli = find('Cliente', $cliente);
         $q = $em->createQuery("SELECT f 
                                FROM FacturacionCliente f
                                JOIN f.cliente c 
                                WHERE c.id = :cliente");
         $q->setParameter('cliente', $cliente);               
         $facturacion = $q->getOneOrNullResult();
         return $facturacion;
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
            $dql = "SELECT d FROM DiaSemana d WHERE d.id NOT IN ($exclude) ORDER BY d.numero_dia";
         }
         else{
            $dql = "SELECT d FROM DiaSemana d ORDER BY d.numero_dia";
         }

         $q = $em->createQuery($dql);             
         $diasSemana = $q->getResult();
         return $diasSemana;
}

function articulosClientesOption($cliente)
{
         $em = $GLOBALS['entityManager'];
        // $cli = find('Cliente', $cliente);
         $q = $em->createQuery("SELECT a 
                                FROM ArticuloCliente a 
                                JOIN a.cliente cl
                                WHERE cl.id = :cliente");
         $q->setParameter('cliente', $cliente->getId());               
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
         return $q->getOneOrNullResult();  
}

function tipoVehiculo($str, $tipo)
{        
         $em = $GLOBALS['entityManager'];     
         $q = $em->createQuery("SELECT t FROM TipoVehiculo t WHERE t.estructura = :str AND t.id = :id");
         $q->setParameter('str', $str);      
         $q->setParameter('id', $tipo);      
         return $q->getOneOrNullResult();  
}

function cronograma($estructura, $crono)
{        
         $em = $GLOBALS['entityManager'];
         $str = find('Estructura', $estructura);         
         $q = $em->createQuery("SELECT c FROM Cronograma c WHERE c.estructura = :str AND c.id = :id");
         $q->setParameter('str', $str);      
         $q->setParameter('id', $crono);      
         return $q->getOneOrNullResult();  
}

function gastosPresupuestos()
{
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT g FROM GastoPresupuesto g ORDER BY g.nombre");             
         $gastos = $q->getResult();
         return $gastos;
}

function articulosCliente($cliente)
{
    try{
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT a 
                                FROM ArticuloCliente a 
                                JOIN a.cliente c
                                WHERE c.id = :cliente");
         $q->setParameter('cliente', $cliente);               
         $facturacion = $q->getResult();
         return $facturacion;
     }catch(Exception $e){ throw $e;}
}

function getCochesOptions($estructura)
{
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT c FROM Unidad c WHERE c.activo = :activo AND c.estructura = :str ORDER BY c.interno");
         $q->setParameter('activo', true);    
         $q->setParameter('str', $estructura);
         $coches = $q->getResult();
         $options="";
         foreach($coches as $c){
           $options.="<option value='".$c->getId()."'>$c</option>";
         }  
         return $options;
}

function getEmpleadosOptions($estructura)
{
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT c FROM Empleado c JOIN c.categoria ca WHERE c.activo = :activo AND c.estructura = :str AND ca.id = 1 ORDER BY c.apellido");
         $q->setParameter('activo', true);    
         $q->setParameter('str', $estructura);
         $coches = $q->getResult();
         $options="";
         foreach($coches as $c){
           $options.="<option value='".$c->getId()."'>$c</option>";
         }
         return $options;
}

function getEmpleados($estructura, $empleador)
{
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT c 
                                FROM Empleado c 
                                JOIN c.estructura strc
                                JOIN c.empleador emp
                                JOIN emp.estructura stremp
                                WHERE c.activo = :activo AND strc = :str AND emp.id = :empleador
                                ORDER BY c.apellido")
                 ->setParameter('activo', true)
                 ->setParameter('str', $estructura)
                 ->setParameter('empleador', $empleador->getId());
         $empleados = $q->getResult();
         return $empleados;
}

/*function getFeriados($estructura, $desde, $hasta)
{
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT c 
                                FROM Feriado c 
                                WHERE c.eliminado = :eliminado AND c.estructura = :str AND c.fecha BETWEEN :desde AND :hasta")
                 ->setParameter('eliminado', false)
                 ->setParameter('desde', $desde)
                 ->setParameter('hasta', $hasta)
                 ->setParameter('str', $estructura);
         $feriados = $q->getResult();
         return $feriados;
}*/

function getFeriados($estructura, $hasta)
{
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT c 
                                FROM Feriado c 
                                WHERE c.eliminado = :eliminado AND c.estructura = :str AND c.fecha <= :hasta")
                 ->setParameter('eliminado', false)
                 ->setParameter('hasta', $hasta)
                 ->setParameter('str', $estructura);
         $feriados = $q->getResult();
         return $feriados;
}

function getOrden($str, $orden){
        try{

         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT o 
                                FROM Orden o 
                                JOIN o.estructura e
                                WHERE e.id = :str AND o.id = :id");
         $q->setParameter('id', $orden);    
         $q->setParameter('str', $str);           
         return $q->getOneOrNullResult();  
        }
        catch (Exception $e){ throw $e;
        }
    }

function getComentarioOrden($orden){

         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT o 
                                FROM ObservacionOrden o 
                                JOIN o.orden ord
                                JOIN ord.estructura e
                                WHERE e.id = :str AND ord.id = :id");
         $q->setParameter('id', $orden->getId());    
         $q->setParameter('str', $orden->getEstructura()->getId());           
         return $q->getOneOrNullResult();  
}

function getOrdenModificada($orden){

         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT o 
                                FROM OrdenModificada o
                                WHERE o.id = :id");
         $q->setParameter('id', $orden);               
         return $q->getResult();  
}


function getViajeOrden($orden){

         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT vje FROM Viaje vje WHERE :orden MEMBER OF vje.ordenes");
         $q->setParameter('orden', $orden);    
         return $q->getOneOrNullResult();  
}

function getFacturaVenta($cliente){ ///devuelve si existe una factura abierta para el cliente dado

         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT f 
                                FROM FacturaVenta f 
                                JOIN f.cliente c
                                WHERE c.id = :cliente AND f.cerrada = :cerrada");
         $q->setParameter('cliente', $cliente);    
         $q->setParameter('cerrada', false);             
         return $q->getOneOrNullResult();  
}

function getFacturasPendientes($cerrada){ ///devuelve las facturas abiertas

         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT f FROM FacturaVenta f WHERE f.cerrada = :cerrada"); 
         $q->setParameter('cerrada', $cerrada);             
         return $q->getResult();  
}

function getOrdenes($listIds){ ///devuelve las facturas abiertas

         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT o FROM Orden o WHERE o.id IN (:ordenes)"); 
         $q->setParameter('ordenes', $listIds);             
         return $q->getResult();  
}

function getAllIdsOrdenesFactura($factura){
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT ord.id
                                FROM FacturaVenta fv 
                                JOIN fv.ordenesFacturadas of
                                JOIN of.ordenes ord
                                WHERE fv = :factura");    
         $q->setParameter('factura', $factura);             
         return $q->getResult();           
}

function getResumenFacturaPorArticulo($factura){
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT ar.descripcion as des, count(ord.id) as cant, o.importeUnitario as unit, (count(ord.id) * o.importeUnitario) as tot, ar.id as art, fv.id as idFact
                                FROM FacturaVenta fv 
                                JOIN fv.ordenesFacturadas o                          
                                JOIN o.ordenes ord
                                JOIN o.articulo ar
                                WHERE fv = :factura
                                GROUP BY ar.id");    
         $q->setParameter('factura', $factura);             
         return $q->getResult();           
}

function tipoV($id, $str){
         $em = $GLOBALS['entityManager'];
         $str = find('Estructura', $str);         
         $q = $em->createQuery("SELECT t FROM TipoVehiculo t WHERE t.estructura = :str AND t.id = :id");  
         $q->setParameter('str', $str);
         $q->setParameter('id', $id);             
         return $q->getOneOrNullResult();    
}

function getServiciosCliente($cliente){
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT s
                                FROM Servicio s
                                JOIN s.cronograma c
                                JOIN c.cliente cl
                                WHERE cl.id = :cliente AND c.activo = :crono AND s.activo = :serv
                                ORDER BY c.nombre, s.salida");    
         $q->setParameter('cliente', $cliente->getId())
           ->setParameter('crono', true)
           ->setParameter('serv', true);             
         return $q->getResult();           
}

function getResumenVentaPorInterno($desde, $hasta, $estructura, $cliente = null){
    try{
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT u.interno, ord.km as km, o.importeUnitario as unitario, c.razonSocial, 1 as cant, 
                                       u.dominio as dominio, u.nuevoDominio as nuevoDominio, p.razonSocial as titular, u.anio as anio, tu.tipo as tipo,
                                       c.id as cli, ord.hcitacionReal as citacion, ord.hfinservicioReal as fin, ord.fservicio as fecha, ord.id as orden
                                FROM FacturaVenta fv
                                JOIN fv.cliente c
                                JOIN fv.ordenesFacturadas o                          
                                JOIN o.ordenes ord
                                JOIN ord.estructura e
                                LEFT JOIN ord.unidad u
                                LEFT JOIN u.tipoUnidad tu
                                LEFT JOIN u.propietario p
                                WHERE ord.fservicio between :desde AND :hasta AND e.id = :estructura ".($cliente?"AND c.id = :cliente ":" ")."         
                                ORDER BY u.interno");    
         $q->setParameter('desde', $desde->format('Y-m-d'))
           ->setParameter('hasta', $hasta->format('Y-m-d'))
           ->setParameter('estructura', $estructura);
        if ($cliente)
            $q->setParameter('cliente', $cliente);        
         return $q->getResult();  
        }
        catch(Exception $e) { throw $e; }         
}

function getPropietarios($estructura, $generateOptions = 1)
{        
         $em = $GLOBALS['entityManager'];    
         $q = $em->createQuery("SELECT p FROM Propietario p JOIN p.estructura e WHERE e.id = :str AND p.activo = :activo ORDER BY p.razonSocial")
                 ->setParameter('str', $estructura)
                 ->setParameter('activo', true);         
         $tipos = $q->getResult();
         if ($generateOptions){
            $options="";
            foreach($tipos as $t){
              $options.="<option value='".$t->getId()."'>".strtoupper($t->getRazonSocial())."</option>";
            }
            return $options;
         }
         else{
            return $tipos;
         }
}

function getPropietarioIndividual($id)
{        
    try{
         $em = $GLOBALS['entityManager'];    
         $q = $em->createQuery("SELECT p 
                                FROM Propietario p 
                                JOIN p.estructura e 
                                WHERE p.id = :id")
                 ->setParameter('id', $id);         
         return $q->getOneOrNullResult();      
    }
    catch (Exception $e){die($e->getTraceAsString());}      
}

function getEvaluacionCurso($idCurso){
         $em = $GLOBALS['entityManager'];
         try
         {
         return $em->createQuery("SELECT c
                                  FROM ClaseAulaVirtual c
                                  JOIN c.curso cso
                                  WHERE cso.id = :curso AND c.eliminada = :eliminada AND c.esEvaluacion = :evaluacion")
                    ->setParameter('curso', $idCurso)
                    ->setParameter('eliminada', false)
                    ->setParameter('evaluacion',true)            
                    ->getOneOrNullResult();   
        }
        catch (Exception $e){throw $e;}        
}

function getClaseRealizada($clase, $empleado){
         $em = $GLOBALS['entityManager'];
         try
         {
           return $em->createQuery("SELECT c FROM ClaseRealizada c JOIN c.clase cl JOIN c.empleado e WHERE e = :emple AND cl = :clase")
                     ->setParameter('emple', $empleado)
                     ->setParameter('clase', $clase)    
                     ->getOneOrNullResult(); 
        }
        catch (Exception $e){throw $e;}        
}

function getEstructuras($generateOptions = 1){
         $em = $GLOBALS['entityManager'];    
         $tipos = $em->createQuery("SELECT e FROM Estructura e ORDER BY e.nombre")
                 ->getResult();
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

function getTarifasOfFacturacion($facturacion){
         $em = $GLOBALS['entityManager'];
         $q = $em->createQuery("SELECT t
                                FROM TarifaServicio t
                                WHERE t.facturacion = :facturacion");    
         $q->setParameter('facturacion', $facturacion);             
         return $q->getResult();           
}

function getCtaCteFeriado($emple){
         $em = $GLOBALS['entityManager'];
         try
         {
         return $em->createQuery("SELECT c
                                  FROM CtaCteFeriado c
                                  JOIN c.empleado e
                                  JOIN e.ciudad ci
                                  WHERE e = :emple")
                    ->setParameter('emple',$emple)            
                    ->getOneOrNullResult();   
        }
        catch (Exception $e){throw $e;}        
}

function getNovedad($id){
         $em = $GLOBALS['entityManager'];
         try
         {
         return $em->createQuery("SELECT n
                                  FROM Novedad n
                                  JOIN n.estructura e
                                  WHERE n.id = :id")
                    ->setParameter('id',$id)            
                    ->getOneOrNullResult();   
        }
        catch (Exception $e){throw $e;}        
}

function getCreditoConFeriado($feriado, $empleado){
         $em = $GLOBALS['entityManager'];
         try
         {
         return $em->createQuery("SELECT m
                                  FROM MovimientoCreditoFeriado m
                                  JOIN m.ctacte cta
                                  JOIN cta.empleado e
                                  JOIN m.feriadoAsociado f
                                  WHERE f = :feriado AND e = :empleado")
                    ->setParameter('feriado',$feriado)
                    ->setParameter('empleado',$empleado)            
                    ->getOneOrNullResult();   
        }
        catch (Exception $e){throw $e;}        
}

function getCreditoConTipoNovedad($tipo, $empleado, $desde, $hasta){
         $em = $GLOBALS['entityManager'];
         try
         {
         return $em->createQuery("SELECT m
                                  FROM MovimientoCreditoFeriado m
                                  JOIN m.ctacte cta
                                  JOIN cta.empleado e
                                  JOIN m.novedadTexto t
                                  WHERE t = :tipo AND e = :empleado AND m.fecha BETWEEN :desde AND :hasta")
                    ->setParameter('tipo', $tipo)
                    ->setParameter('empleado', $empleado)    
                    ->setParameter('desde', $desde)  
                    ->setParameter('hasta', $hasta)          
                    ->getOneOrNullResult();   
        }
        catch (Exception $e){throw $e;}        
}

function getDebitoConNovedad($tipo, $ctacte, $fecha, $tipo2 = 0)
{
         $em = $GLOBALS['entityManager'];
         try
         {
             return $em->createQuery("SELECT m
                                      FROM MovimientoDebitoFeriado m
                                      JOIN m.novedad nv
                                      JOIN nv.novedadTexto nt
                                      WHERE (nt.id = :tipo OR nt.id = :otroTipo) AND m.ctacte = :ctacte AND m.fecha = :fecha AND m.activo = :activo")
                        ->setParameter('tipo', $tipo)
                        ->setParameter('otroTipo', $tipo2)
                        ->setParameter('ctacte', $ctacte)    
                        ->setParameter('fecha', $fecha)    
                        ->setParameter('activo', true)     
                        ->getOneOrNullResult();   
        }
        catch (Exception $e){return $e->getMessage();}        
}

function getFrancosCreditos($fecha, $empleador){
         $em = $GLOBALS['entityManager'];
         try
         {
             return $em->createQuery("SELECT m.cantidad as cant, e.id as idE, e.apellido as ape, e.nombre as nom, e.legajo as leg
                                      FROM MovimientoCreditoFeriado m
                                      JOIN m.ctacte cta
                                      JOIN cta.empleado e
                                      JOIN e.categoria cat
                                      JOIN e.empleador emp
                                      JOIN m.novedadTexto nt
                                      WHERE nt.isFranco = :isFranco AND m.fecha BETWEEN :desde AND :fecha AND m.activo = :activo AND emp.id = :empleador AND cat.id = 1
                                      ORDER BY e.apellido")
                        ->setParameter('isFranco', true)
                        ->setParameter('activo', true)
                        ->setParameter('fecha', $fecha)   
                        ->setParameter('desde', '2020-11-26')
                        ->setParameter('empleador', $empleador)     
                        ->getResult();   
        }
        catch (Exception $e){throw $e;}        
}

function getFrancosCreditosPorPeriodo($desde, $hasta, $empleador){
         $em = $GLOBALS['entityManager'];
         try
         {  //m.cantidad as cant, e.id as idE, e.apellido as ape, e.nombre as nom, e.legajo as leg
             return $em->createQuery("SELECT m.cantidad as cant, e.id as idE, e.apellido as ape, e.nombre as nom, e.legajo as leg, m.periodoAnio as anio, m.periodoMes as mes
                                      FROM MovimientoCreditoFeriado m
                                      JOIN m.ctacte cta
                                      JOIN cta.empleado e
                                      JOIN e.categoria cat
                                      JOIN e.empleador emp
                                      JOIN m.novedadTexto nt
                                      WHERE cat.id = 1 AND e.activo = :activo AND nt.isFranco = :isFranco AND m.fecha BETWEEN :desde AND :hasta AND m.activo = :activo AND emp.id = :empleador
                                      ORDER BY e.apellido")
                        ->setParameter('isFranco', true)
                        ->setParameter('activo', true)
                        ->setParameter('desde', $desde)   
                        ->setParameter('hasta', $hasta)
                        ->setParameter('empleador', $empleador)     
                        ->getResult();   
        }
        catch (Exception $e){throw $e;}        
}

function getFrancosDebitosPorPeriodo($desde, $hasta, $empleador){
         $em = $GLOBALS['entityManager'];

         try
         {
             return $em->createQuery("SELECT count(n.id) as cant, e.id as idE, e.apellido as ape, e.nombre as nom, e.legajo as leg
                                      FROM Novedad n
                                      JOIN n.empleado e
                                      JOIN e.categoria cat
                                      JOIN e.empleador emp
                                      JOIN n.novedadTexto nt
                                      WHERE e.activo = :activo AND n.activa = :activo AND n.desde BETWEEN :desde AND :hasta AND emp.id = :empleador AND cat.id = 1 AND (nt.id in (:novedad))
                                      GROUP BY e.id
                                      ORDER BY e.apellido")
                        ->setParameter('activo', true)
                        ->setParameter('desde', $desde)   
                        ->setParameter('novedad', [15, 16])
                        ->setParameter('hasta', $hasta)
                        ->setParameter('empleador', $empleador)     
                        ->getResult();   
        }
        catch (Exception $e){throw $e;}  
         /*
         try
         {
             return $em->createQuery("SELECT count(e.id) as cant, e.id as idE, e.apellido as ape, e.nombre as nom, e.legajo as leg, e.activo as activo
                                      FROM MovimientoDebitoFeriado m
                                      JOIN m.ctacte cta
                                      JOIN cta.empleado e
                                      JOIN e.categoria cat
                                      JOIN e.empleador emp
                                      JOIN m.novedad nvda
                                      JOIN nvda.novedadTexto nt
                                      WHERE nvda.activa = :activo AND m.fecha BETWEEN :desde AND :hasta AND m.activo = :activo AND emp.id = :empleador AND cat.id = 1 AND (nt.id in (:novedad))
                                      GROUP BY e.id
                                      ORDER BY e.apellido")
                        ->setParameter('activo', true)
                        ->setParameter('desde', $desde)   
                        ->setParameter('novedad', [15, 16])
                        ->setParameter('hasta', $hasta)
                        ->setParameter('empleador', $empleador)     
                        ->getResult();   
        }
        catch (Exception $e){throw $e;}        
        */
}

function getFrancosDebitos($fecha, $empleador){
         $em = $GLOBALS['entityManager'];
         try
         {
             return $em->createQuery("SELECT m.cantidad as cant, e.id as idE, e.apellido as ape, e.nombre as nom, e.legajo as leg, e.activo as activo
                                      FROM MovimientoDebitoFeriado m
                                      JOIN m.ctacte cta
                                      JOIN cta.empleado e
                                      JOIN e.categoria cat
                                      JOIN e.empleador emp
                                      JOIN m.novedad nvda
                                      JOIN nvda.novedadTexto nt
                                      WHERE m.fecha BETWEEN :desde AND :fecha AND m.compensable = :compensable AND m.activo = :activo AND emp.id = :empleador AND cat.id = 1 AND (nt.id in (:novedad))
                                      ORDER BY e.apellido")
                        ->setParameter('activo', true)
                        ->setParameter('compensable', false)
                        ->setParameter('fecha', $fecha)   
                        ->setParameter('novedad', [50, 15, 16, 54, 25])
                        ->setParameter('desde', '2020-11-26')
                        ->setParameter('empleador', $empleador)     
                        ->getResult();   
        }
        catch (Exception $e){throw $e;}        
}

function getFeriadoDebitos($fecha, $empleador){
         $em = $GLOBALS['entityManager'];
         try
         {
             return $em->createQuery("SELECT m.cantidad as cant, e.id as idE, e.apellido as ape, e.nombre as nom, e.legajo as leg
                                      FROM MovimientoDebitoFeriado m
                                      JOIN m.ctacte cta
                                      JOIN cta.empleado e
                                      JOIN e.categoria cat
                                      JOIN e.empleador emp
                                      JOIN m.novedad nvda
                                      JOIN nvda.novedadTexto nt
                                      WHERE nt.isFeriado = :isFeriado AND m.fecha BETWEEN :desde AND :fecha AND m.activo = :activo AND emp.id = :empleador AND cat.id = 1
                                      ORDER BY e.apellido")
                        ->setParameter('isFeriado', true)
                        ->setParameter('activo', true)
                        ->setParameter('fecha', $fecha)   
                        ->setParameter('desde', '2020-11-26')
                        ->setParameter('empleador', $empleador)     
                        ->getResult();   
        }
        catch (Exception $e){return $e->getMessage();}        
}

function getOrdenGPX($id, $str)
{   
    try{
         $em = $GLOBALS['entityManager'];    
         $q = $em->createQuery("SELECT ogpx
                                FROM OrdenGPX ogpx
                                JOIN ogpx.orden o
                                JOIN o.estructura e
                                WHERE o.id = :id AND e.id = :str")
                 ->setParameter('id', $id)
                 ->setParameter('str', $str);         
         return $q->getOneOrNullResult();      
    }
    catch (Exception $e){die($e->getTraceAsString());}      
}

function getPasajeroConDNI($dni)
{   
    try
    {
         $em = $GLOBALS['entityManager'];    
         $q = $em->createQuery("SELECT p
                                FROM Pasajero p
                                WHERE p.dni = :dni AND p.activo = :activo")
                 ->setParameter('activo', true)
                 ->setParameter('dni', $dni);         
         return $q->getResult();      
    }
    catch (Exception $e){
                            throw $e;
                        }      
}

?>

