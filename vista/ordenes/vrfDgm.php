<?php
session_start();
set_time_limit(0);
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
    // include_once('../paneles/viewpanel.php');
    include('../../controlador/bdadmin.php');
     include_once('../main.php');

     define(RAIZ, '');

     encabezado('Menu Principal - Sistema de Administracion - Campana');






     if (isset($_POST['fecha'])){
        $fec = $_POST['fecha'];
        $fecha = explode("/", $fec);
        $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
     }
     else
         $fecha = date("d/m/Y");

?>
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet"/>
 <link href="<?php echo RAIZ;?>/vista/css/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.tablesorter.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.contextMenu.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/scroll-table/jquery.chromatable.js"></script>
   <script defer src="https://use.fontawesome.com/releases/v5.0.9/js/all.js" integrity="sha384-8iPTk2s/jMVj81dnzb/iFR2sdA7u06vHJyyLlAd4snFpCl/SnyUjRrbdJsw1pGIl" crossorigin="anonymous"></script>
 <script>
	$(function(){
                 $(':submit');

                 $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                 $('#cargar').button().click(function(event){
                                                             event.preventDefault();
                                                             $('#simul').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                             $.post('/modelo/ordenes/controlDiag.php',
                                                                    $('#load').serialize(),
                                                                    function(data){
                                                                                      $('#simul').html(data);
                                                                                   });
                                                             });

	});
	




	</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
.small.button, .small.button:visited {
font-size: 11px ;
}

#upcontact div{padding: 2px;}
#cargar, #cnsvac {font-size: 72.5%;}
a {font-size: 72.5%;}

option.0{background-color: #;}
option.1{background-color: ;}

</style>
<BODY>
<?php
     menu();
     $con = conexcion();
?>
    <br><br>
    <div id="result"></div>
    <fieldset class="ui-widget ui-widget-content ui-corner-all">

         <legend class="ui-widget ui-widget-header ui-corner-all">Verificar diagrama</legend>
         <hr align="tr">
         <div>
         <form id="load" method="post">
               <table border="0" width="45%" align="center">
                      <tr>
                          <td>Diagrama del dia</td>
                          <td><input id="fecha" name="fecha" type="text" size="15"></td>
                          <td>Reportar Ordenes No Finalizadas<input type="checkbox" name="finalizada"></td>
                          <td align="right"><input type="submit" id="cargar" name="cargar" class="button" value="Verificar Diagrama"></td>
                      </tr>
               <table>
              <input type="hidden" name="accion" id="accion" value="vvc">
         </form>
         </div>
         <hr align="tr">
         <div id="simul"></div>
	</fieldset>
	

</BODY>
</HTML>
