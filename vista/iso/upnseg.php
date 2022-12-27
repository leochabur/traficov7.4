<?php
     session_start(); //modulo para dar de alta una provincia
     include('../main.php');
     include('../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
     include_once('../../controlador/ejecutar_sql.php');
?>

 <link type="text/css" href="/vista/css/estilos.css" rel="stylesheet" />
<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}

#newepe .error{
	font-size:0.8em;
	color:#ff0000;
}
table tr td{padding: 3px;}
</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#upload').button();


                                                       <?php

                                                            if (isset($_GET['nvid']))
                                                            {
                                                        ?>
                                                                $('#recupd').submit(function(event){
                                                                                                    event.preventDefault();
                                                                                                    var form = $(this);
                                                                                                    $.post(form.attr('action'),
                                                                                                            form.serialize(),
                                                                                                            function(data){
                                                                                                                            var result = $.parseJSON(data);
                                                                                                                            if (result.ok)
                                                                                                                            {
                                                                                                                                window.location.href = "/vista/iso/upnseg.php";
                                                                                                                            }
                                                                                                                            else
                                                                                                                            {
                                                                                                                                alert(result.message);
                                                                                                                            }
                                                                                                            });  
                                                                });
                                                        <?php
                                                            }


                                                       ?>
                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>

    <?php

        if (isset($_GET['nvid']))
        {
            $conn = conexcion(true);
            $sql = "SELECT texto, activo FROM normativa WHERE id = $_GET[nvid]";
            $result = ejecutarSQLPDO($sql, $conn);
            $activo = $texto = $activo = "";
            if ($row = mysqli_fetch_array($result))
            {
                $activo = ($row['activo']?'checked':'');
                $texto = $row['texto'];
            }
            else
            {
                print $sql. '  . . . .  . ' . $_GET['nvid'];
            }

    ?>

        <div id="tabs-1" class="ui-state-highlight ui-corner-all">
             <form action="/vista/iso/upload.php" method="post" enctype="multipart/form-data" id="recupd">
                   <fieldset class="ui-widget ui-widget-content ui-corner-all">
                             <legend class="ui-widget ui-widget-header ui-corner-all">Modificar Recurso</legend>
                             <div id="mensaje"></div>
                             <table border="0" align="center" width="50%" name="tabla">
                                    <tr>
                                        <td>Nombre Link</td>
                                        <td><input name="link" id="link"  type="text" size="45" value="<?php  print $texto;  ?>"></td>
                                    </tr>
                                    <tr>
                                        <td>Activo</td>
                                        <td><input name="activo"  type="checkbox" <?php  print $activo; ?>></td>
                                    </tr>
                                    <tr>
                                        <td>Sectores</td>
                                        <td>
                                            <select name="sectores[]" multiple="multiple">
                                                <?php
                                                        
                                                        $result = ejecutarSQLPDO("SELECT s.id, s.descripcion, nxs.id as selec
                                                                                  FROM sector s
                                                                                  LEFT JOIN (SELECT * from normativaXSector WHERE id_normativa = $_GET[nvid]) nxs ON nxs.id_sector = s.id", $conn);
                                                        while ($row = mysqli_fetch_array($result))
                                                        {
                                                            $selected = ($row['selec'] ? 'selected="selected"':'');
                                                            print "<option value='$row[id]' $selected>".ucwords(strtolower($row['descripcion']))."</option>";
                                                        }


                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" align="rigth">
                                            <input type="submit" id="upload" value="Guardar Cambios">
                                        </td>
                                    </tr>
                             </table>
                             
                </fieldset>
                <input name="action" type="hidden" value="update"/>
                <input name="idrecurso" type="hidden" value="<?php print $_GET['nvid'] ?>"/>
             </form>
       </div>


    <?php

        }
        else
        {

    ?>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form action="/vista/iso/upload.php" method="post" enctype="multipart/form-data">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Subir Recurso</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Buscar</td>
                                    <td>
                                        <input name="archivo" type="file" size="35" >
                                    </td>
                                </tr>
                                <tr>
                                    <td>Nombre Link</td>
                                    <td><input name="link" id="link"  type="text" size="45"></td>
                                </tr>
                                <tr>
                                    <td>Sectores</td>
                                    <td>
                                        <select name="sectores[]" multiple="multiple">
                                            <?php
                                                    $conn = conexcion(true);
                                                    $result = ejecutarSQLPDO("SELECT id, descripcion FROM sector s where activo order by descripcion", $conn);
                                                    while ($row = mysqli_fetch_array($result))
                                                    {
                                                        print "<option value='$row[id]'>".ucwords(strtolower($row['descripcion']))."</option>";
                                                    }


                                            ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" align="rigth">
                                        <input type="submit" id="upload" value="Subir Archivo">
                                    </td>
                                </tr>
                         </table>
                         
            </fieldset>
            <input name="action" type="hidden" value="upload"/>
         </form>
   </div>
   <br>
   <br>
    <div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form action="/vista/iso/upload.php" method="post" enctype="multipart/form-data">
               <fieldset class="ui-widget ui-widget-content ui-corner-all">
                         <legend class="ui-widget ui-widget-header ui-corner-all">Recurso Existentes</legend>
                         <div id="mensaje"></div>
                         <table class="table table-zebra" border="0" align="center" width="75%" name="tabla">
                                <thead>
                                       <tr>
                                           <th>Nombre Recurso</th>
                                           <th>Archivo</th>
                                           <th>Sectores Afectados</th>
                                           <th>Fecha Alta</th>
                                           <th></th>
                                       </tr>
                                </thead>
                                <tbody>
                        <?php
                                $conn = conexcion(true);
                                $result = ejecutarSQLPDO("SELECT n.id, texto, file, date_format(fechaAlta, '%d/%m/%Y - %H:%i:%s') as alta, descripcion
                                                            FROM normativa n
                                                            LEFT JOIN normativaXSector nxs ON nxs.id_normativa = n.id
                                                            LEFT JOIN sector s ON s.id = nxs.id_sector
                                                            where n.activo
                                                            order by texto, n.id", $conn);

                                $row = mysqli_fetch_array($result);
                                while ($row)
                                {
                                    $id = $row['id'];
                                    $fechaAlta = $row['alta'];
                                    $sectores = "";
                                    print "<tr>
                                                <td>$row[texto]</td>
                                                <td>$row[file]</td>";
                                    while (($row) && ($row['id'] == $id))
                                    {
                                        if ($row['descripcion'])
                                        {
                                            $sectores.= ($sectores?' - ':'');
                                            $sectores.=ucwords(strtolower($row['descripcion']));
                                        }
                                        
                                        $row = mysqli_fetch_array($result);
                                    }
                                    print "<td>
                                                $sectores
                                            </td>
                                            <td>
                                                $fechaAlta
                                            </td>
                                            <td>
                                                <a href='/vista/iso/upnseg.php?nvid=$id'>Edit</a>
                                            </td>
                                            </tr>
                                            ";

                                }


                        ?>
                        </tbody>
                         </table>
                         
            </fieldset>
            <input name="action" type="hidden" value="upload"/>
         </form>
         <br>
         <br>
   </div>
   <?php
        }
   ?>
</body>
</html>

