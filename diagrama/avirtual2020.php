<?php
     session_start();

    error_reporting(E_ALL);
ini_set('xcache.admin.enable_auth', false);
   //  include('../paneles/viewpanel.php');
    include('main.php');
    include('../modelsORM/manager.php');
    include_once('../modelsORM/call.php');
    include_once('../modelsORM/controller.php');
    define("RAIZ", '');
    encabezado("");//$_SESSION["chofer"]);//  menu();
    menu();

  //   encabezado('Menu Principal - Sistema de Administracion - Campana');
    $conn = conexcion();
?>






  <link rel="stylesheet" href="./MDB/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./MDB/css/mdb.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./MDB/css/style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">

<script src="./MDB/js/popper.min.js"></script>
<script src="./MDB/js/bootstrap.min.js"></script>
<script src="./MDB/js/mdb.min.js"></script>

<style type="text/css">
body { font-size: 72.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
.text { font-size: 100%; }

</style>
<script type="text/javascript">

$(document).ready(function(){
                                $('.next').click(function(event){
                                                                  event.preventDefault();
                                                                  var a = $(this);
                                                                  if (confirm('El video se informara como VISTO, permitiendole avanzar en el curso. Continuar?')){
                                                                      $.post('updav.php', 
                                                                             {accion: 'setView', cls: a.data('cl')},
                                                                             function(data){
                                                                                          
                                                                                            var response = $.parseJSON(data);
                                                                                            if (response.status){
                                                                                                  location.reload();
                                                                                            }
                                                                                            else{
                                                                                              alert(response.message);
                                                                                            }
                                                                             });
                                                                  }
                                                              
                                });

});

</script>
<BODY bgcolos="red"><br>

<fieldset class="ui-widget ui-widget-content ui-corner-all container ml-0">
                     <legend class="ui-widget ui-widget-header ui-corner-all">
                          <?php 
                              if (isset($_GET['v']))
                                print "Descripcion del curso...";
                              elseif (isset($_GET['c']))
                                print "Descripcion de la clase...";
                              elseif (isset($_GET['ne']))
                                print "Evaluacion";
                              else
                                print "Aula virtual - Cursos disponibles";
                          ?>

                      </legend>
    <?php 
        $url = 'avirtual.php';

        if (isset($_GET['v']))
        {
          if (isset($_GET['c']))
          {
              getDetalleClase($_GET['c'], $entityManager);
          }
          else
          {
            getClasesCurso($_GET['v'], $conn, $entityManager);
          }
        }
        elseif (isset($_GET['ne']))
        {
            getDetalleEvaluacion($_GET['ne'], $entityManager);
        }
        else
        {
             $emple = find('Empleado', $_SESSION['id_chofer']);

              $realizadas = $entityManager->createQuery("SELECT cu.id as id, count(c) as cant
                                                         FROM ClaseRealizada c
                                                         JOIN c.clase cl
                                                         JOIN cl.curso cu
                                                         WHERE c.empleado = :empleado AND cl.eliminada = :eliminada
                                                         GROUP BY cu
                                                        ")
                                          ->setParameter('empleado', $emple)        
                                          ->setParameter('eliminada', false)                     
                                          ->getResult();
             $avance = array();

             foreach ($realizadas as $r)
             {
              $avance[$r['id']] = $r['cant'];
             }

             $q = $entityManager->createQuery("SELECT c, count(cl) as cant 
                                              FROM Curso c 
                                              JOIN c.clases cl
                                              WHERE c.activo = :activo AND cl.eliminada = :eliminada
                                              GROUP BY c
                                              ORDER BY cl.orden")
                                ->setParameter('activo',  true)        
                                ->setParameter('eliminada', false)                       
                                ->getResult();
              $curso = '<div class="container">';
              $creoHeader = false;
              $i = 0;

              foreach ($q as $c)
              {
                  if ((!$c[0]->getEmpleados()->count())||($c[0]->getEmpleados()->contains($emple)))
                  {
                      if (!$creoHeader)
                      {
                        $curso.='<div class="card-group">';
                        $creoHeader = true;
                        $printFooter = false;
                      }
                      $real = ($avance[$c[0]->getId()]?$avance[$c[0]->getId()]:0);

                      $curso.='<div class="card col-sm-6 col-lg-4 mx-3 mt-4">
                              <div class="card-body">
                                <h4 class="card-title">'.$c[0]->getNombre().'</h4>
                                <hr>
                                <p class="card-text">'.$c[0]->getDescripcion().'</p>
                                </div>
                                <div class="card-footer text-left mt-4 text-muted">
                                    Avance del curso: '.$real.'/'.$c['cant'].'
                                </div>
                                <div class="card-footer text-muted text-center mt-4">
                                  <a href="./'.$url.'?v='.$c[0]->getId().'"><i class="fas fa-angle-double-right fa-3x"></i></a>
                                </div>
                              
                            </div>';
                      $i++;
                      if (($i % 3) == 0)
                      {
                        $curso.='</div>';
                        $i = 0;
                        $creoHeader = false;
                        $printFooter = true;
                      }
                  }
              }
              if (!$printFooter)
              {
                $curso.='</div>';
              }
              print $curso;
        } ?>
</fieldset>
</BODY>

</HTML>

<?php

function getDetalleClase($clase, $entityManager)
{
    $url = 'avirtual.php';
    $emple = find('Empleado', $_SESSION['id_chofer']);

    $clase = find('ClaseAulaVirtual', $clase);

    $realizada = $entityManager->createQuery("SELECT c
                                              FROM ClaseRealizada c
                                              JOIN c.clase cl
                                              WHERE cl = :clase AND c.empleado = :empleado AND cl.eliminada = :eliminada")
                                ->setParameter('clase',  $clase)
                                ->setParameter('empleado', $emple)    
                                ->setParameter('eliminada', false)                         
                                ->getOneOrNullResult();
    if ($realizada)
    {
        $view = "<i class='fas fa-clock fa-2x'></i><span class='ml-3'>Visto el: ".$realizada->getFecha()->format('d/m/Y H:i')."</span>";
    }
    else
    {
       $view = '<a href="#" target="_blank" class="next" data-cl="'.$clase->getId().'" data-co="'.$clase->getCurso()->getId().'">
                                      <i class="fas fa-forward fa-2x"></i> <span class="ml-3">Siquiente capitulo</span>
                </a>';
    }
    $list = '';
    foreach ($clase->getLineas() as $ln)
    {
      $list.= '<li class="text-muted">'.$ln->getDescripcion().'</li>';
    }
    $print = '
              <div class="card mt-4 ml-3">
                <div class="card-body">
                  <h4 class="card-title">'.$clase->getTitulo().'</h4>
                  <hr>
                  <div class="row">
                    <div class="col-12 col-sm-6 col-lg-4 border-right">
                        <ul class="list-group list-group-flush">
                          <ul class="list-group">
                            <li class="list-group-item">
                              <i class="fas fa-clipboard-list fa-2x mt-2"></i><span class="ml-3">Temario:</span>
                              <ul class="list-group ml-5">
                              '.$list.'
                              </ul>
                            </li>
                            <li class="list-group-item">
                              <a href="https://docs.google.com/forms/d/e/1FAIpQLSeAgju3F7VZfhdpY0x2QnI0h81Ss-eeDpjyk2emyqBFPMQ9HQ/viewform?usp=sf_link" target="_blank"><i class="fas fa-question fa-2x mt-2"></i> <span class="ml-3">Consultas</span></a>
                            </li>
                            <li class="list-group-item">
                              <a href="https://docs.google.com/forms/d/e/1FAIpQLSe6mvF6GEdhMjh8TGJJ8RTeEmG5tOQOOnpvz_2VYMWMidVIPA/viewform?usp=sf_link" target="_blank"><i class="fas fa-tools fa-2x mt-2"></i><span class="ml-3">Problemas con el video</span></a>
                            </li>
                            <li class="list-group-item">'.$view.'
                            </li>
                          </ul>
                        </ul>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-8">
                      <div class="embed-responsive embed-responsive-4by3">
                        <iframe class="embed-responsive-item" src="'.$clase->getRecurso().'" allowfullscreen></iframe>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card-footer text-muted text-center mb-0">
                    <a href="./'.$url.'?v='.$clase->getCurso()->getId().'"><i class="fas fa-angle-double-left fa-3x"></i></a>
                </div>
              </div>';
      print $print;
}


function getDetalleEvaluacion($clase, $entityManager)
{
    $url = 'avirtual.php';

    $clase = find('ClaseAulaVirtual', $clase);
    $emple = find('Empleado', $_SESSION['id_chofer']);
    $claseRealizada = getClaseRealizada($clase, $emple);

    if ($claseRealizada)
    {
        // '.$claseRealizada->getPuntaje().' pts.
        $print = '
                  <div class="card h6 ml-4">
                      <p class="text-muted ml-2 mt-2">Evaluacion realizada el: '.$claseRealizada->getFecha()->format('d/m/Y - H:i').'</p>
                      <p class="text-muted ml-2 mt-2">Calificacion: '.$claseRealizada->getPuntaje().' pts.</p>
                  </div>
                  <p class="text-center h6">
                        MIS RESPUESTAS
                  </p>
                  <ul>';

        foreach ($claseRealizada->getRespuestas() as $r)
        {
          $class = "text-danger";
          if ($r->getRespuesta()->getCorrecta())
          {
            $class = "text-success";
          }
          $print.="<br><li>
                        ".strtoupper($r->getRespuesta()->getPregunta())."
                          </li>
                          <ul class='list-group list-group-flush'>
                            <li class='list-group-item list-group-item-action'>
                              <div class='row $class'>
                                  <span class='col-12'>$r</span>
                              </div>
                          </li>
                          </ul>";
        }
        $print.="<hr>";
    }
    else
    {
    $print = '<form id="encresp">
              <ul>';

    foreach ($clase->getPreguntas() as $q)
    {
      $print.="<br><li>
                    ".strtoupper($q)."
                      </li>
                      <ul class='list-group list-group-flush'>";
      foreach ($q->getRespuestas() as $rta)
      {
        $print.="<li class='list-group-item list-group-item-action'>
                    <div class='row'>
                    <span class='col-6'>$rta</span><span class='col-2'><input type='radio' value='r-".$rta->getId()."' name='q-".$q->getId()."'></span>
                      </div>

                  </li>";
      }
      $print.="</ul>";
    }
    $print.="</ul>
    <hr>
    <input type='hidden' name='cls' value='".$clase->getId()."'/>
    <input type='button' value='Enviar Respuestas' id='btnsend'/>
    <input type='hidden' name='accion' value='procev'/>
    </form>

    <script>
              $('#btnsend').button().click(function(){
                        if (confirm('Seguro enviar las respuestas?'))
                        {
                             $.post('updav.php', 
                                    $('#encresp').serialize(),
                                    function(data){
                                                  var response = $.parseJSON(data);
                                                  if (!response.status)
                                                  {
                                                    alert(response.message);
                                                  }
                                                  else
                                                  {
                                                    location.reload();
                                                  }
                                   });
                        }
                });
    </script>";
  }
  $print.='<br>
            <p class="text-center">
            <a href="./'.$url.'?v='.$clase->getCurso()->getId().'"><i class="fas fa-angle-double-left fa-3x"></i></a>
            </p>';
print $print;
    $print = '
              <div class="card mt-4 ml-3">
                <div class="card-body">
                  <h4 class="card-title">'.$clase->getTitulo().'</h4>
                  <hr>
                  <div class="row">
                    <div class="col-12 col-sm-6 col-lg-4 border-right">
                        <ul class="list-group list-group-flush">
                          <ul class="list-group">
                            <li class="list-group-item">
                              <i class="fas fa-clipboard-list fa-2x mt-2"></i><span class="ml-3">Temario:</span>
                              <ul class="list-group ml-5">
  
                              </ul>
                            </li>
                            <li class="list-group-item">
                              <a href="https://docs.google.com/forms/d/e/1FAIpQLSeAgju3F7VZfhdpY0x2QnI0h81Ss-eeDpjyk2emyqBFPMQ9HQ/viewform?usp=sf_link" target="_blank"><i class="fas fa-question fa-2x mt-2"></i> <span class="ml-3">Consultas</span></a>
                            </li>
                            <li class="list-group-item">
                              <a href="https://docs.google.com/forms/d/e/1FAIpQLSe6mvF6GEdhMjh8TGJJ8RTeEmG5tOQOOnpvz_2VYMWMidVIPA/viewform?usp=sf_link" target="_blank"><i class="fas fa-tools fa-2x mt-2"></i><span class="ml-3">Problemas con el video</span></a>
                            </li>

                          </ul>
                        </ul>
                    </div>
                  </div>
                </div>
                <div class="card-footer text-muted text-center mb-0">
            
                </div>
              </div>';
      
}



function getClasesCurso($curso, $conn, $entityManager)
{
   GLOBAL $url;
   $emple = find('Empleado', $_SESSION['id_chofer']);
   $curso = find('Curso', $curso);


   $clase = '<div class="card mt-4 ml-3">
            <div class="card-body">
              <h4 class="card-title col-sm-12">'.$curso->getNombre().'</h4>
              <hr>
              <div class="list-group-flush">';
   foreach ($curso->getClases() as $c)
   {
      if (!$c->getEliminada())
      {
          $i = '<i class="fas fa-grip-lines fa-2x mr-4 grey p-3 white-text rounded-circle " aria-hidden="true"></i>';
          if ($c->getEsEvaluacion())
          {
            $i = '<i class="fas fa-book-reader fa-2x mr-4 grey p-3 white-text rounded-circle " aria-hidden="true"></i>';
          }
          $next = '';
          if (!$c->getAnterior())  //es la primer clase del curso, siempre debe estar habilitada para realizarce
          {
              $next = '<a href="./'.$url.'?v='.$curso->getId().'&c='.$c->getId().'" class="align-middle"><i class="fas fa-sign-in-alt fa-2x mt-3"></i></a>';
          }
          else
          {
              $realizadas = $entityManager->createQuery("SELECT c
                                                        FROM ClaseRealizada c
                                                        WHERE c.clase = :clase AND c.empleado = :empleado")
                                          ->setParameter('clase',  $c->getAnterior())
                                          ->setParameter('empleado', $emple)                             
                                          ->getOneOrNullResult();
              if (!$realizadas)
              {
                $next = 'Debe completar la clase anterior';
              }
              else
              {
                if ($c->getEsEvaluacion())
                {
                  if (!$c->getCurso()->getAdmiteEvaluacion())
                  {
                    $next = '<a href="'.$c->getRecurso().'" target="_blank" class="align-middle"><i class="fas fa-sign-in-alt fa-2x mt-3"></i></a>';
                  }
                  else
                  {
                    $next = '<a href="./'.$url.'?ne='.$c->getId().'" class="align-middle"><i class="fas fa-sign-in-alt fa-2x mt-3"></i></a>';
                  }
                }
                else
                {
                  $next = '<a href="./'.$url.'?v='.$curso->getId().'&c='.$c->getId().'" class="align-middle"><i class="fas fa-sign-in-alt fa-2x mt-3"></i></a>';
                }
              }
          }

          $lineas = '';
          foreach ($c->getLineas() as $ln)
          {
            $lineas.= '<li class="text-muted">'.$ln->getDescripcion().'</li>';
          }
          $clase.= '<div class="list-group-item">
                      <div class="row">
                          <div class="col-12 col-lg-1 align-middle">
                              '.$i.'
                          </div>
                          <div class="col-12 col-lg-2 align-middle">
                                <p class="mt-3">'.$c->getTitulo().'</p>
                          </div>
                          <div class="col col-lg-5 align-middle">
                                <p class="mt-3 text-muted">
                                  <ul class="list-group">
                                  '.$lineas.'
                                  </ul>
                                </p>
                          </div>
                          <div class="col-4 align-middle">
                           '.$next.'
                          </div>
                      </div>
                    </div>';
      }
   }
   $clase.='</div>
   <div class="card-footer text-muted text-center">
              <a href="./'.$url.'"><i class="fas fa-angle-double-left fa-3x"></i></a>
            </div>
            </div>
            </div>';
    print $clase;
}


?>