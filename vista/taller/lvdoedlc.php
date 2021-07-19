<?php
     session_start();
     set_time_limit(0);
   //  ini_set('max_execution_time',9000);
    // error_reporting(0);
   //  error_reporting(E_ALL & ~E_NOTICE);

     $_SESSION[old] = 1;
     include_once('../main.php');
     include_once('../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);



     encabezado('Menu Principal - Sistema de Administracion - Campana', 1);

     include '../../modelsORM/manager.php';
     include_once '../../modelsORM/call.php';
     include_once '../../modelsORM/src/lavadero/AccionUnidad.php';

     $hoy = (new DateTime('NOW'))->format('d/m/Y');
?>

 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <link rel="stylesheet" type="text/css" href="<?php echo RAIZ;?>/multiselect/jquery.multiselect.css" />
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/multiselect/src/jquery.multiselect.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/multiselect/i18n/jquery.multiselect.es.js"></script>
  <style>
         .rojo {background-color: #DD614A;}
         .amarillo{background-color: #FFFFC0;}
         .verde{background-color: #C0FFC0;}
         .blue{background-color: #0000FF;}         
         
  </style>
  <script>


          var personal = new Array();
  <?php
       $query = $entityManager->createQuery("SELECT e FROM Empleado e JOIN e.categoria c WHERE e.activo = :activo AND c.id IN (12, 19, 11) ORDER BY e.apellido");

      // $query->setParameter('foo', '%Lavador%');
       $query->setParameter('activo', true);
     //  $query->setParameter('categoria', 12);
       $lavadores = $query->getResult();
       $options = "<option>Seleccione uno</option>";
       foreach ($lavadores as $lavador) {
               $options.= "<option value='".$lavador->getId()."'>$lavador</option>";
            //   print "personal.push(new Array(".$lavador->getId().", '$lavador'));";
       }
  ?>
       function cargarCombo(obj){
               var i;
               for (i = 1; i <= personal.length; i++){
                  var opt = $('<option />', {
                                         value: personal[i-1][0],
			                             text: personal[i-1][1]
		                                 });
                   opt.appendTo( obj );
               }
               obj.multiselect({
		                         header: false,
		                         selectedList: 2});
               obj.multiselect('refresh');

      }
  </script>
<BODY>
<?php
     menu();
?>
    <br><br>
    <?php
          
          try{
                              $sectores = $entityManager->createQuery('SELECT s
                                                                       FROM Sector s                                                                           
                                                                       ORDER BY s.descripcion')
                                                            ->getResult();
                              $tiposLavados = $entityManager->createQuery('SELECT t
                                                                           FROM TipoAccionUnidad t  
                                                                           WHERE t.edilicio = :edilicio
                                                                           ORDER BY t.tipo')
                                                            ->setParameter('edilicio', true)
                                                            ->getResult();
            }
            catch (Exception $e) {
                                     die("error 2: ".$e->getMessage());
                                 }                                                    

                                    ?>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Acciones de la fecha</legend>
                         <table width="100%" border="0">
                            <tr>
                              <td>Fech Accion</td>
                              <td><input type="text" name="fecha" id="fecha" value="<?php print $hoy; ?>"/></td>
                            </tr>

                         </table> 
                         <table width="100%" class="table table-zebra">
                                <thead>
                                       <tr>
                                           <th>Sector</th>
                                          <?php
                                              foreach ($tiposLavados as $tipo) {
                                                             print "<th>$tipo</th>";
                                              }                                                     
                                          ?>
                                       </tr>
                                </thead>
                                <tbody>
                                <?php
                                    foreach ($sectores as $acc) {
                                                   print "<tr><td>".($acc->getDescripcion())."</td>";
                                                   foreach ($tiposLavados as $tipo) {                              
                                                  
                                                      print "<td>
                                                                <form class='form' data-id='".$acc->getId()."'>
                                                                    <select multiple='multiple' id='select_".$acc->getId()."'>
                                                                      $options
                                                                    </select>
                                                                    <input type='text' class='hora' size='5' name='tiempo'>
                                                                    <input type='submit' value='+' class='btn'>
                                                                    <input type='hidden' name='sector' value='".$acc->getId()."'>
                                                                    <input type='hidden' name='tipoAccion' value='".$tipo->getId()."'>
                                                                    <input type='hidden' name='accion' value='sveLvdEd'>
                                                                </form>
                                                              </td>";
                                                   }
                                                   print "</tr>";
                                    }
                                           
                                ?>
                                <tbody>
                         </table>
               </fieldset>
	</div>
	<div id="acciones">
	</div>
</div>

</BODY>
 <script>
	$( document ).ready(function() {
        $('.hora').mask('99:99');

        $('#acciones').dialog({autoOpen: false,
                               height: 400,
                               width: 550,
                               modal: true,
                               title: 'Acciones asociadas a la unidad'});
                               
        $('select').multiselect({
		                         header: false,
		                         selectedList: 2
	                             });
                                                    
		$(".btn").button();
    $('.form').submit(function(event){
                                      event.preventDefault();
                                      var fecha = $('#fecha').val();
                                      var form = $(this);                                      
                                      var emples = $('#select_'+form.data('id')).val();
                                      var data = form.serialize()+ '&fecha=' + fecha+'&emples='+emples;
                                      $.post("/modelo/taller/gestionlavados.php",
                                              data,
                                              function(resp){
                                                              var response = $.parseJSON(resp);    
                                                              if (!response.status) {
                                                                alert(response.mje);
                                                              } 
                                                              else{
                                                                form[0].reset();
                                                              }                                          
                                                            }
                                              );

                                });

    $('td a').click(function(event) {
                                    event.preventDefault();
                                    $('#acciones').load('/modelo/taller/gestionlavados.php', {accion:'lstacc', uda:$(this).attr('id')});
                                    $('#acciones').dialog('open');
                                    }
                    );
    $('td select').focus(function(){alert('ok');});

              $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
          $("#fecha").datepicker({dateFormat:'dd/mm/yy'});

	});

	</script>
</HTML>
