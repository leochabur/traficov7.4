<?php
       session_start();
       set_time_limit(0);

     include('../reporteexcel/lib/PHPExcel/PHPExcel.php');
     include( 'bdadmin.php' );
     include( 'dateutils.php' );
     include('./main.php');
     include('./core_chart.php');
     include_once('../modelsORM/call.php');  
     $_SESSION['structure'] = 1; 
     //define(RAIZ, '');
   //  define(STRUCTURED, 1);
?>

<?php

     encabezado('Menu Principal - Sistema de Administracion - Campana');
       $origen=0;
       $destino=0;
       $servicio=0;
        if (isset($_POST['city_origen'])){
           $origen = $_POST['fcity_origen'];
        }
        if (isset($_POST['city_destino'])){
           $destino = $_POST['fcity_destino'];
        }
        if (isset($_POST['servicios'])){
           $servicio = $_POST['fservicios'];
        }
        if (isset($_POST['cargar'])){

        $avanzada = $_POST['avanzada']?'1':'0';
        $fil_city = isset($_POST['city_origen'])?'1':'0';
        $city_origen = $_POST['fcity_origen'];
        $i_v = $_POST['mostrar'];
        $fil_srv = isset($_POST['servicios'])?'1':'0';
        $servicio = $_POST['fservicios'];
        $title = "Graficos de Confiabilidad";
        $desde = dateToMysql($_POST['desde'], '/');
        $hasta = dateToMysql($_POST['hasta'], '/');
        }

?>

   <link type="text/css" href="../vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="../vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="../vista/css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="../vista/css/jquery.dataTables.css" rel="stylesheet" />
       <link type="text/css" href="/vista/css/estilos.css" rel="stylesheet" />
<script type="text/javascript" src="../vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="../vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="../vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="../vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="../vista/js/jquery.ui.datepicker-es.js"></script>

  <script type="text/javascript" src="../vista/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>


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
                                 $('#fecha').datepicker({dateFormat:'dd/mm/yy' , maxDate: "-2D" });
                                 $('#pl').selectmenu({width: 350});
                                 $('#gen').button().click(function(){
                                                                            $('#mje').html("<br><div align='center'><img  alt='cargando' src='../vista/ajax-loader.gif' /><br></div>");
                                                                            $.post("/vista/ordenes/genplctrl.php", 
                                                                                   $('#upuda').serialize(), 
                                                                                   function(data){
                                                                                                  $('#mje').html(data);
                                                                                                });
                                                                                                                                                                
                                                                      });
                                 $(':radio').click(function(){
                                                              $('#accion').val($(this).val());
                                 });

    });

</script>



<body>
<?php
     menu();
     $planillas = call('PlanillaDiaria','findAll');

?>
    <br><br>
         <form id="upuda" method="POST">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Emision de certificados</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Generar certificado</legend>
		                 <table align='center'>
    		                  <tr>
        		                 <td>
                                  Fecha certificado
                             </td>
                             <td>
                                <input type="text" id="fecha" name="fecha">
                             </td>
                          </tr>
                          <tr>
                             <td style="padding-top:10px">
                                  Tipo certificado
                             </td>
                             <td style="padding-top:10px">
                                Resumido<input type="radio" value="resumen" name="tipo" checked>
                                Completo<input type="radio" value="genpl" name="tipo">
                             </td>
                          </tr>
                          <tr>
                            <td style="padding-top:10px">
                                Base informe
                            </td>        
                            <td style="padding-top:10px">
                                <select id="pl" name="pl" class="ui-widget ui-widget-content  ui-corner-all">
                                        <?php
                                            
                                            foreach ($planillas as $value) {
                                                print "<option value='".$value->getId()."'>$value</option>";
                                            }
                                        ?>
                                </select>
                            </td>  
                          </tr>
                          <tr>
                            <td></td>
                             <td>
                                <input type="button" id="gen" value='Cargar Certificado'>
                             </td>
                          </tr>
                   </table>
            </fieldset>
            <div id="mje" align='center'></div>
            <input type="hidden" id="accion" name="accion" value="resumen">
            <input type="hidden" name="complete" value="1">
         </form>
</body>
</html>
