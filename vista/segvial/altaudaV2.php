<?php
     session_start();
     include('../main.php');
     include('../paneles/viewpanel.php');
     include('../../modelsORM/manager.php');
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


  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
  <!-- Bootstrap core CSS -->
  <link href="/vista/js/MDB-Free_4/css/bootstrap.min.css" rel="stylesheet">
  <!-- Material Design Bootstrap -->
  <link href="/vista/js/MDB-Free_4/css/mdb.min.css" rel="stylesheet">
  <!-- Your custom styles (optional) -->
  <link href="/vista/js/MDB-Free_4/css/style.css" rel="stylesheet">
  <link href="/vista/js/MDB-Free_4/css/addons/datatables.min.css" rel="stylesheet">

<style type="text/css">

</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $("#dominio").mask("aaa-999");
                                                       $("#n_dominio").mask("aa-999-aa");
                                                       $("#anio").mask("9999");
                                                       $("#interno").mask("9999");
                                                       $("#cantas").mask("99");
                                                       $("#consumo").mask("99");
                                                       $("#save").button();
                                                       $('#newuda').validate({
                                                                              submitHandler: function(e){
                                                                                                                 var datos = $("#newuda").serialize();
                                                                                                                 $.post("/modelo/segvial/altauda.php", datos, function(data){
                                                                                                                                                                             obj = JSON.parse(data);

                                                                                                                                                                             if (obj === "iebd"){
                                                                                                                                                                                var mje = "<div class=\"ui-widget\">"+
                                                                                                                                                                                          "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                                                          "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                                          "<strong>Numero de Interno existente en la Base de Datos!</strong></p>"+
                                                                                                                                                                                          "</div>"+
                                                                                                                                                                                          "<div>";
                                                                                                                                                                                $('#mensaje').html(mje);
                                                                                                                                                                             }
                                                                                                                                                                             else{
                                                                                                                                                                                  if (obj == 0){
                                                                                                                                                                                     var mje = "<div class=\"ui-widget\">"+
                                                                                                                                                                                               "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                                                               "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                                               "<strong>Se han producido errores al intentar guardar la unidad en la Base de Datos!</strong></p>"+
                                                                                                                                                                                               "</div>"+
                                                                                                                                                                                               "<div>";
                                                                                                                                                                                     $('#mensaje').html(mje);
                                                                                                                                                                                  }
                                                                                                                                                                                  else{
                                                                                                                                                                                   var mje = "<div class=\"ui-widget\">"+
                                                                                                                                                                                               "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                                                               "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                                               "<strong>Se han guardado con exito el interno en la Base de Datos!</strong></p>"+
                                                                                                                                                                                               "</div>"+
                                                                                                                                                                                               "<div>";
                                                                                                                                                                                     $('#mensaje').html(mje);

                                                                                                                                                                                  }
                                                                                                                                                                             }
                                                                                                                                                                             });


                                                                                                         }
                                                                              });
                          });
</script>

<body class="h7">
<?php
     menu();
     try{
      $sql = "SELECT m FROM MarcaParteVehiculo m";
      $q = $entityManager->createQuery($sql);
      $marcas = $q->getResult();     

      $sql = "SELECT e FROM Estructura e WHERE e.id = :id";
      $q = $entityManager->createQuery($sql);
      $q->setParameter('id', STRUCTURED);
      $estructura = $q->getOneOrNullResult();         
      }
      catch (Exception $e){ die ($e->getMessage());}  
?>

