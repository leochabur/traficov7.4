<?php
    error_reporting(E_ALL);
     session_start();
     include_once('../paneles/viewpanel.php');
     include_once('../../controlador/ejecutar_sql.php');

     $res_cc = ejecutarSQL("SELECT cant_cond FROM estructuras WHERE id = $_SESSION[structure]");
     if ($data_cc = mysql_fetch_array($res_cc))
     {
         $cantTripulacion = $data_cc[0];
     }



     $con = conexcion(true);
     $ciudades = '';
    $cityResult = mysqli_query($con, "SELECT id, ciudad FROM ciudades where id_estructura = $_SESSION[structure] order by ciudad");
    foreach ($cityResult as $city)
    {
        $ciudades.="<option value='$city[id]'>$city[ciudad]</option>";
    }



$tabla = "";

$tabla='<link type="text/css" href="/vista/css/blue/style.css" rel="stylesheet"/>
         <link href="/vista/css/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
         <link type="text/css" href="/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
         <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
         <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
         <script type="text/javascript" src="/vista/js/jquery.ui.datepicker-es.js"></script>
         <script type="text/javascript" src="/vista/js/jquery.maskedinput-1.3.js"></script>
         <script type="text/javascript" src="/vista/js/jquery.jeditable.js"></script>
         <script type="text/javascript" src="/vista/js/jquery.tablesorter.js"></script>
         <script type="text/javascript" src="/vista/js/jquery.contextMenu.js"></script>
         <script type="text/javascript" src="/vista/js/jquery.ui.selectmenu.js"></script>

<script>
 
	$(function() {

                 $.mask.definitions["~"]="[012]";
                 $.mask.definitions["%"]="[012345]";
                 $(".hora").mask("99/99/9999 ~9:%9",{completed:function(){}});
                 $(".fservicio").datepicker({dateFormat:"dd/mm/yy"});
                 
                 $(".change").button();
                 $(".open-orden").button().click(function(){
                                                            if (confirm("Seguro dividir la orden?"))
                                                            {
                                                                let btn = $(this);
                                                                $.post("/modelo/ordenes/modordsur.php", 
                                                                      {
                                                                        accion : "open",
                                                                        orden: btn.data("orden")
                                                                      }, 
                                                                      function(data){
                                                                                        console.log(data);
                                                                                        let result = $.parseJSON(data);
                                                                                        if (result.ok)
                                                                                        {
                                                                                            alert("Orden dividida exitosamente!");
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                            alert("No se ha podido dividir la orden! -  " + result.message)
                                                                                        }
                                                                      }
                                                                      );
                                                            }
                 });
                 $("#close").button().click(function(){
                                                       $("#dialog").dialog("close");
                                                       $("#cargar").trigger("click");
                                                       });';

        $tabla.='

	});
	
</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
.small.button, .small.button:visited {
font-size: 11px ;
}

#modorden .error{
	font-size:0.8em;
	color:#ff0000;
}

</style>
<BODY>';
    $tabla.="<br><br>";


