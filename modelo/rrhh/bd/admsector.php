<?php

  session_start();
  error_reporting(0);
  ////////////////// modulo para dar de alta y mdificar un conductore en la BD  /////////////////////
  include ('../../../controlador/bdadmin.php');
  include ('../../../controlador/ejecutar_sql.php');
  include ('../../../vista/paneles/viewpanel.php');
  include_once ('../../utils/dateutils.php'); 

  $accion = $_POST['accion'];

  if($accion == 'list')
  {
     $conn = conexcion(true);

     $estructuras = ejecutarSQLPDO("SELECT * FROM estructuras WHERE activo ORDER BY nombre", $conn);
     $optionsStr = $optionsSector = '';

     while ($row = mysqli_fetch_array($estructuras))
     {
        $optionsStr .="<option value='$row[id]'>$row[nombre]</option>";
     }

     $sectores = ejecutarSQLPDO("SELECT descripcion, id FROM sector s WHERE activo ORDER BY descripcion", $conn);
     while ($row = mysqli_fetch_array($sectores))
     {
        $optionsSector .="<option value='$row[id]'>$row[descripcion]</option>";
     }

     $sql = "SELECT c.id as id, 
                   upper(c.codigo) as codigoPuesto, 
                   upper(c.descripcion) as puesto, 
                   upper(s.descripcion) as sector, 
                   upper(nombre) as estructura, 
                   c.activo,
                   s.id as idSector,
                   e.id as idEstructura
            FROM cargo c
            left join sector s on s.id = c.id_sector
            join estructuras e on e.id = c.id_estructura
            order by e.nombre, c.descripcion";

     $result = mysqli_query($conn, $sql);

     $table = "<table class='table table-zebra' width='100%'>
                <thead>
                    <tr>
                        <th>Codigo Puesto</th>
                        <th>Puesto</th>
                        <th>Sector</th>
                        <th>Estructura</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>";
    while ($row = mysqli_fetch_array($result))
    {
        $table.="<tr>
                    <td>$row[codigoPuesto]</td>
                    <td>$row[puesto]</td>
                    <td>$row[sector]</td>
                    <td>$row[estructura]</td>
                    <td>".($row['activo']?'Activado':'Desactivado')."</td>
                    <td><a class='updpto' data-id='$row[id]' 
                                          data-code='$row[codigoPuesto]' 
                                          data-puesto='$row[puesto]' 
                                          data-sector='$row[idSector]'
                                          data-str='$row[idEstructura]'
                                          data-activo='$row[activo]'
                                          >
                                                                        <i class='fas fa-screwdriver'></i>
                        </a>
                    </td>
                </tr>";
    }
    $table.="</tbody>
             </table>";

    $updatePuesto = '
                      <style>
                              .formulario1 {
                                            margin-top: 10px;
                                            margin-right: 20px;
                                            margin-left: 20px;
                                            margin-bottom: 20px;
                                          }
                      </style>
                      <div id="updatepto" title="">                         
                          <form id="updatep">
                            <fieldset>
                              <label for="codp">Codigo Puesto</label>
                              <input type="text" name="codp" id="codp" value="Jane Smith" class="required text ui-widget-content ui-corner-all formulario1">
                              <label for="nomp">Puesto</label>
                              <input type="text" name="nomp" id="nomp" class="required text ui-widget-content ui-corner-all formulario1">
                              <label for="sectorp">Sector</label>
                              <select name="sectorp" id="sectorp" class="selector formulario1">
                              '.$optionsSector.'
                              </select>   
                              <br>   
                              <label for="estadop">Activo</label>
                              <br>
                              <input type="checkbox" name="estadop" id="estadop" class="ui-widget-content ui-corner-all formulario1">     
                              <br>
                              <input type="submit" id="changep" value="Guardar Cambios">            
                            </fieldset>
                            <input type="hidden" name="accion" value="updatep"/>
                            <input type="hidden" name="strpto" id="strpto"/>
                            <input type="hidden" name="idp" id="idp"/>
                          </form>
                     </div>
                     <script>
                            $("#updatep").validate({
                                                  submitHandler: function(e){
                                                                             var datos = $("#updatep").serialize();
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
                            $("#changep").button();
                            $(".updpto").click(function(event) {
                                                                  event.preventDefault();
                                                                  let pto = $(this);
                                                                  $("#codp").val(pto.data("code"));
                                                                  $("#nomp").val(pto.data("puesto"));
                                                                  $("#strpto").val(pto.data("str"));
                                                                  $("#idp").val(pto.data("id"));
                                                                  $("#updatepto").dialog( "open" );
                                                                  $("#sectorp option[value="+ pto.data("sector") +"]").attr("selected" , "selected" );
                                                                  $(".selector").selectmenu({width: 300}); 
                                                                  $("#estadop").prop("checked", pto.data("activo"));

                              });

                            $( "#updatepto" ).dialog({  
                                                        modal: true,
                                                        height: 450,
                                                        width: 450,
                                                        autoOpen: false
                              });

                      </script>';
    $table.=$updatePuesto;

     $sql = "SELECT id, upper(codigo) as codigo, upper(descripcion) as descripcion, activo FROM sector ORDER BY descripcion";

     $result = mysqli_query($conn, $sql);

     $tableSector = "<table class='table table-zebra' width='100%'>
                <thead>
                    <tr>
                        <th>Codigo</th>
                        <th>Descripcion</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>";
    while ($row = mysqli_fetch_array($result))
    {
        $tableSector.="<tr>
                    <td>$row[codigo]</td>
                    <td>$row[descripcion]</td>
                    <td>".($row['activo']?'Activado':'Desactivado')."</td>
                    <td><a class='updsec' data-id='$row[id]' 
                                          data-code='$row[codigo]' 
                                          data-sector='$row[descripcion]'
                                          data-activo='$row[activo]'
                                          >
                                                                        <i class='fas fa-screwdriver'></i>
                        </a>
                    </td>
                </tr>";
    }
    $tableSector.="</tbody></table>";

    $updateSector = '
                      <style>
                              .formulario1 {
                                            margin-top: 10px;
                                            margin-right: 20px;
                                            margin-left: 20px;
                                            margin-bottom: 20px;
                                          }
                      </style>
                      <div id="updatesecdialog" title="">                         
                          <form id="updatesec">
                            <fieldset>
                              <label for="codsec">Codigo Sector</label>
                              <input type="text" name="codsec" id="codsec" class="required text ui-widget-content ui-corner-all formulario1">
                              <label for="nomsec">Sector</label>
                              <input type="text" name="nomsec" id="nomsec" class="required text ui-widget-content ui-corner-all formulario1">
                              <br>   
                              <label for="estados">Activo</label>
                              <br>
                              <input type="checkbox" name="estados" id="estados" class="ui-widget-content ui-corner-all formulario1">    
                              <br>
                              <input type="submit" id="changesec" value="Guardar Cambios">            
                            </fieldset>
                            <input type="hidden" name="accion" value="updatesec"/>
                            <input type="hidden" name="ids" id="ids"/>
                          </form>
                     </div>
                     <script>
                            $("#updatesec").validate({
                                                  submitHandler: function(e){
                                                                             var datos = $("#updatesec").serialize();
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
                            $("#changesec").button();
                            $(".updsec").click(function(event) {
                                                                  event.preventDefault();
                                                                  let pto = $(this);
                                                                  $("#codsec").val(pto.data("code"));
                                                                  $("#nomsec").val(pto.data("sector"));
                                                                  $("#ids").val(pto.data("id"));
                                                                  $("#updatesecdialog").dialog( "open" );
                                                                  $("#estados").prop("checked", pto.data("activo"));

                              });

                            $( "#updatesecdialog" ).dialog({  
                                                        modal: true,
                                                        height: 300,
                                                        width: 450,
                                                        autoOpen: false
                              });

                      </script>';
    $tableSector.= $updateSector;
    print json_encode(array( 0 => $table, 1 => $tableSector));
  }
   elseif($accion == 'svepto')
   {
        $conn = conexcion(true);
        $response = array();
   		try
        {
          
          $sql = "SELECT * FROM cargo c WHERE activo AND descripcion = '$_POST[puesto]' AND id_estructura = $_POST[estructura] AND id_sector = $_POST[sector]";

          $result = mysqli_query($conn, $sql);
          if ($data = mysqli_fetch_array($result))
          {
            $response['ok'] = false;
            $response['message'] = 'Existe un puesto activo para el sector en la estructura seleccionada.';
          }
          else
          {
              $sqlNextId = "SELECT max(id) as next FROM cargo";
              $resultNext = mysqli_query($conn, $sqlNextId);
              if ($data = mysqli_fetch_array($resultNext))
              {
                    $next = ($data['next'] + 1);
                    $insert = "INSERT INTO cargo (codigo, descripcion, id, id_estructura, id_sector, activo) VALUES ('$_POST[codigo]', '$_POST[puesto]', $next, $_POST[estructura], $_POST[sector], 1)";
                    mysqli_query($conn, $insert);
                    $response['ok'] = true;
              }
              else
              {
                    $response['ok'] = false;
                    $response['message'] = 'No se pudo recuperar el identificador en la tabla de puestos';
              }
          }
          mysqli_free_result($result);
         }
         catch (Exception $e) {
                                $response['ok'] = false;
                                $response['message'] = 'Error desconocido. '.$e->getMessage();
                              }
        mysqli_close($conn);
        print json_encode($response);
        
  }
  elseif($accion == 'svectr')
   {
        $conn = conexcion(true);
        $response = array();
        try
        {
          
          $sql = "SELECT * FROM sector WHERE activo AND descripcion = '$_POST[sector]'";

          $result = mysqli_query($conn, $sql);
          if ($data = mysqli_fetch_array($result))
          {
            $response['ok'] = false;
            $response['message'] = 'Existe sector con la descripcion ingresada.';
          }
          else
          {
              $sqlNextId = "SELECT max(id) as next FROM sector";
              $resultNext = mysqli_query($conn, $sqlNextId);
              if ($data = mysqli_fetch_array($resultNext))
              {
                    $next = ($data['next'] + 1);
                    $insert = "INSERT INTO sector (codigo, descripcion, id, activo) VALUES ('$_POST[codigo]', '$_POST[sector]', $next,  1)";
                    mysqli_query($conn, $insert);
                    $response['ok'] = true;
              }
              else
              {
                    $response['ok'] = false;
                    $response['message'] = 'No se pudo recuperar el identificador en la tabla de sectores';
              }
          }
          mysqli_free_result($result);
         }
         catch (Exception $e) {
                                $response['ok'] = false;
                                $response['message'] = 'Error desconocido. '.$e->getMessage();
                              }
        mysqli_close($conn);
        print json_encode($response);
  }
  elseif($accion == 'updatep')
  {
      $conn = conexcion(true);
      $response = array();
      try
        {
          
         /* $sql = "SELECT * FROM cargo c WHERE activo AND descripcion = '$_POST[nomp]' AND id_estructura = $_POST[strpto] AND id_sector = $_POST[sectorp]";

          $result = mysqli_query($conn, $sql);
          if ($data = mysqli_fetch_array($result))
          {
            $response['ok'] = false;
            $response['message'] = 'Existe un puesto activo para el sector en la estructura seleccionada.';
          }
          else
          {*/
              $activo = (isset($_POST['estadop'])?1:0);
              $update = "UPDATE cargo SET codigo = '$_POST[codp]', descripcion = '$_POST[nomp]', id_sector = $_POST[sectorp], activo = $activo WHERE id = $_POST[idp]";
              mysqli_query($conn, $update);
              $response['ok'] = true;
         // }
         // mysqli_free_result($result);
         }
         catch (Exception $e) {
                                $response['ok'] = false;
                                $response['message'] = 'Error desconocido. '.$e->getMessage();
                              }
        mysqli_close($conn);
        print json_encode($response);

  }
  elseif($accion == 'updatesec')
  {
      $conn = conexcion(true);
      $response = array();
      try
        {
          
              $activo = (isset($_POST['estados'])?1:0);
              $update = "UPDATE sector SET codigo = '$_POST[codsec]', descripcion = '$_POST[nomsec]', activo = $activo WHERE id = $_POST[ids]";
              mysqli_query($conn, $update);
              $response['ok'] = true;

         }
         catch (Exception $e) {
                                $response['ok'] = false;
                                $response['message'] = 'Error desconocido. '.$e->getMessage();
                              }
        mysqli_close($conn);
        print json_encode($response);

  }


?>

