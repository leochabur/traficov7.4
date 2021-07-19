<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
require('fpdf.php');
require('controlador/bdadmin.php');
require('modelo/utils/utils.php');



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

                     $export = $export = conexcion();//mysql_connect('200.80.61.27', 'mbexpuser', 'Mb2013Exp');
                     //mysql_select_db('mbexport', $export);
                     $sql="select ts.tipo, nombre, count(*), tu.turno,  if(i_v = 'i', 'Entrada a Planta', 'Salida de Planta') as iv, origen, '' as destino, interno, cantasientos, cantpax,
       avg(round((o.cantpax/cantasientos)*100,2)) as Eficiencia,
       hllegada, hllegadaplantareal, hsalida, hsalidaplantareal,
      avg(if (i_v = 'i',
                       cast(if (o.hllegadaplantareal <= o.hllegada,
                                  1,
                                  if((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) <= 5,
                                      (1-(time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)*0.02),
                                      if((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) < 20,
                                         (0.9-((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)-5)*0.06),
                                          0)))*100 as decimal(5,2)),
                       cast(if (o.hsalidaplantareal <= o.hsalida,
                                  1,
                                  if((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)*0.02),
                                       if((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) < 20,
                                           (0.9-((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)-5)*0.06),
                                           0)))*100 as decimal(5,2)))) as Confiabilidad

from(
     Select *
     from(
          select i_v, interno, cantasientos, id_turno, id_tipo_servicio, id_ciudad_origen, fservicio, nombre, cantpax, hllegada, hsalida, hllegadaplantareal, hsalidaplantareal, id_orden, id_srv
          from(
               select if (o.i_v is not null, o.i_v, s.i_v) as i_v,
                      if (o.id_turno is not null, o.id_turno, s.id_turno) as id_turno,
                      if (o.id_tipo_servicio is not null, o.id_tipo_servicio, s.id_TipoServicio) as id_tipo_servicio, id_micro, id_ciudad_origen,
                      fservicio, nombre, cantpax, hllegada, hsalida, hllegadaplantareal, hsalidaplantareal, o.id as id_orden, id_srv
               from(
                    SELECT id_servicio, id, fservicio, nombre, cantpax, hllegada, hsalida, hllegadaplantareal, hsalidaplantareal, id_ciudad_origen,
                           id_micro, tto.i_v, tto.id_turno, tto.id_tipo_servicio
                    FROM ordenes o
                    left join tipotnoordenes tto on tto.id_orden = o.id
                    where month(o.fservicio) = $_GET[mes] and year(o.fservicio) = $_GET[anio] and o.id_estructura = $_GET[str] and o.id_cliente = $_GET[cli] and not borrada and not suspendida and (hllegadaplantareal is not null || hsalidaplantareal is not null)
               ) o
               left join (select id as id_srv, i_v, id_turno, id_TipoServicio from servicios where id_estructura = $_GET[str]) s on s.id_srv = o.id_servicio
          ) o
          left join (select interno, cantasientos, id as id_micro from unidades) u on u.id_micro = o.id_micro
      ) o
) o

inner JOIN (select * from turnos where id_estructura = $_GET[str]) tu ON tu.id = id_turno
inner JOIN (select id, tipo from tiposervicio where id not in (3, 16)) ts ON ts.id = id_tipo_servicio
left JOIN (select id as id_origen, ciudad as origen from ciudades where id_estructura = $_GET[str]) ori ON ori.id_origen = id_ciudad_origen
group by ts.id, tu.id, i_v, id_srv
order by tipo, turno, i_v";

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
                              $pdf->Cell(20,6, utf8_decode($data[3]),1,'R');
                              $pdf->Cell(35,6,$data[4],1,'R');
                              $pdf->Cell(50,6, utf8_decode($data[5]),1,'R');
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
