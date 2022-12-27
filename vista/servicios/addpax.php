<?php
     session_start();
     set_time_limit(0);
     error_reporting(1);
     include('../paneles/viewpanel.php');
     include('../main.php');
     include_once('../../controlador/ejecutar_sql.php');
     //define('RAIZ', '');
     define('STRUCTURED', $_SESSION['structure']);

     encabezado('Menu Principal - Sistema de Administracion - Campana'); 
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
  <script defer src="https://use.fontawesome.com/releases/v5.0.9/js/all.js" integrity="sha384-8iPTk2s/jMVj81dnzb/iFR2sdA7u06vHJyyLlAd4snFpCl/SnyUjRrbdJsw1pGIl" crossorigin="anonymous"></script>
 <script>

	$(function() {

        $('#save').button().click(function(e){
                                                $('#accion').val('addpax');
        });

        $('#edit').button().click(function(e){
                                                $('#accion').val('editpax');
        });

        $('#commentForm').validate({
                                  submitHandler: function(){
                                                            $.post('/modelo/servicios/addpax.php',
                                                                   $('#commentForm').serialize(),
                                                                   function(data){
                                                                                  let result = $.parseJSON(data);
                                                                                  if (result.status)
                                                                                  {
                                                                                      $('#commentForm')[0].reset();
                                                                                      getList();
                                                                                  }
                                                                                  else{
                                                                                    alert(result.msge);
                                                                                  }
                                                                   });
                                                            }
                             });
        getList();
	});

  function getList()
  {
      $('#exists').load('/modelo/servicios/addpax.php', {accion : 'listp'});
  }

	</script>

<style type="text/css">

body { font-size: 82.5%; }

label { display: inline-block; width: 150px; }

legend { padding: 0.5em; }

fieldset fieldset label { display: block; }

td{padding: 10px;}


#commentForm .error{
	font-size:0.8em;
	color:#ff0000;
}

</style>
<BODY>
<?php
     menu();
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                <legend class="ui-widget ui-widget-header ui-corner-all">Administrar pasajeros</legend>
                    <fieldset class="ui-widget ui-widget-content ui-corner-all">
                       <legend class="ui-widget ui-widget-header ui-corner-all">Nuevo</legend>
                          <form class="cmxform" id="commentForm" method="get" action="" name="commentForm">
                           <table border="0" align="center" width="50%"> 
                                  <tr>
                                      <td>
                                            <label for="apellido">Apellido</label>
                                            <input id="apellido" name="apellido" class="required ui-widget ui-widget-content  ui-corner-all"/>
                                      </td>                                    
                                      <td>
                                            <label for="nombre">Nombre</label>
                                            <input id="nombre" name="nombre" class="required ui-widget ui-widget-content  ui-corner-all"/>
                                      </td>
                                      <td>
                                            <label for="dni">DNI</label>
                                            <input id="dni" name="dni" class="required ui-widget ui-widget-content  ui-corner-all" />
                                      </td>
                                      <td>
                                      </td>
                                  </tr>     
                                  <tr>
                                      <td>
                                            <label for="direccion">Direccion</label>
                                            <input id="direccion" name="direccion" class="required ui-widget ui-widget-content  ui-corner-all" />
                                      </td>                                    
                                      <td>
                                            <label for="ciudad">Ciudad</label>
                                            <input id="ciudad" name="ciudad" class="required ui-widget ui-widget-content  ui-corner-all" />
                                      </td>
                                      <td>
                                            <label for="latitud">Latitud</label>
                                            <input id="latitud" name="latitud" class="required ui-widget ui-widget-content  ui-corner-all" />
                                      </td>
                                      <td>
                                            <label for="longitud">Longitud</label>
                                            <input id="longitud" name="longitud" class="required ui-widget ui-widget-content  ui-corner-all" />
                                      </td>
                                  </tr> 
                                  <tr>
                                      <td><input type="submit" value="Guardar" id="save" /></td>
                                      <td colspan="2"><input type="submit" value="Editar" id="edit"/></td>
                                  </tr>                
                           </table>
                           <input type="hidden" id="accion" name="accion" value="addpax"/>
                           <input type="hidden" id="idpax" name="idpax" value="0"/>
                          </form>
                      </fieldset>
                      <fieldset class="ui-widget ui-widget-content ui-corner-all">
                          <legend class="ui-widget ui-widget-header ui-corner-all">Pasajeros Existentes</legend>
                          <div id='exists'>
                            
                          </div>
                      </fieldset>
	             </fieldset>
          
	</div>
</BODY>
</HTML>
