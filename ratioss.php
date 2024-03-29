<?php
  session_start();
set_time_limit(0);
//print '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">';
require('fpdf.php');
require('./controlador/bdadmin.php');
require('./modelo/utils/utils.php');



class PDF extends FPDF
{
// Cabecera de p�gina
function Header()
{
    // Logo
    $this->Image('masterbus-log.png',10,8,33);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Movernos a la derecha
    $this->Cell(80);
    // T�tulo
    if ($_GET['cli'] == 10)
       $this->Cell(45,10,'Toyota Argentina S.A.',0,0,'C');
    else
        $this->Cell(45,10,'Honda Motor de Argentina S.A.',0,0,'C');
    $this->ln();
    $this->Cell(200,10,'Reporte mes de '.ucwords(nombremes($_GET['mes']))." de $_GET[anio]",0,0,'C');
    // Salto de l�nea
    $this->Ln(20);
}

// Pie de p�gina
function Footer()
{
    // Posici�n: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // N�mero de p�gina
    $this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
}
}

// Creaci�n del objeto de la clase heredada
$pdf = new PDF('P');
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
       count(distinct(id_micro)) as micros, sum(km) as km, tipo_serv
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
                               0)))*100 as decimal(5,2)))) as Confiabilidad, o.km, o.id_micro as id_micro, ts.id as tipo_serv, tipo, turno

      FROM (select o.id, id_estructura_servicio, hsalidaplantareal, hllegadaplantareal, id_ciudad_origen, id_estructura_ciudad_origen,
                   id_estructura_ciudad_destino, fservicio, cantpax, id_servicio,
                   id_ciudad_destino, o.hllegada, o.hsalida, km, id_micro,
                   if (tto.id_turno is null, s.id_turno, tto.id_turno) as id_turno,
                   if (tto.id_tipo_servicio is null, s.id_TipoServicio, tto.id_tipo_servicio) as id_tipo_servicio,
                   if (tto.i_v is null, s.i_v, tto.i_v) as i_v
            from ordenes o
            left join (select * from servicios where id_estructura = 1) s on s.id = o.id_servicio
            left join tipotnoordenes tto on tto.id_orden = o.id
            where month(o.fservicio) = $_GET[mes] and year(o.fservicio) = $_GET[anio] and o.id_estructura = $_SESSION[structure] and o.id_cliente = $_GET[cli] and not o.borrada and not o.suspendida and (hllegadaplantareal is not null || hsalidaplantareal is not null)) o
      inner JOIN (select * from tiposervicio where id_estructura = $_SESSION[structure]) ts ON ts.id = id_tipo_servicio
      inner JOIN (select * from turnos where id_estructura = $_SESSION[structure]) tu ON tu.id = id_turno
      left JOIN ciudades ori ON ori.id = o.id_ciudad_origen and ori.id_estructura = o.id_estructura_ciudad_origen
      left JOIN ciudades des ON des.id = o.id_ciudad_destino and des.id_estructura = o.id_estructura_ciudad_destino
      left join unidades u on u.id = o.id_micro
) o
where tipo_serv not in (16, 3)
group by tipo, turno";
          //   die($sql);
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
            $efic=0;
            $conf=0;
            $count=0;
            $pax=0;
            $km=0;
            $recor=array();
            $paxs=array();
            $micros=array();
            while ($data){
                  $tipo = $data['tipo'];
                  $recor[$tipo]=0;
                  $paxs[$tipo]=0;
                  $micros[$tipo]=array();
                  while ($data && ($tipo == $data['tipo'])){
                        $turno = $data['turno'];
                        $micros[$tipo][$turno]=0;
                        while ($data && ($turno == $data['turno']) && ($tipo == $data['tipo'])){
                              $count++;
                              $conf+=$data['conf'];
                              $efic+=$data['efic'];
                              $recor[$tipo]+=$data['recorridos'];
                              $paxs[$tipo]+=$data['cantpax'];
                              $micros[$tipo][$turno]+=$data['micros'];
                              $km+=$data['km'];
                              $data = mysql_fetch_array($result);
                        }
                  }
            }
            //die(print_r($recor));
            $conf=$conf/$count;
            $pdf->ln();
            $pdf->Cell(150,7,'INDICE DE CONFIABILIDAD',0, 0, 'L', false);
            $pdf->Cell(20,7, number_format($conf,2), 0, 0, 'R', false);
            $pdf->ln();
            $pdf->ln();
            $efic=$efic/$count;
            $pdf->Cell(150,7,'INDICE DE EFICIENCIA',0, 0, 'L', false);
            $pdf->Cell(20,7, number_format($efic,2),0, 0, 'R', false);
            $pdf->ln();
            $pdf->ln();
            $pdf->Cell(150,7,'TOTAL DE RECORRIDOS POR TIPO DE SERVICIO',0, 0, 'L', false);
            $pdf->ln();
            $tot_recos=0;
            foreach ($recor as $clave => $valor) {       ///imprime la cant de servicios
                    $pdf->Cell(150,7,$clave,0, 0, 'L', false);
                    $pdf->Cell(20,7,$valor,0, 0, 'R', false);
                    $tot_recos+=$valor;
                    $pdf->ln();
            }
            ////imprime total de servicis
            $pdf->Cell(150,7,'',0, 0, 'L', false);
            $pdf->Cell(20,7,$tot_recos,1, 0, 'R', false);
            $pdf->ln();
            $pdf->ln();
            /////imprime pasajeros
            $pdf->Cell(150,7,'TOTAL DE PASAJEROS TRANSPORTADOS',0, 0, 'L', false);
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
            $pdf->Cell(20,7,$km,0, 0, 'R', false);
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

            
            $pdf->Cell(150,7,'TOTAL DE MICROS UTILIZADOS SIMULTANEAMENTE',0, 0, 'L', false);
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
            $sql="SELECT round(sum(anio)/count(*)) as media
                  from ( select id_micro, if (id_servicio is null, (select id_tipo_servicio from tipotnoordenes where id_orden = o.id), s.id_tipoServicio) as id_TipoServicio
                         from ordenes o
                         left join servicios s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                          where month(o.fservicio) = $_GET[mes] and year(o.fservicio) = $_GET[anio] and o.id_estructura = $_SESSION[structure] and o.id_cliente = $_GET[cli] and not o.borrada and not o.suspendida) o
                  inner join unidades u on u.id = id_micro
                  where id_tipoServicio not in (3, 16) and (anio <> 0) and (anio is not null)";
            $result = mysql_query($sql, $export) or die(mysql_error($export));
            $data = mysql_fetch_array($result);
            $pdf->Cell(150,7,'ANTIG�EDAD FLOTA',0, 0, 'L', false);
            $pdf->Cell(20,7, $data[0],0, 0, 'R', false);
            //////
//print_r($micros);
$pdf->Output();
//$pdf->Output();

?>
