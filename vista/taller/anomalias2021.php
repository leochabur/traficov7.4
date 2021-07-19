<?php     session_start();
     include_once('../main.php');
     include_once('../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);



     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>

   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
    <link href="/vista/css/jquery.treeTable.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
     <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/scroll-table/jquery.chromatable.js"></script>

  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/tables/jquery.tablehover.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.dataTables.min.js"></script>

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
        $('#unidad, #tipo, #str, #origen').selectmenu({width: 350});
        $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
        $("#fecha").datepicker({ dateFormat: "dd/mm/yy" }).datepicker("setDate", new Date());;
		$("#pxd").button();
        $('#str').change(function(){
                                    $.post("/modelo/taller/reanom.php",{accion:'ldint', str:$('#str').val(), sel:1}, function(data){
                                                                                                                             $('#int').html(data);
                                                                                                                             });
                                    });
        $.post("/modelo/taller/reanom.php",{accion:'ldint', str:$('#str').val(), sel:1}, function(data){
                                                                                                                             $('#int').html(data);
                                                                                                                             });
        $("#upanomalia").validate({
                                  submitHandler: function(e){
                                                             var datos = $("#upanomalia").serialize();
                                                             $.post('/modelo/taller/updano.php', datos, function(data){
                                                                                                        if (data == 0)
                                                                                                           alert("No se ha podido generar la anomalia");
                                                                                                        else{
                                                                                                            alert("Se genero con exito la anomalia");
                                                                                                            $('#desc').val('');

                                                                                                        }

                                                                                                        });
                                                             }
                                  });
	});
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
     menu();
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form id="upanomalia" name="upanomalia" method="post" action="vtoemp.php">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Anomalias</legend>
                         <div id="tabs">
                         <ul>
                             <li><a href="#tabs-1">Cargar Anomalias</a></li>
                         </ul>
                         <div id="tabs-1">
                         <table border="0" align="center" width="50%">
                                <tr>
                                    <td>Fecha Anomalia</td>

                                    <td><input type="text" size="20" class="required ui-widget ui-widget-content ui-corner-all" id="fecha" name="fecha"></td>
                                <tr>
                                <tr>
                                    <td>Estructura</td>
                                    <td>
                                        <select id="str" name="str" class="ui-widget ui-widget-content  ui-corner-all">
                                                <?php
                                                     armarSelect('estructuras', 'nombre', 'id', 'nombre', "");
                                                ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Internos</td>
                                    <td id="int"></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Tipo de Anomalia</td>
                                    <td>
                                        <select id="tipo" name="tipo" class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php

                                                  armarSelect('rubros_anomalias', 'rubro', 'id', 'rubro', "");
                                                ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Causa Anomalia</td>
                                    <td>
                                        <select id="origen" name="origen" class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php

                                                  armarSelect('origen_anomalias', 'origen', 'id', 'origen', "");
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
	</fieldset>
	<input type="hidden" name="accion" id="accion" value="sveanom"/>
</form>
	</div>
	<div id="table">
	</div>
</div>

</BODY>
</HTML>
