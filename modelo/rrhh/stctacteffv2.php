<?php
  error_reporting(E_ALL);
  set_time_limit(0);
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');

  include('../../modelsORM/src/MovimientoDebitoFeriado.php');
  include('../../modelsORM/src/MovimientoCreditoFeriado.php');
  include('../../modelsORM/src/Novedad.php');
  include('../../modelsORM/manager.php');
  include_once('../../modelsORM/call.php');
  include_once('../../modelsORM/controller.php');
  $accion = $_POST['accion'];

  if($accion == 'load')
  {
    
      ///En septiembre de 2022 cambia el rango de liquidacion, del 26 al 25   pasa a liquidarse del 21 al 20

      $aux = new DateTime(); //DateTime::createFromFormat('Y-m-d', "$_POST[anio_d]-$_POST[mes_d]-26");   

      $hasta = DateTime::createFromFormat('Y-m-d', $aux->format('Y').'-'.$aux->format('m').'-21'); //crea la fecha hasta el dia 26 del mes actual

      if ($aux->format('d') > 20)
      {
          $hasta->add(new DateInterval('P1M'));
      }

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

          $ff = $entityManager->createQuery("SELECT m.fecha as fecha, m.id as id, e.id as idCtaCte
                                            FROM MovimientoDebitoFeriado m
                                            JOIN m.ctacte cc
                                            JOIN cc.empleado e
                                            JOIN m.novedad nv
                                            JOIN nv.novedadTexto nt
                                            WHERE m.fecha > :fecha AND m.activo = :activo AND m.compensable = :comp AND m.compensado = :compensado AND nt.id in (:novedades) AND e.activo = :activo
                                            ORDER BY e.id, m.fecha ASC");
          $ff->setParameter('activo', true)
             ->setParameter('fecha', '2020-11-20')
             ->setParameter('compensado', false)
             ->setParameter('novedades', [15, 16, 54])
             ->setParameter('comp', true);
          $fferiados = $ff->getResult();

          $fadel = $entityManager->createQuery("SELECT m.fecha as fecha, m.id as id, e.id as idCtaCte
                                                FROM MovimientoDebitoFeriado m
                                                JOIN m.ctacte cc
                                                JOIN cc.empleado e
                                                JOIN m.novedad nv
                                                JOIN nv.novedadTexto nt
                                                WHERE m.fecha > :fecha AND m.activo = :activo AND m.aplicado = :aplicado AND nt.id = :novedad AND e.activo = :activo
                                                ORDER BY e.id, m.fecha ASC");
          $fadel->setParameter('activo', true)
               ->setParameter('aplicado', false)
               ->setParameter('fecha', '2020-11-25')
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

          $tabla ='
                  <style type="text/css">


                      thead { 
                              position: sticky;
                              top: 0;
                              z-index: 10;
                              background-color: #ffffff;
                      }

                  </style>
                   <table id="tablitasssss" align="center" width="100%" class="table table-zebra">
                     <thead>
                            <tr>
                                <th rowspan="2">Legajo</th>                                
                                <th rowspan="2">Apellido, Nombre</th>
                                <th rowspan="2"></th> 
                                <th colspan="2" align="center">
                                    Diagramados
                                </th>
                                <th align="center">Deuda Francos</th>   
                                <th colspan="2" align="center">Compensar</th>  
                                
                                <th colspan="2">Francos Adelantados</th>   
                            </tr>
                            <tr class"sidebar">
                                <th align="center">Francos</th>
                                <th align="center">Francos <br>Adelantados</th>
                                <th align="center">Francos A <br> Compensar</th>
                                
                                <th align="center">Diagramar Franco</th>
                                <th align="center">Compensar Franco</th>
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
                $compAdelant = "";
                $franco = "";
                if (($res['fr'] > $res['frt']))// || (array_key_exists($key, $francosACompensar)?count($francosACompensar[$key]):0))
                {
                    $select = "";
                    if (array_key_exists($key, $francosACompensar))
                    {
                        $select = "<select name='frsfer'>".implode('',$francosACompensar[$key])."</select>";
                        if (array_key_exists($key, $francosAadelantados))
                        {
                            $frac = "<select name='fsfac'>".implode('',$francosACompensar[$key])."</select>";
                            $fradelac= "<select name='fradcmp'>".implode('',$francosAadelantados[$key])."</select>";
                            $compAdelant = "<form class='compadel'>
                                            <div>
                                                <div style='display: inline-block;'>
                                                  Franco A Compensar
                                                </div>/
                                                <div style='display: inline-block;'>
                                                  Franco Adelantado
                                                </div>
                                            </div>
                                            <div>
                                                <div style='display: inline-block;'>
                                                  $frac
                                                </div>/
                                                <div style='display: inline-block;'>
                                                  $fradelac
                                                </div>
                                            </div>
                                            
                                            <input type='button' value='+' class='cmpfsf'/>
                                            <input type='hidden' name='accion' value='cmpadel'/>
                                            <input type='hidden' name='emple' value='".$res['ide']."'/>                              
                                          </form>";
                        }
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
                //<td title='Click para ver detalle...' class='vff' data-emple='$res[e]' data-idemp='".$res['ide']."' align='center'>".$res['fet']."</td>

                $adelantados = 0;
                if (array_key_exists($key, $francosAadelantados))
                {
                    $adelantados = count($francosAadelantados[$key]);
                }

                $francosAdelans = 0;
                if (array_key_exists($res['ide'], $francosACompensar))
                {
                    $francosAdelans = count($francosACompensar[$res['ide']]);
                }
                $tabla.="<tr>
                             <td title='".$key." _ Francos A Diagramar: $res[fr] - Francos Diagramados: $res[frt] - Francos A Commpensar ".$francosAdelans."' >".$res['leg']."</td>
                             <td>".$res['e']."</td>
                             <td><a class='cladjust' data-emp='$res[ide]'><i class='fas fa-screwdriver'></i></a></td>
                             <td title='Click para ver detalle...' class='vff' data-tipo='diag' data-emple='$res[e]' data-idemp='".$res['ide']."' align='center'>".$res['frt']."</td>
                             
                             
                             <td title='Click para ver detalle...' class='vff' data-tipo='adelan' data-emple='$res[e]' data-idemp='".$res['ide']."' align='center'><b>".$adelantados."</b></td>

                             <td title='Click para ver detalle...' class='' data-tipo='facmp' data-emple='$res[e]' data-idemp='".$res['ide']."' align='center'><b>".($res['fr'] - $res['frt'])."</b></td>
                             <td align='right'>$franco</td>
                             <td>$compAdelant</td>
                             <td align='center'>$adelantar</td>
                             <td>$devolver</td>
                         </tr>";
          }
          $mesActual = ($hasta->format('m') -2);
          $mesAdelante = ($hasta->format('m')-1); //en jquery es el mes menos uno
          $yearFrom = $hasta->format('Y');

          $formAjuste = "<form id='formAdjust'>
                            <div>
                              <label for='fecha'>Fecha</label>
                              <input type='text' name='fecha' class='fechaAjuste' id='fechaAjuste'/>
                            </div>
                            <br>
                            <div>
                              <label for='cant'>Cantidad</label>
                              <input type='text' name='cant' id='cantAjuste'/>
                            </div>
                            <div>
                                  <a class='' href='#'>Guardar</a>
                            </div>
                            <input type='hidden' name='accion' value='ajstcc'/>
                            <input type='hidden' name='emple' id='emple'/>
                          </form>
                            ";
          $tabla.="</tbody>
                   </table>
                   <div id='detalle'>
                   </div>
                   <div id='ajuste'>
                      $formAjuste
                   </div>
                   <script type='text/javascript'>

                      $('.saveAdj').button().click(function(event){
                                                                event.preventDefault();  
                                                                $.post('/modelo/rrhh/stctacteffv2.php',
                                                                       $('#formAdjust').serialize(),
                                                                       function(data){
                                                                                var response = $.parseJSON(data);
                                                                                if (!response.ok)
                                                                                {
                                                                                  alert(response.message);
                                                                                }
                                                                                else
                                                                                {
                                                                                  $('#ajuste').dialog('close');
                                                                                }
                                                                        });
                      });

                      $('.cladjust').click(function(){
                                                        var a = $(this);
                                                        $('#emple').val(a.data('emp'));
                                                        $( '#ajuste' ).dialog('open');
                      });

                      $('.cmpfsf').button().click(function(){
                                                            var form = $(this).closest('form');
                                                            var btn = $(this);
                                                            if (confirm('Compensar franco?'))
                                                            {
                                                                $.post('/modelo/rrhh/stctacteffv2.php',
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
                                                            }
                          });

                      $('.devfr').button().click(function(){
                                                            var form = $(this).closest('form');
                                                            $.post('/modelo/rrhh/stctacteffv2.php',
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
                       $('.fechaAjuste').datepicker({autoOpen : false});
                      $('.fechaadelante').datepicker({
                        minDate: new Date($yearFrom,$mesActual, 21)
                        });
                      $('.btn').button().click(function(){
                                                          var btn = $(this);
                                                          var form = btn.closest('form');
                                                          btn.hide();
                                                          $.post('/modelo/rrhh/stctacteffv2.php',
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
                                                $('#detalle').load('/modelo/rrhh/stctacteffv2.php',
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

                      $( '#ajuste' ).dialog({
                            autoOpen: false,
                            height: 250,
                            width: 550,
                            modal: true,
                          });
                   </script>";
          print $tabla;
  }
  elseif($accion == 'ajstcc')
  {
      $novText = find('NovedadTexto', 54);
      $empleado = find('Empleado', $_POST['emple']);

      $estructura = find('Estructura', 1);
      $usuario = find('Usuario', $_SESSION['userid']);

      if ((!$_POST['fecha']) || (!$_POST['cant']) || (!is_numeric($_POST['cant'])))
      {
          print json_encode(array('ok' => false, 'message' => 'Alguno de los datos es invalido!'));
          return;
      }

      $fecha = DateTime::createFromFormat('d/m/Y', $_POST['fecha']);
      $cant = $_POST['cant'];

      try
      {
        
        $query = ejecutarSQL("SELECT AUTO_INCREMENT FROM information_schema.tables WHERE table_name = 'novedades'");
        $nextId = 0;
        if ($row = mysql_fetch_array($query))
        {
          $nextId = $row['AUTO_INCREMENT'];
        }
        else
        {
          print json_encode(array('ok' => false, 'message' => 'No se pudo encontrar el identificador de la tabla de Novedades!'));
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


        if ($fecha->format('d') > 20)
        {
          $fecha->add(new DateInterval('P1M'));
        }

        $debito->setFecha($fecha);
        $debito->setFechaCarga(new DateTime());
        $debito->setDebitoOrigen($francoACompensar);
        $debito->setPeriodoMes($fecha->format('m'));
        $debito->setPeriodoAnio($fecha->format('Y'));
        $debito->setCantidad($cant);
        $debito->setDescripcion('Ajuste Cuenta Francos: '.$fecha->format('d/m/Y'));
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
  elseif($accion == 'cmpadel')
  {
      $francoACompensar = find('MovimientoDebitoFeriado', $_POST['fsfac']);

      if (!$francoACompensar)
      {
          print json_encode(array('ok' => false, 'message' => 'No se pudo encontrar el franco a compensar!'));
          return;
      }

      $francoAdelantado = find('MovimientoDebitoFeriado', $_POST['fradcmp']);

      if (!$francoAdelantado)
      {
          print json_encode(array('ok' => false, 'message' => 'No se pudo encontrar el franco adelantado!'));
          return;
      }

      $francoACompensar->setDebitoOrigen($francoAdelantado);
      $francoACompensar->setCompensado(true);
      $francoAdelantado->setCompensado(true);
      $francoACompensar->setAplicado(true);
      $francoAdelantado->setAplicado(true);
      $fecha = $francoACompensar->getFecha();

      $novText = find('NovedadTexto', 50);
      $empleado = find('Empleado', $_POST['emple']);

      $estructura = find('Estructura', 1);
      $usuario = find('Usuario', $_SESSION['userid']);

      try
      {
        
        $query = ejecutarSQL("SELECT AUTO_INCREMENT FROM information_schema.tables WHERE table_name = 'novedades'");
        $nextId = 0;
        if ($row = mysql_fetch_array($query))
        {
          $nextId = $row['AUTO_INCREMENT'];
        }
        else
        {
          print json_encode(array('ok' => false, 'message' => 'No se pudo encontrar el identificador de la tabla de Novedades!'));
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


        if ($fecha->format('d') > 20)
        {
          $fecha->add(new DateInterval('P1M'));
        }

        $debito->setFecha($fecha);
        $debito->setFechaCarga(new DateTime());
        $debito->setDebitoOrigen($francoACompensar);
        $debito->setPeriodoMes($fecha->format('m'));
        $debito->setPeriodoAnio($fecha->format('Y'));
        $debito->setCantidad(1);
        $debito->setDescripcion('Compensa Franco Fecha: '.$fecha->format('d/m/Y').' - (Adeuda Dia del: '.$francoAdelantado->getFecha()->format('d/m/Y').')');
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
        $mov->setCompensado(true);
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
        $debito->setCompensable(true);
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

      $printer = true;

      $id = "";

      if ($_POST['tipo'] == 'adelan')
      {
        try{
            $q = $entityManager->createQuery("SELECT m
                                                  FROM MovimientoDebitoFeriado m
                                                  JOIN m.ctacte cc
                                                  JOIN cc.empleado e
                                                  JOIN m.novedad nv
                                                  JOIN nv.novedadTexto nt
                                                  WHERE m.fecha > :fecha AND m.activo = :activo AND m.aplicado = :aplicado AND nt.id = :novedad AND e = :empleado
                                                  ORDER BY m.fecha");

          $q->setParameter('activo', true)
            ->setParameter('aplicado', false)
            ->setParameter('fecha', '2020-11-25')
            ->setParameter('empleado', $empleado);
          $q->setParameter('novedad',  49);
          $movimientos = $q->getResult();
        }
        catch (Exception $e){die($e->getMessage());}
      }
      elseif ($_POST['tipo'] == 'diag')
      {

        try{
              $q = $entityManager->createQuery("SELECT m
                                                    FROM MovimientoDebitoFeriado m
                                                    JOIN m.ctacte cc
                                                    JOIN cc.empleado e
                                                    JOIN m.novedad nv
                                                    JOIN nv.novedadTexto nt
                                                    WHERE m.fecha > :fecha AND m.activo = :activo AND (nt.id IN (:novedad)) AND e = :empleado
                                                    ORDER BY m.fecha");

              $q->setParameter('activo', true)
                ->setParameter('fecha', '2020-11-25')
                ->setParameter('empleado', $empleado);
              $q->setParameter('novedad', [50, 15, 16, 54, 49, 25]);
              $movimientos = $q->getResult();
        }
        catch (Exception $e){die($e->getMessage());}
      }
      elseif ($_POST['tipo'] == 'facmp')
      {
        $tabla ='<table align="center" width="100%" class="table table-zebra">
                       <thead>
                              <tr>
                                  <th>#</th>
                                  <th>Cantidad</th>
                                  <th>Descripcion</th>
                              </tr>
                       </thead>
                       <tbody>';
        $printer = false;
        try{
              $q = $entityManager->createQuery("SELECT m
                                                    FROM MovimientoCuentaFeriado m
                                                    JOIN m.ctacte cc
                                                    JOIN cc.empleado e
                                                    WHERE m.fecha > :fecha AND m.activo = :activo AND e = :empleado
                                                    ORDER BY m.fecha");

              $q->setParameter('activo', true)
                ->setParameter('fecha', '2020-11-25')
                ->setParameter('empleado', $empleado);
              $movimientos = $q->getResult();

              $detalle = array();
              foreach ($movimientos as $m)
              {
                  $periodo = $m->getPeriodoMes().'-'.$m->getPeriodoAnio();
                  if (!array_key_exists($periodo, $detalle))
                  {
                    $detalle[$periodo] = array('c' => 0, 'd' => 0, 'fac' => array());
                  }
                  
                  if (is_a($m, 'MovimientoCreditoFeriado'))
                  {
                      if ($m->getNovedadTexto()->getId() == 15)
                      {
                        $detalle[$periodo]['c']+= $m->getCantidad();
                      }
                  }
                  else
                  {
                      $codigo = $m->getNovedad()->getNovedadTexto()->getId();
                      if (in_array($codigo, [15, 16, 25])) //es un franco o franco trabajado o franco compensatorio
                      {
                          if ($m->getCompensable() && !$m->getCompensado()) //es un franco sobre feriado que se adueda
                          {
                              $detalle[$periodo]['fac'][] = $m;
                          }
                          else
                          {
                              $detalle[$periodo]['d']+= $m->getCantidad();
                          }
                      }
                  }
                  $id = $m->getCtacte()->getId();
              }

              $body = "";
              $i=1;
              foreach ($detalle as $k => $d)
              {
                  $state = ($d['c']-$d['d']-count($d['fac']));
                  if ($state > 0)
                  {
                     if (($d['c'] - count($d['fac'])) > $d['d'])
                     {
                          $body.="<tr>
                                    <td>$i</td>
                                    <td>".($d['c'] - count($d['fac']) - $d['d'])."</td>
                                    <td>Francos pendientes periodo $k</td>
                                 </tr>";
                          $i++;
                     }

                     foreach ($d['fac'] as $fsf)
                     {
                        $body.="<tr>
                                    <td>$i</td>
                                    <td>1</td>
                                    <td>Franco Sobre Feriado Fecha: ".$fsf->getFecha()->format('d/m/Y')."</td>
                                </tr>";
                        $i++;
                     }
                  }
                  elseif ($state < 0)
                  {
                          $body.="<tr>
                                    <td>$i</td>
                                    <td>".(-1*$state)."</td>
                                    <td>Francos Diagramados en exeso periodo $k</td>
                                 </tr>";
                          $i++;
                  }
              }
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
                                            WHERE m.fecha BETWEEN :desde AND :hasta AND e = :empleado AND m.activo = :activo AND (nt.id = :novedad OR nt.id = :otra OR nt.id = :otramas)
                                            ORDER BY m.fecha");
          $q->setParameter('activo', true)
            ->setParameter('otra',25)
            ->setParameter('novedad', 15)
            ->setParameter('otramas',50)
            ->setParameter('desde', '2021-11-26')
            ->setParameter('empleado', $empleado);
          $q->setParameter('hasta',  $_POST['hasta']);
          $movimientos = $q->getResult();
      }

      
      $i = 1;
      if ($printer)
      {
          $tabla ='<table align="center" width="100%" class="table table-zebra">
                     <thead>
                            <tr>
                                <th>#</th>
                                <th>Fecha</th>
                                <th></th>
                                <th>Descripcion</th>
                                <th>Orden Diagramada</th>
                                <th>Fecha Diagrama</th>
                            </tr>
                     </thead>
                     <tbody>';
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
                           <td>".$mov->getCantidad()."</td>
                           <td>".($mov->getDescripcion()?$mov->getDescripcion():$orden)."</td>
                           <td>".$mov->getNovedad()->getNovedadTexto()->getTexto()."</td>
                           <td>".$mov->getNovedad()->getDesde()->format("d/m/Y")."</td>
                       </tr>";
              $i++;
          }
      }
      else
      {
        $tabla.=$body;
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

        if ($fec->format('d') > 20)
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
  elseif($accion == 'loadfranco')
  {
      try
      {

          $corte = DateTime::createFromFormat('Y-m-d', "2022-09-01"); // para verificar si debe evualar del 26 al 25 o del 21 al 20 este cambio ocurrio en septiembre 2022

          $fecha = DateTime::createFromFormat('Y-m-d', "$_POST[anio]-$_POST[mes]-01");

          if ($fecha >= $corte)
          {

            $hasta = DateTime::createFromFormat('Y-m-d', "$_POST[anio]-$_POST[mes]-20");
            $desde = clone $hasta;
            $desde->sub(new DateInterval('P1M')); //retrocede un mes
            $desde->add(new DateInterval('P1D')); //agrega un dia
            
          }
          else
          {
            $hasta = DateTime::createFromFormat('Y-m-d', "$_POST[anio]-$_POST[mes]-25");
            $desde = clone $hasta;
            $desde->sub(new DateInterval('P1M')); //retrocede un mes
            $desde->add(new DateInterval('P1D')); //agrega un dia       
          }

         // return print $desde->format('Y-m-d') . " - " . $hasta->format('Y-m-d');

          $francosCreditos = getFrancosDebitosPorPeriodo($desde->format('Y-m-d'), $hasta->format('Y-m-d'), $_POST['empleador']);    



          $data='<table align="center" width="100%" class="table table-zebra">
                  <thead>
                            <tr>
                                <th>Legajo</th>                                
                                <th>Apellido, Nombre</th>
                                <th>Francos Diagramados</th> 
                                <th>Saldo</th>
                            </tr>
                     </thead>
                     <tbody>';

          foreach ($francosCreditos as $m)
          {

              $data.="<tr>
                          <td>$m[leg]</td>
                          <td>$m[ape], $m[nom]</td>
                          <td>$m[cant]</td>
                          <td>".(6-$m['cant'])."</td>
                      </tr>";

               /* if (!array_key_exists($m['idE'], $resumen))
                {
                  $resumen[$m['idE']] = array('leg' => $m['leg'], 'ide' => $m['idE'],'e' => ($m['ape'].', '.$m['nom']), 'fr' => $m['cant'], 'fe' => 0, 'frt' => 0, 'fet' => 0, 'fac' => 0);
                }
                else
                {
                  $resumen[$m['idE']]['fr']+= $m['cant'];
                }*/
              
          }

          $data.="</tbody></table>";
          print $data;

       /*   $francosDebitos = getFrancosDebitos($hasta->format('Y-m-d'), $_POST['empleador']);
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
              
          }*/
      }
      catch (Exception $e){
                          print json_encode(array('ok' => false, 'message' => $e->getMessage()));
                          return;
      }
  }
?>

