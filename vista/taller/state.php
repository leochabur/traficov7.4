<?php
     session_start();
     error_reporting(0);     
     include_once('../main.php');

     $_SESSION["COUNTORIG"] = 60;
     $_SESSION["COUNTPORPAGE"] = 12;
     $_SESSION["PAGE"] = 0;
     $_SESSION["COLS"] = 3;

     setcookie("COUNTORIG", 20); 
     setcookie("COLS", 20);

     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
     include '../../modelsORM/manager.php';
     include_once '../../modelsORM/call.php';  
     include_once '../../modelsORM/src/AccionUnidad.php';     
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
    <link href="/vista/css/estilos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
     <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/scroll-table/jquery.chromatable.js"></script>

  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/tables/jquery.tablehover.js"></script>
   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }

table thead tr th{ padding: 0.5em; }
table tbody tr td{ padding: 1em; }

</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                      $('.view').hide();
                                                      $.post('/modelo/taller/gstcgl.php', 
                                                              {accion: 'state'}, 
                                                              function(data){
                                                                              var response = $.parseJSON(data);
                                                                              if (response.type == 'v')
                                                                              {
                                                                                $('.view').hide();
                                                                                $('.resource').show();
                                                                                $('.resource').html(response.resource);
                                                                              }
                                                                              else{
                                                                                  $('.resource').hide();
                                                                                  $('.view').show();
                                                                                  $(".data").html(response.resource);
                                                                              } 
                                                              });
                                                  function loadCarga()
                                                  {

                                                       $.post('/modelo/taller/gstcgl.php', 
                                                              {accion: 'state'}, 
                                                              function(data){
                                                                              var response = $.parseJSON(data);
                                                                              if (response.type == 'v')
                                                                              {
                                                                                $('.view').hide();
                                                                                $('.resource').show();
                                                                                $('.resource').html(response.resource);
                                                                              }
                                                                              else{
                                                                                  $('.resource').hide();
                                                                                  $('.view').show();
                                                                                  $(".data").html(response.resource);
                                                                              } 
                                                                              
                                                              });  
                                                  }           
                                                  setInterval(loadCarga, 7000);                          

                                                       });

</script>

<body style="background-color:black">
<?php
     //menu();
      /*  $turnos = $entityManager->createQuery("SELECT t FROM Turno t")->getResult();

        $optTurnos = "";
        foreach ($turnos as $turno) {
          $optTurnos.="<option value='".$turno->getId()."'>$turno</option>";
        }*/

     //   $a = $entityManager->createQuery("SELECT a, max(a.fecha) fecha FROM AccionUnidad a GROUP BY a.unidad, a.accion ORDER BY a.unidad");     
?>
<br>
         <form id="upuda">
             <fieldset class="ui-widget ui-widget-content ui-corner-all view" style="background-color:black">
                     <legend class="ui-widget ui-widget-header ui-corner-all">Km disponibles unidades</legend>
                         <div class="data" style="background-color:black"></div>
            </fieldset>            
         </form>
         <div class="resource">
        </div>
</body>
</html>

