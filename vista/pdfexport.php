<?php
error_reporting(0);
set_time_limit(0);
require('fpdf/fpdf.php');
include('../../controlador/bdadmin.php');


class PDF extends FPDF{

function ImprovedTable($header, $sql, $size){
     $conn = conexcion(true);
     $results = mysqli_query($conn, $sql);

     $resultset = array();
     while ($row = mysqli_fetch_array($results)) {
           $resultset[] = $row;
     }
     mysqli_free_result($results);
     mysqli_close($conn);
    $w = $size;
    // Cabeceras
    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C');
    $this->Ln();
    // Datos
    $this->SetFont('Arial','',8);
    foreach($resultset as $row)
    {
        for($i=0;$i<count($header);$i++){
        $this->Cell($w[$i],6,$row[$i],'LR');
        }
        $this->Ln();
    }
    // Línea de cierre
    $this->Cell(array_sum($w),0,'','T');
}
}
?>
