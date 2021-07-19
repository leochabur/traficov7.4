<?php
       session_start();


     include( 'bdadmin.php' );
     include( 'dateutils.php' );
     include('./main.php');
     include('core_reps.php');
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

?>

   <link type="text/css" href="../toyota/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="../toyota/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="../toyota/css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="../toyota/css/jquery.dataTables.css" rel="stylesheet" />
       <link type="text/css" href="../toyota/css/jquery.treeTable.css" rel="stylesheet" />
<script type="text/javascript" src="../toyota/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="../toyota/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="../toyota/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="../toyota/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="../toyota/js/jquery.ui.datepicker-es.js"></script>

  <script type="text/javascript" src="../toyota/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script type="text/javascript" src="../toyota/js/jquery.treeTable.js"></script>
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
                                                       $('#desde,#hasta').datepicker({dateFormat:'dd/mm/yy'});

                                                       $( "#center, #vuelta" ).tabs();
                                                       $('#clis').selectmenu({width: 350});
                                                       $('#cargar').button().click(function(){
                                                                                       <!--       var des = $("#desde").val();
                                                                                              var has = $("#hasta").val();
                                                                                              var cli = $("#clis").val();
                                                                                              $.post('./core_reps.php', {desde: des, hasta: has, cliente: cli}, function(data){$("#dats").html(data);});
                                                                                              -->
                                                                                              });
                                                     <?php
                                                          if (isset($_POST['cargar']))
                                                            echo "$('#examplebasic').treeTable();";
                                                      ?>


  });


</script>



<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda" method="POST">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Graficas de Confiablidad y Eficiencia</legend>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar Datos</legend>
                         <table border="0" align="left" width="50%" name="tabla" CELLPADDING=5>
                                <tr>
                                    <td>Cliente</td>
                                    <td>
                                        <select id="clis" name="clis" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option value="10">TOYOTA</option>
                                        </select>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                    <tr>
                                    <td>Desde</td>
                                    <td><input id="desde" name="desde"  type="text" size="20" value="<?php if (isset($_POST['cargar'])) echo $_POST['desde'];?>"></td>
                                    <td>Hasta</td>
                                    <td><input id="hasta" name="hasta" type="text" size="20" value="<?php if (isset($_POST['cargar'])) echo $_POST['hasta'];?>"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"><input type="submit" value="Cargar Ordenes" id="cargar" name="cargar"></td>
                                    </tr>
                         </table>
                         </fieldset>
            </fieldset>
            <input type="hidden" name="accion" id="accion" value="list">
            <input type="hidden" name="order" id="order">
         </form>
         <br> <br>
         <div>
               <div id="table_div" name="table_div">
               </div>
               <div id="chart_sort_div" name="chart_sort_div">
               </div>
         </div>
              <?php
                   if (isset($_POST['cargar'])){
                      echo loadReps($_POST['desde'], $_POST['hasta'], 10);
                   }
              ?>




</body>
</html>
