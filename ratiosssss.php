<?php
  session_start();
set_time_limit(0);
//print '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">';
require('fpdf.php');
require('./controlador/bdadmin.php');
require('./modelo/utils/utils.php');



class PDF extends FPDF
{
// Cabecera de página
function Header()
{
    // Logo
    $this->Image('masterbus-log.png',10,8,33);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Movernos a la derecha
    $this->Cell(80);
    // Título
    if ($_GET['cli'] == 10)
       $this->Cell(45,10,'Toyota Argentina S.A.',0,0,'C');
    else
        $this->Cell(45,10,'Honda Motor de Argentina S.A.',0,0,'C');
    $this->ln();
    $this->Cell(200,10,'Reporte Comparativo',0,0,'C');
    // Salto de línea
    $this->Ln(5);
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
}
}

// Creación del objeto de la clase heredada
$pdf = new PDF('L');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','B',10);

/*$export = mysql_connect('localhost', 'root', 'leo1979');
                     mysql_select_db('mbexport', $export);
                     $sql="select sum(c.km), ts.tipo, tu.turno, count(*) as recorridos, 5 as cantpax, count(distinct(id_micro)) as micros, c.km
                                  from ordenes o
                                  inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                                  left join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                                  left join tiposervicio ts on ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
                                  left join turnos tu on tu.id = s.id_turno and tu.id_estructura = s.id_estructura_turno
                                  group by s.id_TipoServicio, s.id_turno";         */

                     $export = conexcion();

$sql="select tipo, turno, round(avg(confiabilidad),2) as conf, round(avg(eficiencia),2) as efic, sum(cantpax) as cantpax, count(*) as recorridos,
       count(distinct(id_micro)) as micros, sum(km) as km, tipo_serv, periodo
from (
      SELECT date_format(o.fservicio, '%d/%m/%Y') as fecha, round((o.cantpax/cantasientos)*100,2) as Eficiencia, o.cantpax,
             if (i_v is null,
                     if(o.id_ciudad_destino = 3,
cast(if (o.hllegadaplantareal <= o.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)-5)*0.06),
                               0)))*100 as decimal(5,2))
                     ,
