<?php
     session_start();
     include_once('../main.php');
     include_once('../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
?>

<?php
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


<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }

.small.button, .small.button:visited {
font-size: 11px ;
}

input.text { margin-bottom:12px; width:95%; padding: .4em; }
#example{
	font-size:0.8em;
	color:#000000;
}
#example tbody tr:nth-child(odd){
    background: #D0D0D0;

}

#example tbody tr:nth-child(even){
    background: #FFFFFF;

}

.navigation{
            background-color: #DCE697;
}

</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#desde,#hasta, #inicioev').datepicker({dateFormat:'dd/mm/yy'});
                                                       
                                                       $("#addm").button().click(function(){
                                                                                              $.post("/modelo/taller/elrstc.php", {accion:'adm', mca:$('#nmca').val()}, function(data){
                                                                                                                                                                                         var response = $.parseJSON(data);
                                                                                                                                                                                         if (response.status){
                                                                                                                                                                                            $("#marcas").append(new Option(response.mca, response.id));
                                                                                                                                                                                            $("#marcas option[value="+response.id+"]").attr('selected','selected');
                                                                                                                                                                                            $('#nmca').val('');
                                                                                                                                                                                            $('#marcas').selectmenu({width: 350});
                                                                                                                                                                                         }
                                                                                                                                                                                         else{
                                                                                                                                                                                              alert(response.msge);
                                                                                                                                                                                         }

                                                                                                                                                                                         });
                                                       });
                                                       
                                                       $("#addp").button().click(function(){
                                                                                              $.post("/modelo/taller/elrstc.php", {accion:'adp', prov:$('#npro').val()}, function(data){
                                                                                                                                                                                         var response = $.parseJSON(data);
                                                                                                                                                                                         if (response.status){
                                                                                                                                                                                            $("#proov").append(new Option(response.pro, response.id));
                                                                                                                                                                                            $("#proov option[value="+response.id+"]").attr('selected','selected');
                                                                                                                                                                                            $('#npro').val('');
                                                                                                                                                                                            $('#proov').selectmenu({width: 350});
                                                                                                                                                                                         }
                                                                                                                                                                                         else{
                                                                                                                                                                                              alert(response.msge);
                                                                                                                                                                                         }

                                                                                                                                                                                         });
                                                       });
                                                       
                                                       $("#addpr").button().click(function(){
                                                                                              $.post("/modelo/taller/elrstc.php", $('#upuda').serialize(), function(data){
                                                                                                                                                                                         var response = $.parseJSON(data);
                                                                                                                                                                                         if (response.status){
                                                                                                                                                                                            $('#desc').val('');
                                                                                                                                                                                            $('#code').val('');
                                                                                                                                                                                            $.post("/modelo/taller/elrstc.php", {accion:'lirp', proov:$('#proov').val(), marcas:$('#marcas').val()}, function(data){
                                                                                                                                                                                                                                                                                                                    $('#stock').html(data);
                                                                                                                                                                                                                                                                                                                    });
                                                                                                                                                                                         }
                                                                                                                                                                                         else{
                                                                                                                                                                                              alert(response.msge);
                                                                                                                                                                                         }

                                                                                                                                                                                         });
                                                       });
                                                       $("#ineval").button().click(function(){
                                                                                              $.post("/modelo/taller/elrstc.php", $('#initeval').serialize(), function(data){
                                                                                                                                                                             var response = $.parseJSON(data);
                                                                                                                                                                             if (response.status){
                                                                                                                                                                                            $("#eval").hide();
                                                                                                                                                                                            $.post("/modelo/taller/elrstc.php", {accion:'lirp', proov:$('#proov').val(), marcas:$('#marcas').val()}, function(data){
                                                                                                                                                                                                                                                                                                                    $('#stock').html(data);
                                                                                                                                                                                                                                                                                                                    });
                                                                                                                                                                             }
                                                                                                                                                                             else{
                                                                                                                                                                                              alert(response.msge);
                                                                                                                                                                             }
                                                                                                                                                                             });
                                                                                              });

                                                       
                                                       $('#proov, #marcas').selectmenu({width: 350});
                                                       $('#interno').selectmenu({width: 150});
                                                       $.post("/modelo/taller/elrstc.php", {accion:'lirp'}, function(data){  $('#stock').html(data);});
                                                       $("#eval").hide();

                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Desvio de productos</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Gestion productos</legend>
                         <table border="0" align="center" width="75%" name="tabla">
                                <tr>
                                    <td>Marca producto</td>
                                    <td>
                                        <select id="marcas" name="marcas" class="ui-widget ui-widget-content  ui-corner-all">
                                               <option value="0">TODOS</option>
                                                <?php
                                                     armarSelect('marca_repuesto', 'marca', 'id', 'marca', "");
                                                ?>
                                        </select>
                                    </td>
                                    <td align="left"><input type="text" size="28" placeholder="Ingrese nueva marca" class="ui-widget ui-widget-content ui-corner-all" id="nmca"><input id="addm" type="button" value="Guardar Marca"></td>
                                </tr>
                                <tr>
                                    <td>Proveedores</td>
                                    <td>
                                        <select id="proov" name="proov" class="ui-widget ui-widget-content  ui-corner-all">
                                        <option value="0">TODOS</option>
                                                <?php
                                                     armarSelect('proveedores', 'proveedor', 'id', 'proveedor', "");
                                                ?>
                                        </select>
                                    </td>
                                    <td align="left"><input type="text" size="28" placeholder="Ingrese un nuevo proveedor" class="ui-widget ui-widget-content ui-corner-all" id="npro"><input id="addp" type="button" value="Guardar Proveedor"></td>
                                </tr>
                                <tr>
                                    <td>Descripcion del producto</td>
                                    <td colspan="2">
                                        <input type="text" name="desc" id="desc" size="45" placeholder="Ingrese la descripcion del producto" class="ui-widget ui-widget-content ui-corner-all">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Codigo de Barras</td>
                                    <td colspan="2">
                                        <input type="text" name="code" id="code" size="45" placeholder="Ingrese el codigo de barras" class="ui-widget ui-widget-content ui-corner-all">
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td align="left"><input id="addpr" type="button" value="Guardar Producto"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3"><br></td>
                                </tr>
                                <tr>
                                    <td>Repuestos</td>
                                    <td id="stock" colspan="2"></td>
                                </tr>
                         </table>
                         </fieldset>
                               <input type="hidden" name="accion" id="accion" value="addpr">
         </form>
         <form id="initeval">
                         <div id="eval">
                         <fieldset class="ui-widget ui-widget-content ui-corner-all">
                         <legend class="ui-widget ui-widget-header ui-corner-all" id="txtlgn">Evaluacion de productos</legend>
                          <table border="0" align="center" width="100%" name="tabla">
                                <tr>
                                    <td>Iniciar Evaluacion el....</td>
                                    <td align="left">
                                        <input type="text" size="20" id="inicioev" name="inicioev" class="ui-widget ui-widget-content ui-corner-all" >
                                    </td>
                                    <td>Interno</td>
                                    <td align="left">
                                        <select id="interno" name="interno" class="ui-widget ui-widget-content  ui-corner-all">
                                                <?php
                                                     armarSelect('unidades', 'interno', 'id', 'interno', "(activo) and (id_propietario = 1)");
                                                ?>
                                        </select>
                                    </td>
                                    <td>Evualacion km</td>
                                    <td align="left">
                                        <input type="text" size="10" id="evakm" name="evakm" class="ui-widget ui-widget-content ui-corner-all" >
                                    </td>
                                    <td>Evualacion dias</td>
                                    <td align="left">
                                        <input type="text" size="10" id="evadias" name="evadias" class="ui-widget ui-widget-content ui-corner-all" >
                                    </td>
                                    <td><input type="button" value="Iniciar Evaluacion" id="ineval"></td>
                                </tr>
                         </table>
                         </fieldset>
                         </div>
                         <br>
                         <div id="dats"></div>

            </fieldset>
            <input type="hidden" name="accion" id="accion" value="inev">
            <input type="hidden" name="repto" id="repto">
         </form>

</body>
</html>

