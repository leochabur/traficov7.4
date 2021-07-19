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
                                                       $('#desde,#hasta').datepicker({dateFormat:'dd/mm/yy'});

                                                       $("#cargar").button().click(function(){
                                                                                              var datos = $('#upuda').serialize();
                                                                                              $('#data').html("<div align='center'><img  alt='cargando' src='../../ajax-loader.gif' /></div>");

                                                                                              $.post("/modelo/segvial/reasnt.php", datos, function(data){  $('#dats').html(data);
                                                                                                                                                                           });
                                                       });
                                                       $('#str, #order').selectmenu({width: 350});
                                                       $('#str').change(function(){
                                                                                  $.post("/modelo/segvial/reasnt.php",{accion:'ldint', str:$('#str').val()}, function(data){
                                                                                                                                                                                   $('#int').html(data);
                                                                                                                                                                                   });
                                                                                  $.post("/modelo/segvial/reasnt.php",{accion:'ldcond', str:$('#str').val()}, function(data){
                                                                                                                                                                                   $('#cond').html(data);
                                                                                                                                                                                   });
                                                                                  });
                                                       $.post("/modelo/segvial/reasnt.php",{accion:'ldint'}, function(data){
                                                                                                                                                                                   $('#int').html(data);
                                                                                                                                                                                   });
                                                       $.post("/modelo/segvial/reasnt.php",{accion:'ldcond'}, function(data){
                                                                                                                                                                                   $('#cond').html(data);
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
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Siniestros generados</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar siniestros</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Conductores</td>
                                    <td id="cond"></td>
                                </tr>
                                <tr>
                                    <td>Internos</td>
                                    <td id="int"></td>
                                </tr>
                                <tr>
                                    <td>Desde</td>
                                    <td><input id="desde" name="desde"  type="text" size="20"></td>
                                    <td>Hasta</td>
                                    <td><input id="hasta" name="hasta" type="text" size="20"></td>
                                </tr>
                                <tr>
                                    <td>Ordenar por...</td>
                                    <td><select size="1" id="order" name="order">
                                                <option value="siniestro_numero">Numero siniestro</option>
                                                <option value="apellido">Conductor</option>
                                                <option value="interno">Interno</option>
                                                <option value="ciudad">Ciudad Siniestro</option>
                                                </select>
                                    </td>
                                    <td></td>
                                    <td>Asc.<input name="asds" type="radio" checked value="ASC">Des.<input name="asds" type="radio" value="DESC"></td>
                                </tr>
                                <tr>
                                    <td>Num Sinisetro</td>
                                    <td><input id="nrosin" name="nrosin"  type="text" size="20" class="ui-widget ui-widget-content ui-corner-all"></td>
                                    <td>Num. Sin. Compa&ntilde;ia</td>
                                    <td><input id="nrosincomp" name="nrosincomp" type="text" size="20" class="ui-widget ui-widget-content ui-corner-all"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" align="right">
                                        <input type="button" value="Cargar Siniestros" id="cargar">
                                    </td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <div id="dats"></div>

            </fieldset>
            <input type="hidden" name="accion" id="accion" value="list">
         </form>

</body>
</html>

