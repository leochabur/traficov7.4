<?php
     session_start();
//error_reporting(E_ALL & ~E_NOTICE);     
     include_once('../main.php');

     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
     include '../../modelsORM/manager.php';
     include_once '../../modelsORM/call.php';  
     include_once '../../modelsORM/src/AccionUnidad.php';     
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
    <link href="/vista/css/estilos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
     <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/scroll-table/jquery.chromatable.js"></script>
       <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>


  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/tables/jquery.tablehover.js"></script>
   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">

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

         .rojo {background-color: #DD614A;}
         .amarillo{background-color: #FFFF80;}
         .verde{background-color: #C0FFC0;}

</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                                                       $('select').selectmenu({width: 200});
                                                       $('#print').button().click(function(event){                
                                                                                                  var a = $(this);                                                   
                                                                                                  var fecha = $("#fecha").val();
                                                                                                  if (!fecha){
                                                                                                    event.preventDefault();
                                                                                                    alert('Debe seleccionar una fecha');
                                                                                                  }
                                                                                                  else{
                                                                                                    a.attr("href", "/modelo/taller/planillaCarga.php?fec="+fecha+"&pr="+$('#producto').val());
                                                                                                  }
                                                       });

                                                       $('#view').button().click(function(event){                
                                                                                                  event.preventDefault();                                                
                                                                                                  var fecha = $("#fecha").val();
                                                                                                  if (!fecha){                                                                                         alert('Debe seleccionar una fecha');       

                                                                                                  }
                                                                                                  else{
                                                                                                    $('#data').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");

                                                                                                    $.post('/modelo/taller/printpl.php', $('#dataview').serialize(),
                                                                                                           function(data){
                                                                                                                          $('#data').html(data);
                                                                                                           });
                                                                                                  }
                                                       });                                                       

                                                       });
</script>

<body>
<?php
     menu();
        

     //   $a = $entityManager->createQuery("SELECT a, max(a.fecha) fecha FROM AccionUnidad a GROUP BY a.unidad, a.accion ORDER BY a.unidad");     
     //$optTurnos.="<option value='".$turno->getId()."'>$turno</option>";
?>
<br>
             <fieldset class="ui-widget ui-widget-content ui-corner-all">
                     <legend class="ui-widget ui-widget-header ui-corner-all">Imprimir planilla carga combustible</legend>
                        <form id="dataview">
                         <table border="0" align="center" name="tabla">
                                <tr>
                                    <td>
                                      Fecha: <input type="text" name="fecha" id="fecha" class=" ui-corner-all">
                                    </td>
									<td>
                                      <select id="producto" name="producto">
                                                  <?php
                                                        $destinos = $entityManager->createQuery("SELECT t FROM TipoFluido t")->getResult();
                                                        foreach ($destinos as $destino) {
                                                          print "<option value='".$destino->getId()."''>".$destino->getTipo()."</option>";
                                                        }                                                    
                                                  ?>
                                                </select>
									</td>
                                      <td>
                                        <a id="print" href="/modelo/taller/planillaCarga.php" target="_blanck">Imprimir Planilla</a>
                                    </td>       
                                      <td>
                                        <a id="view" href="/modelo/taller/planillaCarga.php" target="_blanck">Ver en Pantalla</a>
                                    </td>                                                                   
                                </tr>
                         </table>
                         <input type="hidden" name="accion" value="viewload">
                       </form>
                       <div id="data"></div>
            </fieldset>
</body>
</html>

