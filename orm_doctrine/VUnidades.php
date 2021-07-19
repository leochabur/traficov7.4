<?
//require_once($_SERVER['DOCUMENT_ROOT'].'/orm_doctrine/conexcion.php');
include_once ('../controlador/ejecutar_sql.php');




function selectCochesActivos(){

         $rubros =  "SELECT id, interno FROM unidades WHERE activo and id_estructura in (select afectado_a_estructura from empleados where id_empleado = $_SESSION[id_chofer]) ORDER BY interno";
         $rubAct = ejecutarSQL($rubros);
         $options = "";
         while ($data = mysql_fetch_array($rubAct)){
             $options.="<option value='$data[id]'>$data[interno]</option>";
		};
		print $options;
}
