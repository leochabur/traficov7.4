<?php
     session_start();
     set_time_limit(0);
   //  ini_set('max_execution_time',9000);
     error_reporting(0);
   //  error_reporting(E_ALL & ~E_NOTICE);

     $_SESSION[old] = 1;
     include_once('../main.php');
     include_once('../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);



     encabezado('Menu Principal - Sistema de Administracion - Campana', 1);

     include '../../modelsORM/manager.php';
     include_once '../../modelsORM/call.php';
     include_once '../../modelsORM/src/lavadero/AccionUnidad.php';

     $hoy = (new DateTime('NOW'))->format('d/m/Y');
?>


  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <link rel="stylesheet" type="text/css" href="<?php echo RAIZ;?>/multiselect/jquery.multiselect.css" />
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/multiselect/src/jquery.multiselect.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/multiselect/i18n/jquery.multiselect.es.js"></script>

<link type="text/css" href="/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <script type="text/javascript" src="/vista/js/jquery.ui.selectmenu.js"></script>
  <style>
         .rojo {background-color: #DD614A;}
         .amarillo{background-color: #FFFFC0;}
         .verde{background-color: #C0FFC0;}
         .blue{background-color: #0000FF;}         
         
  </style>
  <script>;
          var s = $("<select multiple='multiple' class='miSelect'/>");
          var personal = new Array();

  <?php
       $query = $entityManager->createQuery("SELECT e FROM Empleado e JOIN e.categoria c WHERE e.activo = :activo AND c.id IN (12, 19, 11) ORDER BY e.apellido");

       $query->setParameter('activo', true);

       $lavadores = $query->getResult();
       $options = "";
       foreach ($lavadores as $lavador) 
       {
    ?>    $("<option />", {value: <?php echo $lavador->getId();?>, text: '<?php echo  $lavador.""; ?>'}).appendTo(s);


    <?php
            //   print "personal.push(new Array(".$lavador->getId().", '$lavador'));";
       }
  ?>
      

      function cargarComboChoferes(obj){
               var i;
               if (obj.options.length < 5)
               {
                 for (i = 1; i <= personal.length; i++)
                 {
                     var elOptNew = document.createElement('option');
                     elOptNew.text = personal[i-1][1];
                     elOptNew.value = personal[i-1][0];
                     obj.options.add(elOptNew, i);
                 }
               }
      }

  </script>
<BODY>
<?php
     menu();
?>
    <br><br>
<?php
            $conn = conexcion();

            $sql = "SELECT id_micro, sum(km) as km
                    FROM ordenes o
                    inner join (select id_unidad, id_tipo_accion, max(fecha) as fecha 
                                from accionesunidades
                                where id <> 9
                                group by id_unidad, id_tipo_accion) a ON a.id_unidad = o.id_micro
                    where fservicio between fecha and date(now()) and not borrada and not suspendida
                    group by id_micro";          
          /*  $sql = "SELECT id, 0 as km
from unidades
where activo";      */                                            

            $result = mysql_query($sql, $conn);
            
            $lastAcc = array();
            while ($row = mysql_fetch_array($result)){
                  $lastAcc[$row[0]] = $row[1];

                                        }
							try{
                                        
                    $a = $entityManager->createQuery("SELECT a, max(a.fecha) fecha 
                                                      FROM AccionUnidad a 
                                                      JOIN a.unidad u
                                                      JOIN u.tipoUnidad tu
                                                      GROUP BY u.id, a.accion 
                                                      ORDER BY u.interno");
                    $resAcc = $a->getResult();

                                        $fechaAcciones = array();

                                        $now = new DateTime();
                                        foreach ($resAcc as $acc){
                                                if (!array_key_exists($acc[0]->getUnidad()->getId(), $fechaAcciones)){
                                                   $fechaAcciones[$acc[0]->getUnidad()->getId()] = array();
                                                }
                                                $interval = date_diff(new DateTime($acc['fecha']), $now);
                                                $fechaAcciones[$acc[0]->getUnidad()->getId()][$acc[0]->getAccion()->getId()] = $interval->format('%d');
                                        }
                                        $acciones = $entityManager->createQuery('SELECT t
                                                                                 FROM TipoAccionUnidad t  
                                                                                 WHERE t.edilicio = :edilicio
                                                                                 ORDER BY t.tipo')
                                                                  ->setParameter('edilicio', false)
                                                                  ->getResult();
                  }
                   catch (Exception $e) {
                                        die("error 1: ".$e->getMessage());
                                      }
                  try{
                    $str = find('Estructura', $_SESSION['structure']);
                  $internos = $entityManager->createQuery('SELECT u
                                                          FROM Unidad u  
                                                          JOIN u.propietario pro
                                                          JOIN u.tipoUnidad tu
                                                          WHERE u.activo = :activo and u.estructura = :str
                                                          ORDER BY u.interno');

                                        $internos->setParameter('activo', true);
                                        $internos->setParameter('str', $str);

                                        $results = $internos->getResult();
                                      /*  $inti = "";
                                        foreach ($results as $key) {
                                            $inti.=" , ".$key;
                                        }
                                        die($inti);*/
              }
                   catch (Exception $e) {

    }                                                    

                                    ?>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form id="upanomalia" name="upanomalia" method="post" action="vtoemp.php">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Acciones de la fecha</legend>
                         <table width="100%" border="0">
                            <tr>
                              <td>Fech Accion</td>
                              <td><input type="text" name="fecha" id="fecha" value="<?php print $hoy; ?>"/></td>
                            </tr>

                         </table> 
                         <?php

                         ?>
                         <table width="100%" class="table table-zebra">
                                <thead>
                                       <tr>
                                           <th>Interno</th>
                                           <?php
                                           foreach ($acciones as $acc) {
                                                   print "<th>".($acc->getTipo())."</th>";
                                           }
                                           ?>
                                       </tr>
                                </thead>
                                <tbody>
                                <?php
                                $body = "";
                                try{
                                     foreach ($results as $row) {
                                        
                                                $body.= "<tr><td style='vertical-align:middle'>$row<a href='#' id='".$row->getId()."'><img src='../lupa_2.png' width='15' height='15' border='0' class='cursor'></a></td>";
                                                foreach ($acciones as $acc) {
                                                   if (!$acc->getComenta()){
                                                        $dias = $fechaAcciones[$row->getId()][$acc->getId()];
                                                        if (isset($fechaAcciones[$row->getId()][$acc->getId()])){
                                                              if ($dias == 0)
                                                                 $class = 'verde';
                                                              elseif($dias == 1)
                                                                 $class = "amarillo";
                                                              else
                                                                  $class = "rojo";
                                                        }
                                                        else
                                                            $class="";
                                                        if ($lastAcc[$row->getId()] == 0)
                                                        {
                                                           $class = "blue";
                                                        }
                                                        if (($row) && ($row->admiteAccion($acc)))
                                                   
                                                        {   
                                                            
                                                        if ($acc){
                                                              $body.= "<td class='$class'>
                                                              <div>
                                                                    <select id='dat_".$row->getId()."-".$acc->getId()."' class='sizeselect select empty' style='font-size:9pt'>
                                                                            <option>Click para cargar las opciones</option>
                                                                    </select>
                                                              </div>  
                                                                                            
                                                                  <input type='button' value='+' id='".$row->getId()."-".$acc->getId()."' class='slt'>
                                                             
                                                                                                                       </td>";
                                                        }
                                                        }
                                                        else{
                                                             $body.= "<td></td>";
                                                        }
                                                   }
                                                   else{
                                                        $body.= "<td>
                                                                   <input type='text' size='30' id='cmt_".$row->getId()."-".$acc->getId()."'>
                                                                   <input type='button' value='+' class='btn' id='".$row->getId()."-".$acc->getId()."'>
                                                               </td>";
                                                   }
                                                }
                                                $body.= "</tr>";                                        

                                     }
                                     }
                                              catch (Exception $e) {die($e->getMessage());}
                                     print $body;
                                ?>
                                <tbody>
                         </table>
               </fieldset>
               <input type="hidden" name="accion" id="accion" value="sveanom"/>
         </form>
	</div>
	<div id="acciones">
	</div>
</div>

</BODY>
 <script>
	$(document).ready(function() {
        $( "#tabs" ).tabs();
        $('#acciones').dialog({autoOpen: false,
                               height: 400,
                               width: 550,
                               modal: true,
                               title: 'Acciones asociadas a la unidad'});
                               




		$(".slt").button().click(function(){
                                                    var data = $(this);
                                                    var empl = $('#dat_'+data.attr('id')).val();
                                                    data.hide();
                                                    $.post("/modelo/taller/gestionlavados.php",
                                                           {accion:'addacc', d: data.attr('id'), e:empl, fec: $("#fecha").val()},
                                                           function(res){
                                                                          //alert(res);
                                                                          var response = JSON.parse(res);
                                                                          if (response.error)
                                                                             alert(response.mje);
                                                                          else{
                                                                               $('#dat_'+data.attr('id')+' > option').attr("selected",false);
                                                                               data.parent().removeClass('rojo amarillo');
                                                                               data.parent().addClass('verde');
                                                                               data.parent().parent().removeClass('rojo amarillo');
                                                                               data.parent().parent().addClass('verde');
                                                                          }
                                                                          data.show();
                                                                          }
                                                           );

                                                    });
                                                    
		$(".btn").button().click(function(){
                                                    var ele = $(this);
                                                    var data = $(this).attr('id');
                                                    var cmt = $('#cmt_'+data).val();
                                                    ele.hide();
                                                    $.post("/modelo/taller/gestionlavados.php",
                                                           {accion:'addcmt', d:data, c:cmt, fec: $("#fecha").val()},
                                                           function(resp){
                                                                          var response = JSON.parse(resp);
                                                                          if (response.error)
                                                                             alert(response.mje);
                                                                          else
                                                                          {
                                                                              $('#cmt_'+data).val('');
                                                                          }
                                                                          ele.show();
                                                                          }

                                                           );

                                                    });

    $('td a').click(function(event) {
                                    event.preventDefault();
                                    $('#acciones').load('/modelo/taller/gestionlavados.php', {accion:'lstacc', uda:$(this).attr('id')});
                                    $('#acciones').dialog('open');
                                    }
                    );
    $('td select').focus(function(){
                                     var select = $(this);
                                     var parent = select.parent();
                                     parent.html(''); 
                                     var elem = s.clone().attr('id', select.attr('id'));         

                                     elem.appendTo(parent);
                                     $('#'+select.attr('id')).multiselect();   

               
    });

              $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
          $("#fecha").datepicker({dateFormat:'dd/mm/yy'});


	});

  

	</script>

</HTML>
