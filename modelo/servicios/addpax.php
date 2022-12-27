<?php
//error_reporting(E_ALL);
ini_set('display_errors', 0);
     set_time_limit(0);
     session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }

include ('../../modelsORM/src/Pasajero.php');

  use Doctrine\ORM\Query\ResultSetMapping;
  use Symfony\Component\Validator\Validation as validate;


  include_once($_SERVER['DOCUMENT_ROOT'].'/modelsORM/controller.php');  
  include_once($_SERVER['DOCUMENT_ROOT'].'/modelsORM/manager.php');    


  
  $accion = $_POST['accion'];

  function paxExist($dni)
  {
    global $entityManager;

    try
    {
      $pax = $entityManager->createQuery("SELECT n FROM Pasajero n WHERE n.dni = :dni AND n.activo = :activo")
                           ->setParameter('activo', true)
                           ->setParameter('dni', $dni)
                           ->getOneOrNullResult();
      if ($pax)
      {
        throw new Exception('Pasajero existente');
      }
    }
    catch (Exception $e) {
                            throw new Exception('Pasajero existente');
    }
  }

  function asignarCampos($campos, $pax)
  {
      if (!is_numeric($campos['latitud']))
      {
          throw new Exception('El campo latitud debe ser numerico!');   
      }

      if (!is_numeric($campos['longitud']))
      {
          throw new Exception('El campo longitud debe ser numerico!');   
      }

      $pax->setApellido($campos['apellido']);
      $pax->setNombre($campos['nombre']);
      $pax->setDni($campos['dni']);
      $pax->setCiudad($campos['ciudad']);
      $pax->setDireccion($campos['direccion']);
      $pax->setLatitud($campos['latitud']);
      $pax->setLongtud($campos['longitud']);

      return $pax;
  }

  if ($accion == 'addpax')
  {
    try
    {
       paxExist($_POST['dni']);
       $pax = new Pasajero();
       $pax = asignarCampos($_POST, $pax);
       $entityManager->persist($pax);
       $entityManager->flush();

       $response = array('status' => true );
       print json_encode($response);      
    }
    catch (Exception $e) {
                          print json_encode(['status' => false, 'msge' => $e->getMessage()]);
    }
  }
  elseif ($accion == 'deletepax')
  {
    try
    {
       $pax = $entityManager->createQuery("SELECT n FROM Pasajero n WHERE n.id = :pax")->setParameter('pax', $_POST['idpax'])->getOneOrNullResult();
       if ($pax)
       {
          $pax->setActivo(false);
          $entityManager->flush();
          print json_encode(['status' => true]);
          return;
       }
        print json_encode(['status' => false, 'msge' => 'Pasajero inexistente!!']);
        return;
    }
    catch (Exception $e) {
                          print json_encode(['status' => false, 'msge' => $e->getMessage()]);
    }
  }
  elseif ($accion == 'editpax')
  {
    try
    {
       $pax = $entityManager->createQuery("SELECT n FROM Pasajero n WHERE n.id = :pax")->setParameter('pax', $_POST['idpax'])->getOneOrNullResult();
       if ($pax)
       {
          if ($pax->getDni() != $_POST['dni'])
          {
            paxExist($_POST['dni']);
          }
       }
       else
       {
          print json_encode(['status' => false, 'msge' => 'Pasajero inexistente!']);
          return;
       }

       $pax = asignarCampos($_POST, $pax);
       $entityManager->flush();

       $response = array('status' => true );
       print json_encode($response);      
    }
    catch (Exception $e) {
                          print json_encode(['status' => false, 'msge' => $e->getMessage()]);
    }
  }
  elseif($accion == 'listp')
  {
    try
    {
    $paxs = $entityManager->createQuery("SELECT n FROM Pasajero n WHERE n.activo = true ORDER BY n.apellido")->getResult();

    $tabla = "<table class='table table-zebra table-hover' id='prsList' width='100%'>
              <thead>
                <tr>
                  <th></th>
                  <th>Apellido</th>
                  <th>Nombre</th>
                  <th>DNI</th>
                  <th>Direccion</th>                  
                  <th>Latitud</th>
                  <th>Longitud</th>                  
                  <th>Ciudad</th>
                </tr>
              </thead>
              <tbody>";
    foreach ($paxs as $pax)
    {
      $tabla.="<tr>
                <td>
                    <a class='edited' data-pax='".$pax->getId()."'><i class='fas fa-edit'></i></a>
                    <a class='delete' data-pax='".$pax->getId()."'><i class='far fa-trash-alt'></i></a>
                </td>
                <td data-name='apellido'>".strtoupper($pax->getApellido())."</td>
                <td data-name='nombre'>".strtoupper($pax->getNombre())."</td>
                <td data-name='dni'>".$pax->getDni()."</td>
                <td data-name='direccion'>".strtoupper($pax->getDireccion())."</td>
                <td data-name='latitud'>".$pax->getLatitud()."</td>
                <td data-name='longitud'>".$pax->getLongtud()."</td>
                <td data-name='ciudad'>".strtoupper($pax->getCiudad())."</td>              
               </tr>";
    }
    $tabla.="</tbody>
             </table>";

    $tabla.="<script>
                $('.delete').click(function(){
                                              let a = $(this);
                                              if (confirm('Seguro eliminar el pasajero?'))
                                              {
                                                  $.post('/modelo/servicios/addpax.php',
                                                        { accion : 'deletepax', idpax : a.data('pax')},
                                                        function(data){
                                                                        console.log(data);
                                                                        let response = $.parseJSON(data);
                                                                        if (response.status)
                                                                        {
                                                                            getList();
                                                                        }
                                                                        else
                                                                        {
                                                                          alert(response.msge);
                                                                        }
                                                          });
                                              }
                });
                $('.edited').click(function(){
                                                let td = $(this).parent();
                                                $('#idpax').val($(this).data('pax'));
                                                td.siblings().each(function (){
                                                                                let x = $(this);
                                                                                let k = x.data('name');
                                                                                let v = x.html();
                                                                                $(\"input[name='\"+k+\"']\" ).val(v); 
                                                });
                                              });                
             </script>
             ";
    print $tabla.$script;
    }
    catch (Exception $e) {
      print $e->getMessage();
    }
  }

  
?>
