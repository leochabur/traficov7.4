<?php
     session_start();
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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}

#newpto,  #newcto,  .error{
	font-size:0.8em;
	color:#ff0000;
}
table tr td{padding: 3px;}
</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                        $( "#tabs" ).tabs();    
                                                        $('select').selectmenu({width: 300});                                                    
                                                       $('#save, #save-sector').button();
                                                       $('#newpto').validate({
                                                                              submitHandler: function(e){
                                                                                                         var datos = $("#newpto").serialize();
                                                                                                         $.post("/modelo/rrhh/bd/admsector.php", 
                                                                                                                 datos, 
                                                                                                                 function(data){
                                                                                                                                    console.log(data);
                                                                                                                                    var response = $.parseJSON(data);
                                                                                                                                    if (response.ok)
                                                                                                                                    {
                                                                                                                                        location.reload();
                                                                                                                                    }
                                                                                                                                    else
                                                                                                                                    {
                                                                                                                                        alert(response.message);
                                                                                                                                    }
                                                                                                                                 });


                                                                                                         }
                                                                              });
                                                       $('#newcto').validate({
                                                                              submitHandler: function(e){
                                                                                                         var datos = $("#newcto").serialize();
                                                                                                         $.post("/modelo/rrhh/bd/admsector.php", 
                                                                                                                 datos, 
                                                                                                                 function(data){
                                                                                                                                    console.log(data);
                                                                                                                                    var response = $.parseJSON(data);
                                                                                                                                    if (response.ok)
                                                                                                                                    {
                                                                                                                                        location.reload();
                                                                                                                                    }
                                                                                                                                    else
                                                                                                                                    {
                                                                                                                                        alert(response.message);
                                                                                                                                    }
                                                                                                                                 });


                                                                                                         }
                                                                              });
                                                        loadPuestos();
                          });

                          function loadPuestos()
                          {
                            $.post('/modelo/rrhh/bd/admsector.php',
                                                                {
                                                                    accion : 'list'
                                                                },
                                                                function(data){
                                                                                var response = $.parseJSON(data);
                                                                                $('#data').html(response[0]);
                                                                                $('#data-sector').html(response[1]);
                                                                });
                          }
</script>

<body>
<?php
     menu();

     $estructuras = ejecutarSQLPDO("SELECT * FROM estructuras WHERE activo ORDER BY nombre");
     $sectores = ejecutarSQLPDO("SELECT descripcion, id FROM sector s WHERE activo ORDER BY descripcion");
?>
  <br>
  <br>
  <div id="tabs">
      <ul>
        <li><a href="#tabs-1">Puestos</a></li>
        <li><a href="#tabs-2">Sectores</a></li>
      </ul>
      <div id="tabs-1">
        	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
                 <form id="newpto">
        	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
        		                 <legend class="ui-widget ui-widget-header ui-corner-all">Nuevo Puesto</legend>
        		                 <div id="mensaje"></div>
                                 <table border="0" align="center" width="50%" name="tabla">
                                        <tr>
                                            <td WIDTH="20%"><label for="razon">Codigo Puesto</label></td>
                                            <td WIDTH="20%">Nombre Puesto</td>
                                        </tr>
                                        <tr>

                                            <td><input  name="codigo" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                            
                                            <td><input name="puesto" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                        </tr>
                                        <tr>
                                            <td WIDTH="20%"><label for="razon">Sector</label></td>
                                            <td WIDTH="20%">Estructura</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                
                                                    <select name="sector"/>
                                                    <?php
                                                        foreach ($sectores as $str)
                                                        {
                                                            print "<option value='$str[id]'>$str[descripcion]</option>";
                                                        }
                                                    ?>
                                                    <select>
                                            </td>
                                            <td>
                                                    <select name="estructura"/>
                                                    <?php
                                                        foreach ($estructuras as $str)
                                                        {
                                                            print "<option value='$str[id]'>$str[nombre]</option>";
                                                        }
                                                    ?>
                                                    <select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" align="right"><input type="submit" id="save" value="Guardar Puesto"/> </td>
                                        </tr>
                                 </table>
                    </fieldset>
                    <input type="hidden" name="accion" value="svepto">
                 </form>
                 <hr>
                 <div id="data"></div>
             </div>
        </div>
        <div id="tabs-2">
            <div id="tabs-2" class="ui-state-highlight ui-corner-all">
                 <form id="newcto">
                       <fieldset class="ui-widget ui-widget-content ui-corner-all">
                                 <legend class="ui-widget ui-widget-header ui-corner-all">Nuevo Sector</legend>
                                 <div id="mensaje"></div>
                                 <table border="0" align="center" width="50%" name="tabla">
                                        <tr>
                                            <td WIDTH="20%"><label for="razon">Codigo Sector</label></td>
                                            <td WIDTH="20%">Nombre Sector</td>
                                        </tr>
                                        <tr>

                                            <td><input  name="codigo" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                            
                                            <td><input name="sector" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" align="right"><input type="submit" id="save-sector" value="Guardar Sector"/> </td>
                                        </tr>
                                 </table>
                    </fieldset>
                    <input type="hidden" name="accion" value="svectr">
                 </form>
                 <hr>
                 <div id="data-sector"></div>
             </div>
        </div>
    </div>
</body>
</html>

