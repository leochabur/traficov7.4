<?php
     session_start();
    // include_once('../paneles/viewpanel.php');
    include('../../modelsORM/manager.php');
     include_once('../main.php');
    include_once ('../../modelsORM/call.php');
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
 <script>
    var ordenes;
	$(function(){

                 $(':submit').button();
                 $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                 $('#destino').datepicker({dateFormat:'dd/mm/yy'});
                 
                 ordenes = new Array();
                 $('#copy').click(function(){
                                             var send  = $.datepicker.parseDate('dd/mm/yy', $('#destino').val());
                                             var dest = send.getFullYear()+"-"+(send.getMonth()+1)+"-"+send.getDate();
                                             var orig  = $.datepicker.parseDate('dd/mm/yy', $('#fecha').val());
                                             var forig = orig.getFullYear()+"-"+(orig.getMonth()+1)+"-"+orig.getDate();
                                             $.post('/modelo/ordenes/cpydiagrama.php',
                                                    {accion: "ydg", fecha: dest},
                                                    function(data){
                                                                   var response = $.parseJSON(data);
                                                                   if (response.status){
                                                                      if (confirm('El diagrama ya ha sido copiado! Desea copiarlo nuevamente')){
                                                                        $('#orddest').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                        $.post('/modelo/ordenes/cpydiagrama.php',
                                                                               {accion: "cpy", fecha: dest, forigen: forig, orders: ordenes.join(',')},
                                                                               function(data) {
                                                                                              var response = $.parseJSON(data);
                                                                                              if (response.status){
                                                                                                 $('#orddest').empty();
                                                                                                 alert('Diagrama copiado exitosamente');
                                                                                              }
                                                                                              else{
                                                                                                   $('#orddest').empty();
                                                                                                   alert('Se han producido errores al intentar copoar el diagrama '+response.sql);
                                                                                              }
                                                                               });
                                                                      }
                                                                   }
                                                                   else{
                                                                        $('#orddest').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                        $.post('/modelo/ordenes/cpydiagrama.php',
                                                                               {accion: "cpy", fecha: dest, forigen: forig, orders: ordenes.join(',')},
                                                                               function(data) {
                                                                                              var response = $.parseJSON(data);
                                                                                              if (response.status){
                                                                                                 $('#orddest').empty();
                                                                                                 alert('Diagrama copiado exitosamente');
                                                                                              }
                                                                                              else{
                                                                                                   $('#orddest').empty();
                                                                                                   $('#sisi').html(response.sql);
                                                                                                   alert('Se han producido errores al intentar copoar el diagrama '+response.sql);
                                                                                              }
                                                                               });
                                                                   }
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
     $str = find('Estructura', $_SESSION['structure']);  
     $q = $entityManager->createQuery("SELECT e 
                                       FROM Empleado e 
                                       LEFT JOIN EntregaTelefono et WITH et.empleado = e
                                       WHERE e.borrado = :borrado AND e.estructura = :estructura
                                       ORDER BY e.apellido")
                        ->setParameter('borrado', false)
                        ->setParameter('estructura', $str);
     $result = $q->getResult();

     $q = $entityManager->createQuery("SELECT t
                                       FROM Telefono t
                                       ORDER BY t.alias");
     $telefonos = $q->getResult();

     $select = "<select>";
     foreach ($telefonos as $tel) {
       $select.="<option value='".$tel->getId()."'>".$tel->getAlias()."</option>";
     }
     $select.="</select>";

?>
    <br><br>
    <fieldset class="ui-widget ui-widget-content ui-corner-all">

         <legend class="ui-widget ui-widget-header ui-corner-all">Asignar Telefonos</legend>
         <hr align="tr">
         <div>
              <table id='table' align="center" class="table table-zebra" border="0" width="100%">
                     <thead>
            	            <tr>
                                <th>Lgeajo</th>
                                <th>Apellido, nombre</th>
                                <th>Telefono Asignado</th>
                                <th></th>
                            </tr>
                     </thead>
                     <tbody>
                      <?php
                          foreach ($result as $e) {
                            print "<tr>
                                      <td>".$e->getLegajo()."</td>
                                      <td>$e</td>
                                      <td></td>
                                      <td>".$select."<button>+</button></td>
                                  </tr>";
                          }
                      ?>
                     </tbody>
              </table>
	   </fieldset>
</BODY>
</HTML>
