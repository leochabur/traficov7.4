<?php
error_reporting(E_ALL & ~E_NOTICE);
     session_start();
     include_once('../main.php');
     include_once('../paneles/viewpanel.php');
     include_once('../../modelsORM/controller.php');     
     define('RAIZ', '');
     define('STRUCTURED', $_SESSION['structure']);

     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>

<link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
<link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
<link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
<link type="text/css" href="<?php echo RAIZ;?>/vista/css/DataTables/datatables.min.css" rel="stylesheet" />
<link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet" />
<link rel="stylesheet" href="/vista/css/jquery.treetable.css" />
<link rel="stylesheet" href="/vista/css/jquery.treetable.theme.default.css" />
    
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
<script src="/vista/js/jquery.treetable.js"></script>
  <script defer src="https://use.fontawesome.com/releases/v5.0.9/js/all.js" integrity="sha384-8iPTk2s/jMVj81dnzb/iFR2sdA7u06vHJyyLlAd4snFpCl/SnyUjRrbdJsw1pGIl" crossorigin="anonymous"></script>

<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/DataTables/datatables.min.js"></script>


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
                                  $(':button, :submit').button();    
                                  $('.dayTarifa, #articulos').selectmenu({width: 200}); 
                                  $('.deltpo').click(function(){
                                                                var td = $(this);
                                                                if (confirm('Seguro eliminar el '+td.data('msge')+'?')){
                                                                    
                                                                    $.post('/vista/servicios/addtrfNew.php',
                                                                           {accion: 'delOptionTarifa', delAct: td.data('param'), param: td.data('tpo'), ts: td.data('tfa')},
                                                                           function(data){
                                                                                          var response = $.parseJSON(data);
                                                                                          if (response.status){
                                                                                            location.reload();
                                                                                          }
                                                                                          else{
                                                                                              alert(response.message);
                                                                                          }
                                                                            });
                                                                } 
                                                                });    
    });
</script>

<body>
<?php
     menu();
     $tarifa = find('TarifaServicio', $_GET['trf']);
     $diasSemana = diasSemana();
     //die(" :".$tarifa->getFacturacion()->getCliente());
     $cronogramas = listadoCronogramas($tarifa->getFacturacion()->getCliente());
     $articulos = articulosClientesOption($tarifa->getFacturacion()->getCliente());
     $tipos = listaTiposVehiculos($_SESSION['structure']);
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Detalle de Tarifa</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                    <legend class="ui-widget ui-widget-header ui-corner-all">Nombre: <?php print $tarifa->getNombre(); ?></legend>
                        <table border="0">
                            <tr>
                              <td valign='top'><?php print getFormDiasTarifa($tarifa); ?></td>
                              <td valign='top'><?php print getFormDiasCronogramas($tarifa); ?></td>
                              <td valign='top'><?php print getFormTarifas($tarifa); ?></td>
                            </tr>
                        </table>
                     </fieldset>
            </fieldset>
         </form>

<div id="addDiaSemana" title="Agregar dia semana">
    <form id="formAddDay" action="/vista/servicios/addtrfNew.php">
        <table>
          <tr>
            <td>
              <select id="agregarDia" name="param" class="dayTarifa">
                <?php
                          foreach ($diasSemana as $dia)
                            print "<option value='".$dia->getId()."'>$dia</option>";

                ?>
              </select>
            </td>
            <td>
                <input type="submit" value="Agregar"/>
            </td>
          </tr>
        </table> 
        <input type="hidden" name="accion" value="addOptionTf"> 
        <input type="hidden" name="addAct" value="day">       
        <input type="hidden" name="ts" value="<?php print $tarifa->getId(); ?>">       
    </form>
</div>

<div id="addCronograma" title="Agregar servicio a tarifa">
    <form id="formAddDay" action="/vista/servicios/addtrfNew.php">
        <table id="tableCronos" class="table table-zebra">
          <thead>
            <tr>
              <th>Cronograma</th>
             <th>Origen</th>        
             <th>Destino</th>                  
             <th>Accion</th>                   
            </tr>
          </thead>
          <tbody>
                <?php
                          foreach ($cronogramas as $crono)
                            print "<tr>
                                      <td>$crono</td>
                                      <td>".$crono->getOrigen()."</td>
                                      <td>".$crono->getDestino()."</td>
                                      <td class='tdAddCron' data-tfa='".$tarifa->getId()."' data-cron='".$crono->getId()."'><i class='fas fa-plus'></i></td>
                                    </tr>";

                ?>
          </tbody>
        </table> 
        <input type="hidden" name="accion" value="addOptionTf"> 
        <input type="hidden" name="addAct" value="day">       
        <input type="hidden" name="ts" value="<?php print $tarifa->getId(); ?>">       
    </form>
</div>

<div id="addTipoVehiculo" title="Agregar tipo vehiculo">
    <form id="formAddTipo" action="/vista/servicios/addtrfNew.php">
        <table class="table">
          <tr>
            <td>Articulo</td>
            <td>Tipo Vehihculo</td>
            <td>Presupuestada</td>
            <td></td>
          </tr>
          <tr>
            <td>
              <select id="articulos" name="artNT">
                <?php
                    print $articulos;

                ?>
              </select>
            </td>
            <td>
              <select id="articulos" name="typeNT">
                <?php
                    print $tipos;

                ?>
              </select>
            </td>   
            <td>
              <input type="checkbox" name="presupuestada">
            </td>         
            <td>
                <input type="submit" value="Agregar"/>
            </td>
          </tr>
        </table> 
        <input type="hidden" name="accion" value="addOptionTf"> 
        <input type="hidden" name="addAct" value="type">       
        <input type="hidden" name="ts" value="<?php print $tarifa->getId(); ?>">       
    </form>
</div>

