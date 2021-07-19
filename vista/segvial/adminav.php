<?php
     session_start();
     include_once('../main.php');
     include_once('../paneles/viewpanel.php');
     include_once('../../modelsORM/manager.php');
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
                                             $(".btn").button().click(function(){
                                                                                    var btn = $(this);
                                                                                    $('#dats').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                                    $.post("/modelo/segvial/readadav.php", 
                                                                                           {accion : btn.data('action'), cursos : $('#cursos').val()}, 
                                                                                           function(data){  
                                                                                                            $('#dats').html(data);
                                                                                                         });
                                             });

                                             $('#cursos').selectmenu({width: 350});


                });
</script>

<body>
<?php
     menu();
    $cursos = $entityManager->createQuery("SELECT c
                                           FROM Curso c
                                           ORDER BY c.nombre")                         
                                ->getResult();
        $dql = "SELECT c 
                FROM ClaseCurso c";
       // $curso = find('Curso', $_POST['cursos']);

        $clasesCurso = $entityManager->createQuery($dql)                   
                                     ->getResult();
        print "cantidad ".count($clasesCurso);

?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Aula Virtual - Configuracion</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar</legend>
                         <table border="0" align="center" width="75%" name="tabla">
                                <tr>
                                    <td>Cursos actuales</td>
                                    <td>
                                        <select id='cursos' name='cursos'>
                                            <?php
                                              foreach ($cursos as $c)
                                              {
                                                print "<option value='".$c->getId()."'>".$c->getNombre()."</option>";
                                              }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="button" value="Configurar" id="cargar" class='btn' data-action='config'>
                                        <input type="button" value="Definir Alcance" id="define" class='btn' data-action='access'>
                                    </td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <div id="dats"></div>

            </fieldset>
         </form>

</body>
</html>

