<?php
     session_start();

     include('../paneles/viewpanel.php');
     include('../main.php');
     define(RAIZ, '/nuevotrafico');

     encabezado('Menu Principal - Sistema de Administracion - Campana');
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
        $('#unidades').selectmenu({width: 250});
        $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
        $("#desde, #hasta").datepicker({ dateFormat: "dd/mm/yy" });
		$("#unidades").change(function(){
                                        $.post("/modelo/taller/asigdist.php", {accion: 'dist', uda: $(this).val()}, function(data) {
                                                                                                                                    var obj = jQuery.parseJSON(data);

                                                                                                                                    if (obj == "0"){
                                                                                                                                         $('input:radio').each(function(){
                                                                                                                                                                          $(this).attr('checked', false);
                                                                                                                                                                          });
                                                                                                                                    }
                                                                                                                                    else{
                                                                                                                                         $("#dist_"+obj).attr('checked', true);
                                                                                                                                    }
                                                                                                                                   });
                                        });
         $('input:radio').click(function(){
                                           $.post("/modelo/taller/asigdist.php", {accion: 'asign', uda: $("#unidades").val(), dist: $(this).val()});
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
         <form class="cmxform" id="commentForm" method="get" action="">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Asignar Distribucion de Cubiertas</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%">
                                <tr>
                                    <td align="center" colspan="4">Internos
                                        <select id="unidades" name="unidades" class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <option value="0">Seleccione un Interno</option>
                                                <?php
                                                  armarSelect('unidades', 'CAST(interno as UNSIGNED)', 'id', 'interno', "");
                                                ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><img src="../distA.jpg" width="216" height="78" border="0"></td>
                                    <td><img src="../distB.JPG" width="216" height="78" border="0"></td>
                                    <td><img src="../distC.JPG" width="215" height="76" border="0"></td>
                                    <td><img src="../distD.JPG" width="215" height="76" border="0"></td>
                                </tr>
                                <tr>
                                    <td align="center"><input name="dist" type="radio" value="1" id="dist_1"></td>
                                    <td align="center"><input name="dist" type="radio" value="2" id="dist_2"></td>
                                    <td align="center"><input name="dist" type="radio" value="3" id="dist_3"></td>
                                    <td align="center"><input name="dist" type="radio" value="4" id="dist_4"></td>
                                </tr>
                                
                         </table>
                         <div id="table" align="center"></div>
	</fieldset>
	<input type="hidden" name="accion" id="accion" value="soser"/>
</form>
	</div>
	<div id="table">
	</div>
</div>

</BODY>
</HTML>
