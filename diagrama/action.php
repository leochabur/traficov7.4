<?php 
	include("data.php");
	$link=conectar_mysql();
	
	function getProvincias(){
		global $link;
		$sql="select * from provincia where idpais=".$_POST["idpais"];
		$result=mysql_query($sql,$link);
		$resp="";
		if($result){
			if(mysql_num_rows($result)>0){
				$resp.="<option value=''>- Seleccione Provincia -</option>";
				while($r=mysql_fetch_object($result)){
					$resp.="<option value='".$r->idprovincia."'>".$r->nombre_pro."</option>";
				}
			}else $resp="<option value=''>No hay resultados</option>";			
		}else $resp="ERROR";
		echo $resp;
	}
	function getDiagrama(){
             $mysqli = new mysqli('trafico.masterbus.net', 'masterbus', 'master,07a', 'trafico');
             if (mysqli_connect_error()) {
                die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
             }
             else
             {
              $sql="SELECT date_format(fservicio,'%d/%m/%y') as fservicio, hcitacion, hsalida, nombrecliente, localidad, nombre, interno, id_orden, upper(choferes.apenom) as chofer
                              FROM ordenes a
                              INNER JOIN servicios ON a.id_servicio=servicios.id_servicio
                              INNER JOIN cronogramas ON cronogramas.codigo=servicios.cod_cronograma
                              INNER JOIN micros ON micros.id_micro=a.id_micro
                              INNER JOIN choferes ON ((choferes.id_chofer=a.id_chofer1) OR (choferes.id_chofer=a.id_chofer2))
                              INNER JOIN localidades ON localidades.id_localidad=cronogramas.origen
                              WHERE ((fservicio between curdate() and ADDDATE(curdate(),2)) and (choferes.legajo = $_POST[conductor]))
                              ORDER BY fservicio,hcitacion, hsalida, nombrecliente, localidad, codigo";
              $result= mysqli_query($mysqli,$sql);
              if(mysqli_num_rows($result) == 0){
                   echo "<script> alert('No hay datos para mostrar')</script>";
              }
              else{
                 $res = mysqli_fetch_array(mysqli_query($mysqli, "SELECT date_format(curdate(),'%d/%m/%y') as inicio, date_format(ADDDATE(CURDATE(),2),'%d/%m/%y') as fin"));
                 $data= mysqli_fetch_array($result);
                 print "<HR>";
                 print "<br><center><span class=\"tmediano\">".htmlentities($data['chofer'])."</span></center>";
                 print "<br><center><h3>Diagrama de trabajo desde el <span class=\"tmediano\"> $res[inicio] </span> al <span class=\"tmediano\"> $res[fin] </span></h3></center>";
	             print"<center><table width=\"100%\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\"  id=\"tabla\">";
	             $cliente=$data['nombrecliente'];
	             $origen=$data['localidad'];
				 print"<tr bgcolor=\"#0099FF\">
                       <td ><span class=\"Estilo3\"><div align=\"center\">Fecha servicio</div></span></td>
    			       <td ><span class=\"Estilo3\"><div align=\"center\">Hora Citacion</div></span></td>
    			       <td ><span class=\"Estilo3\"><div align=\"center\">Hora Servicio</div></span></td>
    			       <td ><span class=\"Estilo3\"><div align=\"center\">Cliente</div></span></td>
    			       <td ><span class=\"Estilo3\"><div align=\"center\">Servicio</div></span></td>
    			       <td ><span class=\"Estilo3\"><div align=\"center\">Interno</div></span></td>";

                 $i=0;
                 while($data){
                       if (($i%2)==0){
                          $color="#A8A8A8";
                 }
                 else{
                      $color="#FFFFFF";
                 }
                 $ult = $data['fservicio'];
                 while(($data) && ($ult == $data['fservicio'])){
                      $hcitacion=substr($data['hcitacion'],0,5);
                      $hsalida=substr($data['hsalida'],0,5);
                      $id_orden=$data[id_orden];
                      $finalizada=$data['finalizada'];
                      $chequeada=$data['chequeada'];
			          $fecha = $data['fservicio'];
			          $chofer2 = $listachoferes[ $data['id_chofer2'] ];
                      print"<tr bgcolor=\"$color\">";
			          print "<td><div align=\"center\">$fecha</div></td>
                             <td><div align=\"center\">$hcitacion</div></td>
                             <td><div align=\"center\">$hsalida</div></td>
				             <td>".htmlentities($data['nombrecliente'])."</td>";
                      print "<td><div align=\"center\">".htmlentities($data['nombre'])."</div></td>
			                 <td> $data[interno] </td>";
                      $data= mysqli_fetch_array($result);
                    }
                 $i++;
	           }
	mysqli_free_result($result);
	print"</td>
	</tr>

	</table></center>";
	}
}
	}
	
	if($_POST){
		switch($_POST["tarea"]){
		case "listProvincia":getProvincias();
				break;
		case "listDiagrama":getDiagrama();
				break;
		}
	}
?>
