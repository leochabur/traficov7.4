<?php
  session_start();
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/bdadmin.php');
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
  include($_SERVER['DOCUMENT_ROOT'].'/modelo/utils/dateutils.php');
  
  include '../../modelsORM/manager.php';
  include_once '../../modelsORM/src/Orden.php';
  include_once('../../modelsORM/call.php');
  include_once('../../modelsORM/controller.php');


  $accion = $_POST['accion'];
  if ($accion == 'sbana')
  { //codigo para generar sabana francos

     $desde = DateTime::createFromFormat('d/m/Y', $_POST['desde']);
     $hasta = DateTime::createFromFormat('d/m/Y', $_POST['hasta']);
     
     $diff = $desde->diff($hasta);

     $dias = $diff->days;

     $nomday = array(1=>"Lu",2=>"Ma",3=>"Mi",4=>"Ju",5=>"Vi",6=>"Sa",0=>"Do");
     $optcoches="";
     $conn = conexcion(true);
     $sqlcoches = "SELECT id, interno
                            FROM unidades
                            where (activo) and (id_estructura = $_SESSION[structure])
                            order by interno";
     $result = mysqli_query($conn, $sqlcoches);

     while ($row = mysqli_fetch_array($result))
     {
           $optcoches.="<option value='$row[id]'>$row[interno]</option>";
     }
     $tabla ='<form id="formdiagrama">
               <table id="tablitasssss" align="center" width="100%" >
                     <thead>
                              <tr align="center" valign="middle">
                                 <th rowspan="2" WIDTH="150px">Servicios</th>
                                 <th rowspan="2" WIDTH="50px">Interno</th>
                                 <th colspan="'.($dias+1).'">Periodo '.$_POST['desde'].' - '.$_POST['hasta'].'</th>
                              </tr>
                              <tr align="center" valign="middle">';

      $fechaAux = clone $desde;
      for ($i = 0; $i <= $dias; $i++)
      {
         $tabla.="<th>".$nomday[$fechaAux->format('w')]."<br>".$fechaAux->format('d')."</th>";
         $fechaAux->add(new DateInterval('P1D'));
      }


      $tabla.='</tr>
               </thead>
               <tbody>';

      

      $sqlOrdenes ="SELECT s.id as idServicio, date_format(fservicio, '%d%m%Y') as fecha, o.borrada
                  from servicios s
                  join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                  join ordenes o on o.id_servicio = s.id and o.id_estructura_servicio = s.id_estructura
                  where s.activo and c.activo and c.id_estructura = $_SESSION[structure] and 
                        tipoServicio = 'charter' and fservicio between '".$desde->format('Y-m-d')."' and '".$hasta->format('Y-m-d')."'
                  ORDER BY c.nombre, s.hsalida";

      $result = mysqli_query($conn, $sqlOrdenes);

      $diagrama = array();

      while ($row = mysqli_fetch_array($result))
      {
         if (!array_key_exists($row['idServicio'], $diagrama))
         {
            $diagrama[$row['idServicio']] = array();
         }
         $diagrama[$row['idServicio']][$row['fecha']] = $row['borrada'];
      }

      $sql="SELECT s.id as idServicio, upper(c.nombre) as nombre, time_format(s.hsalida,'%H:%i') as salida, time_format(s.hllegada,'%H:%i') as llegada
            from servicios s
            join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
            where s.activo and c.activo and c.id_estructura = $_SESSION[structure] and tipoServicio = 'charter'
            order by c.nombre, hsalida";

      $result = mysqli_query($conn, $sql);

      while($row = mysqli_fetch_array($result))
      {
            $tabla.="<tr>
                        <td WIDTH='150px'>".htmlentities($row['nombre'])."</td>
                        <td><select name='unidad-$row[idServicio]'>$optcoches</select></td>";

            $fechaAux = clone $desde;
            for ($i = 0; $i <= $dias; $i++)
            {
               $key = $fechaAux->format('dmY');

               if (isset($diagrama[$row['idServicio']][$key]))
               {
                     if (!$diagrama[$row['idServicio']][$key])
                     {
                     $tabla.="<td align='center'><input type='checkbox' checked onclick='return false;'/></td>";
                     }
                     else{
                        $tabla.="<td align='center'>X</td>";
                     }
               }
               else
               {
                  $tabla.="<td align='center'><input name='servicio-$row[idServicio]-$key' type='checkbox'/></td>";
               }

               
               $fechaAux->add(new DateInterval('P1D'));
            }
            $tabla.="</tr>";
      }

      $tabla.="</tbody>
               </table>
               
               <hr>
               <input type='button' value='Diagramar Servicios' id='diagramar'/>
               <input type='hidden' name='accion' value='proccess'/>
               </form>
               <style type='text/css'>
                                    .sab{
                                             background-color: #909090;
                                    }
                                    .dom{
                                             background-color: #FFC0FF;
                                    }

                                    td.click, th.click{
                                       background-color: #bbb;
                                    }

                                    td.hover, tr.hover{
                                       background-color: #F77D7D;
                                    }

                                    th.hover, tfoot td.hover{
                                       background-color: ivory;
                                    }

                                    td.hovercell, th.hovercell{
                                       background-color: #abc;
                                    }


                                    #tablitasssss {
                                       font-family:arial;
                                       background-color: #CDCDCD;
                                       font-size: 8pt;
                                       text-align: left;
                                    }

                                    #tablitasssss tr:nth-child(even) { background: #ddd }
                                    #tablitasssss tr:nth-child(odd) { background: #fff}

                                    table thead tr th, table.tablesorter tfoot tr th {
                                       background-color: #e6EEEE;
                                       font-size: 8pt;
                                    }

                                    table thead tr .header {
                                       background-image: url(bg.gif);
                                       background-repeat: no-repeat;
                                       background-position: center right;
                                       cursor: pointer;
                                    }
               </style>
               <script type='text/javascript'>
                        $('#diagramar').button().click(function(){
                                                                   var btn = $(this);
                                                                   if (confirm('Seguro diagramar los servicios seleccionados?'))
                                                                   {
                                                                         btn.hide();
                                                                         $.post('/modelo/ordenes/diagadmin.php',
                                                                               $('#formdiagrama').serialize(),
                                                                               function(data){
                                                                                              var ok = $.parseJSON(data);
                                                                                              if (ok.ok)
                                                                                              {
                                                                                                alert('Servicios generados exitosamente!!');
                                                                                                location.reload();
                                                                                              }
                                                                                              else
                                                                                              {
                                                                                                alert(ok.message);
                                                                                                btn.show();
                                                                                              }
                                                                                 });
                                                                   }
                           });

                        $('#tablitasssss').chromatable({height: '600px', width:'100%', scrolling: 'yes'});
                        $('#tablitasssss').tableHover({colClass: 'hover', cellClass: 'hovercell', clickClass: 'click', ignoreCols: [1]});
               </script>";
              mysqli_free_result($result);
              mysqli_close($conn);
              print $tabla;
  }
  elseif($accion == 'proccess')
  {
      $entityManager = $GLOBALS['entityManager'];

      $adiagramar = array_filter(array_keys($_POST), 
                   function($val){
                                 if (strpos($val, 'servicio') === 0) 
                                 {
                                    return true;
                                 }
                                 return false;
                   });
      $usuario = find('Usuario', $_SESSION['structure']);
      foreach ($adiagramar as $a)
      {
         $detail = explode("-", $a);
         $idServicio = $detail[1];
         $fecha = DateTime::createFromFormat('dmY', $detail[2]);

         $idUnidad = $_POST["unidad-$idServicio"];
         $unidad = find('Unidad', $idUnidad);

         $servicio = getServicio($idServicio, $_SESSION['structure']);

         if ($servicio)
         {

           $query = ejecutarSQL("SELECT AUTO_INCREMENT
                                 FROM information_schema.tables
                                 WHERE table_name = 'ordenes'");
            $nextId = 0;
            if ($row = mysql_fetch_array($query))
            {
                $nextId = $row['AUTO_INCREMENT'];
            }
            else
            {
                print json_encode(array('ok' => false, 'message' => 'No se pudo ecnnstrar el identificador de la tabla de Novedades!'));
                return;
            }

            $cronograma = $servicio->getCronograma();
            try
            {
               $orden = new Orden($nextId);
               $orden->setFservicio($fecha)
                     ->setEstructura($cronograma->getEstructura())
                     ->setNombre($cronograma->getNombre())
                     ->setHcitacion($servicio->getCitacion())
                     ->setHsalida($servicio->getSalida())
                     ->setHllegada($servicio->getLlegada())
                     ->setHfina($servicio->getFinServicio())
                     ->setKm($cronograma->getKm())
                     ->setServicio($servicio)
                     ->setOrigen($cronograma->getOrigen())
                     ->setDestino($cronograma->getDestino())
                     ->setCliente($cronograma->getCliente())
                     ->setVacio(false)
                     ->setFinalizada(false)
                     ->setBorrada(false)
                     ->setUnidad($unidad)
                     ->setUsuario($usuario);
               $entityManager->persist($orden);
               $entityManager->flush();
            }
            catch (Exception $e)
            {
                print json_encode(array('ok' => false, 'message' => 'Error al generar las ordenes!'));
                return;
            }

         }
      }
                  
    print json_encode(array('ok' => true));
  }
?>

