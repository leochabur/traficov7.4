<? session_start();
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('../../controlador/bdadmin.php');
  $conn = conexcion();
  $sql = $_POST['sql'];
  
  die($sql);
  
  $result = mysql_query($sql, $conn) or die("provblerma sql $sql");

  function encabezado( $query, $field) {
        $names="<tr class='ui-widget-header'>";
        for ( $i = 0; $i < $field; $i++ ) {
            $names.= "<th>".mysql_field_name( $query, $i)."</th>";
        }
        $names.="</tr>";
        return $names;
  }
?>

<HTML>
<HEAD>
 <TITLE>New Document</TITLE>
</HEAD>
<BODY>
<?
  die("paso por aca");
  $campos = mysql_num_fields( $query );
  $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
          <thead>';
  $tabla.=encabezado($result, $campos);
  $tabla.='</thead>
           <tbody>';
  while ($data = mysql_fetch_array($result)){
        $tabla.='<tr>';
        for ($i = 0; $i < $campos; $i++){
            $tabla.="<td>$data[$i]</td>";
        }
        $tabla.='</tr>';
  }
  $tabla.='</tbody>
           </table>';
  print $tabla;
?>
</BODY>
</HTML>