try
{

     $sqlEstado = "SELECT id, fecha
                   FROM estadoDiagramasDiarios
                   WHERE (fecha = (SELECT date(citacion) FROM horarios_ordenes_sur where id_orden = $_POST[orden])) and (finalizado = 1) and (id_estructura = $_SESSION[structure])";

     $resultEstado = mysqli_query($con, $sqlEstado);

     $cerrado = mysqli_num_rows($resultEstado);

     $sql = getSqlOrden($_POST['orden'], $cerrado);

     $query = mysqli_query($con, $sql);

     if (!$query)
     {
        return print 'ERROR '.$sql;
     }


     $tabla.= '<div id="tabs">
                    <ul>';

     $tabs = [];

     while($row = mysqli_fetch_array($query))
     {
        $tabla.= "<li><a href='#tabs$row[id]'>$row[hcitacion]</a></li>";
        $tabs[] = $row;
     }

     $tabla.= '</ul>';

     foreach ($tabs as $row)
     {
        $tabla.="<div id='tabs$row[id]'>".getTabOrden($row, $con, $cantTripulacion, $tabla, $ciudades, true)."</div>" ;
     }

     $tabla.="</div>";
}
catch (Exception $e){ return print ($e->getMessage());}
     $tabla.='</div>
                <br>
                <input type="button" id="close" value="Cerrar"/>
             </BODY>';

     $tabla.='<script type="text/javascript">
                    $( "#tabs" ).tabs();
                    $(".update").change(function(){
                                                    let opt =$(this);
                                                    let id = opt.data("id");
                                                    let str = opt.data("str");
                                                    let val = opt.val();
                                                    let nro = opt.data("orden");
                                                    $.post("/modelo/ordenes/modordsur.php",
                                                            {
                                                                accion : "city",
                                                                campo : id, 
                                                                campoStr: str,
                                                                value: val,
                                                                orden: nro
                                                            }, 
                                                            function(data){
                                                                console.log(data);
                                                                });
                    });
                    $(\'a[href="#tabs'.$_POST['orden'].'"]\').click();

              </script>
              
              </HTML>';
     @mysqli_free_result($query);
     @mysqli_close($con);
print $tabla;


function getSqlOrden($orden, $cerrado)
{
    $cita = 'citacion';
    $sale = 'salida';
    $llega = 'llegada';
    $fina = 'finalizacion';
    if ($cerrado)
    {
        $cita = 'citacion_real';
        $sale = 'salida_real';
        $llega = 'llegada_real';
        $fina = 'finalizacion_real'; 
    }

    $sql = "SELECT o.id, o.km, date_format(date(citacion),'%d/%m/%Y') as fservicio, finalizada, date_format($cita, '%d/%m/%Y %H:%i') as hcitacion,
                    date_format($sale, '%d/%m/%Y %H:%i') as hsalida, date_format($fina, '%d/%m/%Y %H:%i') as hfinserv, date_format($llega, '%d/%m/%Y %H:%i') as hllegada,
                    o.nombre, if(em1.id = 1,concat(ch1.apellido, ', ',ch1.nombre),
                    concat('(',em1.razon_social,') ', ch1.apellido, ', ',ch1.nombre)) as chofer1, upper(c.razon_social) as razon_social,
                    concat(ch2.apellido, ', ',ch2.nombre) as chofer2, comentario, interno, m.id as id_micro, ch1.id_empleado as id_chofer1, ch2.id_empleado as id_chofer2,
                    ori.ciudad as origen, des.ciudad as destino, if (o.borrada, 'selected','') as borrada, if (o.finalizada, 'selected','') as finalizada, vacio, cv.id as id_cli_vac, upper(cv.razon_social) as rsclivac, if(oa.id is null, 0, 1) as tiene_asoc,
                    ori.id as idCiudadOrigen, des.id as idCiudadDestino, cod_servicio
            from (
                    select id_orden_asociada as id_ord
                    from ordenes_abiertas_sur
                    where id_orden in(
                                    SELECT id_orden
                                    FROM ordenes_abiertas_sur o
                                    where id_orden_asociada = $orden)
                    union
                    select id_orden_asociada
                    from ordenes_abiertas_sur
                    where id_orden = $orden
                    union
                    select id_orden
                    from ordenes_abiertas_sur
                    where id_orden_asociada = $orden
                    union
                    select $orden ) oas
            JOIN ordenes o ON o.id = oas.id_ord
            INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id AND hhs.id_estructura_orden = o.id_estructura
            LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
            LEFT JOIN empleadores em1 ON (em1.id = ch1.id_empleador)
            inner join ciudades ori on (ori.id = id_ciudad_origen) and (ori.id_estructura = id_estructura_ciudad_origen)
            inner join ciudades des on (des.id = id_ciudad_destino) and (des.id_estructura = id_estructura_ciudad_destino)
            LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
            LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
            LEFT JOIN clientes cv ON ((cv.id = o.id_cliente_vacio) and (cv.id_estructura = o.id_estructura_cliente_vacio))
            LEFT JOIN unidades m ON (m.id = o.id_micro)
            left join ordenes_asocioadas oa on oa.id_orden = o.id and oa.id_estructura_orden = o.id_estructura
            WHERE not o.borrada
            order by hcitacion, o.id";
    return $sql;
}

function getTabOrden($row, $con, $cantTripulacion, $tabla, $ciudades, $unique = false)
{
    $orden = $row['id'];
    $comentario = $row['comentario'];

    if ($unique) 
    {
        $tabla = '';
    }

    $tabla.="<form id='modorden-$orden'>
             <table>
                 <tr>
                     <td>N".htmlentities("°")." de Orden</td>
                     <td><input type='text' size='20' value='$orden' readonly class='ui-widget ui-widget-content  ui-corner-all'></td>
                 </tr>
                 <tr>
                     <td>Cliente</td>
                     <td><input type='text' size='30' value='".htmlentities($row['razon_social'])."' class='ui-widget ui-widget-content  ui-corner-all' readonly></td>
                 </tr>";

    if ($row['vacio'])
    {
       $clivac="";
       if ($row['id_cli_vac'])
       {
          $clivac="<option value='$row[id_cli_vac]'>$row[rsclivac]</option>";
       }
       $tabla.="<tr>
                    <td>Afectar Vacio a...</td>
                    <td><select id='clivac-$orden' name='clivac'>
                                $clivac
                                <option value'0'></option>
                                ".armarSelect('clientes', 'razon_social', 'id', 'razon_social', "id_estructura = $_SESSION[structure]", 1)."
                        </select>
                    </td>
                </tr>";
    }


    $tabla.="<tr>
                 <td>Nombre Servicio</td>
                 <td><input id='nombre-$orden' name='nombre' type='text' size='30' class='ui-widget ui-widget-content  ui-corner-all {required:true}' value='".htmlentities($row['nombre'])."'></td>
             </tr>
             <tr>
                 <td>Hora Citacion</td>
                 <td><input id='hcitacion-$orden' name='hcitacion' class='hora ui-widget ui-widget-content  ui-corner-all {required:true}' type='text' size='15' value='$row[hcitacion]'></td>
             </tr>
             <tr>
                 <td>Hora Salida</td>
                 <td><input id='hsalida-$orden' name='hsalida' class='hora ui-widget ui-widget-content  ui-corner-all {required:true}' type='text' size='15' value='$row[hsalida]'></td>
             </tr>
             <td>Hora Llegada</td>
                 <td><input id='hllegada-$orden' name='hllegada' class='hora ui-widget ui-widget-content  ui-corner-all {required:true}' type='text' size='15' value='$row[hllegada]'></td>
             </tr>
             <tr>
                 <td>Hora Fin Servicio</td>
                 <td><input id='hfinserv-$orden' name='hfinserv' class='hora ui-widget ui-widget-content  ui-corner-all {required:true}' type='text' size='15' value='$row[hfinserv]'></td>
             </tr>
             <tr>
                 <td>Origen</td>
                 <td>
                        <select class='origen-$orden update' data-orden='$orden' data-id='id_ciudad_origen' data-str='id_estructura_ciudad_origen'>
                            $ciudades
                        </select>
                </td>                                                         
             </tr> 
             <tr>  
                 <td>Destino</td>
                <td>
                        <select class='destino-$orden update' data-orden='$orden' data-id='id_ciudad_destino' data-str='id_estructura_ciudad_destino'>
                            $ciudades
                        </select>
                </td>   
             </tr>
             <tr>
                 <td>Interno</td>
                 <td>
                     <select id='interno-$orden' name='interno'>
                         <option value='$row[id_micro]'>$row[interno]</option>
                     </select>
                 </td>
             </tr>
             <tr>
                 <td>Codigo Servicio</td>
                 <td>
                     <input id='codigo-$orden' name='cod_servicio' class='ui-widget ui-widget-content  ui-corner-all {required:true}' type='text' size='15' value='$row[cod_servicio]'>
                 </td>
             </tr>
             <tr>
                 <td>Km</td>
                 <td><input id='km-$orden' name='km' type='text' size='4' value='$row[km]' class='ui-widget ui-widget-content  ui-corner-all {required:true}'></td>
             </tr>
             <tr>
                 <td>Conductor 1</td>
                 <td>
                     <select id='chofer1-$orden' name='chofer1'>
                         <option value='$row[id_chofer1]'>".htmlentities($row['chofer1'])."</option>
                     </select>
                 </td>
             </tr>
             <tr>
                 <td>Conductor 2</td>
                 <td>
                     <select id='chofer2-$orden' name='chofer2'>
                         <option value='$row[id_chofer2]'>".htmlentities($row['chofer2'])."</option>
                     </select>
                 </td>
             </tr>";


            $sql = "SELECT e.id_empleado, concat(apellido, ', ', nombre) as conductor
                    FROM tripulacionXOrdenes t
                    inner join empleados e on e.id_empleado = t.id_empleado
                    where id_orden = $orden and id_estructura_orden = $_SESSION[structure]";

            $result = mysqli_query($con, $sql);

            for ($i = 3; $i <= $cantTripulacion; $i++)
            {
                $option = "<option value='0'></option>";
                if ($rowin = mysqli_fetch_array($result))
                {
                    $option = "<option value='$rowin[0]'>".htmlentities($rowin[1])."</option>";
                }
                $tabla.="<tr>
                         <td>Conductor $i</td>
                         <td>
                             <select id='chofer$i-$orden' name='chofer$i'>
                                 $option
                             </select>
                         </td>
                     </tr>";
            }

            $tabla.="<tr>
                     <tr>
                        <td>
                            Observaciones
                        </td>
                        <td>
                            <textarea name='obs' rows='2' cols='40'>$comentario</textarea>
                        </td>
                     </tr>
                     <td>Finalizada</td>
                         <td><input type='checkbox' class='ui-widget ui-widget-content  ui-corner-all' name='finalizada' $row[finalizada] value='1'></td>
                     </tr>
                     <td>Eliminar</td>
                         <td><input type='checkbox' class='ui-widget ui-widget-content  ui-corner-all' name='borrada' value='1'></td>
                     </tr>
                     <tr>
                         <td colspan='2'>
                                         <input type='submit' class='change' value='Guardar Cambios'>
                                         <input type='button' class='open-orden' data-orden='$orden' value='Abrir Orden'></td>
                    </td>
                     </tr>";
                                 
                 $tabla.='</table>
                          <input type="hidden" name="nroorden" value="'.$orden.'">
                          <input type="hidden" id="tieneasoc-'.$orden.'" name="tieneasoc" value="'.$row['tiene_asoc'].'">
                          </form>
                          <script type="text/javascript">
                                $(".origen-'.$orden.' option[value='.$row['idCiudadOrigen'].']").attr("selected","selected");
                                $(".destino-'.$orden.' option[value='.$row['idCiudadDestino'].']").attr("selected","selected");

                                 $.post("/vista/ordenes/cargar_combo_conductores.php", 
                                        {orden: '.$orden.'}, 
                                        function(data){
                                                      $("#chofer1-'.$orden.'").append(data);
                                                      $("#chofer2-'.$orden.'").append(data);';
                                                      for ($i = 3; $i <= $cantTripulacion; $i++)
                                                      {
                                                        $tabla.="$('#chofer$i-$orden').append(data);";
                                                      }

                $tabla.='});
                                $.post("/vista/ordenes/cargar_combo_internos.php", 
                                        {orden: '.$orden.'}, 
                                        function(data){
                                                              $("#interno-'.$orden.'").append(data);

                                                       });

                                 $("#modorden-'.$orden.'").validate({
                                                           submitHandler: function(){
                                                                                     var asoc = $("#tieneasoc-'.$orden.'").val();
                                                                                     if (asoc == 1){
                                                                                        if (confirm("La orden que intenta modificar, tiene asociadas un conjunto de ordenes!. Modificar todas?")){
                                                                                           asoc = 1;
                                                                                        }
                                                                                        else{
                                                                                             asoc = 0;
                                                                                        }
                                                                                     }
                                                                                     var datos = $("#modorden-'.$orden.'").serialize();
                                                                                     datos = datos+"&asocia="+asoc;

                                                                                     $.post("/modelo/ordenes/modordsur.php", datos, function(data){
                                                                                                                                                var res = $.parseJSON(data);

                                                                                                                                                if (!res.ok)
                                                                                                                                                {                                                                                                                                                
                                                                                                                                                     alert("Error al modificar la orden!!!");
                                                                                                                                                }
                                                                                                                                                else
                                                                                                                                                {
                                                                                                                                                    alert("Orden modificada exitosamente");
                                                                                                                                                }
                                                                                                                                                }).fail(function(data) { console.log(data); alert("Error al modificar la orden!");});
                                                                                     }
                                                         });
                          </script>';
    return $tabla;
}
?>

