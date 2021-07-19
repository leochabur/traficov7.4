<?php
  session_start();
  set_time_limit(0);

  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../controlador/bdadmin.php');
  include_once ('../../modelsORM/manager.php');
  include_once ('../../modelsORM/call.php');
  include_once ('../../modelsORM/controller.php');
  include ('../../modelsORM/src/ClaseAulaVirtual.php');
  include ('../../modelsORM/src/ClaseRealizada.php');
  $accion= $_POST['accion'];

  if($accion == 'resumen')
  {
    $curso = find('Curso', $_POST['cursos']);

    $dql = "SELECT e.legajo as legajo, e.apellido as apellido, e.id as idEmp, e.telefono as telefono, e.nombre as nombre, count(cr) as realizadas, emp.razonSocial as empleador
            FROM Curso c
            JOIN c.empleados e
            JOIN e.empleador emp
            JOIN c.clases cl
            LEFT JOIN ClaseRealizada cr WITH cr.clase = cl AND cr.empleado = e
            WHERE c.id = :curso and cl.eliminada = :eliminada and e.activo = :activo 
            GROUP BY e.id
            ORDER BY emp.razonSocial, e.apellido";

    $result = $entityManager->createQuery($dql)
                            ->setParameter('curso', $_POST['cursos'])
                            ->setParameter('eliminada', false)
                            ->setParameter('activo', true)
                            ->getResult();
     $finalizar = '';
     $scriptFinalizar = '';

     /*var response = $.parseJSON(data);
                                                                          if (response.ok)
                                                                          {
                                                                            btn.parent().html(response.message);
                                                                          }
                                                                          else
                                                                          {
                                                                            alert(response.message);
                                                                          }*/
     if (in_array($_SESSION['userid'], array(9, 10, 17)))
     {
        $finalizar = "<th>Marcar curso como finalizado</th>";
        $scriptFinalizar = "$('.submit').button().click(function(event){
                                                                        event.preventDefault();
                                                                        var btn = $(this);
                                                                        var form = btn.closest('form');
                                                                        if (confirm('La curso correspondiente a '+btn.data('emp')+' se marcara como realizado, continuar?'))
                                                                        {
                                                                            $.post('/modelo/segvial/readadav.php',
                                                                                   form.serialize(),
                                                                                    function(data){
                                                                                                  var response = $.parseJSON(data);
                                                                                                  if (response.ok)
                                                                                                  {
                                                                                                    btn.parent().empty();
                                                                                                  }
                                                                                                  else
                                                                                                  {
                                                                                                    alert(response.message);
                                                                                                  }

                                                                                      });
                                                                        }
                                    });
                                     $('.fecha').datepicker({dateFormat:'dd/mm/yy'});";
     }

    $script = "<script>
                    $('.make').button().click(function(){
                                                          var btn = $(this);
                                                          if (confirm('La evaluacion del curso correpondiente a '+btn.data('emp')+' se marcara como realizada'))
                                                          {
                                                              $.post('/modelo/segvial/readadav.php',
                                                                    { accion : 'makeEval',
                                                                      curso : btn.data('cso'),
                                                                      emp : btn.data('id')
                                                                      },
                                                                      function(data){
                                                                          var response = $.parseJSON(data);
                                                                          if (response.ok)
                                                                          {
                                                                            btn.parent().html(response.message);
                                                                          }
                                                                          else
                                                                          {
                                                                            alert(response.message);
                                                                          }

                                                                        });
                                                          }
                      });
                       $scriptFinalizar
              </script>";
     

     $tabla='<table id="example" name="example" class="table table-zebra" width="100%" align="center">
                    <thead>
                    <tr>
                        <th>Legajo</th>
                        <th>Apellido, Nombre</th>
                        <th>Telefono</th>
                        <th>Empleador</th>
                        <th>Clases realizadas</th>
                        <th>Avance</th>
                        <th>Registrar Evaluacion</th>
                        '.$finalizar.'
                    </tr>
                    </thead>
                    <tbody>';
     $marcadores = "";
     $i=0;
     $info = $curso->getDetalleClases();
     $cantClases = $info['cantClases']+$info['cantEval'];
     foreach ($result as $data)
     {
          $cursoCompleto = false;
          $emple = $data['apellido'].", ".$data['nombre'];
          $button = '';
          if ($data['realizadas'] == $cantClases)
          {
            $avance = 'Curso Completo';
            $cursoCompleto = true;
          }
          elseif ((($cantClases - $data['realizadas']) == 1) && ($info['cantEval']))
          {
            $avance = 'Debe Evaluacion';
            $button = "<input type='button' class='make' value='Realizo Evaluacion' data-id='$data[idEmp]' data-emp='$emple' data-cso='".$curso->getId()."'/>";
          }
          else
          {
            $avance = $data['realizadas'].'/'.$cantClases;
          }

          $findAll = '';
          if (in_array($_SESSION['userid'], array(9, 10, 17)))
          {
            if (!$cursoCompleto)
            {
              $findAll = '<td><form>
                                    <input class="fecha" name="fecha"/>
                                    <input type="submit" class="submit" name="Finalizar Curso" data-emp="'.$emple.'"/>
                                    <input type="hidden" name="accion" value="makefin"/>
                                    <input type="hidden" name="curso" value="'.$curso->getId().'"/>
                                    <input type="hidden" name="empleado" value="'.$data['idEmp'].'"/>
                              </form>
                          </td>';
            }
            else{
              $findAll = "<td></td>";
            }
          }

          $tabla.="<tr>
                      <td align='right'>$data[legajo]</td>
                      <td align='left'>".$emple."</td>
                      <td align='left'>$data[telefono]</td>
                      <td align='left'>$data[empleador]</td>
                      <td align='center'>$data[realizadas]</td>
                      <td align='center'>$avance</td>
                      <td align='center'>$button</td>
                      $findAll
                  </tr>";
     }
     $tabla.='</tbody>
              </table>'.$script;
    print $tabla;
  }
  elseif ($accion == 'makefin')
  {
    if (!$_POST['fecha'])
    {
        print json_encode(array('ok' => false, 'message' => 'No ha seleccionado una fecha!'));
        exit();
    }
    try
    {
        $emple = find('Empleado', $_POST['empleado']);
        $curso = find('Curso', $_POST['curso']);

        $dql = "SELECT cr
                FROM ClaseRealizada cr
                JOIN cr.clase cl
                WHERE cl.curso = :curso and cl.eliminada = :eliminada and cr.empleado = :empleado";

        $result = $entityManager->createQuery($dql)
                                ->setParameter('curso', $curso)
                                ->setParameter('eliminada', false)
                                ->setParameter('empleado', $emple)
                                ->getResult();
        $realizadas = array();
        foreach ($result as $cr)
        {
          $realizadas[] = $cr->getClase();
        }

        $fecha = DateTime::createFromFormat('d/m/Y', $_POST['fecha']);
        foreach ($curso->getClases() as $cl)
        {
            if (!in_array($cl, $realizadas))
            {
                $claseRealizada = new ClaseRealizada();
                $claseRealizada->setEmpleado($emple);
                $claseRealizada->setClase($cl);
                $claseRealizada->setFecha($fecha);
                $entityManager->persist($claseRealizada);
            }
        }
        $entityManager->flush();
        print json_encode(array('ok' => true));
      }
      catch (Exception $e){ print json_encode(array('ok' => false, 'message' => $e->getMessage())); }
  }
  elseif ($accion == 'makeEval')
  {
    $curso = find('Curso', $_POST['curso']);
    $dql = "SELECT cl
            FROM ClaseAulaVirtual cl
            WHERE cl.curso = :curso AND cl.esEvaluacion = :evaluacion AND cl.eliminada = :eliminada";

    $clase =  $entityManager->createQuery($dql)
                            ->setParameter('curso', $curso)
                            ->setParameter('evaluacion', true)
                            ->setParameter('eliminada', false)
                            ->getOneOrNullResult();
    if ($clase) //existe la evaluacion, debe verificar que no se haya realizado la misma
    {
       $emple = find('Empleado', $_POST['emp']);
       $dql = "SELECT cr
               FROM ClaseRealizada cr
               WHERE cr.clase = :clase AND cr.empleado = :emple";
       $evaluacion =  $entityManager->createQuery($dql)
                                        ->setParameter('clase', $clase)
                                        ->setParameter('emple', $emple)
                                        ->getOneOrNullResult();
       if (!$evaluacion)///
       {
          try
          {
            $evaluacion = new ClaseRealizada();
            $evaluacion->setEmpleado($emple);
            $evaluacion->setClase($clase);
            $evaluacion->setFecha(new DateTime());
            $entityManager->persist($evaluacion);
            $entityManager->flush();
            print json_encode(array('ok' => true, 'message' => 'Accion exitosa!'));
          }
          catch (Exception $e) {print json_encode(array('ok' => false, 'message' => $e->getMessage()));}
       }
       else
       {
        print json_encode(array('ok' => false, 'message' => 'La evaluacion ya se ha realizado!!'));
       }
    }
    else
    {
      print json_encode(array('ok' => false, 'message' => 'No se ha encontrado la evaluacion en el curso!'));
    }
  }
  elseif ($accion == 'access')
  {
     
      $cate = call('Categoria', 'findAll');
      $curso = find('Curso', $_POST['cursos']);
      $categorias = "<select id='cates' name='cates'>
                        <option value='0'>Todos</option>";
      foreach ($cate as $c)
      {
        $categorias.="<option value='".$c->getId()."'>".$c->getDescripcion()."</option>";
      }
      $categorias.='</select>';

      
      $propietario = "<select id='prop' name='prop'>
                      <option value='0'>Todos el Personal</option>
                      <option value='1'>Personal Propio</option>
                      <option value='E'>Personal Externo</option>";
      $propietario.= getPropietarios($_SESSION['structure']);
      $propietario.='</select>';

      $tabla = '<fieldset class="ui-widget ui-widget-content ui-corner-all">
                     <legend class="ui-widget ui-widget-header ui-corner-all">Configurar accesos a: '.strtoupper($curso->getNombre()).'</legend>';
      $tabla.= "<form id='finder'>
                  <table border='0'>
                  <tr>
                      <td>Empleador</td>
                      <td>$propietario</td>
                      <td align='right'>
                          <input type='button' value='Cargar Personal' id='apply'/>
                      </td>
                  </tr>
                  </table>
                  <input type='hidden' name='accion' value='finder'/>
                  <input type='hidden' name='cursos' value='".$curso->getId()."'/>
                </form>
                <hr>
                  <div id='filter'>
                  </div>
                </fieldset>
                  <script>
                        $('#prop').selectmenu({width: 350});
                        $('#cates').selectmenu({width: 250});
                        $('#apply').button().click(function(){

                                    $('#filter').html(\"<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>\");
                                    $.post('/modelo/segvial/readadav.php', 
                                           $('#finder').serialize(), 
                                           function(data){  
                                                            $('#filter').html(data);
                                                         });

                          });
                  </script>";
      print $tabla;
  }
  elseif ($accion == 'finder')
  {
    $curso = find('Curso', $_POST['cursos']);
    $dql = "SELECT e 
            FROM Empleado e 
            LEFT JOIN e.categoria c
            JOIN e.empleador p ";
    $where = " WHERE e.activo = :activo ";
    $cate = null;

    $prop = null;
    if ($_POST['prop'])
    {
      if ($_POST['prop'] == 'E')
      {
        $prop = 1;
        $where.= "AND p.id != :prop";
      }
      else
      {
        $prop = $_POST['prop'];
        $where.= "AND p.id = :prop";
      }
    }

    $dql.=$where ." ORDER BY p.razonSocial, c.descripcion, e.apellido ";

    $query = $entityManager->createQuery($dql);
    if ($cate){
      $query->setParameter('cate', $_POST['cates']);
    }     
    if ($prop){
      $query->setParameter('prop', $prop);
    }
    
    $data = $query->setParameter('activo', true)
                  ->getResult();

    $body = "";
    foreach ($data as $e)
    {
      if ($curso->getEmpleados()->contains($e))
      {
        $check = "<input type='checkbox' data-id='".$e->getId()."' class='sino' checked/>";
      }
      else{
        $check = "<input type='checkbox' data-id='".$e->getId()."' class='sino'/>";
      }
      $body.="<tr>
                <td>".$e->getLegajo()."</td>
                <td>$e</td>
                <td>".$e->getCategoria()->getDescripcion()."</td>
                <td>".$e->getEmpleador()->getRazonSocial()."</td>
                <td>$check</td>
              </tr>";
    }
    $tabla = '<br><input type="button" value="Aplicar Accesos" class="apply"/>
              <br>
                <table class="table table-zebra">
                <tr>
                    <td colspan="4" align="right">Seleccionar todos</td>
                    <td><input type="checkbox" class="selall"/></td>
                </tr>
                <thead>
                    <tr>
                        <th>Legajo</th>
                        <th>Apellido, Nombre</th>
                        <th>Categoria</th>
                        <th>Empleador</th>
                        <th>Si/No</th>
                    </tr>
                </thead>
                <tbody>
                '.$body.'
                </tbody>
              </table>
              <script>
                        $(".selall").change(function(){
                                                      $(".sino").attr("checked", $(this).is(":checked"));
                          });
                        $(".apply").button().click(function(){
                                                              if (confirm("Seguro aplicar el alcance del curso '.strtoupper($curso->getNombre()).' para el personal seleccionado?"))
                                                              {
                                                                  var emples = new Array();
                                                                  $("input[type=checkbox]:checked").each(function(){
                                                                                                                        emples.push($(this).data("id"));
                                                                                                                    });
                                                                  $.post("/modelo/segvial/readadav.php", 
                                                                         {accion : "appacc", empleador : '.$_POST['prop'].',curso: '.$curso->getId().', emp : emples.join(",")}, 
                                                                         function(data){  
                                                                                        alert(data);
                                                                                       });
                                                              }
                                                              
                        });
              </script>';
    print $tabla;

  }
  elseif ($accion == 'appacc')
  {
    $curso = find('Curso', $_POST['curso']);
    $dql = "SELECT e 
            FROM Empleado e 
            LEFT JOIN e.categoria c
            JOIN e.empleador p ";
    $where = '';
    $emp = null;
    if ($_POST['empleador'])
    {
      $emp = $_POST['empleador'];
      $where = 'WHERE p.id = :idEmp';
    }

    $dql.=$where;
    $query = $entityManager->createQuery($dql);
    if ($emp)
    {
      $query->setParameter('idEmp', $emp);
    }
    $data = $query->getResult();
    $empleados = explode(",", $_POST['emp']);
    foreach ($data as $e)
    {
      $curso->removeEmpleado($e);
      if (in_array($e->getId(), $empleados))
      {
        $curso->addEmpleado($e);
      }
    }
    $entityManager->flush();
    print 'ok';
  }
  elseif ($accion == 'config')
  {
    try{

         $curso = find('Curso', $_POST['cursos']);

    $clases = '<table class="table table-zebra">
                  <thead>
                  <tr>
                    <th>Titulo</th>
                    <th>Tipo</th>
                    <th>Oden</th>
                    <th>Clase Anterior</th>
                    <th>Estado</th>
                    <th></th>
                  </tr>
                  </thead>
                  <tbody>
                  ';
    foreach ($curso->getClases() as $cl)
    {
            
        $tipo = 'Clase';
        if ($cl->getEsEvaluacion())
        {
          $tipo = 'Evaluacion';
        }
        $clases.="<tr>
                    <td>".$cl->getTitulo()."</td>
                    <td>$tipo</td>
                    <td>".$cl->getOrden()."</td>
                    <td>".($cl->getAnterior()?$cl->getAnterior()->getTitulo():'')."</td>
                    <td>".($cl->getEliminada()?'Eliminada':'Activa')."</td>
                    <td><input type='button' value='Editar' class='edit' data-id='".$cl->getId()."'/></td>
                  </tr>";
    }

    $clases.="</tbody>
              </table>
              <br>
              <input type='button' class='newcl' value='Agregar Clase'/>
              <br>
              <div id='addcl'>
              </div>";

    $tabla = '<fieldset class="ui-widget ui-widget-content ui-corner-all">
                   <legend class="ui-widget ui-widget-header ui-corner-all">Administrar clases: '.strtoupper($curso->getNombre()).'</legend>
                   '.$clases.'</fieldset>';
    $script = "<script>
                  $('.edit').button().click(function(){
                                                      var btn = $(this);
                                                      $.post('/modelo/segvial/readadav.php', 
                                                            {accion : 'feditcl' ,cls: btn.data('id')}, 
                                                            function(data){  
                                                                              $('#addcl').html(data);
                                                                          });
                  });

                  $('.newcl').button().click(function(data){
                                                            $.post('/modelo/segvial/readadav.php', 
                                                            {accion : 'addclase' ,curso: ".$curso->getId()."}, 
                                                            function(data){  
                                                                              $('#addcl').html(data);
                                                                          });
                  })
                </script>";
    print $tabla.$script;
  }
  catch (Exception $e){ print $e->getMessage(); exit();}
  }
  elseif ($accion == 'addclase')
  {
      $curso = find('Curso', $_POST['curso']);
      $clases = "<select name='pred' class='pred'>
                 <option value='0'></option>";
      foreach ($curso->getClases() as $cl)
      {
        $clases.="<option value='".$cl->getId()."'>".$cl->getTitulo()."</option>";
      }
      $clases.="</select>";
      $tabla = '<br>
                <fieldset class="ui-widget ui-widget-content ui-corner-all">
                     <legend class="ui-widget ui-widget-header ui-corner-all">Agregar clase a: '.strtoupper($curso->getNombre()).'</legend>';
      $tabla.= "<form enctype='multipart/form-data' id='saveclass'>
                  <table border='0'>
                  <tr>
                      <td>Titulo Clase</td>
                      <td><input type='text' name='title'class=''/></td>
                      <td>Tipo</td>
                      <td>
                          <select name='tipo' class='type'>
                            <option value='0'>Clase</option>
                            <option value='1'>Evaluacion</option>
                          </select>
                      </td>
                  </tr>
                  <tr>
                      <td>Clase anterior</td>
                      <td>$clases</td>
                      <td></td>
                      <td></td>
                  </tr>    
                  <tr>
                      <td>URL Formulario</td>
                      <td><input type='text' name='resource' class=''/><td>
                      <td></td>
                      <td></td>
                  </tr>
                  <tr>
                      <td>Archivo</td>
                      <td colspan='3'>
                        <input type='hidden' name='MAX_FILE_SIZE' value='41943040'/>
                        <input name='recurso' type='file' />
                      </td>
                  </tr>
                  <tr>
                    <td colspan='4' align='right'>
                      <input type='submit' value='Guardar Clase' class='save submitBtn'/>
                    </td>
                  </tr>
                  </table>
                  <input type='hidden' name='accion' value='saveclass'/>
                  <input type='hidden' name='curso' value='".$curso->getId()."'/>
                  </form>
                  </fieldset>";
      $script = '<script>
                        $(".type").selectmenu({width: 150});
                        $(".pred").selectmenu({width: 250});
                        $(".save").button();
    $("#saveclass").on("submit", function(e){
        e.preventDefault();
        $(".save").hide();
        $.ajax({
            type: "POST",
            url: "/modelo/segvial/readadav.php",
            data: new FormData(this),
            dataType: "json",
            contentType: false,
            cache: false,
            processData:false,
            success: function(response){ 
                alert(response.message);
                $(".save").show();

            }
        });
    });

                </script>';
      print $tabla.$script;
  }
  elseif ($accion == 'saveclass')
  {   
      
      $response = array( 
          'status' => 0, 
          'message' => 'Form submission failed, please try again.' 
      );

      if (!$_POST['title'])
      {
          $response['message'] = 'El nombre de la clase no puede permanecer en blanco';
      }
      else
      {   
          if ($_POST['tipo'] == 1) //es una evaluacion debe verificar el el campo URL
          {
              if (!$_POST['resource'])
              {
                $response['message'] = 'El campo URL no puede permanecer en blanco';
              }
              else
              {
                try{
                      $curso = find('Curso', $_POST['curso']);
                      $clase = new ClaseAulaVirtual();
                      $clase->setCodigo('cl'.($curso->getClases()->count()+1));
                      $clase->setCurso($curso);
                      $clase->setTitulo($_POST['title']);
                      $clase->setEsEvaluacion(true);
                      $clase->setRecurso($_POST['resource']);
                      if ($_POST['pred'])
                      {
                          $anterior = find('ClaseAulaVirtual', $_POST['pred']);
                          $clase->setAnterior($anterior);
                      }
                      $entityManager->persist($clase);
                      $entityManager->flush();
                      $response['status'] = 1;
                    }
                    catch (Exception $e){
                                          $response['message'] = $e->getMessage();
                    }
              }
          }
          elseif(!empty($_FILES["recurso"]["name"]))
          {                
                    $curso = find('Curso', $_POST['curso']);
                    $clase = new ClaseAulaVirtual();
                    $clase->setCodigo('cl'.($curso->getClases()->count()+1));
                    $clase->setCurso($curso);
                    $clase->setTitulo($_POST['title']);
                    $clase->setEsEvaluacion(false);
                    if ($_POST['pred'])
                    {
                        $anterior = find('ClaseAulaVirtual', $_POST['pred']);
                        $clase->setAnterior($anterior);
                    }
                    $entityManager->persist($clase);
                    $entityManager->flush();

                    $nombreArchivo = 'cso_'.$curso->getId().'_cl_'.$clase->getId().'.mp4';
                    $uploadDir = realpath(__DIR__ . '/../../diagrama'); 

                    $fileName = basename($_FILES["recurso"]["name"]); 
                    $targetFilePath = $fileName; 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); 
                    $allowTypes = array('mp4'); 
                    if(in_array($fileType, $allowTypes))
                    {                    
                        if(move_uploaded_file($_FILES["recurso"]["tmp_name"], ($uploadDir."/".$nombreArchivo)))
                        { 
                            $clase->setRecurso($nombreArchivo);
                            $uploadedFile = $fileName; 
                            $response['message'] = 'Carga realizada exitosamente';
                            $entityManager->flush();
                        }
                        else
                        { 
                            $uploadStatus = 0; 
                            $response['message'] = 'Sorry, there was an error uploading your file.'; 
                        } 
                    }
                    else{ 
                        $uploadStatus = 0; 
                        $response['message'] = 'Sorry, only PDF, DOC, JPG, JPEG, & PNG files are allowed to upload.'; 
                    } 
          }
          else
          {
              $response['message'] = 'Debe seleccionar un archivo'; 
          } 
      }
      print json_encode($response);
  }
  elseif($accion == 'feditcl')
  {
      $clase = find('ClaseAulaVirtual', $_POST['cls']);

      $clases = "<select name='pred' class='pred'>";
      $clases.=($clase->getAnterior()?"<option value='".$clase->getAnterior()->getId()."'>".$clase->getAnterior()->getTitulo()."</option>":'');
      $clases.="<option value='0'></option>";
      foreach ($clase->getCurso()->getClases() as $cl)
      {
        if (!$cl->getEliminada())
          $clases.="<option value='".$cl->getId()."'>".$cl->getTitulo()."</option>";
      }
      $clases.="</select>";
      $tabla = '<br>
                <fieldset class="ui-widget ui-widget-content ui-corner-all">
                     <legend class="ui-widget ui-widget-header ui-corner-all">Editar clase: '.strtoupper($clase->getTitulo()."").'</legend>';
      $tabla.= "<form enctype='multipart/form-data' id='saveclass'>
                  <table border='0'>
                  <tr>
                      <td>Titulo Clase</td>
                      <td><input type='text' name='title' class='' value='".$clase->getTitulo()."'/></td>
                      <td>Tipo</td>
                      <td>
                          <select name='tipo' class='type'>".
                          ($clase->getEsEvaluacion()?"<option value='1'>Evaluacion</option>":"<option value='0'>Clase</option>")
                          ."<option value='0'>Clase</option>
                            <option value='1'>Evaluacion</option>
                          </select>
                      </td>
                  </tr>
                  <tr>
                      <td>Orden</td>
                      <td><input type='text' name='orden' class='' value='".$clase->getOrden()."'/></td>
                  </tr>
                  <tr>
                      <td>Clase anterior</td>
                      <td>$clases</td>
                      <td></td>
                      <td></td>
                  </tr>    
                  <tr>
                      <td>URL Formulario</td>
                      <td><input type='text' name='resource' value='".($clase->getEsEvaluacion()?$clase->getRecurso():'')."'/><td>
                      <td></td>
                      <td></td>
                  </tr>
                  <tr>
                      <td>Archivo</td>
                      <td colspan='3'>
                        <input type='hidden' name='MAX_FILE_SIZE' value='41943040'/>
                        <input name='recurso' type='file' />
                      </td>
                  </tr>
                  <tr>
                    <td>
                      Eliminada
                    </td>
                    <td>
                        <input type='checkbox' name='eliminada' ".($clase->getEliminada()?'checked':'')." />
                    </td>
                  </tr>
                  <tr>
                    <td colspan='4' align='right'>
                      <input type='submit' value='Modificar Clase' class='save submitBtn'/>
                    </td>
                  </tr>
                  </table>
                  <input type='hidden' name='accion' value='editClass'/>
                  <input type='hidden' name='clase' value='".$clase->getId()."'/>
                  </form>
                  </fieldset>";
      $script = '<script>
                        $(".type").selectmenu({width: 150});
                        $(".pred").selectmenu({width: 250});
                        $(".save").button();
    $("#saveclass").on("submit", function(e){
        e.preventDefault();
        $(".save").hide();
        $.ajax({
            type: "POST",
            url: "/modelo/segvial/readadav.php",
            data: new FormData(this),
            dataType: "json",
            contentType: false,
            cache: false,
            processData:false,
            success: function(response){ 
                if (response.status)
                {
                    $("#cargar").trigger("click");
                }
                else
                {
                  alert(response.message);
                  $(".save").show();
                }

            }
        });
    });

                </script>';
      print $tabla.$script;
  }
  elseif ($accion == 'editClass')
  {   
      $clase = find('ClaseAulaVirtual', $_POST['clase']);
      $response = array( 
          'status' => 0, 
          'message' => 'Form submission failed, please try again.' 
      );

      if (!$_POST['title'])
      {
          $response['message'] = 'El nombre de la clase no puede permanecer en blanco';
      }
      else
      {   
          if ($_POST['tipo'] == 1) //es una evaluacion debe verificar el el campo URL
          {
              if (!$_POST['resource'])
              {
                $response['message'] = 'El campo URL no puede permanecer en blanco';
              }
              else
              {
                    try
                    {
                      $clase->setTitulo($_POST['title']);
                      $clase->setEsEvaluacion(true);
                      $clase->setRecurso($_POST['resource']);
                      $clase->setOrden($_POST['orden']);
                      if ($_POST['pred'])
                      {
                          $anterior = find('ClaseAulaVirtual', $_POST['pred']);
                          $clase->setAnterior($anterior);
                      }
                      $clase->setEliminada(isset($_POST['eliminada']));
                      $entityManager->flush();
                      $response['status'] = 1;
                    }
                    catch (Exception $e)
                    {
                                          $response['message'] = $e->getMessage();
                    }
              }
          }
          elseif(!empty($_FILES["recurso"]["name"]))
          {         $curso = $clase->getCurso();
                    $clase->setTitulo($_POST['title']);
                    $clase->setEsEvaluacion(false);
                    $clase->setAnterior(null);
                    $clase->setOrden($_POST['orden']);
                    if ($_POST['pred'])
                    {
                        $anterior = find('ClaseAulaVirtual', $_POST['pred']);
                        $clase->setAnterior($anterior);
                    }
                    $clase->setEliminada(isset($_POST['eliminada']));

                    $nombreArchivo = 'cso_'.$curso->getId().'_cl_'.$clase->getId().'.mp4';
                    $uploadDir = realpath(__DIR__ . '/../../diagrama'); 

                    $fileName = basename($_FILES["recurso"]["name"]); 
                    $targetFilePath = $fileName; 
                    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION); 
                    $allowTypes = array('mp4'); 
                    if(in_array($fileType, $allowTypes))
                    {                    
                        if(move_uploaded_file($_FILES["recurso"]["tmp_name"], ($uploadDir."/".$nombreArchivo)))
                        { 
                            $clase->setRecurso($nombreArchivo); 
                            $response['message'] = 'Carga realizada exitosamente';
                            $entityManager->flush();
                            $response['status'] = 1;
                        }
                        else
                        { 
                            $response['message'] = 'Sorry, there was an error uploading your file.'; 
                        } 
                    }
                    else{ 
                        $response['message'] = 'El unico formato admitido es MP4'; 
                    } 
          }
          else
          {
              if ($clase->getRecurso())
              {
                    $clase->setTitulo($_POST['title']);
                    $clase->setEsEvaluacion(false);
                    $clase->setAnterior(null);
                    $clase->setOrden($_POST['orden']);
                    if ($_POST['pred'])
                    {
                        $anterior = find('ClaseAulaVirtual', $_POST['pred']);
                        $clase->setAnterior($anterior);
                    }
                    $clase->setEliminada(isset($_POST['eliminada']));
                    $entityManager->flush();
                    $response['status'] = 1;
              }
              else
              {
                $response['message'] = 'Debe seleccionar un archivo'; 
              }
              
          } 
      }
      print json_encode($response);
  }
?>

