<?php
     session_start();
     include_once('../paneles/viewpanel.php');
     include_once('../main.php');
     include_once('../../controlador/bdadmin.php');
     define(RAIZ, '/nuevotrafico');

     encabezado('Menu Principal - Sistema de Administracion - Campana');

    // $con = conexcion();




     if (isset($_POST['fecha'])){
        $fec = $_POST['fecha'];
        $fecha = explode("/", $fec);
        $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
     }
     else
         $fecha = date("d/m/Y");

?>
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
 <link href="<?php echo RAIZ;?>/vista/css/jquery.contextMenu.css" rel="stylesheet" type="text/css" />

  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.multiselect.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.tablesorter.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.contextMenu.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/scroll-table/jquery.chromatable.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.multiselect.js"></script>
 <script>
    var ordenes;
	$(function(){

                 $("#table").chromatable({height: "400px", scrolling: "yes"});
                 $(':button').button();
                 $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                 $('#destino').datepicker({dateFormat:'dd/mm/yy'});
                 $("#table tr:even").css("background-color", "#FFF");
                 ordenes = new Array();
                 $('#cargar').click(function(){
                                             var send = $('#fecha').val();
                                             $('#cnddis').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                             $('#orasig').html('');
                                             $.post('/modelo/ordenes/asscndord.php', {accion: "ldcn", fecha: send}, function(data) {
                                                                                                                                      $('#cnddis').html(data);
                                                                                                                                     });
                                             });
	});
	
    function checkTodos (obj) {
             ordenes = new Array();
             if (obj.checked){
                   $( "#table td input:checkbox" ).each(function (){
                                                                     ordenes.push(this.id);
                                                                   });
             }
             $("#table input:checkbox").attr('checked', obj.checked);
    }

    if (!Array.indexOf) {
        Array.prototype.indexOf = function (obj, start) {
                                for (var i = (start || 0); i < this.length; i++) {
                                    if (this[i] == obj) {
                                       return i;
                                    }
                                }
                                return -1;
                                }
    }
    
    function cargarCheck(orden){
             if (orden.checked){
                ordenes.push(orden.id);
             }
             else{
                  var a = ordenes.indexOf(orden.id);
                  ordenes.splice(a,1);
             }
    }



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
#cargar {font-size: 72.5%;}

option.0{background-color: #;}
option.1{background-color: ;}

</style>
<BODY>
<?php
     menu();
?>
    <br><br>
    <div id="result"></div>
    <fieldset class="ui-widget ui-widget-content ui-corner-all">

         <legend class="ui-widget ui-widget-header ui-corner-all">Asignar conductores</legend>
         <hr align="tr">
         <div>

              <div align="center">Ordenes del dia<input id="fecha" name="fecha"  type="text" size="30"><input type="button" id="cargar" name="cargar" class="button" value="Cargar Ordenes"></div>

         </div>
         <hr align="tr">
         <div id="cnddis" align="center" >
         </div>
         <div id="orasig" align="center" >
         </div>
	</fieldset>
</BODY>
</HTML>
