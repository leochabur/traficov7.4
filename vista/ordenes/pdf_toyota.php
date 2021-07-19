<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
require('fpdf.php');



class PDF extends FPDF
{
// Cabecera de página
function Header()
{
    // Logo
    $this->Image('masterbus-log.png',10,8,33);     // <img src="masterbus-logo.png" border="0">
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Movernos a la derecha
    $this->Cell(80);
    // Título
    $this->Cell(45,10,"Infome de Confibialidad - Mes: $_GET[mes] - Año: $_GET[anio]",0,0,'C');
    // Salto de línea
    $this->Ln(20);
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}

// Creación del objeto de la clase heredada
$pdf = new PDF('L');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',10);

                     $export = mysql_connect('200.80.61.27', 'mbexpuser', 'Mb2013Exp');
                     mysql_select_db('mbexport', $export);
                     $sql="SELECT if (s.id_tipoServicio is null, 'Produccion', ts.tipo) as tipo_s, upper(o.nombre) as servicio, count(*), turno,
       if (i_v is null, if(o.id_ciudad_destino = 3, 'Llegada a Planta', 'Salida de Planta'), if (i_v = 'i', 'Llegada a Planta', 'Salida de Planta')) as Entrada_Salida,
        upper(ori.ciudad) as Origen, upper(des.ciudad) as Destino, Interno, u.cantasientos, o.cantpax, avg(round((o.cantpax/cantasientos)*100,2)) as Eficiencia,
       if (i_v is null, if(o.id_ciudad_destino = 3, ho.hllegada, null), if (i_v = 'i', ho.hllegada, null)) as Hora_Diagramada_Llegada_Planta,
       if (i_v is null, if(o.id_ciudad_destino = 3, o.hllegada, null), if (i_v = 'i', o.hllegada, null)) as Hora_Real_Llegada_Planta,
       if (i_v is null, if(o.id_ciudad_destino = 3, null, ho.hsalida), if (i_v = 'i', null, ho.hsalida)) as Hora_Diagramada_Salida_Planta,
       if (i_v is null, if(o.id_ciudad_destino = 3, null, o.hsalida), if (i_v = 'i', null, o.hsalida)) as Hora_Real_Salida_Planta,

       avg(if (i_v is null,
                     if(o.id_ciudad_destino = 3,
cast(if (o.hllegada <= ho.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegada, ho.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegada, ho.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegada, ho.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegada, ho.hllegada))/60)-5)*0.06),
                               0)))*100 as decimal(5,2))
                     ,
cast(if (o.hsalida <= ho.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalida, ho.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalida, ho.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalida, ho.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalida, ho.hsalida))/60)-5)*0.06),
                               0)))*100 as decimal(5,2))
                   ), if (i_v = 'i',
cast(if (o.hllegada <= ho.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegada, ho.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegada, ho.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegada, ho.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegada, ho.hllegada))/60)-5)*0.06),
                               0)))*100 as decimal(5,2))
                                                 ,
  cast(if (o.hsalida <= ho.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalida, ho.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalida, ho.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalida, ho.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalida, ho.hsalida))/60)-5)*0.06),
                               0)))*100 as decimal(5,2))))) as Confiabilidad

FROM ordenes o
inner join horarios_ordenes ho on ho.id = o.id
left JOIN servicios s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
left JOIN tiposervicio ts ON ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
left JOIN turnos tu ON tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
left JOIN ciudades ori ON ori.id = o.id_ciudad_origen and ori.id_estructura = o.id_estructura_ciudad_origen
left JOIN ciudades des ON des.id = o.id_ciudad_destino and des.id_estructura = o.id_estructura_ciudad_destino
left join unidades u on u.id = o.id_micro
where month(o.fservicio) = $_GET[mes] and year(o.fservicio) = $_GET[anio] and o.id_estructura = $_SESSION[structure] and o.id_cliente = $_GET[cli] and not o.borrada and not o.suspendida
group by ts.id, tu.id, i_v, s.id
order by tipo_s, tu.turno, entrada_salida";
                     $result = mysql_query($sql, $export);
                     $i=1;
                     $pdf->SetFillColor(255, 255, 0);
            $pdf->Cell(25,7,'Tipo',1, 0, 'C', True);
            $pdf->Cell(75,7,'Servicio',1, 0, 'C', True);
            $pdf->Cell(20,7,'Cant Dias',1, 0, 'C', True);
            $pdf->Cell(20,7,'Turno',1, 0, 'C', True);
            $pdf->Cell(35,7,'Entrada_Salida',1, 0, 'C', True);
            $pdf->Cell(50,7,'Origen',1, 0, 'C', True);
            $pdf->Cell(28,7,'Confiabilidad (%)',1, 0, 'C', True);
                    $pdf->Ln();
            $data = mysql_fetch_array($result);
            $resumen = array();
            while ($data){
                  $tipo = $data[0];
                  $last_tipo = $tipo;
                  $resumen[$tipo]=array();
                  while ($data && ($tipo == $data[0])){
                        $turno = $data[3];
                        $last_turno = $turno;
                        $suma=0;
                        $cant=0;
                        $resumen[$tipo][$turno]=array();
                        $resuemn[$tipo][$turno][$data[4]]=0;
                        while ($data && ($tipo == $data[0]) && (($turno == $data[3]) || ($tipo != 'Produccion'))){
                              $pdf->Cell(25,6,$data[0],1);
                              $pdf->Cell(75,6,$data[1],1);
                              $pdf->Cell(20,6,$data[2],1,'R');
                              $pdf->Cell(20,6,$data[3],1,'R');
                              $pdf->Cell(35,6,$data[4],1,'R');
                              $pdf->Cell(50,6,$data[5],1,'R');
                              $pdf->Cell(28,6,number_format($data[15],2),1,'R');
                              $pdf->Ln();
                              $data = mysql_fetch_array($result);
                              $suma+=$data[15];
                              $cant++;
                              $resuemn[$tipo][$turno][$data[4]]+=$data[15];
                        }
                        $prom=number_format($suma/$cant,2);
                        if ($last_tipo != 'Produccion'){
                           $pdf->Cell(253,6,"TOTAL CONFIABILIDAD $last_tipo: $prom",1, 0, 'C', True);
                           $pdf->Ln();
                        }
                        else{
                             $pdf->Cell(253,6,"TOTAL CONFIABILIDAD $last_tipo TURNO $last_turno: $prom",1, 0, 'C', True);
                           $pdf->Ln();
                        }
                  }
            }
//print_r($resumen);
$pdf->Output();
//$pdf->Output();

?>
