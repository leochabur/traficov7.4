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
    
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>

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

#example{
	color:#000000;
}
#example tbody tr:nth-child(odd){
    background: #D0D0D0;

}

#example tbody tr:nth-child(even){
    background: #FFFFFF;

}

</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#desde,#hasta').datepicker({dateFormat:'dd/mm/yy'});
                                                       $("#cargar").button().click(function(){
                                                                                              $('#dats').html("<div align='center'><img  alt='cargando' src='../../ajax-loader.gif' /></div>");
                                                                                              $.post("/modelo/informes/cria/lstuda.php", {type:$('#type').val(),accion:'reskm', desde: $('#desde').val(), hasta: $('#hasta').val(), str: $('#str').val(), tpo:$('#tipo').val(), cli:$('#clientes').val()}, function(data){  $('#dats').html(data);
                                                                                                                                                                           });
                                                       });

                                                       $('#str').change(function(){
                                                                                  $.post("/modelo/informes/cria/kmrecli.php",{accion:'ldcli', str:$('#str').val()}, function(data){
                                                                                                                                                                                   $('#clis').html(data);
                                                                                                                                                                                   });
                                                                                  });
                                                       $("#str option[value=1]").attr("selected", "selected");
                                                       $('#str, #type, #tipo').selectmenu({width: 350});
                                                       $.post("/modelo/informes/cria/kmrecli.php",{accion:'ldcli', str:$('#str').val()}, function(data){$('#clis').html(data);});

                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Resumen de Internos Utilizados por Cliente</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar Datos</legend>
                         <table border="0" align="center" width="50%" name="tabla">
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
                                    <td id="clis">
                                    </td>
                                    </tr>
                                <tr>
                                    <td>Tipo Servicio</td>
                                    <td>
                                        <select id="tipo" name="tipo" class="ui-widget ui-widget-content  ui-corner-all">
                                                <?php
                                                     armarSelect('tiposervicio', 'tipo', 'id', 'tipo', "id_estructura = $_SESSION[structure]");
                                                ?>
                                        </select>
                                    </td>

                                    <td></td>
                                    <td id="clis">
                                    </td>
                                    </tr>
                                    <tr>
                                    <td>Desde</td>
                                    <td><input id="desde" name="desde"  type="text" size="20"></td>
                                    <td>Hasta</td>
                                    <td><input id="hasta" name="hasta" type="text" size="20"></td>
                                    </tr>

                                    <tr>
                                    <td colspan="4" align="right">
                                        <input type="button" value="Cargar Ordenes" id="cargar">
                                    </td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <div id="dats"></div>

            </fieldset>
            <input type="hidden" name="accion" id="accion" value="list">
            <input type="hidden" name="order" id="order">
         </form>

</body>
</html>

