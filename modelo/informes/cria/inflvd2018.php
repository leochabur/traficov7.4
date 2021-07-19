<?php
  session_start();

  include '../../../modelsORM/src/lavadero/AccionUnidad.php';
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  

  include '../../../modelsORM/manager.php';
  include_once '../../../modelsORM/call.php';
 // use Symfony\Component\Validator\Validation;
  include ('../../utils/dateutils.php');
  $accion= $_POST['accion'];
//
  if($accion == 'loadLast'){
             if ($_POST[internos]){
                $uda = "AND a.unidad = :uda";
                $unidad = find('Unidad', $_POST[internos]);
             }
             if ($_POST[acciones]){
                $acc = "AND a.accion = :acc";
                $accion = find('TipoAccionUnidad', $_POST[acciones]);
             }
             
             if ($_POST[personal]){
                $empl = "AND :emple MEMBER OF a.responsables";
                $empleado = find('Empleado', $_POST[personal]);
             }


             
            // $a = $entityManager->createQuery("SELECT a FROM AccionUnidad a WHERE a.fecha BETWEEN :desde and :hasta $uda $acc $empl");

            $a = $entityManager->createQuery("SELECT a, MAX(a.fecha) FROM AccionUnidad a GROUP BY a.unidad, a.accion"); 



           /*  if ($_POST[internos]){
                 $a->setParameter('uda', $unidad);
             }
             if ($_POST[acciones]){
                $a->setParameter('acc', $accion);
             }
             if ($_POST[personal]){
                $a->setParameter('emple', $empleado);
             }
             
             
             $a->setParameter('desde', (new \DateTime(dateToMysql($_POST[desde],'/')))->format('Y-m-d'));
             $a->setParameter('hasta', (new \DateTime(dateToMysql($_POST[hasta], '/')))->format('Y-m-d'));*/
            // $a->setParameter('comenta', false);

                 $acciones = $a->getResult();

                 $now = new DateTime();
                 $data = array();
                 foreach ($acciones as $value) {
                    $fecha = DateTime::createFromFormat('Y-m-d', $value[1]);
                    $interval = $fecha->diff($now);
                    $dias = $interval->format('%d');

                    if (($dias)&&(!$value[0]->getAccion()->getComenta())){
                        $data[($value[0]->getUnidad()->getInterno().'-'.$value[0]->getAccion()->getId())]= "<tr>
                                                                       <td>".$value[0]->getUnidad()."</td>
                                                                       <td>".$value[0]->getAccion()."</td>
                                                                       <td>".$fecha->format('d/m/Y')."</td>
                                                                       <td>".$dias."</td>
                                                                       <td>".$value[0]->responsablesList()."</td>
                                                                   </td>";
                    }
                  }
                  ksort($data);
                  $data = implode('', $data);
                 $tabla="<table class='table' width='100%' id='reslv'>
                                <thead>
                                       <tr>
                                       <th>Interno</th>
                                       <th>Tipo Accion</th>
                                       <th>Fecha ultima Accion</th>
                                       <th>Dias</th>
                                       <th>Respopnsables accion</th>
                                       </tr>
                                </thead>
                                <tbody>$data";
                  /*
                 foreach($acciones as $accion){
                     $resp = "";
                     foreach($accion->getResponsables() as $responsable){
                         $resp.=$responsable."; ";
                     }
                     $tabla.="<tr>
                                  <td>".$accion->getFecha()->format('d-m-Y')."</td>
                                  <td>".$accion->getUnidad()."</td>
                                  <td>".$accion->getAccion()."</td>
                                  <td>$resp</td>
                                  <td>".$accion->getObservaciones()."</td>
                              </tr> ";
                 }*/
                 $tabla.="</tbody></table>
                  <script>
                            $('#reslv').dataTable({'scrollY':400,
                          'scrollCollapse': true,
                          'jQueryUI': true,
                           'orderClasses': true,
                           'paging': false
                        }); 
                  </script>";
                 print $tabla;
  }
  elseif($accion == 'loadPdo'){

            $desde = DateTime::createFromFormat('d/m/Y', $_POST['desde']);
            $hasta = DateTime::createFromFormat('d/m/Y', $_POST['hasta']);
            $a = $entityManager->createQuery("SELECT a FROM AccionUnidad a WHERE a.fecha BETWEEN :desde and :hasta ORDER BY a.unidad, a.accion");
            $a->setParameter('desde', $desde->format('Y-m-d'));
            $a->setParameter('hasta', $hasta->format('Y-m-d'));


            $acciones = $a->getResult();
            $data = array();
            foreach ($acciones as $value) {
                        $data[($value->getUnidad()->getInterno().'-'.$value->getAccion()->getId())]= "<tr>
                                                                       <td>".$value->getUnidad()."</td>
                                                                       <td>".$value->getAccion()."</td>
                                                                       <td>".$value->getFecha()->format('d/m/Y')."</td>
                                                                       <td>".$value->getObservaciones()."</td>
                                                                       <td>".$value->responsablesList()."</td>
                                                                   </td>";
            }
                  ksort($data);
                  $data = implode('', $data);
                 $tabla="<table class='table' width='100%' id='reslv'>
                                <thead>
                                       <tr>
                                       <th>Interno</th>
                                       <th>Tipo Accion</th>
                                       <th>Fecha Accion</th>
                                       <th>Observaciones</th>
                                       <th>Respopnsables accion</th>
                                       </tr>
                                </thead>
                                <tbody>$data";

                 $tabla.="</tbody></table>
                  <script>
                            $('#reslv').dataTable({'scrollY':400,
                          'scrollCollapse': true,
                          'jQueryUI': true,
                           'orderClasses': true,
                           'paging': false
                        }); 
                  </script>";
                 print $tabla;
  
  }
  elseif($accion == 'loadRspo'){
            $desde = DateTime::createFromFormat('d/m/Y', $_POST['desde']);
            $hasta = DateTime::createFromFormat('d/m/Y', $_POST['hasta']);
            $emp = find('Empleado', $_POST['respo']);

            $a = $entityManager->createQuery("SELECT a FROM AccionUnidad a WHERE a.fecha BETWEEN :desde and :hasta and :emple MEMBER OF a.responsables ORDER BY a.unidad, a.accion");
            $a->setParameter('desde', $desde->format('Y-m-d'));
            $a->setParameter('hasta', $hasta->format('Y-m-d'));
            $a->setParameter('emple', $emp); 


            $acciones = $a->getResult();
            $data = array();
            foreach ($acciones as $value) {
                        $data[($value->getUnidad()->getInterno().'-'.$value->getAccion()->getId())]= "<tr>
                                                                       <td>".$value->getUnidad()."</td>
                                                                       <td>".$value->getAccion()."</td>
                                                                       <td>".$value->getFecha()->format('d/m/Y')."</td>
                                                                       <td>".$value->getObservaciones()."</td>
                                                                       <td>".$value->responsablesList()."</td>
                                                                   </td>";
            }
                  ksort($data);
                  $data = implode('', $data);
                 $tabla="<table class='table' width='100%' id='reslv'>
                                <thead>
                                       <tr>
                                       <th>Interno</th>
                                       <th>Tipo Accion</th>
                                       <th>Fecha Accion</th>
                                       <th>Observaciones</th>
                                       <th>Respopnsables accion</th>
                                       </tr>
                                </thead>
                                <tbody>$data";

                 $tabla.="</tbody></table>
                  <script>
                            $('#reslv').dataTable({'scrollY':400,
                          'scrollCollapse': true,
                          'jQueryUI': true,
                           'orderClasses': true,
                           'paging': false
                        }); 
                  </script>";
                 print $tabla;
  
  } 
  elseif($accion == 'loadTypo'){
            $desde = DateTime::createFromFormat('d/m/Y', $_POST['desde']);
            $hasta = DateTime::createFromFormat('d/m/Y', $_POST['hasta']);
            $emp = find('TipoAccionUnidad', $_POST['typo']);

            $a = $entityManager->createQuery("SELECT a FROM AccionUnidad a WHERE a.fecha BETWEEN :desde and :hasta and a.accion = :action ORDER BY a.unidad, a.accion");
            $a->setParameter('desde', $desde->format('Y-m-d'));
            $a->setParameter('hasta', $hasta->format('Y-m-d'));
            $a->setParameter('action', $emp); 

           // die(getFullSQL($a));
            $acciones = $a->getResult();
            $data = array();
            foreach ($acciones as $value) {
                        $data[($value->getUnidad()->getInterno().'-'.$value->getAccion()->getId())]= "<tr>
                                                                       <td>".$value->getUnidad()."</td>
                                                                       <td>".$value->getAccion()."</td>
                                                                       <td>".$value->getFecha()->format('d/m/Y')."</td>
                                                                       <td>".$value->getObservaciones()."</td>
                                                                       <td>".$value->responsablesList()."</td>
                                                                   </td>";
            }
                  ksort($data);
                  $data = implode('', $data);
                 $tabla="<table class='table' width='100%' id='reslv'>
                                <thead>
                                       <tr>
                                       <th>Interno</th>
                                       <th>Tipo Accion</th>
                                       <th>Fecha Accion</th>
                                       <th>Observaciones</th>
                                       <th>Respopnsables accion</th>
                                       </tr>
                                </thead>
                                <tbody>$data";

                 $tabla.="</tbody></table>
                  <script>
                            $('#reslv').dataTable({'scrollY':400,
                          'scrollCollapse': true,
                          'jQueryUI': true,
                           'orderClasses': true,
                           'paging': false
                        }); 
                  </script>";
                 print $tabla;
  
  } 
  elseif($accion == 'infNow'){
            $a = $entityManager->createQuery("SELECT a, MAX(a.fecha) fecha FROM AccionUnidad a GROUP BY a.unidad, a.accion ORDER BY fecha"); 

            $acciones = $a->getResult();

               /*  $now = new DateTime();
                 $data = array();
                 foreach ($acciones as $value) {
                    $fecha = DateTime::createFromFormat('Y-m-d', $value[1]);
                    $interval = $fecha->diff($now);
                    $dias = $interval->format('%d');

                    if (($dias)&&(!$value[0]->getAccion()->getComenta())){
                        $data[($value[0]->getUnidad()->getInterno().'-'.$value[0]->getAccion()->getId())]= "<tr>
                                                                       <td>".$value[0]->getUnidad()."</td>
                                                                       <td>".$value[0]->getAccion()."</td>
                                                                       <td>".$fecha->format('d/m/Y')."</td>
                                                                       <td>".$dias."</td>
                                                                       <td>".$value[0]->responsablesList()."</td>
                                                                   </td>";
                    }
                  }
                  ksort($data);
                  $data = implode('', $data);
                 $tabla="<table class='table' width='100%' id='reslv'>
                                <thead>
                                       <tr>
                                       <th>Interno</th>
                                       <th>Tipo Accion</th>
                                       <th>Fecha ultima Accion</th>
                                       <th>Dias</th>
                                       <th>Respopnsables accion</th>
                                       </tr>
                                </thead>
                                <tbody>$data";

                 $tabla.="</tbody></table>
                  <script>
                            $('#reslv').dataTable({'scrollY':400,
                          'scrollCollapse': true,
                          'jQueryUI': true,
                           'orderClasses': true,
                           'paging': false
                        }); 
                  </script>";*/
                 print "cantidad ".count($acciones);
  }

