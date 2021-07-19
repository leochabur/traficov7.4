<?php
     session_start(); //modulo para dar de alta una provincia
     include('../../main.php');
     include('../../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
<link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet" />
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
                                                       $('.cuit').mask('99-99999999-9');
                                                       $('#loca').selectmenu({'width':350});
                                                       $('#save').button();
                                                       $('#newepe').validate({
                                                                              email: {
                                                                                        required: true,
                                                                                        email: true
                                                                                      },
                                                                              submitHandler: function(e){
                                                                                                                 $('#save').toggle();
                                                                                                                 var datos = $("#newepe").serialize();
                                                                                                                 $.post("/modelo/bd/tablas/empup.php", datos, function(data){
                                                                                                                                                                              if (data){
                                                                                                                                                                                  $("#name").val('');
                                                                                                                                                                                  $("#dire").val('');
                                                                                                                                                                                  $("#tele").val('');
                                                                                                                                                                               }
                                                                                                                                                                               $('#save').toggle();
                                                                                                                                                                             });


                                                                                                         }
                                                                              });
                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form id="newepe">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Alta Empleador</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Razon Social</td>
                                    <td><input id="name" name="name" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>CUIT</td>
                                    <td><input id="cuit" name="cuit" class="cuit ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Direccion</td>
                                    <td><input id="dire" name="dire" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Mail</td>
                                    <td><input type="email" id="mail" name="mail" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Localidad</td>
                                    <td>
                                        <select id="loca" name="loca" class="ui-widget ui-widget-content">
                                        <?php
                                              armarSelect ('ciudades', 'ciudad', 'id', 'ciudad', "id_estructura = $_SESSION[structure]");
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Telefono</td>
                                    <td><input id="tele" name="tele" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right"><input type="submit" id="save" value="Guardar Empleador"/> </td>
                                </tr>
                         </table>
            </fieldset>
            <input type="hidden" name="accion" value="sve">
         </form>
         <fieldset class="ui-widget ui-widget-content ui-corner-all">
                     <legend class="ui-widget ui-widget-header ui-corner-all">Empleadores existentes</legend>
                     
                     <table class="table table-zebra" width="100%">
                        <thead>
                          <tr>
                            <th>Razon Social</th>
                            <th>CUIT</th>
                            <th>Direccion</th>
                            <th>Telefono</th>
                            <th>Mail</th>
                            <th>Cant. Empleados</th>
                            <th>Estado</th>
                            <th></th>
                          </tr>
                        </thead>
                        <tbody>
                    <?php
                        $conn = conexcion(true);
                        $result = mysqli_query($conn, "SELECT emp.*, count(e.id_empleado) as cant
                                                       FROM empleadores emp
                                                       LEFT JOIN (SELECT * FROM empleados WHERE activo) e ON e.id_empleador = emp.id
                                                       WHERE emp.id_estructura = $_SESSION[structure]
                                                       GROUP BY emp.id
                                                       ORDER BY razon_social") or die(mysqli_error($conn));
                        while ($row = mysqli_fetch_array($result))
                        {
                          print "<tr>
                                  <td>".strtoupper($row['razon_social'])."</td>
                                  <td>$row[cuit_cuil]</td>
                                  <td>$row[direccion]</td>
                                  <td>$row[telefono]</td>
                                  <td>$row[mail]</td>
                                  <td align='right'>$row[cant]</td>";
                          $state = "INACTIVO";
                          if ($row['activo'])
                          {
                              $state = "ACTIVO";
                          }

                          print "<td>$state</td>
                                 <td><a href='/vista/bd/tablas/empupda.php?ide=$row[id]'>Editar</a></td>";  
                          
                          print "</tr>";
                        }
                        $result->close();
                        $conn->close();

                     ?>
                        </tbody>
                      </table>
         </fieldset>
</body>
</html>

