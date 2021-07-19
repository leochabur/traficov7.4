<?php
     session_start();
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
                                                       $("#cargar").button().click(function(){
                                                                                              var datos = $("#upuda").serialize();
                                                                                              $('#data').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                                              $.post("/modelo/ordenes/modkmtpo.php", datos, function(data){$('#data').html(data);});
                                                       });
                                                       <?php
                                                            if (isset($_GET[des])){
                                                               print "$('#ori option[value=$_GET[des]]').attr('selected','selected');";
                                                               print "$.mask.definitions['~']='[012]';
                                                                       $.mask.definitions['%']='[012345]';
                                                                       $('.hora').mask('~9:%9');"; ?>
                                                             $("#savedata").button().click(function(){

                                                                                                      $('#data').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                                                      $.post("/modelo/ordenes/modkmtpo.php",
                                                                                                            {or: $('#ori').val(), de: $('#des').val(), km:$('#km').val(), to:$('#tpo').val(), accion:'sver'},
                                                                                                            function(data){
                                                                                                                           var response = $.parseJSON(data);
                                                                                                                           $('#data').html(response.msge);
                                                                                                                           });
                                                                                                      });
                                                                       
                                                       <?php
                                                            }
                                                            if (isset($_GET[has])){
                                                               print "$('#des option[value=$_GET[has]]').attr('selected','selected');";
                                                            }

                                                       ?>
                                                       $('select').selectmenu({width: 350});


                          });
                          

</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Modificar KM - Tiempo Recorridos</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar por:</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Origen</td>
                                    <td>
                                        <select id="ori" name="ori" class="ui-widget ui-widget-content  ui-corner-all">
                                            <option value="0">Todos</option>
                                                <?php
                                                     armarSelect("ciudades", "ciudad", "id", "ciudad", "id_estructura = $_SESSION[structure]");
                                                ?>
                                        </select>
                                    </td>
                                    <td>Destino</td>
                                    <td>
                                        <select id="des" name="des" class="ui-widget ui-widget-content  ui-corner-all">
                                            <option value="0">Todos</option>
                                                <?php
                                                     armarSelect("ciudades", "ciudad", "id", "ciudad", "id_estructura = $_SESSION[structure]");
                                                ?>
                                        </select>
                                    </td>
                                    <td><input type="button" value="Cargar Recorridos" id="cargar"></td>
                                </tr>
                                <?php
                                     if (isset($_GET[des])){
                                ?>
                                <tr>
                                    <td>KM</td>
                                    <td align='center'><input type='text' size='5' id='km' style='text-align:right' class="ui-widget ui-widget-content  ui-corner-all"></td>
                                    <td>Tiempo (HH:MM)</td>
                                     <td align='center'><input type='text' size='5' id='tpo' style='text-align:right' class='ui-widget ui-widget-content  ui-corner-all hora'></td>
                                    <td><input type="button" value="Guardar Datos" id="savedata"></td>
                                </tr>
                                <?php
                                     }
                                
                                ?>
                         </table>
                         </fieldset>
                         <br>
                         <div id="data">
                              <?//include_once("../../../modelo/informes/trafico/diagdia.php");?>
                         </div>
            </fieldset>
            <input type="hidden" name="accion" id="accion" value="load">
            <input type='hidden' id='posy' name='posy'>
         </form>

</body>
</html>

