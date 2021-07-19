<?php
     session_start();
   /*  include('../../modelo/provincia.php');
     include('../../modelo/ciudades.php');     */
     include('../paneles/viewpanel.php');
     include('../main.php');
     define(RAIZ, '/nuevotrafico');

     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet" />

 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>

<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.dataTables.min.js"></script>


 

 <script>
	$(function() {
        $('#bse').selectmenu({width: 250});
        $('#int').selectmenu({width: 100});        

        
        load();
        $('#ass').button().click(function(){
                                            var x = $('#commentForm').serialize();
                                            $.post("/modelo/servicios/addintbse.php",
                                                    x,
                                                    function(data){
                                                                    var res = JSON.parse(data);
                                                                    if (res.status){
                                                                      load();
                                                                    }
                                                                    else{
                                                                      alert('No se ha podido realizar la accion');
                                                                    }
                                                                  });
                                            });

	});

  function load(){
              $('#data').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
              $.post("/modelo/servicios/addintbse.php",
                      {accion:'ld'},
                      function(data){
                                      $("#data").html(data);
                                    });
  }
	</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend {padding: 0.5em;}

</style>
<BODY>
<?php
     menu();
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form class="cmxform" id="commentForm">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Asignar Unidades a Base</legend>
                         <table border="0" align="center">
                                <tr>
                                    <td WIDTH="20%">Bases Operativas</td>
                                    <td>
                                        <select id="bse" name="bse">
                                                <?php
                                                  //   armarSelect('unidades', 'interno', 'id', 'interno', "(id_estructura = $_SESSION[structure]) and (activo)");
                                                armarSelect('baseoperativaxestructura', 'nombre', 'id', 'nombre', "(id_estructura = $_SESSION[structure])");
                                                ?>
                                        </select>
                                    </td>
                                    <td WIDTH="20%">Unidades</td>                                    
                                    <td>
                                        <select id="int" name="int">
                                                <?php
                                                  armarSelect('unidades', 'interno', 'id', 'interno', "(id_estructura = $_SESSION[structure]) and (activo)");
                                                ?>
                                        </select>
                                    </td>
                                    <td> 
                                        <input type="button" id="ass" value="Asignar Unidad">
                                    </td>
                                    
                                </tr>
                         </table>
	           </fieldset>
             <input type="hidden" name="accion" value="ass"/>
            </form>
            <div id="data">
            </div>            
</div>

</BODY>
</HTML>
