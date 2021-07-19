<?php
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');

  include('../../modelsORM/src/MovimientoDebitoFeriado.php');
  include('../../modelsORM/src/Novedad.php');
  include('../../modelsORM/manager.php');
  include_once('../../modelsORM/call.php');
  include_once('../../modelsORM/controller.php');
  $accion = $_POST['accion'];

  if($accion == 'load')
  {
    
      $inicio = DateTime::createFromFormat('Y-m-d', "$_POST[anio]-$_POST[mes]-25");      
      $fin = clone $inicio;
      $fin->sub(new DateInterval('P1M'));
      $fin->add(new DateInterval('P1D'));

      try
      {
          $q = $entityManager->createQuery("SELECT m
                                            FROM MovimientoCuentaFeriado m
                                            JOIN m.ctacte c
                                            JOIN c.empleado e
                                            JOIN e.empleador emp
                                            JOIN emp.estructura stremp
                                            JOIN e.estructura str
                                            JOIN e.categoria cat
                                            WHERE m.fecha BETWEEN :desde AND :hasta AND cat.id = 1 AND m.activo = :activo AND str.id = 1 AND emp.id = :empleador
                                            ORDER BY e.apellido");
          $q->setParameter('desde',  $fin->format('Y-m-d'))
            ->setParameter('empleador', $_POST['empleador'])
            ->setParameter('activo', true);
          $q->setParameter('hasta',  $inicio->format('Y-m-d'));
          $movimientos = $q->getResult();
      }
      catch(Exception $e){
                          die($e->getMessage());
                         }

       $str = find('Estructura', 1); 
       $feriados = getFeriados($str, $fin, $inicio);
       $select = "<select class='sel' name='feriado'>";
       foreach ($feriados as $f)
       {
          $select.="<option value='".$f->getId()."'>".$f->getFecha()->format('d/m/Y')."</option>";
       }
       $select.="</select>";

          $tabla ='<table id="tablitasssss" align="center" width="100%" class="table table-zebra">
                     <thead>
                            <tr>
                                <th rowspan="2">Legajo</th>                                
                                <th rowspan="2">Apellido, Nombre</th>
                                <th colspan="2">
                                    Asignados
                                </th>
                                <th colspan="3" align="center">
                                    Diagramados
                                </th>
                                <th colspan="3">Compensar</th>   
                            </tr>
                            <tr>
                                <th>Francos</th>
                                <th>Feriados</th>
                                <th align="center">Francos</th>
                                <th align="center">Feriados</th>
                                <th align="center">A Compensar</th>
                                <th align="center">Francos</th>
                                <th align="center">Feriados</th>
                                <th align="center">Adelantar Franco</th>
                            </tr>
                     </thead>
                     <tbody>';
          $resumen = array();
          foreach ($movimientos as $mov)
          {
              $emple = $mov->getCtaCte()->getEmpleado();           
              if (!array_key_exists($emple->getId(), $resumen))
              {
                $resumen[$emple->getId()] = array('e' => $emple, 'fr' => 0, 'fe' => 0, 'frt' => 0, 'fet' => 0, 'fac' => 0);
              }

              $resumen[$emple->getId()]['fr']+= $mov->getFrancoCredito();
              $resumen[$emple->getId()]['fe']+= $mov->getFeriadoCredito();
              $resumen[$emple->getId()]['frt']+= $mov->getFrancoDebito();
              $resumen[$emple->getId()]['fet']+= $mov->getFeriadoDebito();
              $resumen[$emple->getId()]['fac']+= $mov->getDebitosACompensar();
          }

          
          foreach ($resumen as $res)
          {
                $franco = "";
                if ($res['fr'] > $res['frt'])
                {
                    $franco = "<form class='cpms'>
                                  <input type='text' class='fecha' name='fechacomp'/>
                                  <input type='button' value='+' class='btn'/>
                                  <input type='hidden' name='accion' value='cpmsf'/>
                                  <input type='hidden' name='emple' value='".$res['e']->getId()."'/>
                                  <input type='hidden' name='fecha' value='".$inicio->format('Y-m-d')."'/>
                                </form>";
                }
                $adelantar = "<form class='adelantar'>
                              <input type='text' class='fechaadelante' name='fechacompadel'/>
                              <input type='button' value='+' class='btn'/>
                              <input type='hidden' name='accion' value='adelfr'/>
                              <input type='hidden' name='emple' value='".$res['e']->getId()."'/>
                              <input type='hidden' name='fecha' value='".$inicio->format('Y-m-d')."'/>
                            </form>";

                $compensa = "";
                if ($res['fe'] > $res['fet'])
                {
                    $compensa = "<form class='cpms'>
                                  $select
                                  <input type='text' class='fecha' name='fechacomp'/>
                                  <input type='button' value='+' class='btn'/>
                                  <input type='hidden' name='accion' value='cpms'/>
                                  <input type='hidden' name='emple' value='".$res['e']->getId()."'/>
                                </form>";
                }
                $tabla.="<tr>
                             <td>".$res['e']->getLegajo()."</td>
                             <td>".$res['e']."</td>
                             <td class='frt'>".$res['fr']."</td>
                             <td class='fet'>".$res['fe']."</td>
                             <td title='Click para ver detalle...' class='vff' data-emple='$res[e]' data-idemp='".$res['e']->getId()."' align='center'><b>".$res['frt']."</b></td>
                             <td title='Click para ver detalle...' class='vff' data-emple='$res[e]' data-idemp='".$res['e']->getId()."' align='center'><b>".$res['fet']."</b></td>
                             <td title='Click para ver detalle...' class='vff' data-emple='$res[e]' data-idemp='".$res['e']->getId()."' align='center'><b>".$res['fac']."</b></td>
                             <td align='center'>$franco</td>
                             <td align='center'>$compensa</td>
                             <td align='center'>$adelantar</td>
                         </tr>";
          }
          $mesActual = ($_POST['mes'] -2);
          $mesAdelante = ($_POST['mes']-1); //en jquery es el mes menos uno
          $tabla.="</tbody>
                   </table>
                   <div id='detalle'>
                   </div>
                   <script type='text/javascript'>
                      $('.fecha').datepicker();
                      $('.fechaadelante').datepicker({
                        minDate: new Date($_POST[anio],$mesActual, 26), maxDate: new Date($_POST[anio],$mesAdelante, 25)
                        });
                      $('.btn').button().click(function(){
                                                          var btn = $(this);
                                                          var form = btn.closest('form');
                                                          btn.hide();
                                                          $.post('/modelo/rrhh/stctacteff.php',
                                                                 form.serialize(),
                                                                 function(data){
                                                                                var response = $.parseJSON(data);
                                                                                if (!response.ok)
                                                                                {
                                                                                  alert(response.message);
                                                                                  btn.show();
                                                                                }
                                                                                else
                                                                                {
                                                                                  form.closest('td').html('Accion exitosa!');
                                                                                }
                                                                  });
                        });
                      $('.sel').selectmenu({width: 100});

                      $('.vff').click(function(){
                                                var values = $(this);
                                                $( '#detalle' ).dialog('option', 'title', values.data('emple'));
                                                $('#detalle').load('/modelo/rrhh/stctacteff.php',
                                                                   {accion:'detview',
                                                                    emple: values.data('idemp'),
                                                                    desde: '".$fin->format('Y-m-d')."',
                                                                    hasta: '".$inicio->format('Y-m-d')."'},
                                                                    function(){
                                                                                 $( '#detalle' ).dialog('open');
                                                                      });
                                               
                        });
                      $( '#detalle' ).dialog({
                            autoOpen: false,
                            height: 400,
                            width: 550,
                            modal: true,
                          });
                   </script>";
          print $tabla;
  }
  elseif($accion == 'adelfr')
  {
      if (!$_POST['fechacompadel']) //es la fecha en la que se va a diagramar el franco que finalmente se va a adelantar, debe crear la NOVEDAD de FRANCO para la fecha seleccionada y generar El MOVIMIENTO DE DEBITO para el mes siguiente
      {
        print json_encode(array('ok' => false, 'message' => 'El campo fecha no puede permanecer en blanco!'));
        return;
      }

      //Esta es la fecha en la que se le va a diagramar la Novedad de Franco
      $fecha = DateTime::createFromFormat('d/m/Y', $_POST['fechacompadel']);

      $novText = find('NovedadTexto', 49);
      $empleado = find('Empleado', $_POST['emple']);

      $estructura = find('Estructura', 1);
      $usuario = find('Usuario', $_SESSION['userid']);

      try
      {
          //verifica si no existe ya una novedad de Franco para el empleado en la fecha dada
          $q = $entityManager->createQuery("SELECT n
                                            FROM Novedad n
                                            JOIN n.novedadTexto nt
                                            JOIN n.empleado e
                                            WHERE n.desde = :fecha AND e = :empleado AND n.activa = :activo AND nt = :novedad");
          $q->setParameter('fecha',  $fecha->format('Y-m-d'))
            ->setParameter('empleado', $empleado)
            ->setParameter('activo', true)
            ->setParameter('novedad', $novText);
          $novDiagramada = $q->getOneOrNullResult();
          if ($novDiagramada)
          {
              print json_encode(array('ok' => false, 'message' => 'Ya existe un Franco diagramado en la fecha seleccionada!'));
              return;
          }
          //fin de verificacion de existencia de Novedad Diagramada


          $fechaDebito = clone $fecha;//$fecha->format('Y-m-d'));
         // $fechaDebito->add(new DateInterval('P1D'));
      /*  //verifica si no existe ya una novedad de Franco para el empleado en la fecha dada
        $q = $entityManager->createQuery("SELECT m
                                          FROM MovimientoDebitoFeriado m
                                          JOIN m.novedad n
                                          JOIN n.novedadTexto nt
                                          JOIN m.ctacte c
                                          JOIN c.empleado e
                                          WHERE m.fecha = :fecha AND e = :empleado AND m.activo = :activo AND n = :novedad
                                          ORDER BY m.fecha");
        $q->setParameter('fecha',  $fechaDebito->format('Y-m-d'))
          ->setParameter('empleado', $empleado)
          ->setParameter('activo', true)
          ->setParameter('novedad', $novText);
        $novDiagramada = $q->getOneOrNullResult();
        if ($novDiagramada)
        {
          $fechaDebito->add(new DateInterval('P1D')); //si ya existe corre un dia y la generaria al dia siguiente....ojo esto permite que solo se puedan adelantar dos francos, el tercero se pisaria con el segundo...
        }*/

        $query = ejecutarSQL("SELECT AUTO_INCREMENT
                                            FROM information_schema.tables
                                            WHERE table_name = 'novedades'");
        $nextId = 0;
        if ($row = mysql_fetch_array($query))
        {
          $nextId = $row['AUTO_INCREMENT'];
        }
        else
        {
          print json_encode(array('ok' => false, 'message' => 'No se pudo ecnnstrar el identificador de la tabla de Novedades!'));
          return;
        }

        ///debe generar la Novedad de Franco Compensatorio para la fecha indicada
        $novedad = new Novedad($nextId);
        $novedad->setEstructura($estructura);
        $novedad->setEmpleado($empleado);
        $novedad->setDesde($fecha);
        $novedad->setHasta($fecha);
        $novedad->setNovedadTexto($novText);
        $novedad->setEstado('no_disp');
        $novedad->setActiva(true);
        $novedad->setPendiente(false);
        $novedad->setUsuario($usuario);
        $novedad->setFechaAlta(new DateTime());
        $novedad->setUsertxt('');
        $entityManager->persist($novedad);

        $cc = getCtaCteFeriado($empleado);

        $debito = new MovimientoDebitoFeriado();

        $fec = clone $fechaDebito;

        $debito->setFecha($fec);
        $debito->setFechaCarga(new DateTime());


        $debito->setPeriodoMes($fec->format('m'));
        $debito->setPeriodoAnio($fec->format('Y'));
        $debito->setCantidad(1);
        $debito->setDescripcion($novText->getTexto().' - '.$fecha->format('d/m/Y'));
        $debito->setEstructura($estructura);
        $debito->setCtacte($cc);

        $debito->setNovedad($novedad);
        $entityManager->persist($debito);
        $entityManager->flush();
        print json_encode(array('ok' => true));
        return;
      }
      catch (Exception $e){
                          print json_encode(array('ok' => false, 'message' => $e->getMessage()));
                          return;
      }
  }
  elseif ($accion == 'detview')
  {
      $empleado = find('Empleado', $_POST['emple']);

      $q = $entityManager->createQuery("SELECT m
                                        FROM MovimientoDebitoFeriado m
                                        JOIN m.ctacte c
                                        JOIN c.empleado e
                                        JOIN e.empleador emp
                                        JOIN emp.estructura stremp
                                        JOIN e.estructura str
                                        JOIN e.categoria cat
                                        WHERE m.fecha BETWEEN :desde AND :hasta AND e = :empleado AND m.activo = :activo 
                                        ORDER BY m.fecha");
      $q->setParameter('desde',  $_POST['desde'])
        ->setParameter('activo', true)
        ->setParameter('empleado', $empleado);
      $q->setParameter('hasta',  $_POST['hasta']);
      $movimientos = $q->getResult();

      $tabla ='<table align="center" width="100%" class="table table-zebra">
                 <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Descripcion</th>
                            <th>Orden Diagramada</th>
                            <th>Fecha Diagrama</th>
                        </tr>
                 </thead>
                 <tbody>';
      foreach ($movimientos as $mov)
      {
          $orden = $mov->getNovedad()->getNovedadTexto()->getTexto();
          if ($mov->getFeriadoAsociado())
          {
            $orden = "Feriado ".$mov->getFeriadoAsociado()->getFecha()->format('d/m/Y');
          }

          $tabla.="<tr>
                       <td>".$mov->getFecha()->format('d/m/Y')."</td>
                       <td>".$orden."</td>
                       <td>".$mov->getNovedad()->getNovedadTexto()->getTexto()."</td>
                       <td>".$mov->getNovedad()->getDesde()->format("d/m/Y")."</td>
                   </tr>";
      }
      $tabla.="</tbody>
              </table>";
      print $tabla;
  }
  elseif($accion == 'cpms')
  {
      if (!$_POST['fechacomp'])
      {
        print json_encode(array('ok' => false, 'message' => 'El campo fecha no puede permanecer en blanco!'));
        return;
      }



      $fecha = DateTime::createFromFormat('d/m/Y', $_POST['fechacomp']);

      $feriado = find('Feriado', $_POST['feriado']);

      $novText = find('NovedadTexto', 25);
      $empleado = find('Empleado', $_POST['emple']);

      $estructura = find('Estructura', 1);
      $usuario = find('Usuario', $_SESSION['userid']);

      try
      {
        
        $query = ejecutarSQL("SELECT AUTO_INCREMENT
                                            FROM information_schema.tables
                                            WHERE table_name = 'novedades'");
        $nextId = 0;
        if ($row = mysql_fetch_array($query))
        {
          $nextId = $row['AUTO_INCREMENT'];
        }
        else
        {
          print json_encode(array('ok' => false, 'message' => 'No se pudo ecnnstrar el identificador de la tabla de Novedades!'));
          return;
        }

        ///debe generar la Novedad de Franco Compensatorio para la fecha indicada
        $novedad = new Novedad($nextId);
        $novedad->setEstructura($estructura);
        $novedad->setEmpleado($empleado);
        $novedad->setDesde($fecha);
        $novedad->setHasta($fecha);
        $novedad->setNovedadTexto($novText);
        $novedad->setEstado('no_disp');
        $novedad->setActiva(true);
        $novedad->setPendiente(false);
        $novedad->setUsuario($usuario);
        $novedad->setFechaAlta(new DateTime());
        $novedad->setUsertxt('');
        $entityManager->persist($novedad);

        $cc = getCtaCteFeriado($empleado);

        $debito = new MovimientoDebitoFeriado();

        $fec = $feriado->getFecha();

        $debito->setFecha($fec);
        $debito->setFechaCarga(new DateTime());

        if ($fec->format('d') > 25)
        {
          $fec->add(new DateInterval('P1M'));
        }

        $debito->setPeriodoMes($fec->format('m'));
        $debito->setPeriodoAnio($fec->format('Y'));
        $debito->setCantidad(1);
        $debito->setDescripcion($novText->getTexto().' - '.$fecha->format('d/m/Y'));
        $debito->setEstructura($estructura);
        $debito->setCtacte($cc);
        $debito->setFeriadoAsociado($feriado);
        $debito->setNovedad($novedad);
        $entityManager->persist($debito);
        $entityManager->flush();
        print json_encode(array('ok' => true));
        return;
      }
      catch (Exception $e){
                          print json_encode(array('ok' => false, 'message' => $e->getMessage()));
                          return;
      }

  }
  elseif($accion == 'cpmsf')
  {
      if (!$_POST['fechacomp'])
      {
        print json_encode(array('ok' => false, 'message' => 'El campo fecha no puede permanecer en blanco!'));
        return;
      }
      $fecha = DateTime::createFromFormat('d/m/Y', $_POST['fechacomp']);
      $novText = find('NovedadTexto', 25);
      $empleado = find('Empleado', $_POST['emple']);

      $estructura = find('Estructura', 1);
      $usuario = find('Usuario', $_SESSION['userid']);
      try
      {
        
        $query = ejecutarSQL("SELECT AUTO_INCREMENT
                                            FROM information_schema.tables
                                            WHERE table_name = 'novedades'");
        $nextId = 0;
        if ($row = mysql_fetch_array($query))
        {
          $nextId = $row['AUTO_INCREMENT'];
        }
        else
        {
          print json_encode(array('ok' => false, 'message' => 'No se pudo ecnnstrar el identificador de la tabla de Novedades!'));
          return;
        }
        ///debe generar la Novedad de Franco Compensatorio para la fecha indicada
        $novedad = new Novedad($nextId);
        $novedad->setEstructura($estructura);
        $novedad->setEmpleado($empleado);
        $novedad->setDesde($fecha);
        $novedad->setHasta($fecha);
        $novedad->setNovedadTexto($novText);
        $novedad->setEstado('no_disp');
        $novedad->setActiva(true);
        $novedad->setPendiente(false);
        $novedad->setUsuario($usuario);
        $novedad->setFechaAlta(new DateTime());
        $novedad->setUsertxt('');
        $entityManager->persist($novedad);

        $cc = getCtaCteFeriado($empleado);

        $debito = new MovimientoDebitoFeriado();

        $fec = DateTime::createFromFormat('Y-m-d', $_POST['fecha']);

        $debito->setFecha($fec);
        $debito->setFechaCarga(new DateTime());

        $debito->setPeriodoMes($fec->format('m'));
        $debito->setPeriodoAnio($fec->format('Y'));
        $debito->setCantidad(1);
        $debito->setDescripcion($novText->getTexto().' - '.$fecha->format('d/m/Y'));
        $debito->setEstructura($estructura);
        $debito->setCtacte($cc);
        $debito->setNovedad($novedad);
        $cc->addMovimiento($debito);
        $entityManager->persist($debito);
        $entityManager->flush();
        print json_encode(array('ok' => true));
        return;
      }
      catch (Exception $e){
                          print json_encode(array('ok' => false, 'message' => $e->getMessage()));
                          return;
      }

  }
?>

