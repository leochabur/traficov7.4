<?php
     session_start();
     include_once('../main.php');
     include_once('../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     include('../../modelsORM/controller.php');
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>

   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.dataTables.css" rel="stylesheet" />
    <link rel="stylesheet" href="/vista/css/jquery.treetable.css" />
    <link rel="stylesheet" href="/vista/css/jquery.treetable.theme.default.css" />
    
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
        <script src="/vista/js/jquery.treetable.js"></script>

  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.dataTables.min.js"></script>


<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }

.small.button, .small.button:visited {
font-size: 11px ;
}

input.text { margin-bottom:12px; width:95%; padding: .4em; }
#newuda .error{
	font-size:0.8em;
	color:#ff0000;
}

</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $("#save").button().click(function(){
                                                                                              var btn = $(this);
                                                                                              btn.hide();
                                                                                              var data = $("#upuda").serialize();
                                                                                              $.post("/modelo/servicios/valcro.php",
                                                                                                     data,
                                                                                                     function(dats){
                                                                                                                    var res = $.parseJSON(dats);
                                                                                                                    if (!res.ok){
                                                                                                                       alert(res.message);
                                                                                                                    }
                                                                                                                    else{
                                                                                                                         $('#upuda')[0].reset();
                                                                                                                    }
                                                                                                                    btn.show();
                                                                                                                    });
                                                       });
                                                       
                                                       $('#clientes').selectmenu({width: 350});


                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Cargar Articulo Cliente</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Cliente</td>
                                    <td>
                                        <select id="clientes" name="clientes">
                                                <option value="0">Seleccione un cliente</option>
                                                <?php
                                                     print clientesOptions();
                                                ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Codigo Articulo</td>
                                    <td>
                                        <input type="text" size="40" name="desc" class="ui-widget ui-widget-content ui-corner-all">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="right"><input type="button" value="Guardar Articulo" id="save"></td>
                                </tr>

                         </table>

            </fieldset>
            <input type="hidden" name="accion" value="addart">
         </form>

</body>
</html>

