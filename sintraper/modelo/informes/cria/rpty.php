<?
  session_start();
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  require('../../../modelo/utils/utils.php');

  print "<br><table border='0' width='50%' align='center'>
<tr>
  <td  style='vertical-align:middle;' align='left'>Reporte Confiabilidad - ".ucwords(nombremes($_POST['mes']))." $_POST[anio] </td><td><a href='../../../pdf_toyota.php?mes=$_POST[mes]&anio=$_POST[anio]&cli=$_POST[cli]' target='_blank'><img src='../../../pdf.png' width='45' height='45' border='0'></a></td>
</tr>
<tr>
 <td  style='vertical-align:middle;' align='left'>Reporte Eficiencia - ".ucwords(nombremes($_POST['mes']))." $_POST[anio] </td><td><a href='../../../efi_resumen.php?mes=$_POST[mes]&anio=$_POST[anio]&cli=$_POST[cli]' target='_blank'><img src='../../../pdf.png' width='45' height='45' border='0'></a></td>
</tr>
<tr>
 <td  style='vertical-align:middle;' align='left'>Sucesos - ".ucwords(nombremes($_POST['mes']))." $_POST[anio] </td><td><a href='../../../sucesos.php?mes=$_POST[mes]&anio=$_POST[anio]&cli=$_POST[cli]' target='_blank'><img src='../../../pdf.png' width='45' height='45' border='0'></a></td>
</tr>
<tr>
 <td  style='vertical-align:middle;' align='left'>Ratios - ".ucwords(nombremes($_POST['mes']))." $_POST[anio] </td><td><a href='../../../ratios.php?mes=$_POST[mes]&anio=$_POST[anio]&cli=$_POST[cli]' target='_blank'><img src='../../../pdf.png' width='45' height='45' border='0'></a></td>
</tr>
<tr>
<tr>
 <td  style='vertical-align:middle;' align='left'>Graficos Confiabilidad - ".ucwords(nombremes($_POST['mes']))." $_POST[anio] </td><td><a href='../../../ex.php?mes=$_POST[mes]&anio=$_POST[anio]&cli=$_POST[cli]' target='_blank'><img src='../../../pdf.png' width='45' height='45' border='0'></a></td>
</tr>
<tr>
 <td  style='vertical-align:middle;' align='left'>Graficos Eficiencia - ".ucwords(nombremes($_POST['mes']))." $_POST[anio] </td><td><a href='../../../exe.php?mes=$_POST[mes]&anio=$_POST[anio]&cli=$_POST[cli]' target='_blank'><img src='../../../pdf.png' width='45' height='45' border='0'></a></td>
</tr>
<tr>
  <td>&nbsp;</td>
</tr>
<tr>
  <td>&nbsp;</td>
</tr>
<tr>
  <td>&nbsp;</td>
</tr>
</table>";
  
?>

