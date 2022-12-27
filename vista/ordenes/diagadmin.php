<?php

     include('../main.php');
     include('../paneles/viewpanel.php');
     include_once('../../controlador/ejecutar_sql.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     $vacio = getOpcion('empleador-default', $_SESSION['structure']);

     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
     <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/scroll-table/jquery.chromatable.js"></script>
     <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/tables/jquery.tablehover.js"></script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 150px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}
#commentForm td{padding: 2px;}

#commentForm .error{
    font-size:0.8em;
    color:#ff0000;
}
</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#desde, #hasta').datepicker({dateFormat:'dd/mm/yy'});

                                                       $('#cargar').button().click(function(){
                                                                                              $.post("/modelo/ordenes/diagadmin.php", 
                                                                                                    $("#commentForm").serialize(), 
                                                                                                    function(data){
                                                                                                                   $("#sbana").html(data);                                                                                                                                                                                                                               $('#sbana').html(data);
                                                                                                                                                                                                                             });
                                                                                                    });
                                                       });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form class="cmxform" id="commentForm">
                    <fieldset class="ui-widget ui-widget-content ui-corner-all">
                       <legend class="ui-widget ui-widget-header ui-corner-all">Diagramar servicios administrativos </legend>
                         <table border="0" align="center" name="tabla">
                                <tr>
                                    <td>
                                     Diagramar Desde el:
                                    </td>
                                    <td><input id="desde" name="desde" size="20" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td>Hasta el:</td>
                                    <td><input id="hasta" name="hasta" size="20" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td><input type="button" id="cargar" value="Cargar Diagrama"/> </td>
                                    <input type="hidden" name="accion" value="sbana"/>
                                </tr>
                         </table>
                         <div id="sbana">
                         </div>
            </fieldset>
         </form>
</body>
</html>

