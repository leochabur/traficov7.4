<?php
     session_start();
     include('../main.php');
     include('../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
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

.small.button, .small.button:visited {
font-size: 11px ;
}

#newepe .error{
	font-size:0.8em;
	color:#ff0000;
}
table tr td{padding: 13px;}
</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $("table input:text").mask("999.99");
                                                       $("#save").button().click(function(){
                                                                                            var datos = $("#newprpe").serialize();
                                                                                             $.post("/modelo/segvial/altestpea.php", datos, function(data){alert(data);});
                                                                                            });
                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form id="newprpe">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Cargar Importes Estacion Peaje</legend>
		                 <div id="mensaje"></div>
                         <table border="0" class="tablesorter" align="center" name="tabla">
                         <tr>
                             <td></td>

                             <?php
                               $array = array();
                               $conn = conexcion();
                               $sql = "SELECT * FROM tipounidad WHERE (id_estructura = ".STRUCTURED.")";
                               $result = mysql_query($sql);
                               $i = 0;
                               $precios = "";
                               print "<td align='center' colspan=".(2*mysql_num_rows($result)).">Tipos Unidades</td></tr><tr><td></td>";
                               while ($data = mysql_fetch_array($result)){
                                     print "<td colspan='2'>$data[tipo]</td>";

                                        $precios.="<td>Normal</td><td>Telepase</td>";

                                     $array[$i] = $data['id'];
                                     $i++;
                               }
                               print "</tr>";
                              print "<tr><td></td>$precios</tr>";
                               

                               $sql = "SELECT id, concat(id_estacionpeaje,id_tipounidad) as clave, LPAD(round(precio_peaje,2),6,'0') as precio_peaje,
                                              LPAD(round(precio_telepase,2),6,'0') as precio_telepase 
                                              FROM preciopeajeunidad p where (id_estructura = ".STRUCTURED.")";
                               $result = mysql_query($sql);
                               $importes = array();
                               while ($data = mysql_fetch_array($result)){
                                     $importes[$data['clave']."PN"] = $data['precio_peaje'].'-'.$data['id'];
                                     $importes[$data['clave']."PT"] = $data['precio_telepase'].'-'.$data['id'];
                               }
                               
                               $sql = "SELECT id, Concat(nombre, ' (', lugar, ')') as nombre FROM estacionespeaje WHERE (id_estructura = ".STRUCTURED.")";
                               $result = mysql_query($sql);
                               while ($data = mysql_fetch_array($result)){
                                     print "<tr><td>$data[nombre]</td>";
                                     for($i=0; $i < count($array); $i++){
                                               $key = $data['id'];
                                               $key.=$array[$i];
                                               $imp = explode('-', $importes[$key."PN"]);
                                               $impTele = explode('-', $importes[$key."PT"]);
                                               print "<td><input name='$data[id]-$array[$i]-$imp[1]-PN' type='text' size='5' value='$imp[0]'></td>";
                                               print "<td><input name='$data[id]-$array[$i]-$imp[1]-PT' type='text' size='5' value='$impTele[0]'></td>";
                                     }
                                     print "</tr>";
                               }
                               mysql_free_result($result);
                               cerrarconexcion($conn);
                             ?>
                             <tr><td align="right" colspan="<?echo ((count($array)*2) + 1);?>"><input type="button" id="save" value="Guardar Datos"></td></tr>
                         </table>
            </fieldset>
            <input type="hidden" name="accion" value="spp"/>
         </form>
         
</body>
</html>

