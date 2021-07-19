<?php
     set_time_limit(0);
     //error_reporting(E_ALL);
     session_start();


     include_once('../main.php');
     include_once('../../modelo/utils/dateutils.php');     
     define(RAIZ, '');


     encabezado('Menu Principal - Sistema de Administracion - Campana');
     
    $todas="";
	$notodas="";

	if (isset($_POST['mostrar'])){
       $_SESSION['todas'] = $_POST['mostrar'];
    }

     
     if (isset($_POST['fecha'])){
        $fec = $_POST['fecha'];
        $fecha = explode("/", $fec);
        $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
        $fechaVis = $_POST['fecha'];
     }
     elseif(isset($_GET['fecha'])){
        $fec = $_GET['fecha'];
        $fecha = explode("/", $fec);
        $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
        $fechaVis = $_GET['fecha'];
     }
     else{
         $fecha = date("Y-m-d");
         $fechaVis = date("d/m/Y");
     }
     
     $orden="hcitacion";
     if (isset($_POST['order']) && ($_POST['order'] != '')){
        $orden = $_POST['order'];
     }

     $tomorrow  = mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));//date("Y")."-".date("m")."-".(date("d")+1);
     $maniana= date("Y-m-d", $tomorrow);
     //$maniana= date("Y")."-".date("m")."-".(date("d")+1);

?>
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet"/>
<link type="text/css" href="/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />


 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.ui.selectmenu.js"></script>




 <script>
        $(document).ready(function(){
                                      <?php
                                        if ($_POST['interno'])
                                          print "$('select option[value=$_POST[interno]]').attr('selected','selected');";
                                      ?>
                                      $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                                      $(':submit').button();
                                      $('select').selectmenu({width: 350});

          });

	</script>
  <style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend {padding: 0.5em;}
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}
.button-small{font-size: 62.5%;}
div#users-contain {margin: 20px 0; font-size: 62.5%;}
div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
//input.text { margin-bottom:12px; width:95%; padding: .4em; }
#upcontact .error{
  font-size:0.8em;
  color:#ff0000;
}

#upnwserv .error{
  font-size:0.8em;
  color:#ff0000;
}

fieldset .div {
  padding-top: 7px;
  padding-right: 7px;
  padding-bottom: 7px;
  padding-left: 7px;
}

form table td{
  padding-top: 3px;
  padding-right: 3px;
  padding-bottom: 3px;
  padding-left: 3px;
}

</style>


<BODY>
<?php
     menu();