<br>
         
            <div class="card bg-red">
                <div class="card-header text-white danger-color-dark">
                 Nuvea Unidad
                </div>
                <div class="card-body">
                    <form id="newuda">
                     <div class="container">
                          <div class="form-group row">
                               <div class="col-4">
                                               <label for="estructura" >Estructura</label>
                                                <select id="estructura" name="estructura" class="form-control form-control-sm">
                                                        <?php armarSelect('estructuras', 'nombre', 'id', 'nombre');?>
                                                </select>                         
                               </div>                    
                               <div class="col-4">
                                                <label for="propietario" >Propietario</label>
                                                <select id="propietario" name="propietario" class="form-control form-control-sm">
                                                        <?php
                                                             armarSelect('empleadores', 'razon_social', 'id', 'razon_social', STRUCTURED);
                                                        ?>
                                                </select>
                                 
                               </div>
                               <div class="col-2">
                                                <label for="propietario" >Interno</label>
                                                <input type="text" id="" name="" required="required" maxlength="255" class="form-control form-control-sm" />
                               </div>                               
                         </div>
                          <div class="form-group row">

                               <div class="col-2">
                                               <label for="estructura" >Dominio</label>     
                                               <input type="text" id="" name="" required="required" maxlength="255" class="form-control form-control-sm" />                 
                               </div>
                               <div class="col-2">
                                               <label for="estructura" >PUM</label>     
                                               <input type="text" id="" name="" required="required" maxlength="255" class="form-control form-control-sm" />                 
                               </div>                               
                         </div>  
                          <div class="form-group row">
                               <div class="col-4">
                                                <label for="propietario" >Marca Chasis</label>
                                                <select id="propietario" name="propietario" class="form-control form-control-sm">
                                                        <?php
                                                             foreach($marcas as $marca){
                                                               if ($marca->getTipo() == "ch")
                                                                print "<option value='".$marca->getId()."'>$marca</option>";
                                                             }                                                             
                                                        ?>
                                                </select>
                                 
                               </div>
                               <div class="col-4">
                                               <label for="estructura" >Modelo Chasis</label>     
                                               <input type="text" id="" name="" required="required" maxlength="255" class="form-control form-control-sm" />                 
                               </div>  
                               <div class="col-4">
                                               <label for="estructura" >Numero Chasis</label>     
                                               <input type="text" id="" name="" required="required" maxlength="255" class="form-control form-control-sm" />                 
                               </div>  
                          </div>    
                          <div class="form-group row">
                               <div class="col-4">
                                                <label for="carroMarca" >Marca Carroceria</label>
                                                <select id="carroMarca" name="carroMarca" class="form-control form-control-sm">
                                                        <?php
                                                             foreach($marcas as $marca){
                                                               if ($marca->getTipo() == "ca")
                                                                print "<option value='".$marca->getId()."'>$marca</option>";
                                                             }                                                             
                                                        ?>
                                                </select>
                                 
                               </div>
                               <div class="col-4">
                                                <label for="carroTipo" >Tipo Carroceria</label>
                                                <select id="carroTipo" name="carroTipo" class="form-control form-control-sm">
                                                        <?php
                                                        try{
                                                             $sql = "SELECT t FROM TipoVehiculo t WHERE t.estructura = :str ORDER BY t.tipo";
                                                             $q = $entityManager->createQuery($sql);
                                                             $q->setParameter('str', $estructura);
                                                             $tipos = $q->getResult();
                                                             foreach($tipos as $tipo){
                                                                print "<option value='".$tipo->getId()."'>$tipo</option>";
                                                             }     
                                                          }
                                                          catch (Exception $e){ die ($e->getMessage());}                                                        
                                                        ?>
                                                </select>            
                               </div>  
                               <div class="col-4">
                                                <label for="carroCalidad" >Calidad Carroceria</label>
                                                <select id="carroCalidad" name="carroCalidad" class="form-control form-control-sm">
                                                        <?php
                                                        try{
                                                             $sql = "SELECT c FROM CalidadCoche c WHERE c.estructura = :str ORDER BY c.calidad";
                                                             $q = $entityManager->createQuery($sql);
                                                             $q->setParameter('str', $estructura);
                                                             $tipos = $q->getResult();
                                                             foreach($tipos as $tipo){
                                                                print "<option value='".$tipo->getId()."'>$tipo</option>";
                                                             }     
                                                          }
                                                          catch (Exception $e){ die ($e->getMessage());}                                                        
                                                        ?>
                                                </select>               
                               </div>  
                          </div>                                                  
                   </div>




                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Propietario</td>
                                    <td>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="razon">Interno</label></td>
                                    <td><input id="interno" name="interno" size="4" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Dominio</td>
                                    <td><input id="dominio" name="dominio" size="8" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Nuevo Dominio</td>
                                    <td><input id="n_dominio" name="n_dominio" size="8" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <?php
                                  if ($_SESSION['permisos'][4] > 2){
                                     print '<tr>
                                                <td WIDTH="20%">Marca</td>
                                                <td><input id="marca" name="marca" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                                <td></td>
                                           </tr>
                                           <tr>
                                               <td WIDTH="20%">Modelo</td>
                                               <td><input id="modelo" name="modelo" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                               <td></td>
                                           </tr>
                                           <tr>
                                               <td WIDTH="20%">Motor</td>
                                               <td><input id="motor" name="motor" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                               <td></td>
                                           </tr>
                                           <tr>
                                               <td WIDTH="20%">'.htmlentities('Año').'</td>
                                               <td><input id="anio" name="anio" size="4" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                               <td></td>
                                           </tr>
                                           <tr>
                                               <td WIDTH="20%"><label for="razon">Cant. Asientos</label></td>
                                               <td><input id="cantas" name="cantas" size="2" class="ui-widget ui-widget-content ui-corner-all" minlength="2"/></td>
                                               <td></td>
                                           </tr>
                                           <tr>
                                               <td WIDTH="20%"><label for="razon">Consumo c/ 100 Km</label></td>
                                               <td><input id="consumo" name="consumo" size="2" class="ui-widget ui-widget-content ui-corner-all" minlength="2"/></td>
                                               <td></td>
                                           </tr>
                                           <tr>
                                               <td WIDTH="20%"><label for="razon">Servicios</label></td>
                                               <td>Video<input name="video" type="checkbox" class="ui-widget ui-widget-content  ui-corner-all">&nbsp;Bar<input name="bar" type="checkbox" class="ui-widget ui-widget-content  ui-corner-all">&nbsp;Ba&ntilde;o<input name="banio" type="checkbox" class="ui-widget ui-widget-content  ui-corner-all"></td>
                                               <td></td>
                                           </tr>
                                           <td WIDTH="20%">Tipo Unidad</td>
                                               <td><select id="tipo" name="tipo" class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">'.
                                                     armarSelect('tipounidad', 'tipo', 'id', 'tipo', "(id_estructura = ".STRUCTURED.")",1).'
                                                   </select>
                                               </td>
                                               <td></td>
                                           </tr>
                                           <tr>
                                           <td WIDTH="20%">Calidad</td>
                                           <td><select id="calidad" name="calidad" class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">'.
                                                     armarSelect('calidadcoche', 'calidad', 'id', 'calidad', "(id_estructura = ".STRUCTURED.")",1).'
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>';
                                }
                                ?>
                                <tr>
                                    <td colspan="3" align="right"><input type="submit" id="save" value="Guardar Unidad"/> </td>
                                </tr>
                         </table>
            <input type="hidden" name="accion" value="sve">
         </form>
       </div>
     </div>
</body>
  <script type="text/javascript" src="/vista/js/MDB-Free_4/js/jquery-3.4.1.min.js"></script>

  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/bootstrap.min.js"></script> 

  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/popper.min.js"></script>


  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/mdb.min.js"></script>   
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/addons/datatables.js"></script>     
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/bootbox/bootbox.all.min.js"></script>     
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>  

  <script type="text/javascript">
    var v34 = $.noConflict(true);
</script>
</html>

