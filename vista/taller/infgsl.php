<?php
     session_start();
//error_reporting(E_ALL & ~E_NOTICE);     
     include_once('../main.php');

     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
     include '../../modelsORM/manager.php';
     include_once '../../modelsORM/call.php';  
     include_once '../../modelsORM/src/AccionUnidad.php';     
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
     <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/scroll-table/jquery.chromatable.js"></script>
       <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>


  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/tables/jquery.tablehover.js"></script>
   <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }

.small.button, .small.button:visited {
font-size: 11px ;
}

input.text { margin-bottom:12px; width:95%; padding: .4em; }
#newuda .error{
  font-size:0.8em;
  color:#ff0000;
}

         .rojo {background-color: #DD614A;}
         .amarillo{background-color: #FFFF80;}
         .verde{background-color: #C0FFC0;}

</style>
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
  <!-- Bootstrap core CSS -->
  <link href="/vista/js/MDB-Free_4/css/bootstrap.min.css" rel="stylesheet">
  <!-- Material Design Bootstrap -->
  <link href="/vista/js/MDB-Free_4/css/mdb.min.css" rel="stylesheet">
  <!-- Your custom styles (optional) -->
  <link href="/vista/js/MDB-Free_4/css/style.css" rel="stylesheet">
  <link href="/vista/js/MDB-Free_4/css/addons/datatables.min.css" rel="stylesheet">


<body>
<?php
     menu();
        

     //   $a = $entityManager->createQuery("SELECT a, max(a.fecha) fecha FROM AccionUnidad a GROUP BY a.unidad, a.accion ORDER BY a.unidad");     
     //$optTurnos.="<option value='".$turno->getId()."'>$turno</option>";
     $productos = $entityManager->createQuery("SELECT t FROM TipoFluido t")->getResult();  
     $options = "";
     foreach ($productos as $p) {
        $options.="<option value='".$p->getId()."'>".$p->getTipo()."</option>";
     }
?>
<br>
      <div class="card">
        <div class="card-header text-white danger-color-dark mb-3">
          Informe despachos combustible
        </div>
        <div class="card-body">
            <div class="container">
              <form id="form">
                  <div class="row">
                    <div class="col-1"><label>Desde</label></div>
                    <div class="col"><input type="date" class="form-control form-control-sm datepicker" name="desde"></div>
                    <div class="col-1"><label>Hasta</label></div>
                    <div class="col"><input type="date" class="form-control form-control-sm datepicker" name="hasta"></div>
                    <div class="col-1"><label>Producto</label></div>
                    <div class="col">
                        <select class="custom-select custom-select-sm" name="tipo">
                            <?php print $options; ?>
                        </select>
                    </div>
                    <div class="col"><input type="button" class="btn btn-sm danger-color-dark btn-block text-white" id="send" value="Cargar Informe"></div>
                  </div>
                  <input type="hidden" name="accion" value="resumen">
              </form>
            </div>
            <hr>
            <div id="result">
            </div>
            <div class="prg">
                <div class="progress">
                  <div class="progress-bar progress-bar-striped progress-bar-animated red lighten-1" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
                </div>
            </div>
        </div>
      </div>
</body>
  <script type="text/javascript" src="/vista/js/MDB-Free_4/js/jquery-3.4.1.min.js"></script>
  <!-- Bootstrap core JavaScript -->
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/bootstrap.min.js"></script> 
  <!-- Bootstrap tooltips -->
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/popper.min.js"></script>

  <!-- MDB core JavaScript -->
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/mdb.min.js"></script>   
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/MDB-Free_4/js/addons/datatables.js"></script>     
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/bootbox/bootbox.all.min.js"></script>     
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.15/jquery.mask.min.js"></script>  

  <script type="text/javascript">
    var v34 = $.noConflict(true);

  v34(function() {
                    v34('#send').on('click', function (e) {
                                                            var data = $('#form').serialize();
                                                            v34('#result').empty();
                                                            v34(".prg").show();
                                                            $.post('/modelo/taller/infgsl.php', 
                                                                   data,
                                                                   function(result){
                                                                      v34('#result').html(result);
                                                                      v34(".prg").hide();
                                                                   });
                    });
                  
                    v34('#dtBasicExample').DataTable({
                                                        "paging": false,
                                                        "searching": false // false to disable pagination (or any other option)
                                                      });
                    v34('.dataTables_length').addClass('bs-select');

                   v34(".prg").hide();
        
});
  </script>
</html>

