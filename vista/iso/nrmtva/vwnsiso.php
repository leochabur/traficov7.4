<?php
     session_start(); //modulo para dar de alta una provincia
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
                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Recursos existentes</legend>
                         <table class="table" border="0" align="center" width="50%" name="tabla">
                                <thead>
                                       <tr>
                                           <th>Nombre Recurso</th>
                                           <th>Descripcion</th>
                                           <th>Descarga</th>
                                       </tr>
                                </thead>
                                <tbody>
                                <?php
                                     $result = ejecutarSQL("SELECT * FROM normativaMB");
                                     while ($data = mysql_fetch_array($result)){
                                           print "<tr>
                                                      <td>$data[1]</td>
                                                      <td>$data[3]</td>
                                                      <td align='center'><a href='/vista/iso/nrmtva/$data[2]'  target='_blank'><img src='../../download.png' width='30' height='30' border='0'></a></td>
                                                 </tr>";
                                     }
                                ?>
                                </tbody>
                         </table>
         </fieldset>
</body>
</html>

