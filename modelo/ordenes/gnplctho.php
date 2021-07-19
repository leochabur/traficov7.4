<?php
    //   die('que sapa');
  //     exit();
//  session_start();
 // set_time_limit(0);

  //ini_set("memory_limit","40M");

  /*if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }       */
  include ('../../controlador/bdadmin.php');
 // include ('../../../modelo/utils/dateutils.php');
  require('../../fpdf.php');

  
  
class PDF extends FPDF
{

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
     $conn=conexcion();
     $estr = $_SESSION[structure];
     
     ///////////////////////
   /*  $conn = mysql_connect('traficonuevo.masterbus.net', 'c0mbexpuser', 'Mb2013Exp');
           //mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $conn);
     mysql_query("SET NAMES 'utf8'", $conn);
     mysql_select_db('c0mbexport', $conn);    */
     
     //////////////////////////
     $cond = $_GET['cond'];
     $desde = $_GET['desde'];//$_POST['desde'];
     $hasta = $_GET['hasta']; //$_POST['hasta'];
     if ($cond != 0)
        $cond = "where id_cond = $cond";
     else
         $cond = "";

    $sql="select *
          from(
          select i_v, tipo, ts.id, nombre, if(i_v = 'i', o.hllegada, o.hsalida) as horario, if(i_v = 'i', 'Ingreso', 'Egreso') as entrada
          from ordenes o
          inner join servicios s on s.id = o.id_servicio
          inner join tiposervicio ts on ts.id = s.id_TipoServicio
          where fservicio = '2016-09-16' and o.id_cliente = 10 and o.id_estructura = 1 and id_turno = 1 and id_TipoServicio in (1,2,16)) o
          order by i_v, id desc, horario";
    //  die($sql);

     $result = mysql_query($sql, $conn);

     $data = mysql_fetch_array($result);
     $pdf->SetFillColor(110);
     $pdf->SetFont('Times','',10);


     $auxX = $pdf->GetX();
     $auxY = $pdf->GetY();
     $ok = false;
     while ($data){
        //   $pdf->SetFillColor(150);
           $pdf->SetX($pdf->GetX()+45);
           $pdf->SetY($auxY);
           if ($ok)
          //    die(" ddfdfd  ".$auxX);
           $ok=true;
           $pdf->text($auxX, $auxY, "toronjaaaa  ");
           $iv = $data['i_v'];
           $pdf->Cell(10,7,"$data[entrada]",1,0,'C');
           while (($data) && ($iv == $data['i_v'])){
                 $pdf->Cell("$data[tipo]",1,0,'C');
                 $tipo = $data['tipo'];
                 while (($data) && ($iv == $data['i_v']) && ($tipo == $data['tipo'])){
                       $pdf->Cell("$data[Horario]",1,0,'C');
                       $horario = $data['horario'];
                       while (($data) && ($iv == $data['i_v']) && ($tipo == $data['tipo']) && ($horario == $data['horario'])){




                             $data = mysql_fetch_array($result);
                       }

                  }
           }
           $auxX=($auxX+65);
          // die("yyyyyyyyyyyy $auxX");
       //    die("$iv  $tipo  $horario  ffffffffff");
          // $pdf->ln();
     }
$pdf->Output();
  
?>