</body>
<script type="text/javascript">
    $("#formAddTipo").submit(function(event){
                                            var form = $(this);
                                            event.preventDefault();
                                            if (confirm('Seguro agregar el tipo de vehiculo?')){
                                              $.post(form.attr('action'),
                                                     form.serialize(),
                                                     function(data){
                                                                        var response = $.parseJSON(data);
                                                                        if (response.status){
                                                                          location.reload();
                                                                        }
                                                                        else{
                                                                          alert(response.message);
                                                                        }
                                                     });
                                            }
    });

    $( "#addTipoVehiculo" ).dialog({
                                  resizable: true,
                                  height: "auto",
                                  width: "650",
                                  modal: true, 
                                  autoOpen: false
                                });
    $("#addTfa").click(function(){
        $( "#addTipoVehiculo" ).dialog("open");
    });    

    $(".tdAddCron").click(function(event){
                                            var td = $(this);

                                            if (confirm('Seguro agregar el servicio?')){
                                              $.post('/vista/servicios/addtrfNew.php',
                                                     {accion: 'addOptionTf', addAct: 'crono', ts: td.data('tfa'), param: td.data('cron')},
                                                     function(data){
                                                                        var response = $.parseJSON(data);
                                                                        if (response.status){
                                                                          alert('Servicio agregado exitosamente!')
                                                                        }
                                                                        else{
                                                                          alert(response.message);
                                                                        }
                                                     });
                                            }
    });  
    $('#tableCronos').dataTable({'scrollY':200,
                    'scrollCollapse': true,
                    'jQueryUI': true,
                     'orderClasses': false,
                     'paging': false
                  });  
    $( "#addCronograma" ).dialog({
                                  resizable: true,
                                  height: "auto",
                                  width: "600",
                                  modal: true, 
                                  autoOpen: false,
                                  close: function() {
                                                      location.reload();
                                  }
                                });
    $("#addCron").click(function(){
        $( "#addCronograma" ).dialog("open");
    });

    $("#formAddDay").submit(function(event){
                                            var form = $(this);
                                            event.preventDefault();
                                            if (confirm('Seguro agregar el dia?')){
                                              $.post(form.attr('action'),
                                                     form.serialize(),
                                                     function(data){
                                                                        var response = $.parseJSON(data);
                                                                        if (response.status){
                                                                          location.reload();
                                                                        }
                                                                        else{
                                                                          alert(response.message);
                                                                        }
                                                     });
                                            }
    });

    $( "#addDiaSemana" ).dialog({
                                  resizable: false,
                                  height: "auto",
                                  width: "auto",
                                  modal: true, 
                                  autoOpen: false});
    $("#addDay").click(function(){
        $( "#addDiaSemana" ).dialog("open");
    });

</script>
</html>

<?php 

    function getFormDiasTarifa($tarifa){
        $tabla = '<fieldset class="ui-widget ui-widget-content ui-corner-all">
                     <legend class="ui-widget ui-widget-header ui-corner-all">Dias aplicados</legend>';
        $tabla.= "<table class='table table-zebra'>
                  <tr>
                      <td>Dia Semana</td>
                      <td>Quitar</td>
                  </tr>";
        foreach ($tarifa->getDiasSemana() as $dia) {
            $tabla.= "<tr>
                          <td>$dia</td>
                          <td class='deltpo' data-msge='Dia' data-tfa='".$tarifa->getId()."' data-param='day' data-tpo='".$dia->getId()."'><i class='fas fa-times fa-2x'></i></td>                          
                      </tr>";
        }
        $tabla.="</table>
                <input type='button' value='Agregar Dia' id='addDay'/>
                </fieldset>";
        return $tabla;
    }

    function getFormDiasCronogramas($tarifa){
        $tabla = '<fieldset class="ui-widget ui-widget-content ui-corner-all">
                     <legend class="ui-widget ui-widget-header ui-corner-all">Servicios aplicados</legend>';      
        $tabla.= "<table class='table table-zebra'>
                  <tr>
                      <td>Cronograma</td>
                      <td>Quitar</td>
                  </tr>";
        foreach ($tarifa->getCronogramas() as $crono) {
            $tabla.= "<tr>
                          <td>$crono</td>
                          <td class='deltpo' data-msge='Servicio' data-tfa='".$tarifa->getId()."' data-param='crono' data-tpo='".$crono->getId()."'><i class='fas fa-times fa-2x'></i></td>
                      </tr>";
        }
        $tabla.="</table>
                 <input type='button' value='Agregar Servicio' id='addCron'/>      
                </fieldset>";
        return $tabla;
    }    

    function getFormTarifas($tarifa){
        $tabla = '<fieldset class="ui-widget ui-widget-content ui-corner-all">
                     <legend class="ui-widget ui-widget-header ui-corner-all">Tipo Vehiculo</legend>';      
        $tabla.= "<table class='table table-zebra'>
                  <tr>
                      <td>Tipo Unidad</td>
                      <td>Articulo</td>
                      <td>Presupuestada</td>
                      <td>Quitar</td>
                  </tr>";
        foreach ($tarifa->getTarifasTipoVehiculo() as $tipo) {
            $tabla.= "<tr>
                          <td>".$tipo->getTipo()."</td>
                          <td>$tipo</td>
                          <td>".($tipo->getDefecto()?'SI':'NO')."</td>   
                          <td class='deltpo' data-msge='Tipo Vehiculo' data-tfa='".$tarifa->getId()."' data-param='tpo' data-tpo='".$tipo->getId()."'><i class='fas fa-times fa-2x'></i></td>                       
                      </tr>";
        }
        $tabla.="</table>
                  <input type='button' value='Agregar Vehiculo' id='addTfa'/>       
                  </fieldset>";
        return $tabla;
    } 

?>

