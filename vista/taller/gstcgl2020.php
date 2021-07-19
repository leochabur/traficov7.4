<?php
     session_start();
error_reporting(E_ALL & ~E_NOTICE);     
     include_once('../main.php');

     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
     include '../../modelsORM/manager.php';
     include_once '../../modelsORM/call.php';
     include_once '../../modelsORM/src/lavadero/AccionUnidad.php';     
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

.small.button, .small.button:visited {
font-size: 11px ;
}

input.text { margin-bottom:12px; width:95%; padding: .4em; }
#newuda .error{
  font-size:0.8em;
  color:#ff0000;
}

         .rojo {background-color: #DD614A;}
         .amarillo{background-color: #FFFF80;}
         .verde{background-color: #C0FFC0;}

</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                                                       $('select').selectmenu({width: 200});
                                                       $('#cargar').button().click(function(){
                                                                                              $('#data').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                                              $.post('/modelo/taller/gstcgl.php',
                                                                                                     $("#upuda").serialize(),
                                                                                                     function(data){
                                                                                                                    $("#data").html(data);
                                                                                                     });

                                                       });



                          });
</script>

<body>
<?php
     menu();
      /*  $turnos = $entityManager->createQuery("SELECT t FROM Turno t")->getResult();

        $optTurnos = "";
        foreach ($turnos as $turno) {
          $optTurnos.="<option value='".$turno->getId()."'>$turno</option>";
        }*/

     //   $a = $entityManager->createQuery("SELECT a, max(a.fecha) fecha FROM AccionUnidad a GROUP BY a.unidad, a.accion ORDER BY a.unidad");     
?>
<br>
         <form id="upuda">
             <fieldset class="ui-widget ui-widget-content ui-corner-all">
                     <legend class="ui-widget ui-widget-header ui-corner-all">Despacho de Combustible</legend>

                         <table border="0" align="center" name="tabla">
                                <tr>
                                    <td>
                                        <input type="button" value="Cargar Unidades" id="cargar">
                                    </td>
                                </tr>
                         </table>
                         <div id="data"></div>
            </fieldset>
            <input type="hidden" name="accion" value="load">
         </form>
</body>
</html>

