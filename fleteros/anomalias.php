<?php
     session_start();

     require_once($_SERVER['DOCUMENT_ROOT'].'/orm_doctrine/VAnomalias.php');
     require_once($_SERVER['DOCUMENT_ROOT'].'/orm_doctrine/VUnidades.php');
     require_once($_SERVER['DOCUMENT_ROOT'].'/orm_doctrine/VEstructuras.php');
   //  include('../paneles/viewpanel.php');
     include('main.php');
    define("RAIZ", '');
    encabezado("");//$_SESSION["chofer"]);//  menu();
    menu();

  //   encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.dataTables.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.dataTables.min.js"></script>
 <script>
	$(function() {
        $( "#tabs" ).tabs();
        $('#unidad, #tipo').selectmenu({width: 350});
        $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
        $("#fecha").datepicker({ dateFormat: "dd/mm/yy" }).datepicker("setDate", new Date());;
		$("#pxd").button();
		$('a').click(function(){loadAn();});
        $("#upanomalia").validate({
                                  submitHandler: function(e){
                                                             var datos = $("#upanomalia").serialize();
                                                             $.post('updano.php', datos, function(data){
                                                                                                        if (data == 0)
                                                                                                           alert("No se ha podido generar la anomalia");
                                                                                                        else{
                                                                                                            alert("Se genero con exito la anomalia");
                                                                                                            $('#desc').val('');

                                                                                                        }

                                                                                                        });
                                                             }
                                  });
        loadAn();
	});
	
	function loadAn(){
             $('#tabs-2').html("<div align='center'><img  alt='cargando' src='../vista/ajax-loader.gif' /></div>");
	         $.post('updano.php', {accion: 'load'}, function(data){$('#tabs-2').html(data);});
	}
	</script>

<style type="text/css">
body { font-size: 72.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}
form table td{
  padding-top: 3px;
  padding-right: 3px;
  padding-bottom: 3px;
  padding-left: 3px;
}

#upanomalia .error{
	font-size:0.8em;
	color:#ff0000;
}

#anomalias .reparada{


}

#anomalias tbody tr:nth-child(odd){


}

#anomalias tbody tr:nth-child(even){


}

</style>
<BODY>
<?php

                                                 //    selectAnomaliasActivas();


?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form id="upanomalia" name="upanomalia" method="post" action="vtoemp.php">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Anomalias</legend>
                         <div id="tabs">
                         <ul>
                             <li><a href="#tabs-1">Cargar Anomalias</a></li>
                             <li><a href="#tabs-2">Anomalias generadas</a></li>
                         </ul>
                         <div id="tabs-1">
                         <table border="0" align="center" width="50%">
                                <tr>
                                    <td>Fecha Anomalia</td>

                                    <td><input type="text" size="20" class="required ui-widget ui-widget-content ui-corner-all" id="fecha" name="fecha"></td>
                                <tr>
                                <tr>
                                    <td>Interno</td>
                                    <td>
                                        <select id="unidad" name="unidad" class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     selectCochesActivos();
                                                ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Tipo de Anomalia</td>
                                    <td>
                                        <select id="tipo" name="tipo" class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php

                                                  selectAnomaliasActivas();
                                                ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Descripcion Anomalia</td>
                                    <td><textarea id='desc' name='desc' rows="20" cols="55" class="required ui-widget ui-widget-content  ui-corner-all"></textarea></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td><input type="submit" value="Guardar Anomalia" id="pxd"></td>
                                </tr>
                         </table>
                         </div>
                         <div id="tabs-2">

                         </div>
	</fieldset>
	<input type="hidden" name="accion" id="accion" value="sveanom"/>
</form>
	</div>
	<div id="table">
	</div>
</div>

</BODY>
</HTML>
