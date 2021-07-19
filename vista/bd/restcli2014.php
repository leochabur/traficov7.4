<?php
     session_start();

     include('../paneles/viewpanel.php');
     include('../main.php');
     define(RAIZ, '/nuevotrafico');

     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script>
	$(function() {
        $('#savesrv').button();
        $('select').selectmenu({style:'popup',
                                width: 350});
        $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
		$("#sverest").button().click(function(){
                                                var datos = $("#commentForm").serialize();
                                                $.post("/modelo/procesa/procesar_rest_clientes.php", datos, function(data) {alert(data);});
                                                });

        
        $("#cliente").change(function(){
                                         var cliente = $("#cliente option:selected").val();
                                         });
        $( "#tabs" ).tabs();
	});
	</script>

<style type="text/css">
body { font-size: 72.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}
form table td{
  padding-top: 3px;
  padding-right: 3px;
  padding-bottom: 3px;
  padding-left: 3px;
}

#commentForm .error{
	font-size:0.8em;
	color:#ff0000;
}
</style>
<BODY>
<?php
     menu();
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form class="cmxform" id="commentForm" method="get" action="">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Restriciones por Cliente</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%">
                                <tr>
                                    <td WIDTH="20%">Cliente</td>
                                    <td>
                                        <select id="cliente" name="cliente" class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <option value="0">Seleccione un Cliente</option>
                                                <?php
                                                  armarSelect('clientes', 'razon_social', 'id', 'razon_social', "(id_estructura = $_SESSION[structure])");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <div id="tabs">
                                             <ul>
                                                 <li><a href="#tabs-1">Unidades Admitidas</a></li>
                                                 <li><a href="#tabs-3">Conductores Admitidos</a></li>
                                             </ul>
                                             <div id="tabs-1">
                                             <table border="0" width="100%">
                                                    <thead>
                                                           <tr>
                                                               <th>Descripcion</th>
                                                               <th>Si</th>
                                                               <th>No</th>
                                                           </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?
                                                      $con = conexcion();
                                                      $sql = "SELECT id, upper(tipo) as tipo FROM tipounidad WHERE id_estructura = $_SESSION[structure] order by tipo";
                                                      $result = mysql_query($sql, $con);
                                                      while ($data = mysql_fetch_array($result)){
                                                            print "<tr>
                                                                       <td>$data[tipo]</td>
                                                                       <td align='center'><input value='1' name='restipo-$data[id]' type='radio' checked></td>
                                                                       <td align='center'><input value='0' name='restipo-$data[id]' type='radio'></td>
                                                                  </tr>";
                                                      }
                                                      $sql = "SELECT id, upper(concat('VTV ',nombre)) as nombre FROM tipovtv where id_estructura = $_SESSION[structure] order by nombre";
                                                      $result = mysql_query($sql, $con);
                                                      while ($data = mysql_fetch_array($result)){
                                                            print "<tr>
                                                                       <td>$data[nombre]</td>
                                                                       <td align='center'><input value='1' name='resvtv-$data[id]' type='radio' checked></td>
                                                                       <td align='center'><input value='0' name='resvtv-$data[id]' type='radio'></td>
                                                                  </tr>";
                                                      }
                                                    ?>
                                                    </tbody>
                                             </table>
                                             </div>
                                             <div id="tabs-3">
                                             <table border="0" width="100%">
                                                    <thead>
                                                           <tr>
                                                               <th>Descripcion</th>
                                                               <th>Si</th>
                                                               <th>No</th>
                                                           </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?
                                                      $con = conexcion();
                                                      $sql = "SELECT id, upper(licencia) as lic FROM licencias l where id_estructura = $_SESSION[structure] order by licencia";
                                                      $result = mysql_query($sql, $con);
                                                      while ($data = mysql_fetch_array($result)){
                                                            print "<tr>
                                                                       <td>$data[lic]</td>
                                                                       <td align='center'><input value='1' name='reslic-$data[id]' type='radio' checked></td>
                                                                       <td align='center'><input value='0' name='reslic-$data[id]' type='radio'></td>
                                                                  </tr>";
                                                      }
                                                      mysql_free_result($result);
                                                      cerrarconexcion($con);
                                                    ?>
                                                    </tbody>
                                             </table>
                                             </div>
                                        </div
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right">
                                        <input type="button" value="Guardar Restricciones" id="sverest">
                                    </td>
                                </tr>
                         </table>
	</fieldset>
	<input type="hidden" name="accion" id="accion" value="soser"/>
</form>
	</div>
	<div id="tabs-2" class="ui-state-highlight ui-corner-all">
	</div>
</div>

</BODY>
</HTML>
