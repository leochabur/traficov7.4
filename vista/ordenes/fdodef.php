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
  $res_cc = ejecutarSQL("SELECT cant_cond FROM estructuras WHERE id = $_SESSION[structure]");
  if ($data_cc = mysql_fetch_array($res_cc)){
         $cantTripulacion = $data_cc[0];
  }      
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script>
	$(function() {
        $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );

        $('#save').button();

		    $("#fecha").datepicker({
                                    dateFormat : 'dd/mm/yy'
                                   });
        $('#str option[value=<?php print $_SESSION['structure']; ?>]').attr('selected','selected');
        $('#str').selectmenu({width: 350});

        $('#commentForm').validate({
                                  submitHandler: function(){
                                                            $.post('/modelo/ordenes/fdodef.php',
                                                                   $('#commentForm').serialize(),
                                                                   function(data){
                                                                                    $('#commentForm')[0].reset();
                                                                                    setExist();
                                                                   });
                                                            }
                             });
        setExist();
	});
  function setExist()
  {
      $.post('/modelo/ordenes/fdodef.php',
             {accion : 'getyears', initial: 1},
             function(data){
                              $('#exists').html(data);
             });
  }
	</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 150px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}
#commentForm td{padding: 2px;}

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
		                <legend class="ui-widget ui-widget-header ui-corner-all">Administrar feriados</legend>
                    <fieldset class="ui-widget ui-widget-content ui-corner-all">
                       <legend class="ui-widget ui-widget-header ui-corner-all">Definir feriado</legend>
                          <form class="cmxform" id="commentForm" method="get" action="" name="commentForm">
                           <table border="0" align="center" width="50%">
                                  <tr>
                                      <td WIDTH="20%">Estructura</td>
                                      <td><select id="str" name="str" class="ui-widget-content  ui-corner-all"  validate="required:true">
                                              <?php
                                                   armarSelect('estructuras', 'nombre', 'id', 'nombre', "");
                                              ?>
                                          </select>
                                      </td>
                                      <td>
                                      </td>
                                  </tr>  
                                  <tr>
                                      <td WIDTH="20%"><label for="fservicio">Fecha</label></td>
                                      <td><input id="fecha" name="fecha" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                      <td></td>
                                  </tr>
                                  <tr>
                                      <td WIDTH="20%"><label for="fservicio">Descripcion</label></td>
                                      <td><input id="descricion" name="descripcion" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                      <td></td>
                                  </tr>     
                                  <tr>
                                      <td WIDTH="20%"></td>
                                      <td></td>
                                      <td><input type="submit" value="Guardar" id="save" /></td>
                                  </tr>                
                           </table>
                           <input type="hidden" name="accion" value="save"/>
                          </form>
                      </fieldset>
                      <fieldset class="ui-widget ui-widget-content ui-corner-all">
                          <legend class="ui-widget ui-widget-header ui-corner-all">Feridados Existentes</legend>
                          <div id='exists'>
                            
                          </div>
                      </fieldset>
	             </fieldset>
          
	</div>
</BODY>
</HTML>
