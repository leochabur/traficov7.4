<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
include('../../fpdf.php');
include_once ('../../modelsORM/manager.php');
include_once ('../../modelsORM/call.php');
include_once ('../../modelsORM/controller.php');


class PDF extends FPDF
{
    private $curso, $calif;

    public function setCurso($curso, $calif)
    {
        $this->curso = $curso;
        $this->calif = $calif;
    }
    // Cabecera de página
    function Header()
    {
        $cantClases = 0;
        foreach($this->curso->getClases() as $cl)
        {
          if (!$cl->getEliminada())
            $cantClases++;
        }
        $desp = 5;
        // Logo
        $this->Image('../../masterbus-log.png',19,17,44);     // <img src="masterbus-logo.png" border="0">
        $this->rect(15, 5+$desp, 55, 21);
        // Arial bold 15
        $this->SetFont('TIMES','B',10);
        // Movernos a la derecha

        // Título
        $this->rect(70, 5+$desp, 88, 21);

        $this->text(80, 17+$desp, "REGISTRO DE CAPACITACION VIRTUAL");

        $this->SetFont('');

        $this->text(160, 10+$desp, 'FORM.227.00');
        $this->text(160, 17+$desp, 'Fecha de Vigencia: 22/09/2020');
        $this->text(160, 24+$desp, 'Pagina: '.$this->PageNo().' de {nb}');
        $this->rect(158, 5+$desp, 47, 21);
        // Salto de línea
        $this->Ln(24);

        $this->setX(15);
        $this->cell(190, 7, "TEMA DE CAPACITACION: ".$this->curso,1);
        $this->ln();
        $this->setX(15);
        $this->cell(190, 7, "OBJETIVO: ".$this->curso->getDescripcion(),1);
        $this->ln();
        $this->setX(15);
        $this->cell(120, 7, "Duracion: ".$cantClases." Modulos",1);
        $this->cell(70, 7, "Modalidad: AULA VIRTUAL",1);
        $this->ln();
        $this->setX(15);
        $this->cell(95, 7, "Instructor:",1);
        $this->cell(95, 7, "Lugar: AULA VIRTUAL",1);
        $this->Ln(10);
        $this->setX(15);

        $this->cell(90, 5, "APELLIDO Y NOMBRE", 1, 0,'C');

        $this->cell(18, 5, "DNI", 1, 0,'C');

        $this->cell(45, 5, "PUESTO", 1, 0,'C');
        if ($this->calif)
        {
          $this->cell(25, 5, "FECHA-HORA", 1,0, 'C');
          $this->cell(12, 5, "CALIF", 1,0, 'C');
        }
        else
        {
          $this->cell(37, 5, "FECHA-HORA", 1,0, 'C');
        }
        $this->SetFont('TIMES','B',8);
        $this->SetFont('');
        $this->Ln();
        $this->setX(15);
    }

}

$curso = find('Curso', $_GET['cso']);

$dql = "SELECT cr
        FROM ClaseRealizada cr
        JOIN cr.clase cl
        JOIN cl.curso cu
        JOIN cr.empleado e
        JOIN e.categoria cgo
        WHERE cl.esEvaluacion = :evaluacion AND cu = :socur AND cl.eliminada = :eliminada
        ORDER BY e.apellido
        ";
$result = $entityManager->createQuery($dql)
                        ->setParameter('evaluacion', true)
                        ->setParameter('eliminada', false)
                        ->setParameter('socur', $curso)
                        ->getResult();

// Creación del objeto de la clase heredada
$pdf = new PDF();
$pdf->setCurso($curso, $_GET['pje']);
$pdf->AliasNbPages();
$pdf->AddPage('P', 'A4');



foreach ($result as $res)
{
    $ok = true;

    if ($_GET['str'])
    {
        if ($_GET['str'] != $res->getEmpleado()->getEstructura()->getId())
        {
          $ok = false;
        }
    }

    if ($_GET['emp'])
    {
        if ($_GET['emp'] != $res->getEmpleado()->getEmpleador()->getId())
        {
          $ok = false;
        }
    }

    if ($ok)
    {
      $emple = $res->getEmpleado();
      $str = iconv('UTF-8', 'windows-1252', "$emple");
      $pdf->cell(90, 5, "$str", 1, 0,'L');

      $pdf->cell(18, 5, $emple->getNumeroDocumento(), 1, 0,'L');


      $pdf->cell(45, 5, ($emple->getCategoria()?$emple->getCategoria()->getDescripcion():''), 1, 0,'L');
     // $pdf->cell(45, 5, '', 1, 0,'L');
      
      if ($_GET['pje'])
      {
          $pdf->cell(25, 5, $res->getFecha()->format('d/m/Y - H:i'), 1,0, 'C');
          $pdf->cell(12, 5, $res->getPuntaje(), 1,0, 'R');
      }
      else
      {
        $pdf->cell(37, 5, $res->getFecha()->format('d/m/Y - H:i'), 1,0, 'C');
      }
      
      $pdf->Ln();
      $pdf->setX(15);
    }
}





$pdf->Output();



       /* $key = $cso->getNombre().$res->getEmpleado()->getEstructura().$res->getEmpleado()->getEmpleador()->getRazonSocial().$res->getEmpleado().$res->getEmpleado()->getLegajo();          
        $data[$key] = array($cso,
                            $res->getEmpleado()->getEstructura()->getNombre(),
                            $res->getEmpleado()->getEmpleador()->getRazonSocial(),
                            $res->getEmpleado()->getLegajo(),
                            $res->getEmpleado()."",
                            $res->getFecha()->format('d/m/Y'),
                            true,
                            $res->getEmpleado()->getId(),
                            $res->getId(),
                            $res->getPuntaje()
                            );       */

?>
