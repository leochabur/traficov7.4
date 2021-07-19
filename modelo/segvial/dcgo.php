<?php
  session_start();
  if (!$_SESSION['auth']){
     //print "<br><b>La sesion a expirado</b>";
    // exit;
  }
require('../../fpdf.php');
require('../../controlador/bdadmin.php');
require('../../modelo/utils/utils.php');



class PDF extends FPDF{
function drawTextBox($strText, $w, $h, $align='L', $valign='T', $border=true)
{
    $xi=$this->GetX();
    $yi=$this->GetY();

    $hrow=$this->FontSize;
    $textrows=$this->drawRows($w,$hrow,$strText,0,$align,0,0,0);
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
function Header()
{
    // Logo
    $this->Line($this->GetX(), 5, 190, 5);
    $this->Image('../../masterbus-log.png',10,8,33);     // <img src="masterbus-logo.png" border="0">
    // Arial bold 15
    $this->SetFont('Arial','B',10);
    // Movernos a la derecha
    // Título
   // $this->Text(85, 10, "REGISTRO DE");
    $this->Text(80, 15, "SOLICITUD DE EXPLICACION");
        $this->SetFont('Arial','',10);
    $this->Text(145, 10, "FORM. 172.");
    $this->Text(145, 15, "Fecha de Vigencia: 26/02/15");
    $this->Text(145, 20, "Pagina 1 de 1");
    $this->Line($this->GetX(), 25, 190, 25);
    // Salto de línea
    $this->Ln(20);
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
/*    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');    */
}
}

// Creación del objeto de la clase heredada
$pdf = new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);

$conn = conexcion();//mysql_connect('200.80.61.27', 'mbexpuser', 'Mb2013Exp');
$sql="SELECT nro_descargo, date_format(fecha_emision, '%d/%m/%Y') as emision, upper(concat(apellido,', ',nombre)) as emple, legajo, descripcion_hecho
                  FROM descargos d
                  inner join empleados e on e.id_empleado = d.id_empleado
                  where d.id = '$_GET[dcgo]'";

$result = mysql_query($sql, $conn) or die(mysql_error($conn));
if (mysql_num_rows($result)){
if ($row = mysql_fetch_array($result)){
            $pdf->SetFillColor(255, 255, 255);
            $pdf->Cell(90,7,"Nº:   $row[0]",1, 0, 'L', True);
            $pdf->Cell(90,7,"Fecha de emisión:   $row[emision]",1, 1, 'L', True);
                        $pdf->ln();
            $pdf->Cell(120,10,"Empleado :   $row[emple]",1, 0, 'L', True);
            $pdf->Cell(60,10,"Legajo:  $row[legajo]",1, 0, 'L', True);
            $pdf->ln();
                        $pdf->ln();
            $pdf->SetFont('Times','',10);
            $pdf->drawTextBox("DESCRIPCION DEL HECHO:  ". utf8_decode($row[descripcion_hecho]),180,25,'L', 'T');
            $pdf->SetFont('Times','',10);
            $pdf->ln();
                        $pdf->ln();
                                    $pdf->ln();

            $pdf->text($pdf->getX(), $pdf->getY(),'Firma responsable RRHH/REDI____________________________               Fecha de Entrega:...../...../..........',180,20,'L', 'B', false);
            $pdf->SetFont('Times','B',12);
                        $pdf->ln();
            $pdf->drawTextBox('NOTA: La presente solicitud  deberá completarla y entregarla dentro de los  cinco días hábiles de recibido el presente ante la oficina de Recursos Humanos, vencido este término se darán por cierto los hechos.',180,14,'L', 'T');
            $pdf->ln();
            $pdf->SetFont('Times','',10);
            $pdf->Text($pdf->GetX(), $pdf->GetY()+3,'EXPLICACIÓN DEL EMPLEADO: (En caso de necesitar más espacio, podrá continuar al dorso de esta hoja).');
            $pdf->Rect($pdf->GetX(), $pdf->GetY(), 180,57);
            $pdf->SetY($pdf->GetY()+60);

            $pdf->drawTextBox('RESOLUCION:',180,55,'L', 'T');
            $pdf->SetY($pdf->GetY()+55);
            $pdf->Rect($pdf->GetX(), $pdf->GetY(), 180,25);
            $pdf->SetY($pdf->GetY()+5);
            $pdf->Text($pdf->GetX()+72, $pdf->GetY(), "Fecha de Notificacion");
            $pdf->SetY($pdf->GetY()+5);
            $pdf->Text($pdf->GetX()+75, $pdf->GetY(), "____/____/______");
            $pdf->Text($pdf->GetX()+8, $pdf->GetY()+8, ".........................................................");
            $pdf->Text($pdf->GetX()+120, $pdf->GetY()+8, ".....................................................");
            $pdf->Text($pdf->GetX()+10, $pdf->GetY()+13, "Firma del Responsable de RRHH");
            $pdf->Text($pdf->GetX()+130, $pdf->GetY()+13, "Firma del Notificado");
            $pdf->SetY($pdf->GetY()+5);
            $pdf->Text($pdf->GetX(), $pdf->GetY()+16, "******************************************************************************************************");
            $pdf->SetY($pdf->GetY()+20);
            $pdf->Rect($pdf->GetX(), $pdf->GetY(), 180,25);
            $pdf->Text($pdf->GetX()+72, $pdf->GetY()+12, "____/____/______");
            $pdf->SetY($pdf->GetY()+5);
            $pdf->Text($pdf->GetX()+8, $pdf->GetY(), "Nº de Parte: $row[0]                              Fecha de Entrega de Solicitud de Explicación                          Legajo: $row[legajo]");
            $pdf->Text($pdf->GetX()+8, $pdf->GetY()+15, ".........................................................");
            $pdf->Text($pdf->GetX()+120, $pdf->GetY()+15, ".....................................................");
            $pdf->Text($pdf->GetX()+18, $pdf->GetY()+18, "Firma del Empleado");
            $pdf->Text($pdf->GetX()+135, $pdf->GetY()+18, "Aclaracion");
            $pdf->SetFont('Times','B',12);
            $pdf->Text($pdf->GetX()+60, $pdf->GetY()+13, "PARA RECURSOS HUMANOS");
            $pdf->SetY($pdf->GetY()+5);
}
else{
            $pdf->SetFont('Times','B',12);
            $pdf->Text($pdf->GetX()+60, $pdf->GetY()+13, "No se ha encontrado el descargo en la Base de Datos");
}
}
else{
            $pdf->SetFont('Times','B',12);
            $pdf->Text($pdf->GetX()+60, $pdf->GetY()+13, "No se ha encontrado el descargo en la Base de Datos");
}

//print_r($resumen);
$pdf->Output();
//$pdf->Output();

?>
