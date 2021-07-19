<?
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  include '../../../modelsORM/manager.php';
  include '../../../modelsORM/src/GrupoNovedad.php';
  use Doctrine\ORM\Query\ResultSetMapping;
  use Symfony\Component\Validator\Validation;
  $accion= $_POST['accion'];
  
  function array_column_function( array $input, $column_key, $index_key = null ) {
           $result = array();
           foreach( $input as $k => $v )
                    $result[ $index_key ? $v[ $index_key ] : $k ] = $v[ $column_key ];
           return $result;
  }
                    
  if($accion == 'reskm'){
     $xcond=1;
     if ($xcond){
        $order = "apellido, n.id_empleado, nov_text";
        $group = "n.id_empleado, c.id";
     }
     else{
          $order = "nov_text, apellido";
          $group = "c.id, n.id_empleado";
     }
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $sql = "SELECT upper(nov_text) as nov_txt, c.id as code_n, concat(apellido,', ', nombre) as conductor, n.id_empleado as emple, sum(if ((desde >= '$desde') and (hasta <= '$hasta')
                         ,DATEDIFF(hasta, desde)+1,
                         if ((desde < '$desde') and (hasta > '$hasta'),
                             DATEDIFF('$hasta', '$desde')+1,
                             if (desde < '$desde',
                                DATEDIFF(hasta, '$desde')+1,
                                DATEDIFF('$hasta', desde)+1
                             )
                         )
                     )) as dias, count(distinct(n.id_empleado)) as personal, count(*) as cant_noves
