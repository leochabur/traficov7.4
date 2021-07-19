<?
  session_start();
  set_time_limit(0);
  ini_set("memory_limit","40M");

  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
 // include ('../../../modelo/utils/dateutils.php');
  require('../../../fpdf.php');

  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];
  
  
class PDF extends FPDF
{
function drawTextBox($strText, $w, $h, $align='L', $valign='T', $border=true)
{
    $xi=$this->GetX();
    $yi=$this->GetY();

    $hrow=$this->FontSize;
    $textrows=$this->drawRows($w,$hrow,$strText,0,$align,1,0,0);
    $maxrows=floor($h/$this->FontSize);
    $rows=min($textrows,$maxrows);

    $dy=0;
    if (strtoupper($valign)=='M')
        $dy=($h-$rows*$this->FontSize)/2;
    if (strtoupper($valign)=='B')
        $dy=$h-$rows*$this->FontSize;

    $this->SetY($yi+$dy);
    $this->SetX($xi);

    $this->drawRows($w,$hrow,$strText,0,$align,false,$rows,1);

    if ($border)
        $this->Rect($xi,$yi,$w,$h);
}

function drawRows($w, $h, $txt, $border=0, $align='J', $fill=false, $maxline=0, $prn=0)
{
    $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 && $s[$nb-1]=="\n")
        $nb--;
    $b=0;
    if($border)
    {
        if($border==1)
        {
            $border='LTRB';
            $b='LRT';
            $b2='LR';
        }
        else
        {
            $b2='';
            if(is_int(strpos($border,'L')))
                $b2.='L';
            if(is_int(strpos($border,'R')))
                $b2.='R';
            $b=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
        }
    }
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $ns=0;
    $nl=1;
    while($i<$nb)
    {
        //Get next character
        $c=$s[$i];
        if($c=="\n")
        {
            //Explicit line break
            if($this->ws>0)
            {
                $this->ws=0;
                if ($prn==1) $this->_out('0 Tw');
            }
            if ($prn==1) {
                $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
            }
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $ns=0;
            $nl++;
            if($border && $nl==2)
                $b=$b2;
            if ( $maxline && $nl > $maxline )
                return substr($s,$i);
            continue;
        }
        if($c==' ')
        {
            $sep=$i;
            $ls=$l;
            $ns++;
        }
        $l+=$cw[$c];
        if($l>$wmax)
        {
            //Automatic line break
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
                if($this->ws>0)
                {
                    $this->ws=0;
                    if ($prn==1) $this->_out('0 Tw');
                }
                if ($prn==1) {
                    $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
                }
            }
            else
            {
                if($align=='J')
                {
                    $this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
                    if ($prn==1) $this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
                }
                if ($prn==1){
                    $this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);
                }
                $i=$sep+1;
            }
            $sep=-1;
            $j=$i;
            $l=0;
            $ns=0;
            $nl++;
            if($border && $nl==2)
                $b=$b2;
            if ( $maxline && $nl > $maxline )
                return substr($s,$i);
        }
        else
            $i++;
    }
    //Last chunk
    if($this->ws>0)
    {
        $this->ws=0;
        if ($prn==1) $this->_out('0 Tw');
    }
    if($border && is_int(strpos($border,'B')))
        $b.='B';
    if ($prn==1) {
        $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
    }
    $this->x=$this->lMargin;
    return $nl;
}
// Cabecera de página
/*function Header()
{
    // Logo
//    $this->Image('masterbus-log.png',10,8,33);     // <img src="masterbus-logo.png" border="0">
    // Arial bold 15
  //  $this->SetFont('Arial','B',15);
    // Movernos a la derecha
  //  $this->Cell(80);
    // Título
   // $this->Cell(45,10,"Infome de Confibialidad - Mes: $_GET[mes] - Año: $_GET[anio]",0,0,'C');
    // Salto de línea
  //  $this->Ln(20);
}

// Pie de página  */
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10,'Pag. '.$this->PageNo().'/{nb}',0,0,'C');
}
}

