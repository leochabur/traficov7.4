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
                                                                                                                
                                                                                                                 var datos = $("#newepe").serialize();
                                                                                           
                                                                                                                 $.post("/modelo/bd/tablas/empup.php", datos, function(data){
                                                                                                                                                                             var response = $.parseJSON(data);
                                                                                                                                                                        
                                                                                                                                                                             if (response.ok)
                                                                                                                                                                                window.location='/vista/bd/tablas/empup.php';
                                                                                                                                                                             });


                                                                                                         }
                                                                              });
                          });
</script>

<body>
<?php
     menu();
     $conn = conexcion(true);
     $result = mysqli_query($conn, "SELECT *
                                   FROM empleadores emp
                                   WHERE emp.id = $_GET[ide]") or die(mysqli_error($conn));
     $row = mysqli_fetch_array($result);
     if (!$row)
     {
        print "Empleador inexcistente en la base de datos!";
     }
     $result->close();
     $conn->close();
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form id="newepe">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Modificar Empleador</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Razon Social</td>
                                    <td><input id="name" value="<?php print $row['razon_social']; ?>" name="name" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>CUIT</td>
                                    <td><input id="cuit" value="<?php print $row['cuit_cuil']; ?>" name="cuit" class="cuit ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Direccion</td>
                                    <td><input id="dire" value="<?php print $row['direccion']; ?>" name="dire" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Mail</td>
                                    <td><input value="<?php print $row['mail']; ?>" type="email" id="mail" name="mail" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Localidad</td>
                                    <td>
                                        <select id="loca" value="<?php print $row['id_localidad']; ?>" name="loca" class="ui-widget ui-widget-content">
                                        <?php
                                              armarSelect ('ciudades', 'ciudad', 'id', 'ciudad', "id_estructura = $_SESSION[structure]");
                                        ?>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Telefono</td>
                                    <td><input id="tele" name="tele" value="<?php print $row['telefono']; ?>" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Activo</td>
                                    <td>
                                        <input type="checkbox" id="act" name="act" <?php print ($row['activo']?"checked":""); ?> 
                                              class="ui-widget ui-widget-content  ui-corner-all"/>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right"><input type="submit" id="save" value="Modificar Empleador"/> </td>
                                </tr>
                         </table>
            </fieldset>
            <input type="hidden" name="accion" value="edit">
            <input type="hidden" name="idemp" value="<?php print $_GET['ide']; ?>">
         </form>
</body>
</html>

