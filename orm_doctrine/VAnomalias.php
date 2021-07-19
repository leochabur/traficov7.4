<?
//require_once($_SERVER['DOCUMENT_ROOT'].'/orm_doctrine/conexcion.php');
include_once ('../controlador/ejecutar_sql.php');




function selectAnomaliasActivas(){

         $rubros =  "SELECT id, rubro FROM rubros_anomalias order by rubro";
         $rubAct = ejecutarSQL($rubros);
         $options = "";
         while ($data = mysql_fetch_array($rubAct)){
             $options.="<option value='$data[id]'>$data[rubro]</option>";
         }
		 print $options;
}
