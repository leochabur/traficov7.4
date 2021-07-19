<?php

use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Query\QueryException;


class PresupuestoRepository extends EntityRepository
{
    public function proximoNumeroPresupuesto()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT MAX(p.id) FROM Presupuesto p')
            ->getResult();
    }

    public function listaPresupuestos($desde, $hasta, $cliente, $numero)
    {
    	$set = false;
        $dql = "SELECT p FROM Presupuesto p";
      /*  $parameters = array();
        if (!(empty($desde) && empty($hasta) && empty($cliente) && empty($numero)))
        	$dql.=" WHERE ";

        if ($desde){
        	$dql.=" p.fechaSolicitud = :desde";
        	$set = true;
        	$parameters['desde'] = $desde;
        }
        if ($hasta){
        	if ($set)
        		$dql.= " AND ";
        	$dql.="p.fechaSolicitud = :hasta";
        	$set = true;
        	$parameters['hasta'] = $hasta;
        }
        if ($cliente){
        	if ($set)
        		$dql.=" AND ";
        	$dql.="p.cliente = :cliente";
        	$set = true;
        	$parameters['cliente'] = $cliente;
        }
        if ($numero){
        	if ($set)
        		$dql.= " AND ";
        	$dql.=" p.id = :id";
        	$parameters['id'] = $numero;
        }*/

     //  return $parameters;
       // return array('dql' => $dql , 'params' => $parameters);
   //     exit;

        $query = $this->getEntityManager()->createQuery($dql);
        
        /*if (!empty($parameters)){
	        foreach ($parameters as $key => $value) {
	        	$query->setParameter($key, $value);
	        }
    	}*/

 //       return array('dql' => $dql );
        try{
        	return $query->getResult();
        //	return $query;
    	} catch (QueryException $e){return array('staus' => 'errorororororor');}    
    }
}


?>