// Creación del objeto de la clase heredada
$pdf = new PDF('P');
$pdf->AliasNbPages();
$pdf->SetMargins(5, 5);
$pdf->AddPage();
$pdf->SetFont('Times','',10);

     $estr = $_SESSION[structure];
     
     ///////////////////////
     $conn = mysql_connect('traficonuevo.masterbus.net', 'c0mbexpuser', 'Mb2013Exp');
           //mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $conn);
     mysql_query("SET NAMES 'utf8'", $conn);
     mysql_select_db('c0mbexport', $conn);
     
     //////////////////////////
     $cond = $_GET['cond'];
     $desde = $_GET['desde'];//$_POST['desde'];
     $hasta = $_GET['hasta']; //$_POST['hasta'];
     if ($cond != 0)
        $cond = "where id_cond = $cond";
     else
         $cond = "";

    $sql="SELECT o.*
          FROM(	(SELECT interno, upper(c.razon_social)as razon_social, 0 as id_cond, fservicio as fsrv, upper(servicio) as servicio, date_format(hsalida, '%H:%i') as hsalida, o.id, date_format(fservicio, '%d/%m/%Y') as fservicio, date_format(hcitacion, '%H:%i') as hcitacion, '                  ORDENES A DESIGNAR' as apellido, 0 as legajo, fservicio as li, fservicio as ls, DATEDIFF(fservicio, fservicio) as dias, date_format(hfinservicio, '%H:%i') as hfinservicio, comentario
                 FROM (SELECT  nombre as servicio, if (hsalidaplantareal is null, hsalida, hsalidaplantareal) as hsalida, hfinservicio, id, id_chofer_1, fservicio, hcitacion, id_cliente, id_estructura_cliente, id_micro, comentario
                       FROM ordenes
                       WHERE ((fservicio between '$desde' and '$hasta') and (id_estructura = $estr)) and (not suspendida) and (not borrada) and (id_chofer_1 is null) and (id_chofer_2 is null)
                       ) o
                 INNER JOIN clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
                 LEFT JOIN unidades un on (un.id = o.id_micro))
                UNION
			(SELECT interno, upper(c.razon_social)as razon_social, o.id_chofer_1 as id_cond, fservicio as fsrv, upper(servicio) as servicio, date_format(hsalida, '%H:%i') as hsalida, o.id, date_format(fservicio, '%d/%m/%Y') as fservicio, date_format(hcitacion, '%H:%i') as hcitacion, upper(if(emp.id = 1,concat(ch1.apellido, ', ',ch1.nombre), concat('(',emp.razon_social,') ', ch1.apellido, ', ',ch1.nombre))) as apellido, legajo, fservicio as li, fservicio as ls, DATEDIFF(fservicio, fservicio) as dias, date_format(hfinservicio, '%H:%i') as hfinservicio, comentario
                         FROM (SELECT  nombre as servicio, if (hsalidaplantareal is null, hsalida, hsalidaplantareal) as hsalida , hfinservicio, id, id_chofer_1, fservicio, hcitacion, id_cliente, id_estructura_cliente, id_micro, comentario
			       FROM ordenes
			       WHERE ((fservicio between '$desde' and '$hasta') and (id_estructura = $estr)) and (not suspendida) and (not borrada)
			       ) o
                         inner JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1) and (ch1.id_cargo = 1)
                         inner join empleadores emp on (emp.id = ch1.id_empleador) and (emp.id = $_GET[emp])
			             inner join clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
                         left join unidades un on (un.id = o.id_micro)
			 )
        UNION
			(SELECT interno, upper(c.razon_social)as razon_social, o.id_chofer_2 as id_cond, fservicio as fsrv, upper(servicio) as servicio, date_format(hsalida, '%H:%i') as hsalida, o.id, date_format(fservicio, '%d/%m/%Y') as fservicio, date_format(hcitacion, '%H:%i') as hcitacion, upper(if(emp.id = 1,concat(ch2.apellido, ', ',ch2.nombre), concat('(',emp.razon_social,') ', ch2.apellido, ', ',ch2.nombre))) as apellido, legajo, fservicio as li, fservicio as ls, DATEDIFF(fservicio, fservicio) as dias, date_format(hfinservicio, '%H:%i') as hfinservicio, comentario
                         FROM (SELECT  nombre as servicio,hfinservicio, if (hsalidaplantareal is null, hsalida, hsalidaplantareal) as hsalida, id, id_chofer_2, fservicio, hcitacion, id_cliente, id_estructura_cliente, id_micro, comentario
			       FROM ordenes
			       WHERE ((fservicio between '$desde' and '$hasta') and (id_estructura = $estr)) and (not suspendida) and (not borrada)
			       ) o
                         inner JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2) and (ch2.id_cargo = 1)
                         inner join empleadores emp on (emp.id = ch2.id_empleador) and (emp.id = $_GET[emp])
			 inner join clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
                         left join unidades un on (un.id = o.id_micro)
			 )
        UNION ALL
			(SELECT '' as interno, '' as razon_social, n.id_empleado as id_cond, '$desde' as fsrv, upper(CONCAT(nov_text, ' (',date_format(desde, '%d/%m/%Y'), ' - ', date_format(hasta, '%d/%m/%Y'),')')) as servicio,
			        '00:00' as hsalida, 0 as id, date_format('$desde', '%d/%m/%Y') as fservicio, '00:00' as hcitacion, upper(if(emp.id = 1,concat(em.apellido, ', ',em.nombre), concat('(',emp.razon_social,') ', em.apellido, ', ',em.nombre))) as apellido, legajo, if (desde < '$desde', '$desde', desde) as li, if (hasta > '$hasta', '$hasta', hasta) as ls, DATEDIFF(if (hasta > '$hasta', '$hasta', hasta), if (desde < '$desde', '$desde', desde)) as dias, '00:00' as hfinservicio, '' as comentario
			 FROM (SELECT * FROM novedades WHERE (((hasta between '$desde' and '$hasta') or (desde between '$desde' and '$hasta') or ('$desde' between desde and hasta) or ('$hasta' between desde and hasta)) and (activa) and (id_estructura = $estr)) ) n
			 inner join cod_novedades cn on cn.id = n.id_novedad
			 inner join empleados em on (em.id_empleado = n.id_empleado) and (em.id_cargo = 1)
			 inner join empleadores emp on (emp.id = em.id_empleador) and (emp.id = $_GET[emp])
			)
        ) o
        $cond
        order by apellido, li, hcitacion";
    //  die($sql);

     $result = mysql_query($sql, $conn);

     $data = mysql_fetch_array($result);
     $pdf->SetFillColor(110);
          $pdf->SetFont('Times','',10);
     $pdf->Cell(195,7,"KILOMETROS RECORRIDOS POR CONDUCTOR",1, 1, 'C', true);
     $pdf->SetFont('Times','',6);
     $pdf->ln();

     while ($data){
            $pdf->SetFillColor(150);
        //   die($sql);
           $cond = $data['legajo'];
           $pdf->Cell(195,7,"Conductor:  ".htmlentities($data['apellido'])."     -     Legajo:  $data[legajo]",1, 1, 'C', true);
           $pdf->SetFillColor(190);
           $pdf->Cell(15,7,"F. Servicio",1,0,'C', true);
           $pdf->Cell(10,7,"H. Cita.",1,0,'C', true);
           $pdf->Cell(10,7,"H. Sal.",1,0,'C', true);
           $pdf->Cell(10,7,"H. Fin.",1,0,'C', true);
           $pdf->Cell(45,7,"Servicio",1,0,'C', true);
           $pdf->Cell(10,7,"Interno",1,0,'C', true);
           $pdf->Cell(35,7,"Cliente",1,0,'C', true);
           $pdf->Cell(60,7,"Observaciones",1,1,'C', true);
         /*  $tabla.= "<table width='100%' class='order'>
                           <thead>
                                <tr>
                                    <th colspan='7'>Conductor:  ".htmlentities($data['apellido'])."     -     Legajo:  $data[legajo]</th>
                                </tr>
                                <tr>
                                    <th>Fecha de Servicio</th>
                                    <th>Hora de Citacion</th>
                                    <th>Hora de Salida</th>
                                    <th>Hora Finalizacion</th>
                                    <th>Servicio</th>
                                    <th>Interno</th>
                                    <th>Cliente</th>
                                    <th>Observaciones</th>
                                </tr>
                           </thead>
                           <tbody>";*/
           $ult='';
           $j=0;
           while (($data) && ($cond == $data['legajo'])){

              //   while (($data) && ($cond == $data['legajo'])){
                    $fserv=$data['li'];
                    $fec = date_create($fserv);

                    for ($i=0; $i <= $data['dias']; $i++){
                    
                       /*if ($ult){
                           //die("ultimo registro ".$ult);
                          if ($interval > 0){
                             //die($ult."          ".$fec);
                             $tabla.="<tr><td colspan='8'><hr align='tr'></td></tr>";
                          }
                          else{

                               //$ult=$fec;
                               }
                       }     */
                     /*  if ($data['legajo'] == 0){
                          $color="#C0FFFF";
                       }
                       else
                       if(!in_array($data['fservicio'], $state)){
                           $color="#FFC0C0";
                       }
                       else{
                            $color = (($j%2)==0)?'#CFCFCF':'#96B8B6';
                       }  */
                       $pdf->Cell(15,7,date_format($fec,'d/m/y'),0,0,'C');
                       $pdf->Cell(10,7,"$data[hcitacion]",0,0,'C');
                       $pdf->Cell(10,7,"$data[hsalida]",0,0,'C');
                       $pdf->Cell(10,7,"$data[hfinservicio]",0,0,'C');
                       $auxY = $pdf->GetY();
                       $auxX = $pdf->GetX();
                       $pdf->drawTextBox(utf8_decode($data['servicio']),45,7,'L', 'M', false);
                       $pdf->SetY($auxY);
                       $pdf->SetX($auxX+45);
                     //  $pdf->Cell(30,7,,0,0,'L');
                       $pdf->Cell(10,7,"$data[interno]",0,0,'C');
                       
                       $pdf->Cell(35,7,"$data[razon_social]",0,0,'L');
                       
                       $auxY = $pdf->GetY();
                       $auxX = $pdf->GetX();
                       $pdf->drawTextBox(utf8_decode($data['comentario']),60,7,'L', 'M', false);
                     //  $pdf->SetY($auxY);
                     //  $pdf->SetX($auxX+35);
                       
                       
                 //      $pdf->Cell(20,7,htmlentities($data['razon_social']),0,0,'L');
                  //     $pdf->Cell(60,7,"$data[comentario]",0,1,'L');

                     /*  $tabla.="<tr bgcolor='$color' id='$data[id]' class='modord'>
                                    <td width='10%' align='center'>".date_format($fec,'d/m/Y')."</td>
                                    <td width='7%' align='center'>$data[hcitacion]</td>
                                    <td width='7%' align='center'>$data[hsalida]</td>
                                    <td width='10%' align='center'>$data[hfinservicio]</td>
                                    <td width='25%'>".utf8_decode($data['servicio'])."</td>
                                    <td width='5%'>$data[interno]</td>
                                    <td width='10%'>".htmlentities($data['razon_social'])."</td>
                                    <td width='25%'>$data[comentario]</td>

                                </tr>";       */
                       $ult = $fec;
                       date_modify($fec, '+1 day');
                    }
                    $data = mysql_fetch_array($result);
                    
                    if ($fserv != $data['li']){
                       $pdf->ln(1);
                       $pdf->Line($pdf->GetX(), $pdf->GetY(), ($pdf->GetX()+195), $pdf->GetY());
                       $pdf->ln(1);
                    //   $tabla.="<tr><td colspan='8' bgcolor='#FFFFFF'><hr align='tr'></td></tr>";
                       $j++;
                    }
               //  }

           }
           $pdf->ln();
      //     $tabla.="</tbody></table><br>";
     }
$pdf->Output();
  
?>