cast(if (o.hsalidaplantareal <= o.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)-5)*0.06),
                               0)))*100 as decimal(5,2))
                   ), if (i_v = 'i',
cast(if (o.hllegadaplantareal <= o.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)-5)*0.06),
                               0)))*100 as decimal(5,2))
                                                 ,
  cast(if (o.hsalidaplantareal <= o.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)-5)*0.06),
                               0)))*100 as decimal(5,2)))) as Confiabilidad, o.km, o.id_micro as id_micro, ts.id as tipo_serv, tipo, turno, periodo

      FROM (select o.id, id_estructura_servicio, hsalidaplantareal, hllegadaplantareal, id_ciudad_origen, id_estructura_ciudad_origen,
                   id_estructura_ciudad_destino, fservicio, cantpax, id_servicio,
                   id_ciudad_destino, o.hllegada, o.hsalida, km, id_micro,
                   if (tto.id_turno is null, s.id_turno, tto.id_turno) as id_turno,
                   if (tto.id_tipo_servicio is null, s.id_TipoServicio, tto.id_tipo_servicio) as id_tipo_servicio,
                   if (tto.i_v is null, s.i_v, tto.i_v) as i_v, concat(date_format(o.fservicio,'%m'),'-',year(o.fservicio)) as periodo
            from ordenes o
            left join (select * from servicios where id_estructura = 1) s on s.id = o.id_servicio
            left join tipotnoordenes tto on tto.id_orden = o.id
            where month(o.fservicio) in(1,2,3,4) and year(o.fservicio) = $_GET[anio] and o.id_estructura = $_SESSION[structure] and o.id_cliente = $_GET[cli] and not o.borrada and not o.suspendida and (hllegadaplantareal is not null || hsalidaplantareal is not null)) o
      inner JOIN (select * from tiposervicio where id_estructura = $_SESSION[structure]) ts ON ts.id = id_tipo_servicio
      inner JOIN (select * from turnos where id_estructura = $_SESSION[structure]) tu ON tu.id = id_turno
      left JOIN ciudades ori ON ori.id = o.id_ciudad_origen and ori.id_estructura = o.id_estructura_ciudad_origen
      left JOIN ciudades des ON des.id = o.id_ciudad_destino and des.id_estructura = o.id_estructura_ciudad_destino
      left join unidades u on u.id = o.id_micro
) o
where tipo_serv not in (16, 3)
group by periodo, tipo, turno";
         //    die($sql);
            $i=1;
          /*  $pdf->SetFillColor(255, 255, 0);
            $pdf->Cell(25,7,'Tipo',1, 0, 'C', True);
            $pdf->Cell(75,7,'Servicio',1, 0, 'C', True);
            $pdf->Cell(20,7,'Cant Dias',1, 0, 'C', True);
            $pdf->Cell(20,7,'Turno',1, 0, 'C', True);
            $pdf->Cell(35,7,'Entrada_Salida',1, 0, 'C', True);
            $pdf->Cell(50,7,'Origen',1, 0, 'C', True);
            $pdf->Cell(28,7,'Confiabilidad (%)',1, 0, 'C', True);
            $pdf->Ln();   */
            $result = mysql_query($sql, $export) or die(mysql_error($export));
            $data = mysql_fetch_array($result);

            $resumen = array();
            $mese_compara = array();
            while ($data){
               $efic=0;
               $conf=0;
               $count=0;
               $pax=0;
               $km=0;
               $recor=array();
               $paxs=array();
               $micros=array();
               $periodo = $data['periodo']; // logisticabbva@atento.com.ar              asunto: dni - fotocopia dni
               $mese_compara[$periodo] = array();
               $mese_compara[$periodo][3]=$efic;
               $mese_compara[$periodo][4]=$conf;
               $mese_compara[$periodo][5]=$count;
               $mese_compara[$periodo][6]=$km;
               while ($data && ($periodo == $data['periodo'])){
                  $tipo = $data['tipo'];
                  $recor[$tipo]=0;     //acumula la catnidad de recorridos pot tipo de servicio
                  $mese_compara[$periodo][0]=$recor;
                  $paxs[$tipo]=0;
                  $mese_compara[$periodo][1]=$paxs;
                  $micros[$tipo]=array();
                  $mese_compara[$periodo][2]=$micros;
                  while ($data && ($tipo == $data['tipo']) && ($periodo == $data['periodo'])){
                        $turno = $data['turno'];
                        $micros[$tipo][$turno]=0;//inicializa un arreglo para acumular la cantidad de micros por tipo servicio y turno
                        $mese_compara[$periodo][2] = $micros;
                        while ($data && ($turno == $data['turno']) && ($tipo == $data['tipo']) && ($periodo == $data['periodo'])){
                              $count++;
                              $conf+=$data['conf'];
                              $efic+=$data['efic'];
                              $mese_compara[$periodo][3]=$efic;
                              $mese_compara[$periodo][4]=$conf;
                              $mese_compara[$periodo][5]=$count;

                              $recor[$tipo]+=$data['recorridos'];
                              $mese_compara[$periodo][0] = $recor;
                              
                              $paxs[$tipo]+=$data['cantpax'];
                              $mese_compara[$periodo][1] = $paxs;
                              
                              $micros[$tipo][$turno]+=$data['micros'];
                              $mese_compara[$periodo][2] = $micros;
                              $km+=$data['km'];
                              $mese_compara[$periodo][6]=$km;
                              $data = mysql_fetch_array($result);
                        }
                  }
               }
            }
     //       print_r($mese_compara);
       //     die("cantidad ".count($mese_compara));
            $conf=$conf/$count;
            $pdf->ln();
            $posx = $pdf->getX();
            $pdf->setXY($posx+70,$pdf->getY());
            foreach ($mese_compara as $clave => $valor){
                    $pdf->Cell(20,7, $clave, 0, 0, 'R', false);
            }
            $pdf->ln();
            $pdf->Cell(150,7,'INDICE DE CONFIABILIDAD',0, 0, 'L', false);
            
            $pdf->setXY($posx+70,$pdf->getY());
            foreach ($mese_compara as $clave => $valor){
                    $conf = $mese_compara[$clave][4] / $mese_compara[$clave][5];

                   // $pdf->Cell(20,7, $clave, 0, 0, 'R', false);
                   $pdf->Cell(20,7, number_format($conf,2), 0, 0, 'R', false);
            }

            $pdf->ln();
            $efic=$efic/$count;
            $pdf->Cell(150,7,'INDICE DE EFICIENCIA',0, 0, 'L', false);
            $pdf->setXY($posx+70,$pdf->getY());
            foreach ($mese_compara as $clave => $valor){
                    $efic = $mese_compara[$clave][3] / $mese_compara[$clave][5];

                   // $pdf->Cell(20,7, $clave, 0, 0, 'R', false);
                   $pdf->Cell(20,7, number_format($efic,2), 0, 0, 'R', false);
            }


         //   $pdf->Cell(20,7, number_format($efic,2),0, 0, 'R', false);
            $pdf->ln();
            $pdf->ln();
            $pdf->Cell(150,7,'RECORRIDOS POR TIPO DE SERVICIO',0, 0, 'L', false);
            $pdf->ln();
            $aux=$pdf->GetX();
            $auy=$pdf->GetY();
            $aux_claves = array();
            foreach ($mese_compara as $key => $value){
                    $tot_recos=0;
                    $pdf->SetXY($aux, $auy);
                    foreach ($value[0] as $clave => $valor) {       ///imprime la cant de servicios

                            if (!in_array($clave, $aux_claves)){
                               $pdf->Cell(150,7,$clave,0, 0, 'L', false);
                               $aux_claves[]=$clave;
                            }
                            $pdf->SetXY($posx+70, $auy);
                            $pdf->Cell(20,7,$valor,0, 0, 'R', false);
                            $tot_recos+=$valor;
                            $pdf->ln();
                    }
                    ////imprime total de servicis
                    $pdf->Cell(150,7,'',0, 0, 'L', false);
                    $pdf->Cell(20,7,$tot_recos,1, 0, 'R', false);
            }
            $pdf->ln();
            $pdf->ln();
            /////imprime pasajeros
            $pdf->Cell(150,7,'PASAJEROS TRANSPORTADOS',0, 0, 'L', false);
            $pdf->ln();
            $tot_pax=0;
            $pdf->SetFont('Times','',10);
            foreach ($paxs as $clave => $valor) {       ///imprime la cant de servicios
                    $pdf->Cell(150,7,$clave,0, 0, 'L', false);
                    $pdf->Cell(20,7,$valor,0, 0, 'R', false);
                    $tot_pax+=$valor;
                    $pdf->ln();
            }
            ////imprime total de servicis
            $pdf->Cell(150,7,'',0, 0, 'L', false);
            $pdf->Cell(20,7,$tot_pax,1, 0, 'R', false);
            $pdf->ln();
            $pdf->ln();
            ////////////////
            $pdf->SetFont('Times','B',10);
            $pdf->Cell(150,7,'TOTAL DE KILOMETROS',0, 0, 'L', false);
            
            $pdf->setXY($posx+70,$pdf->getY());
            foreach ($mese_compara as $clave => $valor){
                    $km = $mese_compara[$clave][6];
                   // $pdf->Cell(20,7, $clave, 0, 0, 'R', false);
                   $pdf->Cell(20,7, $km, 0, 0, 'R', false);
            }
        //    $pdf->Cell(20,7,$km,0, 0, 'R', false);
            $pdf->ln();
            $pdf->ln();
            ////////imprimes micros utilizados simultaneamnete
            $sql="select *