FROM novedades n
inner join empleados e on e.id_empleado = n.id_empleado
inner join cod_novedades c on c.id = n.id_novedad
where ((desde between '$desde' and '$hasta') or (hasta between '$desde' and '$hasta') or ((desde < '$desde')and(hasta > '$hasta'))) and n.activa and n.id_estructura = $_POST[str]
group by $group
order by $order";
//die($sql);
     $conn = conexcion();
     
   /*  $sql_cant_emp = "SELECT count(*)
                  FROM empleados e
                  where activo and id_estructura = $_SESSION[structure] and id_cargo = 1 and id_empleador = 1";
     $result = mysql_query($sql_cant_emp, $conn);
     $cant_emp = 0;
     if ($row = mysql_fetch_array($result)){
        $cant_emp = $row[0];
     }    */

     $result = mysql_query($sql, $conn);
     
     $datos = array();

     $cant_empleados = 0;
     
     $row = mysql_fetch_array($result);
     while ($row){
           $cond = $row[emple];
           $cant_dias = 0;
           $cant_novedades = 0;
           while (($row)&&($cond == $row[emple])){
                 $cant_dias+=$row[4];
                 $cant_novedades+=$row[cant_noves];
                 $row = mysql_fetch_array($result);
           }
           $datos[$cond] = array(0 => $cant_dias, 1 => $cant_novedades);
     }
     
     
     
     $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" align="center" width="75%">
                     <thead>
            	            <tr class="ui-widget-header">
                                <th>Apellido, Nombre</th>
                                <th>Cant. Novedades</th>
                                <th>Cant. Dias</th>
                             </tr>
                     </thead>
                    <tbody>';

   //  $data = mysql_fetch_array($result);
   $i=0;
   $por=0;
   $result = mysql_query($sql, $conn);
   $row = mysql_fetch_array($result);


   if ($xcond){ ///agruoalas novedades x conductor
      while ($row){
            $data = $row[emple];
            $tabla.="<tr data-tt-id='$row[emple]'>
                         <td><b>$row[conductor]</b></td>
                         <td><b>".$datos[$data][1]."</b></td>
                         <td><b>".$datos[$data][0]."</b></td>
                    </tr>";
            while (($row) &&($data == $row[emple])){
                  $tabla.="<tr data-tt-id='1.$row[code_n]' data-tt-parent-id='$row[emple]'>
                               <td>$row[nov_txt]</td>
                               <td>$row[cant_noves]</td>
                               <td>$row[dias]</td>
                           </tr>";
                  $row = mysql_fetch_array($result);
            }
      }
   }
   
   $tabla.="</tbody></table>
       <script>
      $('#example').treetable({ expandable: true });
       $('#example tbody').on('mousedown', 'tr', function() {
        $('.selected').not(this).removeClass('selected');
        $(this).toggleClass('selected');
      });
      </script>";

    /* foreach ($datos as $clave => $valor){

                 $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                 $tabla.="<tr bgcolor='$color'>
                              <td align='left'>$clave</td>
                              <td align='right'>$valor[0]</td>
                              <td align='right'>".round(($valor[0]/$cant_novedades)*100,3)." %</td>
                              <td align='right'>$valor[1]</td>
                              <td align='right'>".round(($valor[1]/$cant_emp)*100,3)." %</td>

                          </tr>";
                 $por+=($valor[0]/$cant_novedades)*100;
                 $i++;

     }*/
   /*  $tabla.='</tbody>
              <tr bgcolor="#606060"><td><b>TOTALES</b></td>
                  <td align="right"><b>'.$cant_novedades.'</b></td>
                  <td></td>
                  <td align="right"><b>'.$cant_empleados.'</b></td>
                  <td></td>
                  </tr>
              </table>
              <style type="text/css">
                         #example { font-size: 85%; }
                         #example tbody tr:hover {background-color: #FF8080;}
                  </style>
                  <script type="text/javascript">

                  </script>'; */
    print $tabla;
  }
  elseif($accion == 'ldnv'){
         if ($_POST[gpn]){
                $tabla = "";
                $productRepository = $entityManager->getRepository('GrupoNovedad');
                $grupo = $productRepository->find($_POST[gpn]);
                $tabla='<select id="noves" name="noves" class="ui-widget ui-widget-content  ui-corner-all">';
                foreach ($grupo->getNovedades() as $nov) {
                        $tabla.="<option value='".$nov->getId()."'>".strtoupper($nov->getDescripcion())."</option>";
                }
                $tabla.="</select>";
                
                if (!$grupo->getNovedades()->isEmpty()){
                   $tabla.="<input type='button' value='Quitar' id='del'>
                         <script>
                                 $('#del').button().click(function(){
                                                                     $.post('/modelo/informes/cria/auscnd.php',
                                                                            {accion:'deln', id:$('#noves').val(), gpo:$('#gpo').val()},
                                                                            function(data){
                                                                                           var response = $.parseJSON(data);
                                                                                           if (response.error){
                                                                                              alert(response.message);
                                                                                           }
                                                                                           else{
                                                                                                delElementSelect('noves', response.key);
                                                                                                addElementSelect('novesd', response.key, response.value, 350);
                                                                                           }
                                                                            });
                                                                     });
                         </script>";
                }
         }
         else{
              $tabla="";
         }
         print $tabla;
  }
  elseif($accion == 'deln'){
                 try {
                     $productRepository = $entityManager->getRepository('GrupoNovedad');
                     $gpo = $productRepository->find($_POST[gpo]);
                     $novedadRepository = $entityManager->getRepository('NovedadTitulo');
                     $nove = $novedadRepository->find($_POST[id]);
                     $gpo->removeNovedad($nove);
                     $entityManager->flush();
                     $response = array("error"=> false, "key"=>$_POST[id], "value"=>strtoupper($nove->getDescripcion()));
                 } catch (Exception $e) {
                                         $response = array("error"=> true, "message"=>"No se pudo eliminar");
                                        }
                 print json_encode($response);

  }
  elseif($accion == 'svgpo'){
                 $grupo = new GrupoNovedad();
                 $grupo->setNombre($_POST[grpo]);
                 $entityManager->persist($grupo);
                 $validator = Validation::createValidatorBuilder()
                              ->addMethodMapping('loadValidatorMetadata')
                              ->getValidator();
                 $violations = $validator->validate($grupo);
                 if (count($violations) > 0) {
                    $response = array("error"=> true, "message"=>"Se produjo un error al intentar guardar el grupo de Informe!");
                    print (json_encode($response));
                    exit();
                    
                 }
                 $entityManager->flush();
                 $response = array("error"=> false, "key"=>$grupo->getId(), "value"=>$grupo->getNombre());
                 print (json_encode($response));

  }
  elseif($accion == 'ldnvtxt'){

                 $rsm = new ResultSetMapping();
                 $query = $entityManager->createNativeQuery('SELECT id_cod_anomalia FROM anomxgrupoinforme', $rsm);

                 $rsm->addScalarResult('id_cod_anomalia', 'id_cod_anomalia');
            //     die('anda dff '.$query->getScalarResult());
               //  if (! function_exists('array_column')){

              //   }
                 $ids = array_column_function($query->getScalarResult(), "id_cod_anomalia");
                 $exc = "";

                 if (!empty($ids)){
                    $exc = "WHERE n.id NOT IN (".implode(',',$ids).")";
                 }

                 $noves = $entityManager->createQuery("SELECT n FROM NovedadTitulo n $exc ORDER BY n.descripcion");
                 $novgpo = $noves->getResult();
                 $datas = '<select id="novesd" name="novesd" class="ui-widget ui-widget-content  ui-corner-all">';
                 foreach ($novgpo as $nov) {
                         $datas.= "<option value='".$nov->getId()."'>".strtoupper($nov->getDescripcion())."</option>";
                 }
                 $datas.="</select>";
                 $datas.="<input type='button' value='Agregar' id='add'>
                         <script>
                                 $('#add').button().click(function(){
                                                                     $.post('/modelo/informes/cria/auscnd.php',
                                                                            {accion:'addn', nov:$('#novesd').val(), gpo:$('#gpo').val()},
                                                                            function(data){

                                                                            });
                                                                     });
                         </script>";
                 print ($datas);

  }
  elseif($accion == 'addn'){
                 $productRepository = $entityManager->getRepository('GrupoNovedad');
                 $gpo = $productRepository->find($_POST[gpo]);
                 $novedadRepository = $entityManager->getRepository('NovedadTitulo');
                 $nove = $novedadRepository->find($_POST[nov]);

                 $gpo->addNovedad($nove);
                 $entityManager->flush();
                 print $_POST[id];

  }
  elseif($accion == 'delgpo'){
                 $productRepository = $entityManager->getRepository('GrupoNovedad');
                 $gpo = $productRepository->find($_POST[gpo]);
              //   $novedadRepository = $entityManager->getRepository('NovedadTitulo');
              //   $nove = $novedadRepository->find($_POST[nov]);

               //  $gpo->addNovedad($nove);
                 $entityManager->remove($gpo);
                 $entityManager->flush();
                 print $_POST[gpo];

  }
  
                                                                                       //      $('#noves option[value='+data+']').remove();
                                                                                        //   $('#noves').selectmenu({width: 350});
  
?>

