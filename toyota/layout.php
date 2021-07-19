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

?>

   <link type="text/css" href="../vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="../vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="../vista/css/demo_table.css" rel="stylesheet" />
     <link type="text/css" href="../vista/css/estilos.css" rel="stylesheet" />
  <link type="text/css" href="../vista/css/jquery.dataTables.css" rel="stylesheet" />
       <link type="text/css" href="/vista/css/estilos.css" rel="stylesheet" />
<script type="text/javascript" src="../vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="../vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="../vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="../vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="../vista/js/jquery.ui.datepicker-es.js"></script>

  <script type="text/javascript" src="../vista/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script defer src="https://use.fontawesome.com/releases/v5.0.9/js/all.js" integrity="sha384-8iPTk2s/jMVj81dnzb/iFR2sdA7u06vHJyyLlAd4snFpCl/SnyUjRrbdJsw1pGIl" crossorigin="anonymous"></script>

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
                                 $('#data').html("<br><div align='center'><img  alt='cargando' src='../vista/ajax-loader.gif' /><br></div>");
                                $.post("/toyota/modelo/layout.php", 
                                       {accion: 'list'}, 
                                       function(data){
                                                      $('#data').html(data);
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
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Layout unidades</legend>
                     <hr>
                     <div id="data"></div>
            </fieldset>
         </form>
</body>
</html>