from (
    SELECT fservicio, count(distinct(id_micro)) as cant_micros, ts.tipo, tu.turno, id_tipo_servicio
    from (
          select o.id, id_estructura_servicio, fservicio, cantpax, id_servicio, o.hllegada, o.hsalida, km, id_micro,
                 if (tto.id_turno is null, s.id_turno, tto.id_turno) as id_turno,
                 if (tto.id_tipo_servicio is null, s.id_TipoServicio, tto.id_tipo_servicio) as id_tipo_servicio,
                 if (tto.i_v is null, s.i_v, tto.i_v) as i_v
          from ordenes o
          left join (select * from servicios where id_estructura = $_SESSION[structure]) s on s.id = o.id_servicio
          left join tipotnoordenes tto on tto.id_orden = o.id
          where month(o.fservicio) = $_GET[mes] and year(o.fservicio) = $_GET[anio] and o.id_estructura = $_SESSION[structure] and o.id_cliente = $_GET[cli] and not o.borrada and not o.suspendida
          ) o
    left JOIN (Select * from tiposervicio where (id_estructura = $_SESSION[structure]) and (id not in (16, 3))) ts ON ts.id = id_tipo_servicio
    left JOIN turnos tu ON tu.id = id_turno
    where i_v = 'i'
    group by fservicio, id_tipo_servicio, id_turno
) o
where tipo is not null";
          //  die($sql);
            $result = mysql_query($sql, $export) or die(mysql_error($export));

            
            $pdf->Cell(150,7,'MICROS UTILIZADOS SIMULTANEAMENTE',0, 0, 'L', false);
            $pdf->ln();
            $pdf->SetFont('Times','',10);
            $data = mysql_fetch_array($result);
            $micros=array();
            while ($data){
                  $tipo = $data['tipo'];
                  $turno=$data['turno'];
                  if (!isset($micros[$tipo])){
                     $micros[$tipo]=array();
                  }
                  if (!isset($micros[$tipo][$turno])){
                          $micros[$tipo][$turno]=0;
                  }
                  if ($data['cant_micros'] > $micros[$tipo][$turno]){
                     $micros[$tipo][$turno] = $data['cant_micros'];
                  }
                  $data = mysql_fetch_array($result);
            }
         //   print_r($micros);
            foreach ($micros as $clave => $valor) {
                    foreach ($valor as $clave_tu => $valor_tu) {
                            $pdf->Cell(150,7, ("$clave turno ".utf8_decode($clave_tu)),0, 0, 'L', false);
                            $pdf->Cell(20,7,$valor_tu,0, 0, 'R', false);
                           // $tot_pax+=$valor;
                            $pdf->ln();
                    }
            }
            ///////
            $pdf->ln();
            ///antiguedad media parque
            $pdf->SetFont('Times','B',10);
            $sql="SELECT periodo, round(sum(anio)/count(*)) as media
                  from ( select id_micro, concat(date_format(fservicio,'%m'),'-',year(fservicio)) as periodo
                         from ordenes o
                          where month(o.fservicio) in (1,2,3,4) and year(o.fservicio) = 2016 and o.id_estructura = 1 and o.id_cliente = 10 and not o.borrada and not o.suspendida) o
                  inner join unidades u on u.id = id_micro
                  where (anio <> 0) and (anio is not null)
                  group by periodo";
            $result = mysql_query($sql, $export) or die(mysql_error($export));

            $pdf->Cell(150,7,'ANTIGÜEDAD FLOTA',0, 0, 'L', false);
            $pdf->setXY($posx+70,$pdf->getY());
            while ($data = mysql_fetch_array($result))
                  $pdf->Cell(20,7, $data[1], 0, 0, 'R', false);
                //  $pdf->Cell(20,7, $data[0],0, 0, 'R', false);
            //////
//print_r($micros);
$pdf->Output();
//$pdf->Output();

?>