?>
<br>
<br>
  <div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form method="post">
             <fieldset class="ui-widget ui-widget-content ui-corner-all">
                     <legend class="ui-widget ui-widget-header ui-corner-all">Diagrama de Trabajo</legend>
                      <table border="0" align="center">
                        <tr>
                          <td>Fecha </td>
                          <td><input type="text" name="fecha" id="fecha" class="ui-widget ui-widget-content ui-corner-all" <?php print ($_POST['fecha']?"value='".$_POST['fecha']."'":'');?>></td>
                        </tr>
                        <tr>
                          <td>Interno</td>
                          <td>
                              <select name="interno" name="interno">
                                  <option value="0">Todos</option>
                                  <?php
                                      $conn = conexcion();   
                                      $sql = "select u.id, concat(interno, if(e.id = 1,'', concat(' - ',razon_social))) as interno
                                              from unidades u
                                              left join empleadores e on e.id = u.id_propietario  and e.id_estructura = u.id_estructura_propietario
                                              where u.activo and e.activo and u.id_estructura = $_SESSION[structure]
                                              order by e.id, u.interno";
                                      $result = mysql_query($sql, $conn);
                                      while ($row = mysql_fetch_array($result)){
                                        print "<option value='$row[0]'>$row[1]</option>";
                                      }
                                  ?>
                              </select>

                          </td>
                        </tr>
                        <tr>
                          <td></td>
                          <td><input type="submit" id="send" value="Cargar Ordenes"></td>
                        </tr>

                      </table>
                      
                      <?php
                        if (isset($_POST['fecha']))
                        {
                            $ids = array();
                              
                            $fecha = dateToMysql($_POST['fecha'],'/');                      
                            $sql = "select date_format(fservicio, '%d/%m/%Y') as fecha, time_format(hsalida, '%H:%i') as sale, time_format(hllegada, '%H:%i') as llega, o.nombre,
                                           concat(e1.apellido,', ',e1.nombre) as ch1, concat(e2.apellido,', ',e2.nombre) as ch2, interno, o.id, hsalida
                                    from ordenes o
                                    left join empleados e1 on e1.id_empleado = o.id_chofer_1
                                    left join empleados e2 on e2.id_empleado = o.id_chofer_2
                                    left join unidades u on u.id = o.id_micro
                                    where o.fservicio = '$fecha' and not suspendida and not borrada and o.nombre like '%combustible%' and o.id_estructura = $_SESSION[structure]
                                    union all
                                    select date_format(fservicio, '%d/%m/%Y') as fecha, time_format(hsalida, '%H:%i') as sale, time_format(hllegada, '%H:%i') as llega, o.nombre,
                                           concat(e1.apellido,', ',e1.nombre) as ch1, concat(e2.apellido,', ',e2.nombre) as ch2, interno, o.id, hsalida
                                    from ordenes o
                                    left join empleados e1 on e1.id_empleado = o.id_chofer_1
                                    left join empleados e2 on e2.id_empleado = o.id_chofer_2
                                    left join unidades u on u.id = o.id_micro
                                    where o.fservicio = '$fecha' and not suspendida and not borrada and id_claseservicio = 4 and o.id_estructura = $_SESSION[structure]
                                    order by hsalida";
							//die($sql);
									//order by nombre, sale";
                            $result = mysql_query($sql, $conn) or die("error");
                            print "<table class='table table-zebra'>
                                      <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Servicio</th>
                                            <th>Inicio</th>
                                            <th>Fin</th>
                                            <th>Interno</th>
                                            <th>Conductor 1</th>
                                            <th>Conductor 2</th>
                                        </tr>
                                      </thead>
                                      <tbody>";                                      
                            while ($row = mysql_fetch_array($result)){
                              $ids[] = $row['id'];
                              print "<tr>
                                        <td>$row[fecha]</td>
                                        <td>$row[nombre]</td>
                                        <td>$row[sale]</td>
                                        <td>$row[llega]</td>
                                        <td>$row[interno]</td>
                                        <td>$row[ch1]</td>
                                        <td>$row[ch2]</td>
                                     </tr>";
                            }
                            if ($_POST['interno'])
                              $coches = "INNER JOIN (SELECT * FROM unidades WHERE id = $_POST[interno])";
                            else
                              $coches = "LEFT JOIN unidades";
                            $sql= "select date_format(fservicio, '%d/%m/%Y') as fecha, time_format(hsalida, '%H:%i') as sale, time_format(hllegada, '%H:%i') as llega, o.nombre,
                                           concat(e1.apellido,', ',e1.nombre) as ch1, concat(e2.apellido,', ',e2.nombre) as ch2, interno, o.id
                                    from ordenes o
                                    left join empleados e1 on e1.id_empleado = o.id_chofer_1
                                    left join empleados e2 on e2.id_empleado = o.id_chofer_2
                                    $coches u on u.id = o.id_micro
                                    where o.fservicio = '$fecha' and not suspendida and not borrada and o.id not in (".implode(",", $ids).") and o.id_estructura = $_SESSION[structure]
                                    order by sale";
                            $result = mysql_query($sql, $conn) or die("error");
                            while ($row = mysql_fetch_array($result)){
                              $ids[] = $row['id'];
                              print "<tr>
                                        <td>$row[fecha]</td>
                                        <td>$row[nombre]</td>
                                        <td>$row[sale]</td>
                                        <td>$row[llega]</td>
                                        <td>$row[interno]</td>
                                        <td>$row[ch1]</td>
                                        <td>$row[ch2]</td>
                                     </tr>";
                            }                            

                            print "</tbody></table>";
                                               
                        }
                      ?>
            </fieldset>
          </form>
  </div>
</BODY>
</html>