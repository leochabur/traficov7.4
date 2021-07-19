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
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet"/>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
     <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/scroll-table/jquery.chromatable.js"></script>

  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/tables/jquery.tablehover.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.dataTables.min.js"></script>
  <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDv8b-qrzRkI9kvtJV6l3Lko-mCdrGh7oE&sensor=false"></script>
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
                $(document).ready(function()
                {
                                             $("#cargar").button().click(function(){

                                                                                    $('#dats').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                                    $.post("/modelo/segvial/readadav.php", 
                                                                                           $('#upuda').serialize(), function(data){  $('#dats').html(data);
                                                                                                                                                                 });
                                             });

                                             $('#cursos, #empleadores').selectmenu({width: 350});
                                             $('#estructura, #state').selectmenu({width: 250});


                });
</script>

<body>
<?php
     menu();

?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Aula Virtual - Avances</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar</legend>
                         <table border="0" align="center" width="" name="tabla">
                                <tr>
                                    <td>Cursos</td>
                                    
                                    <td>
                                        <select id='cursos' name='cursos'>
                                            <option value='0'>Todos</option>
                                            <?php
                                              armarSelect('av_cursos_disponibles', 'nombre', 'id', 'nombre');
                                            ?>
                                        </select>
                                    </td>
                                    <td>Estados</td>
                                    
                                    <td>
                                        <select id='state' name='state'>
                                            <option value='t'>Todos</option>
                                                <option value='r'>Realizado</option>
                                                <option value='p'>Pendiente</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Empleador</td>
                                    <td>
                                        <select id='empleadores' name='empleadores'>
                                            <option value='0'>Todos</option>
                                            <?php
                                              armarSelect('empleadores', 'razon_social', 'id', 'razon_social', 'activo');
                                            ?>
                                        </select>
                                    </td>
                                
                                    <td>Estructura</td>
                                    <td>
                                        <select id='estructura' name='estructura'>
                                            <option value='0'>Todas</option>
                                            <?php
                                              armarSelect('estructuras', 'nombre', 'id', 'nombre');
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Mostrar Calificacion</td>
                                    <td>
                                            <input type="checkbox" name="calif"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" align="right">
                                        <input type="button" value="Cargar Datos" id="cargar">
                                    </td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <div id="dats"></div>

            </fieldset>
            <input type="hidden" name="accion" id="accion" value="resumido">
         </form>

</body>
</html>

