<?php
     session_start();
     include('../main.php');
     include('../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}

input.text { margin-bottom:12px; width:95%; padding: .4em; }
#newuda .error{
	font-size:0.8em;
	color:#ff0000;
}
table tr td{padding: 3px;}
</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $("#cargar").button().click(function(){
                                                                                              var datos = $("#upuda").serialize();
                                                                                              $('#data').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                                              $.post("/modelo/segvial/upvtouda.php", datos, function(data){$('#data').html(data);});
                                                       });
                                                       $('select').selectmenu({width: 150});

                          });

</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Cargar Vencimientos</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Seleccionar Unidad</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Numero de Interno</td>
                                    <td><select id="unidad" name="unidad" class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('unidades', 'CAST(interno as UNSIGNED)', 'id', 'interno', "(id_estructura = ".STRUCTURED.")");
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="button" value="Cargar" id="cargar">
                                    </td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <div id="data"> </div>
            </fieldset>
            <input type="hidden" name="accion" id="accion" value="load">
         </form>
<?
  if (isset($_GET['int'])){
     print "<script type=\"text/javascript\">
                    \$(\"#unidad> option[value=$_GET[int]]\").attr(\"selected\", \"selected\");
                    \$(\"#data\").html(\"<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>\");
                    \$.post(\"/modelo/segvial/altauda.php\", {accion:\"load\", unidad:$_GET[int]}, function(data){\$(\"#data\").html(data);});
            </script>";
  }
?>
</body>
</html>

