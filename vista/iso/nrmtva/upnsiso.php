<?php
     session_start(); //modulo para dar de alta una provincia
set_time_limit(0);
     include('../../main.php');
     include('../../paneles/viewpanel.php');
     include_once('../../../controlador/ejecutar_sql.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}

#newepe .error{
	font-size:0.8em;
	color:#ff0000;
}
table tr td{padding: 3px;}
</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#upload').button();
                                                       
                                                       $('#recasi :button').button().click(function(){
                                                                                                         var bt = $(this);
                                                                                                         $.post('/vista/iso/nrmtva/upload.php',
                                                                                                                {accion: 'del', id: bt.data('id')},
                                                                                                                function(data){
                                                                                                                               bt.parent().parent().remove();
                                                                                                                               });
                                                                                                      });
                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form action="/vista/iso/nrmtva/upload.php" method="post" enctype="multipart/form-data">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Normativa Master Bus</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Buscar</td>
                                    <td>
                                        <input name="archivo" type="file" size="35" >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Nombre Link</td>
                                    <td><input name="link" id="link"  type="text" size="45"></td>
                                </tr>
                                <tr>
                                    <td>Descripcion</td>
                                    <td><textarea rows="5" cols="30" name='desc'></textarea></td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="rigth">
                                        <input type="submit" id="upload" value="Subir Archivo">
                                    </td>
                                </tr>
                         </table>
                         
            </fieldset>
            <input name="action" type="hidden" value="upload"/>
         </form>
         
         <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Archivos existentes</legend>
                         <table class="table" border="0" align="center" width="50%" id="recasi">
                                <thead>
                                       <tr>
                                           <th>Nombre Recurso</th>
                                           <th>Nombre Archivo</th>
                                           <th>Descripcion</th>
                                           <th>Quitar</th>
                                       </tr>
                                </thead>
                                <tbody>
                                <?php
                                     $result = ejecutarSQL("SELECT * FROM normativaMB");
                                     while ($data = mysql_fetch_array($result)){
                                           print "<tr>
                                                      <td>$data[1]</td>
                                                      <td>$data[2]</td>
                                                      <td>$data[3]</td>
                                                      <td><input type='button' value='Quitar' data-id='$data[0]'></td>
                                                 </tr>";
                                     }
                                ?>
                                </tbody>
                         </table>
         </fieldset>
</body>
</html>

