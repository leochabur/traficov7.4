<?php
     session_start();
error_reporting(E_ALL & ~E_NOTICE);     
     include_once('../main.php');

     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
     include '../../modelsORM/manager.php';
     include_once '../../modelsORM/call.php';
     include_once '../../modelsORM/src/lavadero/AccionUnidad.php';     
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
    <link href="/vista/css/estilos.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
     <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/scroll-table/jquery.chromatable.js"></script>

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
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                                                       $('select').selectmenu({width: 200});

                                                       $('.btn').button().click(function(){
                                                                                          if (confirm('Seguro confirmar la carga?')){
                                                                                              var btn = $(this);
                                                                                              btn.hide();
                                                                                              var unit = new Array();
                                                                                              $("input:checkbox:checked").each(function() {
                                                                                                                                                 unit.push($(this).val());
                                                                                                                                            });
                                                                                              $.post('/modelo/taller/gsttcf.php',
                                                                                                    {accion:'setta', fecha: $('#fecha').val(), tipo: $('select').val(), cch:unit.join(',')},
                                                                                                    function(data){
                                                                                                                    var response = $.parseJSON(data);
                                                                                                                    if (response.ok){
                                                                                                                        location.reload();
                                                                                                                    }
                                                                                                                    else{
                                                                                                                      alert(response.msge);
                                                                                                                      btn.show();
                                                                                                                    }
                                                                                                    });
                                                                                            }
                                                       });            
                          });
</script>

<body>
<?php
     menu();
        $coches = $entityManager->createQuery("SELECT u.interno as interno, t.descripcion as taco, ta.fechaCambio as desde, ta.vencimiento as hasta, ta.id as idTaco, u.id as idUda FROM Unidad u LEFT JOIN TacografoUnidad ta WITH ta.unidad = u LEFT JOIN Tacografo t WITH ta.tacografo = t WHERE u.activo = :activo and u.estructura = :estructura ORDER BY u.interno")->setParameter('activo', true)->setParameter('estructura', $_SESSION['structure'])->getResult();

        $tacografos = $entityManager->createQuery('SELECT t FROM Tacografo t')->getResult();

        $select = "";
        foreach ($tacografos as $tacografo) {
            $select.="<option value='".$tacografo->getId()."'>".$tacografo->getDescripcion()."</option>";
        }

        $body = "";
        $hoy = new DateTime();
        foreach ($coches as $coche) {
          $clase="";
          if ($coche['desde'] && $coche['hasta']){

            $interval = date_diff($hoy, $coche['hasta']);
            $val = $interval->format('%r%a');
            
            if ($val < 1){
              $clase="rojo";
            }
          }

          $body.="<tr>
                      <td>".$coche['interno']."</td>
                      <td class='tco'>".$coche['taco']."</td>
                      <td class='dsd'>".($coche['desde']?$coche['desde']->format('d/m/Y'):'')."</td>
                      <td class='$clase hst'>".($coche['hasta']?$coche['hasta']->format('d/m/Y'):'')."</td>        
                      <td>
                          <input type='checkbox' value='".$coche['idUda']."'/>
                      </td>
                  </tr>";        
        }

     //   $a = $entityManager->createQuery("SELECT a, max(a.fecha) fecha FROM AccionUnidad a GROUP BY a.unidad, a.accion ORDER BY a.unidad");     
?>
<br>

             <fieldset class="ui-widget ui-widget-content ui-corner-all">
                     <legend class="ui-widget ui-widget-header ui-corner-all">Estados Tacografos</legend>
                         <table border="0" align="center" class="table">
                            <tr>
                                <td>
                                  <select name='tipoTaco'><?php print $select; ?></select>
                                </td>
                                <td>
                                  <input type='text' id='fecha' name='fecha'/>
                                </td>
                                <td>
                                    <input type='submit' value='Cambiar' class='btn'/>
                                </td>
                            </tr>
                         </table>
                         <table border="0" align="center" name="tabla" class="table table-zebra" id='tablat'>
                            <thead>
                                <tr>
                                  <th>Interno</th>
                                  <th>Tacografo Actual</th>
                                  <th>Ultimo Cambio</th>
                                  <th>Vto. Disco</th>
                                  <th>Guardar</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php   
                                      print $body;
                              ?>
                            </tbody>
                         </table>
                         <div id="data"></div>
            </fieldset>
</body>
</html>