function getFullSQL($query)
{
    $sql = $query->getSql();
    $paramsList = getListParamsByDql($query->getDql());
    $paramsArr =getParamsArray($query->getParameters());
    $fullSql='';
    for($i=0;$i<strlen($sql);$i++){
        if($sql[$i]=='?'){
            $nameParam=array_shift($paramsList);

            if(is_string ($paramsArr[$nameParam])){
                $fullSql.= '"'.addslashes($paramsArr[$nameParam]).'"';
             }
            elseif(is_array($paramsArr[$nameParam])){
                $sqlArr='';
                foreach ($paramsArr[$nameParam] as $var){
                    if(!empty($sqlArr))
                        $sqlArr.=',';

                    if(is_string($var)){
                        $sqlArr.='"'.addslashes($var).'"';
                    }else
                        $sqlArr.=$var;
                }
                $fullSql.=$sqlArr;
            }elseif(is_object($paramsArr[$nameParam])){
                switch(get_class($paramsArr[$nameParam])){
                    case 'DateTime':
                             $fullSql.= "'".$paramsArr[$nameParam]->format('Y-m-d H:i:s')."'";
                          break;
                    default:
                        $fullSql.= $paramsArr[$nameParam]->getId();
                }

            }
            else
                $fullSql.= $paramsArr[$nameParam];

        }  else {
            $fullSql.=$sql[$i];
        }
    }
    return $fullSql;
}

 /**
 * Get query params list
 *
 * @author Yosef Kaminskyi <yosefk@spotoption.com>
 * @param  Doctrine\ORM\Query\Parameter $paramObj
 * @return int
 */
function getParamsArray($paramObj)
{
    $parameters=array();
    foreach ($paramObj as $val){
        /* @var $val Doctrine\ORM\Query\Parameter */
        $parameters[$val->getName()]=$val->getValue();
    }

    return $parameters;
}
 function getListParamsByDql($dql)
{
    $parsedDql = preg_split("/:/", $dql);
    $length = count($parsedDql);
    $parmeters = array();
    for($i=1;$i<$length;$i++){
        if(ctype_alpha($parsedDql[$i][0])){
            $param = (preg_split("/[' ' )]/", $parsedDql[$i]));
            $parmeters[] = $param[0];
        }
    }

    return $parmeters;}


  
?>

