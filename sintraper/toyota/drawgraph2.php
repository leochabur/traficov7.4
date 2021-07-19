<?php
     session_start();
     include_once('./main.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
?>

<?php
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>

   <link type="text/css" href="./css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="./css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="./css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="./css/jquery.dataTables.css" rel="stylesheet" />
    
<script type="text/javascript" src="./js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="./js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="./js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="./js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="./js/jquery.ui.datepicker-es.js"></script>

  <script type="text/javascript" src="./js/jquery.dataTables.min.js"></script>


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
                                                       $('#desde,#hasta').datepicker({dateFormat:'dd/mm/yy'});
                                                       $("#cargar").button().click(function(){
                                                                                              var ec = 'efc';

                                                                                              if ($('#cnf').attr('checked')){
                                                                                                  ec = 'cnf';

                                                                                              }
                                                                                              $('#dats').html("<div align='center'><img  alt='cargando' src='./ajax-loader.gif' /></div>");
                                                                                              var des = $('#desde').val();
                                                                                              var has = $('#hasta').val();
                                                                                              var tipo = $('#tipo').val();
                                                                                              var tur = $('#turno').val();

                                                                                              var txt_tur = $('#tipo :selected').text();

                                                                                              $.post("./graph.php",{desde:des, hasta:has, ts:tipo, tu:tur, txtt: txt_tur, c_e:ec}, function(data){

                                                                                                                                                                                                   $('#order').html(data);
                                                                                                                                                                                                   });


                                                       });

                                                       $('#tipo, #turno, #clis').selectmenu({width: 350});


                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Graficas de Confiablidad y Eficiencia</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar Datos</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Cliente</td>
                                    <td>
                                        <select id="clis" name="clis" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option value="1">TOYOTA</option>
                                        </select>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Tipo</td>
                                    <td>
                                        <select id="tipo" name="tipo" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option value="1">PRODUCCION</option>
                                                <option value="2">ADMINISTRACION</option>
                                        </select>
                                    </td>
                                    <td>Turno</td>
                                    <td>
                                        <select id="turno" name="turno" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option value="1"><?php print htmlentities('MAÑANA'); ?></option>
                                                <option value="2">TARDE</option>
                                                <option value="3">NOCHE</option>
                                        </select>
                                    </td>

                                    </tr>
                                    <tr>
                                    <td>Desde</td>
                                    <td><input id="desde" name="desde"  type="text" size="20"></td>
                                    <td>Hasta</td>
                                    <td><input id="hasta" name="hasta" type="text" size="20"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                    </tr>
                                    <tr>
                                    <td>Confiabilidad</td>
                                    <td><input id="cnf" type="radio" name="cnf" checked value="cnf"></td>
                                    <td>Eficiencia</td>
                                    <td><input id="efc" type="radio" name="cnf" value="efc"></td>
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

