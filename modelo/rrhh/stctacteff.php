<?php
  error_reporting(E_ALL);
  set_time_limit(0);
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
    
      $aux = new DateTime(); //DateTime::createFromFormat('Y-m-d', "$_POST[anio_d]-$_POST[mes_d]-26");   

      $hasta = DateTime::createFromFormat('Y-m-d', $aux->format('Y').'-'.$aux->format('m').'-26'); //crea la fecha hasta el dia 26 del mes actual
      if ($aux->format('d') > 25)
      {
          $hasta->add(new DateInterval('P1M'));
      }

      
     /* $fin = clone $inicio;
      $fin->sub(new DateInterval('P1M'));
      $fin->add(new DateInterval('P1D'));*/
    //  $fin = DateTime::createFromFormat('Y-m-d', "$_POST[anio_h]-$_POST[mes_h]-25");   
      $resumen = array();
      try
      {
          $francosCreditos = getFrancosCreditos($hasta->format('Y-m-d'), $_POST['empleador']);    

          foreach ($francosCreditos as $m)
          {
              if (!array_key_exists($m['idE'], $resumen))
              {
                $resumen[$m['idE']] = array('leg' => $m['leg'], 'ide' => $m['idE'],'e' => ($m['ape'].', '.$m['nom']), 'fr' => $m['cant'], 'fe' => 0, 'frt' => 0, 'fet' => 0, 'fac' => 0);
              }
              else
              {
                $resumen[$m['idE']]['fr']+= $m['cant'];
              }
          }

         // die(print_r($resumen));

          $francosDebitos = getFrancosDebitos($hasta->format('Y-m-d'), $_POST['empleador']);
          foreach ($francosDebitos as $m)
          {
              if (!array_key_exists($m['idE'], $resumen))
              {
                $resumen[$m['idE']] = array('leg' => $m['leg'], 'ide' => $m['idE'],'e' => ($m['ape'].', '.$m['nom']), 
                                            'fr' => 0, 'fe' => 0, 'frt' => $m['cant'], 'fet' => 0, 'fac' => 0);
              }
              else
              {
                $resumen[$m['idE']]['frt']+= $m['cant'];
              }
          }


          $feriadosDebitos = getFeriadoDebitos($hasta->format('Y-m-d'), $_POST['empleador']);
          foreach ($feriadosDebitos as $m)
          {
              if (!array_key_exists($m['idE'], $resumen))
              {
                $resumen[$m['idE']] = array('leg' => $m['leg'], 'ide' => $m['idE'],'e' => ($m['ape'].', '.$m['nom']), 
                                            'fr' => 0, 'fe' => 0, 'frt' => 0, 'fet' => $m['cant'], 'fac' => 0);
              }
              else
              {
                $resumen[$m['idE']]['fet']+= $m['cant'];
              }
          }

       //   $conductor  = array_column($resumen, 'e');
     //     array_multisort($conductor, SORT_ASC, $resumen);

        //  die(print_r($resumen));

          $ff = $entityManager->createQuery("SELECT m.fecha as fecha, m.id as id, e.id as idCtaCte
                                            FROM MovimientoDebitoFeriado m
                                            JOIN m.ctacte cc
                                            JOIN cc.empleado e
                                            WHERE m.activo = :activo AND m.compensable = :comp AND m.compensado = :compensado
                                            ORDER BY e.id, m.fecha ASC");
          $ff->setParameter('activo', false)
             ->setParameter('compensado', false)
             ->setParameter('comp', true);
          $fferiados = $ff->getResult();

          $fadel = $entityManager->createQuery("SELECT m.fecha as fecha, m.id as id, e.id as idCtaCte
                                                FROM MovimientoDebitoFeriado m
                                                JOIN m.ctacte cc
                                                JOIN cc.empleado e
                                                JOIN m.novedad nv
                                                JOIN nv.novedadTexto nt
                                                WHERE m.activo = :activo AND m.aplicado = :aplicado AND nt.id = :novedad
                                                ORDER BY e.id, m.fecha ASC");
          $fadel->setParameter('activo', true)
               ->setParameter('aplicado', false)
               ->setParameter('novedad', 49);
          $francoAdelantado = $fadel->getResult();

      }
      catch(Exception $e){
                          die($e->getMessage());
                         }

       $francosACompensar = array();

       foreach($fferiados as $fsf)
       {
          if (!array_key_exists($fsf['idCtaCte'], $francosACompensar))
          {
            $francosACompensar[$fsf['idCtaCte']] =  array();
          }
          $francosACompensar[$fsf['idCtaCte']][]="<option value='$fsf[id]'>".$fsf['fecha']->format('d/m/Y')."</option>";
       }

      // die(print_r($francosACompensar));

       $francosAadelantados = array();

       foreach($francoAdelantado as $fa)
       {
          if (!array_key_exists($fa['idCtaCte'], $francosAadelantados))
          {
            $francosAadelantados[$fa['idCtaCte']] =  array();
          }
          $francosAadelantados[$fa['idCtaCte']][]="<option value='$fa[id]'>".$fa['fecha']->format('d/m/Y')."</option>";
       }


       $str = find('Estructura', 1); 
       //$feriados = getFeriados($str, $fin, $inicio);
       $feriados = getFeriados($str, $hasta);
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
                                <th colspan="4" align="center">
                                    Diagramados
                                </th>
                                <th>Compensar</th>   
                                <th colspan="2">Francos Adelantados</th>   
                            </tr>
                            <tr>
                                <th align="center">Francos</th>
                                <th align="center">Feriados</th>
                                <th align="center">Francos A Compensar</th>
                                <th align="center">Francos Adelantados</th>
                                <th align="center">Francos</th>
                                <th align="center">Adelantar Franco</th>
                                <th align="center">Devolver Franco</th>
                            </tr>
                     </thead>
                     <tbody>';
/*
          foreach ($movimientos as $m)
          {
              $mov = $m[0];
             // $emple = $mov->getCtaCte()->getEmpleado();           
              if (!array_key_exists($m['idE'], $resumen))
              {
                $resumen[$m['idE']] = array('leg' => $m['leg'], 'ide' => $m['idE'],'e' => ($m['ape'].', '.$m['nom']), 'fr' => 0, 'fe' => 0, 'frt' => 0, 'fet' => 0, 'fac' => 0);
              }

              $resumen[$m['idE']]['fr']+= $mov->getFrancoCredito();
              //$resumen[$emple->getId()]['fe']+= $mov->getFeriadoCredito();
              $resumen[$m['idE']]['frt']+= $mov->getFrancoDebito();
              $resumen[$m['idE']]['fet']+= $mov->getFeriadoDebito();
              //$resumen[$emple->getId()]['fac']+= $mov->getDebitosACompensar();
          }*/

         // die(print_r(array_keys($resumen)));
          
          foreach ($resumen as $key => $res)
          {
                $franco = "";
                if (($res['fr'] > $res['frt']) || count($francosACompensar[$key]))
                {
                    $select = "";
                    if (array_key_exists($key, $francosACompensar))
                    {
                        $select = "<select name='frsfer'>".implode('',$francosACompensar[$key])."</select>";
                    }

                    $franco = "<form class='cpms'>
                                $select
                                <input type='text' class='fecha' name='fechacomp'/>
                                <input type='button' value='+' class='btn'/>
                                <input type='hidden' name='accion' value='cpmsf'/>
                                <input type='hidden' name='emple' value='".$res['ide']."'/>                                
                              </form>";                
                    //<input type='hidden' name='fecha' value='".$inicio->format('Y-m-d')."'/>
                    /*$franco = "<form class='cpms'>
                                  <input type='text' class='fecha' name='fechacomp'/>
                                  <input type='button' value='+' class='btn'/>
                                  <input type='hidden' name='accion' value='cpmsf'/>
                                  <input type='hidden' name='emple' value='".$res['e']->getId()."'/>
                                  <input type='hidden' name='fecha' value='".$inicio->format('Y-m-d')."'/>
                                </form>";*/
                }
                $adelantar = "<form class='adelantar'>
                              <input type='text' class='fechaadelante' name='fechacompadel'/>
                              <input type='button' value='+' class='btn'/>
                              <input type='hidden' name='accion' value='adelfr'/>
                              <input type='hidden' name='emple' value='".$res['ide']."'/>                              
                            </form>";
                            ///<input type='hidden' name='fecha' value='".$inicio->format('Y-m-d')."'/>

                $compensa = "";
               /* if ($res['fe'] > $res['fet'])
                {
                    $compensa = "<form class='cpms'>
                                  $select
                                  <input type='text' class='fecha' name='fechacomp'/>
                                  <input type='button' value='+' class='btn'/>
                                  <input type='hidden' name='accion' value='cpms'/>
                                  <input type='hidden' name='emple' value='".$res['e']->getId()."'/>
                                </form>";
                }*/
                $devolver="";
                if (array_key_exists($key, $francosAadelantados))
                {
                    $devolver = "<form class='dev'>
                                  <select name='francodev'>".implode('',$francosAadelantados[$key])."</select>
                                  <input size='10' type='text' class='fecha' name='fechadevolucion'/>
                                  <input type='button' value='+' class='devfr'/>
                                  <input type='hidden' name='accion' value='devolver'/>
                                  <input type='hidden' name='emple' value='".$res['ide']."'/>
                                </form>";
                }

                $frAComp = $res['fr']; 
                $tabla.="<tr>
                             <td title='".$key." _ Francos A Diagramar: $res[fr] - Francos Diagramados: $res[frt] - Francos A Commpensar ".count($francosACompensar[$res['ide']])."' >".$res['leg']."</td>
                             <td>".$res['e']."</td>
                             <td title='Click para ver detalle...' class='vff' data-emple='$res[e]' data-idemp='".$res['ide']."' align='center'>".$res['frt']."</td>
                             <td title='Click para ver detalle...' class='vff' data-emple='$res[e]' data-idemp='".$res['ide']."' align='center'>".$res['fet']."</td>
                             <td title='Click para ver detalle...' class='' data-emple='$res[e]' data-idemp='".$res['ide']."' align='center'><b>".($res['fr'] - $res['frt'])."</b></td>
                             <td title='Click para ver detalle...' class='vff' data-tipo='adelan' data-emple='$res[e]' data-idemp='".$res['ide']."' align='center'><b>".count($francosAadelantados[$key])."</b></td>
                             <td align='right'>$franco</td>

                             <td align='center'>$adelantar</td>
                             <td>$devolver</td>
                         </tr>";
          }
          $mesActual = ($hasta->format('m') -2);
          $mesAdelante = ($hasta->format('m')-1); //en jquery es el mes menos uno
          $yearFrom = $hasta->format('Y');
          $tabla.="</tbody>
                   </table>
                   <div id='detalle'>
                   </div>
                   <script type='text/javascript'>

                      $('.devfr').button().click(function(){
                                                            var form = $(this).closest('form');
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

                      $('.fecha').datepicker();
                      $('.fechaadelante').datepicker({
                        minDate: new Date($yearFrom,$mesActual, 26)
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
                                                                    tipo: values.data('tipo'),
                                                                    hasta: '".$hasta->format('Y-m-d')."'},
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
  elseif($accion == 'devolver')
  {
      if (!$_POST['fechadevolucion'])
      {
           print json_encode(array('ok' => false, 'message' => 'Debe cargar un periodo para afectar la compenacion!'));
           return;
      }
      $empleado = find('Empleado', $_POST['emple']);
      $mov = null;
      try
      {

             /* $q = $entityManager->createQuery("SELECT m
                                        FROM MovimientoDebitoFeriado m
                                        JOIN m.novedad n
                                        JOIN n.novedadTexto nt
                                        JOIN m.ctacte c
                                        JOIN c.empleado e
                                        WHERE e = :empleado AND m.activo = :activo AND nt.id = :novtext AND m.aplicado =:aplicado
                                        ORDER BY m.fecha ASC");
              $q->setParameter('novtext', 49)
                ->setParameter('activo', true)
                ->setParameter('aplicado', false)
                ->setParameter('empleado', $empleado);
              $movimientos = $q->getResult();

              $mov = array_shift($movimientos);*/

              $mov = find('MovimientoDebitoFeriado', $_POST['francodev']);

              if (!$mov)
              {
                   print json_encode(array('ok' => false, 'message' => 'No existen Francos A Compensar para '.$empleado));
                   return;
              }

     


     $fecha = $_POST['fechadevolucion'];//explode("-", $_POST['fechadevolucion']); //el formato que llega es mm-yy debe desarmarlo y restarle uno al mes para que la novedad se cargue con el primer dia del periodo al cual se desea aplicar la compensacion
   //  $mes = $periodo[0];
  //   $anio = $periodo[1];
 //    $fecha = "26-$mes-$anio";
     $fecha = DateTime::createFromFormat('d/m/Y', $fecha);
  //   $fecha->sub(new DateInterval('P1M'));
     $mes = $fecha->format('m');
     $anio = $fecha->format('Y');

     $novText = find('NovedadTexto', 50);
     $estructura = find('Estructura', 1);
     $usuario = find('Usuario', $_SESSION['userid']);


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

        ///debe generar la Novedad de Compensa Franco para la fecha indicada
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

        $fec = clone $fecha;

        $debito->setFecha($fec);
        $debito->setFechaCarga(new DateTime());


        $debito->setPeriodoMes($mes);
        $debito->setPeriodoAnio($anio);
        $debito->setCantidad(1);
        $debito->setDescripcion($novText->getTexto().' de fecha '.$mov->getFecha()->format('d/m/Y'));
        $debito->setEstructura($estructura);
        $debito->setCtacte($cc);
        $debito->setDebitoOrigen($mov);
        $debito->setNovedad($novedad);
        $mov->setAplicado(true);
        $entityManager->persist($debito);

        //al devolver un franco debe cancelar si existen las novedades de franco y solo ejar la novedad de franco compensado
        $novedadFranco= $entityManager->createQuery("SELECT n
                                                    FROM Novedad n
                                                    JOIN n.estructura es
                                                    JOIN n.empleado e
                                                    JOIN n.novedadTexto nt
                                                    WHERE e.id = :empleado AND n.desde = :fecha AND nt.id = :idCodigo")
                                      ->setParameter('empleado',$empleado->getId())  
                                      ->setParameter('idCodigo', 15)
                                      ->setParameter('fecha',$fecha->format('Y-m-d'))          
                                      ->getResult();   
        foreach ($novedadFranco as $nf)
        {
          $nf->setActiva(false);
        }

        $entityManager->flush();
        print json_encode(array('ok' => true));
        return;
  }
     catch (Exception $e){ print json_encode(array('ok' => false, 'message' => $e->getMessage()));}
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


      if ($_POST['tipo'] == 'adelan')
      {
        try{
            $q = $entityManager->createQuery("SELECT m
                                                  FROM MovimientoDebitoFeriado m
                                                  JOIN m.ctacte cc
                                                  JOIN cc.empleado e
                                                  JOIN m.novedad nv
                                                  JOIN nv.novedadTexto nt
                                                  WHERE m.activo = :activo AND m.aplicado = :aplicado AND nt.id = :novedad AND e = :empleado
                                                  ORDER BY m.fecha");

          $q->setParameter('activo', true)
            ->setParameter('aplicado', false)
            ->setParameter('empleado', $empleado);
          $q->setParameter('novedad',  49);
          $movimientos = $q->getResult();
        }
        catch (Exception $e){die($e->getMessage());}
      }
      else
      {
        $q = $entityManager->createQuery("SELECT m
                                            FROM MovimientoDebitoFeriado m
                                            JOIN m.ctacte c
                                            JOIN c.empleado e
                                            JOIN e.empleador emp
                                            JOIN emp.estructura stremp
                                            JOIN e.estructura str
                                            JOIN e.categoria cat
                                            JOIN m.novedad nv
                                            JOIN nv.novedadTexto nt
                                            WHERE m.fecha <= :hasta AND e = :empleado AND m.activo = :activo AND (nt.id = :novedad OR nt.id = :otra OR nt.id = :otramas)
                                            ORDER BY m.fecha");
          $q->setParameter('activo', true)
            ->setParameter('otra',25)
            ->setParameter('novedad', 15)
            ->setParameter('otramas',50)
            ->setParameter('empleado', $empleado);
          $q->setParameter('hasta',  $_POST['hasta']);
          $movimientos = $q->getResult();
      }
      $tabla ='<table align="center" width="100%" class="table table-zebra">
                 <thead>
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Descripcion</th>
                            <th>Orden Diagramada</th>
                            <th>Fecha Diagrama</th>
                        </tr>
                 </thead>
                 <tbody>';
      $id = "";
      $i = 1;
      foreach ($movimientos as $mov)
      {
          $id = $mov->getCtacte()->getId();
          $orden = $mov->getNovedad()->getNovedadTexto()->getTexto();
          if ($mov->getFeriadoAsociado())
          {
            $orden = "Feriado ".$mov->getFeriadoAsociado()->getFecha()->format('d/m/Y');
          }

          $tabla.="<tr>
                       <td>$i</td>
                       <td>".$mov->getFecha()->format('d/m/Y')."</td>
                       <td>".($mov->getDescripcion()?$mov->getDescripcion():$orden)."</td>
                       <td>".$mov->getNovedad()->getNovedadTexto()->getTexto()."</td>
                       <td>".$mov->getNovedad()->getDesde()->format("d/m/Y")."</td>
                   </tr>";
          $i++;
      }
      $tabla.="</tbody>
              </table>
              CC: $id";
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

        $francoACompensar = null;

        $texto = '';

        if ($_POST['frsfer'])
        {
          $francoACompensar = find('MovimientoDebitoFeriado', $_POST['frsfer']);
          if ($francoACompensar)
          {
             $francoACompensar->setCompensado(true);
             $texto = $novText->getTexto().' - '.$francoACompensar->getFecha()->format('d/m/Y');
          }
        }
        else
        {
            $texto = $novText->getTexto().' - '.$fecha->format('d/m/Y');
        }

       // die(json_encode(array('ok' => false, 'message' => 'EL ID ES '.$francoACompensar->getId())));


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

        $fec = $fecha;//DateTime::createFromFormat('Y-m-d', $_POST['fecha']);

        $debito->setFecha($fec);
        $debito->setFechaCarga(new DateTime());

        $debito->setDebitoOrigen($francoACompensar);

        $debito->setPeriodoMes($fec->format('m'));
        $debito->setPeriodoAnio($fec->format('Y'));
        $debito->setCantidad(1);
        $debito->setDescripcion($texto);
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

