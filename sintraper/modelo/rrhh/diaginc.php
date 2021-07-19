<?
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../controlador/bdadmin.php');
  include_once('../../controlador/ejecutar_sql.php');

  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];
  if ($accion == 'load'){
     $conn = conexcion();
     $sql = "SELECT upper(nov_text) as novedad, c.id, if (n.id is null, '', 'checked') as seleccionada
             FROM cod_novedades c
             left join novporincent n on n.id_novedad = c.id
             where activa
             order by nov_text";
     $result = mysql_query($sql, $conn);
     $tabla.= "<table width='50%' class='order' align='center'>
                           <thead>
                                <tr>
                                    <th>Novedad</th>
                                    <th align='center'>Incluir Si/No</th>
                                </tr>
                           </thead>
                           <tbody>";
           while ($data = mysql_fetch_array($result)){
                       $tabla.="<tr >
                                    <td width='80%'>".htmlentities($data['novedad'])."</td>
                                    <td width='20%' align='center'><input $data[seleccionada] type='checkbox' id='$data[id]'></td>
                                </tr>";

           }
           $tabla.="</tbody></table><br>";
     $tabla.="<style type='text/css'>
                     table.order {
	                              font-family:arial;
	                              background-color: #CDCDCD;
                                  font-size: 8pt;
	                              text-align: left;
                               }
                     table.order thead tr th, table.tablesorter tfoot tr th {
                                                                            background-color: #e6EEEE;
                                                                            border: 1px solid #FFF;
	                                                                        font-size: 8pt;
	                                                                        padding: 4px;}
                     table.order tbody td {
	                                        color: #3D3D3D;
	                                        padding: 4px;
	                                        vertical-align: top;
                                         }
                     td.click, th.click{
                                        background-color: #bbb;
                                        }
                     td.hover, tr.hover{
                                        background-color: #69f;
                                        }
                     th.hover, tfoot td.hover{
                                              background-color: ivory;
                                              }
                     td.hovercell, th.hovercell{
                                                background-color: #abc;
                                                }
                     td.hoverrow, th.hoverrow{
                                              background-color: #6df;
                                              }
              </style>
               <script type='text/javascript'>
                                $('.order').tableHover();
                                $(':checkbox').click(function(){
                                                                var activado;
                                                                if($(this).attr('checked')) {
                                                                    activado = 1;
                                                                } else {
                                                                    activado = 0;
                                                                }
                                                                $.post('/modelo/rrhh/diaginc.php', {accion: 'addnov', state: activado, nov: $(this).attr('id')}, function(data){});
                                                                });
               </script>";
     print $tabla;
  }
  elseif ($accion == 'addnov'){
         if ($_POST['state']){
            insert('novporincent', 'id, id_novedad, suma_resta', "$_POST[nov], 's'");
         }
         else{
              delete('novporincent', 'id_novedad', $_POST['nov']);
         }
  }
?>

