<?php
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  include ('../../vista/paneles/viewpanel.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];

  if ($accion == 'sve'){ ///codigo para guardar ////
    try{
     $conn = conexcion();
     $sql = "SELECT * FROM unidades WHERE (interno = $_POST[interno]) and (id_propietario = $_POST[propietario])";
     $result = mysql_query($sql, $conn);
     if (mysql_num_rows($result)){
        $ok = "iebd"; //codigo de error para indicar que ya existe el numero de interno que se esta intentando asignar
     }
     else{
          if ($_SESSION['permisos'][4] > 2){
             $campos = "id, anio, interno, patente, nueva_patente, marca, modelo, cantasientos, id_pase, video, bar, banio, activo, id_estructura, id_calidadcoche, id_estructura_calidadcoche, id_tipounidad, id_estructura_tipounidad, consumo, id_propietario, id_estructura_propietario";
             $video = $_POST['video'] ? 1 : 0;
             $banio = $_POST['banio'] ? 1 : 0;
             $bar = $_POST['bar'] ? 1 : 0;
             $values = "$_POST[anio], $_POST[interno], '$_POST[dominio]', '$_POST[n_dominio]', '$_POST[marca]', '$_POST[modelo]', '$_POST[cantas]', '0', $video, $bar, $banio, 1, $_SESSION[structure], $_POST[calidad], $_SESSION[structure], $_POST[tipo], $_SESSION[structure], '$_POST[consumo]', $_POST[propietario], $_SESSION[structure]";
          }
          else{
               $campos = "id, interno, patente, nueva_patente, id_estructura, id_propietario, id_estructura_propietario";
               $values = "$_POST[interno], '$_POST[dominio]', '$_POST[n_dominio]', $_SESSION[structure], $_POST[propietario], $_SESSION[structure]";
          }
          $ok = insert('unidades', $campos, $values);
     }
     //cerrarconexcion($conn);
     print json_encode($ok);
   }
   catch (Exception $e){print $e->getMessage();}
  }
  elseif($accion == 'sveevt'){
     $conn = conexcion();
     $sql = "SELECT *
             FROM unidades
             WHERE (interno = $_POST[n_interno]) and (id_propietario = $_POST[propietario]) and (id_estructura_propietario,$_SESSION[structure])";
     $result = mysql_query($sql, $conn);
     cerrarconexcion($conn);
     if (mysql_fetch_array($result)){
        $ok = "0"; //codigo de error para indicar que ya existe el numero de interno que se esta intentando asignar
     }
     else{
          $ok = insert('unidades', 'id, interno, id_estructura, procesado, id_propietario, id_estructura_propietario', "$_POST[n_interno], $_SESSION[structure], 0, $_POST[propietario], $_SESSION[structure]"); //agrega una unidad pendiente de procesamiento por parte del sector  seg vial
     }
     print json_encode($ok);
  }
  elseif($accion == 'load'){
          $conn = conexcion();
          $sql = "SELECT u.id as id_unidad, interno, patente, upper(nueva_patente) as nueva_patente, marca, modelo, marca_motor, anio, cantasientos, if(u.activo, 'checked', '') as activo, if(video, 'checked', '') as video, if(bar, 'checked', '') as bar, if(banio, 'checked', '') as banio, id_calidadcoche, id_tipounidad, consumo, id_propietario, calidad,  tipo, e.id as id_propietario, e.razon_social
                  FROM (SELECT * FROM unidades WHERE id = $_POST[unidad]) u
                  LEFT JOIN calidadcoche cc ON (cc.id = u.id_calidadcoche) and (cc.id_estructura = u.id_estructura_calidadcoche)
                  LEFT JOIN tipounidad tu ON (tu.id = u.id_tipounidad) and (tu.id_estructura = u.id_estructura_tipounidad)
                  LEFT JOIN empleadores e ON (e.id = u.id_propietario) and (e.id_estructura = u.id_estructura_propietario)";

          $result = mysql_query($sql, $conn);
          $data = mysql_fetch_array($result);
          cerrarconexcion($conn);
          print '<fieldset class="ui-widget ui-widget-content ui-corner-all">
                <legend class="ui-widget ui-widget-header ui-corner-all">Datos de la Unidad</legend>
                <form id="modunidad">
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Propietario</td>
                                    <td>
                                        <select id="propietario" name="propietario" class="ui-widget ui-widget-content  ui-corner-all">'.
                                                '<option value="'.$data['id_propietario'].'">'.$data['razon_social'].'</option>'.
                                                armarSelect('empleadores', 'razon_social', 'id', 'razon_social', STRUCTURED,1).
                                       '</select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Dominio</td>
                                    <td><input id="dominio" name="dominio" size="8" value="'.$data['patente'].'" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Nuevo Dominio</td>
                                    <td><input id="n_dominio" name="n_dominio" size="9" value="'.$data['nueva_patente'].'" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Marca</td>
                                    <td><input id="marca" name="marca" value="'.$data['marca'].'" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Modelo</td>
                                    <td><input id="modelo" name="modelo" value="'.$data['modelo'].'" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Marca Motor</td>
                                    <td><input id="motor" name="motor" value="'.$data['marca_motor'].'" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">A&ntilde;o</td>
                                    <td><input id="anio" name="anio" size="4" value="'.$data['anio'].'" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="razon">Cant. Asientos</label></td>
                                    <td><input id="cantas" name="cantas" size="2" value="'.$data['cantasientos'].'" class="ui-widget ui-widget-content ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="razon">Consumo c/ 100 Km</label></td>
                                    <td><input id="consumo" name="consumo" value="'.$data['consumo'].'" size="2" class="ui-widget ui-widget-content ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="razon">Servicios</label></td>
                                    <td>Video<input name="video" '.$data['video'].' type="checkbox" class="ui-widget ui-widget-content  ui-corner-all">&nbsp;Bar<input name="bar" type="checkbox" '.$data['bar'].' class="ui-widget ui-widget-content  ui-corner-all">&nbsp;Ba&ntilde;o<input name="banio" type="checkbox" '.$data['banio'].' class="ui-widget ui-widget-content  ui-corner-all"></td>
                                    <td></td>
                                </tr>
                                <td WIDTH="20%">Tipo Unidad</td>
                                    <td><select id="tipo" name="tipo" class="ui-widget ui-widget-content  ui-corner-all" >'.
                                    '<option value="'.$data['id_tipounidad'].'">'.$data['tipo'].'</option>'.
                                    armarSelect('tipounidad', 'tipo', 'id', 'tipo', "(id_estructura = ".STRUCTURED.")", 1).
                              '</select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                <td WIDTH="20%">Calidad</td>
                                    <td><select id="calidad" name="calidad" class="ui-widget ui-widget-content  ui-corner-all">'.
                                    '<option value="'.$data['id_calidadcoche'].'">'.$data['calidad'].'</option>'.
                                    armarSelect('calidadcoche', 'calidad', 'id', 'calidad', "(id_estructura = ".STRUCTURED.")",1).
                            '</select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="razon">Activo</label></td>
                                    <td><input name="activo" '.$data['activo'].' type="checkbox" class="ui-widget ui-widget-content  ui-corner-all"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right"><input type="submit" id="save" value="Guardar Unidad"/> </td>
                                </tr>
                         </table>

            <input type="hidden" name="accion" value="upduda">
            <input type="hidden" name="id_unidad" value="'.$data['id_unidad'].'">
            </fieldset>
         </form>
         </fieldset>
         <style type="text/css">
                #modunidad .error{
	                    font-size:0.8em;
	                    color:#ff0000;
                 }
         </style>
         <script type="text/javascript">
                          $(document).ready(function(){
                                                       $("#save").button();
                                                       $("select").selectmenu({width: 250});
                                                        $("#dominio").mask("aaa-999");
                                                        $("#n_dominio").mask("aa-999-aa");
                                                       $("#anio").mask("9999");
                                                       $("#interno").mask("9999");
                                                       $("#cantas").mask("99");
                                                       $("#consumo").mask("99");
                                                       $("#modunidad").validate({
                                                                              submitHandler: function(e){
                                                                                                                 var datos = $("#modunidad").serialize();
                                                                                                                 $.post("/modelo/segvial/altauda.php", datos, function(data){
                                                                                                                                                                             if (data == 1){
                                                                                                                                                                                      var mje = "<div class=\"ui-widget\">"+
                                                                                                                                                                                                 "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                                                                 "<p><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                                                 "<strong>Se ha modificado con exito la unidad en la Base de Datos!</strong></p>"+
                                                                                                                                                                                                 "</div>"+
                                                                                                                                                                                                 "<div>";
                                                                                                                                                                                       $("#data").html(mje);

                                                                                                                                                                             }
                                                                                                                                                                             });
                                                                                                        }
                                                                              });
                          });

         </script>';
  }
  elseif($accion == 'upduda'){
          $video = $_POST['video'] ? 1 : 0;
          $banio = $_POST['banio'] ? 1 : 0;
          $bar = $_POST['bar'] ? 1 : 0;
          $activo = $_POST['activo'] ? 1 : 0;
          
          $campos = "marca_motor, anio, patente, nueva_patente, marca, modelo, cantasientos, video, bar, banio, activo, consumo, id_propietario, id_estructura_propietario";
          $values = "'$_POST[motor]', '$_POST[anio]', '$_POST[dominio]', '$_POST[n_dominio]', '$_POST[marca]', '$_POST[modelo]', '$_POST[cantas]', $video, $bar, $banio, $activo, '$_POST[consumo]', $_POST[propietario], $_SESSION[structure]";
          
          if ($_POST['calidad']){
             $campos.=", id_calidadcoche, id_estructura_calidadcoche";
             $values.=", $_POST[calidad], $_SESSION[structure]";
          }
          if ($_POST['tipo']){
             $campos.=", id_tipounidad, id_estructura_tipounidad";
             $values.=", $_POST[tipo], $_SESSION[structure]";
          }
          print update("unidades", $campos, $values, "(id = $_POST[id_unidad])and(id_estructura = $_SESSION[structure])");

  }
  elseif($accion == 'list'){
      if ($_POST['propietario']){
          $sql="SELECT upper(e.nombre) as estr, u.id as id_unidad, interno, patente, upper(nueva_patente) as nueva_patente, marca, modelo, marca_motor, anio, cantasientos, if(u.activo, 'checked', '') as activo, if(video, 'checked', '') as video, if(bar, 'checked', '') as bar, if(banio, 'checked', '') as banio, id_calidadcoche, id_tipounidad, consumo, calidad,  tipo
                FROM (SELECT * FROM unidades WHERE (activo = $_POST[state]) and (id_propietario = $_POST[propietario]) and (id_estructura_propietario = $_SESSION[structure])) u
                LEFT JOIN calidadcoche cc ON (cc.id = u.id_calidadcoche) and (cc.id_estructura = u.id_estructura_calidadcoche)
                LEFT JOIN tipounidad tu ON (tu.id = u.id_tipounidad) and (tu.id_estructura = u.id_estructura_tipounidad)
                left join estructuras e on e.id = u.id_estructura
                ORDER BY u.interno";
          }
          else{
          $sql="SELECT upper(e.nombre) as estr, u.id as id_unidad, interno, patente, upper(nueva_patente) as nueva_patente, marca, modelo, marca_motor, anio, cantasientos, if(u.activo, 'checked', '') as activo, if(video, 'checked', '') as video, if(bar, 'checked', '') as bar, if(banio, 'checked', '') as banio, id_calidadcoche, id_tipounidad, consumo, calidad,  tipo,
                upper(razon_social) as razon_social
                FROM unidades u
                LEFT JOIN calidadcoche cc ON (cc.id = u.id_calidadcoche) and (cc.id_estructura = u.id_estructura_calidadcoche)
                LEFT JOIN tipounidad tu ON (tu.id = u.id_tipounidad) and (tu.id_estructura = u.id_estructura_tipounidad)
                left join estructuras e on e.id = u.id_estructura
                inner join empleadores emp on emp.id = u.id_propietario and emp.id_estructura = id_estructura_propietario
                where (u.activo = $_POST[state]) and u.id_estructura = $_SESSION[structure] 
                ORDER BY razon_social, u.interno";
          }
          $conn = conexcion();
          $result = mysql_query($sql, $conn) or die (mysql_error($conn));
          $tabla = "<fieldset class='ui-widget ui-widget-content ui-corner-all'>
                    <legend class='ui-widget ui-widget-header ui-corner-all'>Unidades</legend>
                    <table id='example' align='center' border='0' width='100%'>
                     <thead>
            	            <tr>";
                                  if (!$_POST['propietario'])
                                     $tabla.="<th>Propietario</th>";
                                $tabla.="<th>Interno</th>
                                <th>Dominio</th>
                                <th>Nuevo Dominio</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Cant. As.</th>
                                <th>Consumo</th>
                                <th>Tipo</th>
                                <th>Calidad</th>
                                <th>Afectado a Estructura</th>
                                <th>Activo</th>
                            </tr>
                     </thead>
                     <tbody>";
          while($data = mysql_fetch_array($result)){
                      $tabla.="<tr>";
                                  if (!$_POST['propietario'])
                                     $tabla.="<td>$data[razon_social]</td>";
                                   $tabla.="<td><a href='../../vista/segvial/moduda.php?int=$data[id_unidad]'>$data[interno]</a></td>
                                   <td>$data[patente]</td>
                                   <td>$data[nueva_patente]</td>
                                   <td>$data[marca]</td>
                                   <td>$data[modelo]</td>
                                   <td>$data[cantasientos]</td>
                                   <td>$data[consumo]</td>
                                   <td>$data[tipo]</td>
                                   <td>$data[calidad]</td>
                                   <td>$data[estr]</td>
                                   <td><input type='checkbox' $data[activo] readonly='readonly' onClick='cambioEstado($data[id_unidad],this.checked);'></td>
                               </tr>";
          }
          $tabla.="</tbody>
                  </table>
                  </fieldset>
                  <style type='text/css'>
                         #example { font-size: 85%; }
                         #example tbody tr.even:hover, #example tbody tr.even td.highlighted {background-color: #ECFFB3;}
                         #example tbody tr.odd:hover, #example tbody tr.odd td.highlighted {background-color: #E6FF99;}
                         #example tr.even:hover {background-color: #ECFFB3;}
                         #example tr.even:hover td.sorting_1 {background-color: #DDFF75;}
                         #example tr.even:hover td.sorting_2 {background-color: #E7FF9E;}
                         #example tr.even:hover td.sorting_3 {background-color: #E2FF89;}
                         #example tr.odd:hover {background-color: #E6FF99;}
                         #example tr.odd:hover td.sorting_1 {background-color: #D6FF5C;}
                         #example tr.odd:hover td.sorting_2 {background-color: #E0FF84;}
                         #example tr.odd:hover td.sorting_3 {background-color: #DBFF70;}
                  </style>
                  <script type='text/javascript'>
                          		$('#example').dataTable({
                                                         'sScrollY': '350px',
					                                    'bPaginate': false,
					                                    'bScrollCollapse': true,
					                                    'bJQueryUI': true,
					                                    'oLanguage': {
                                                                     'sLengthMenu': '',
                                                                     'sZeroRecords': 'Sin Registros para mostrar',
                                                                     'sInfo': '',
                                                                     'sInfoEmpty': '',
                                                                     'sInfoFiltered': ''}
				                                       });
                          
                          
                          
                          
                          function cambioEstado(id, state){
                                   if (state){
                                      $.post('/modelo/segvial/altauda.php',{coche: id, accion:'change', st:'up'});
                                   }
                                   else{
                                        $.post('/modelo/segvial/altauda.php',{coche: id, accion:'change', st:'down'});
                                   }
                          }
                  </script>
                  ";
          mysql_free_result($result);
          cerrarconexcion($conn);
          print $tabla;
  }
  elseif($accion == 'change'){

                 if ($_POST['st'] == 'up'){
                    update("unidades", "activo", "1", "(id = $_POST[coche])");
                 }
                 elseif ($_POST['st'] == 'down'){
                    update("unidades", "activo", "0", "(id = $_POST[coche])");
                 }
  }
?>

