<?php
     session_start();
     include_once('../../main.php');
     include_once('../../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
?>

<?php
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

 <script type="text/javascript" src="/vista/js/jquery.tablesorter.js"></script>

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

.padd{
  padding-top: 0.5em;
}

</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#desde,#hasta').datepicker({dateFormat:'dd/mm/yy'});
                                                       $("#cargar").button().click(function(){                                                                                            
                                                                                              var data = $("#upuda").serialize();
                                                                                              $.post("/modelo/informes/cria/factvta.php", 
                                                                                                     data, 
                                                                                                     function(data){
                                                                                                                    var response = $.parseJSON(data);
                                                                                                                    if (response.ok){
                                                                                                                      window.location.href = "/vista/informes/cria/addordfac.php?fec="+$('#desde').val()+"&has="+$('#hasta').val()+"&fv="+response.fact;
                                                                                                                    }
                                                                                                                    else{
                                                                                                                      alert(response.msge);
                                                                                                                    }
                                                                                                     });
                                                                                             
                                                       });
                                                       
                                                       $("#str option[value=1]").attr("selected", "selected");
                                                       $('#str, #type, #mes').selectmenu({width: 350});
                                                       $.post("/modelo/informes/cria/kmrecli.php",{accion:'ldcli', str:$('#str').val()}, function(data){$('#clis').html(data);});
                                                       $('#str').change(function(){
                                                                                  $.post("/modelo/informes/cria/kmrecli.php",{accion:'ldcli', str:$('#str').val()}, function(data){
                                                                                                                                                                                   $('#clis').html(data);
                                                                                                                                                                                   });
                                                                                  });


                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Nueva Factura Venta</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Datos factura</legend>
                         <table border="0" align="center" width="75%" name="tabla">
                                <tr>
                                    <td>Estructura</td>
                                    <td>
                                        <select id="str" name="str" class="ui-widget ui-widget-content  ui-corner-all">
                                                <?php
                                                     armarSelect('estructuras', 'nombre', 'id', 'nombre', "");
                                                ?>
                                        </select>
                                    </td>

                                    <td>Cliente</td>
                                    <td id="clis"></td>
                                </tr>
                                <tr>
                                        <td class="padd">Desde</td>
                                        <td class="padd"><input type="text" size="20" id="desde" name="desde"></td>
                                        <td class="padd">Hasta</td>
                                        <td class="padd"><input type="text" size="20" id="hasta" name="hasta"></td>
                                </tr>
                                <tr >
                                        <td class="padd">Observacion</td>
                                        <td class="padd" colspan="3"><input type="text" size="40" id="observa" name="observa"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" align="right">
                                        <input type="button" value="Crear Factura" id="cargar">
                                    </td>
                                </tr>
                         </table>
<div id="files">
</div>
                         </fieldset>
                         <br>
                         <div id="dats"></div>

            </fieldset>
            <input type="hidden" name="accion" value="addfact">
         </form>

</body>
</html>

