<html>

<head>

        <link rel="stylesheet" href="/recorridos/bootstrap.min.css"/>
    <style>
        #map {
            height: 100%;
        }

        .uploaded {
                    background-color: #65EC6F;
                    }
    </style>
</head>

<body>

<?php
        //error_reporting(E_ALL);
        
        include '../controlador/bdadmin.php';
        include_once '../controlador/ejecutar_sql.php';

        $conn = conexcion(true);

        $error = '';
        $success = '';

        if (isset($_POST['submit']))
        {

            $basename = basename($_FILES['fichero_usuario']['name']);

            if ($basename)
            {
                if (!$_POST['cronograma'])
                {
                    $error = 'Debe seleccionar un recorrido';
                }
                else
                {
                    try
                    {
                        $insert = "INSERT INTO cronogramas_gpx (id_cronograma, gpx_file) VALUES ($_POST[cronograma], '$basename') ON DUPLICATE KEY UPDATE gpx_file = '$basename'";
                        ejecutarSQLPDO($insert, $conn);

                        $dir_subida = './files/';
                        $fichero_subido = $dir_subida . $basename;

                        if (move_uploaded_file($_FILES['fichero_usuario']['tmp_name'], $fichero_subido)) 
                        {
                            $success = 'Archivo subido exitosamente!';
                        } 
                        else 
                        {
                            $error = "¡Posible ataque de subida de ficheros!\n";
                        }        
                    }
                    catch (Exception $e)
                    {
                                        $error = $e->getMessage();
                    }
                }
            }
            else
            {
                $error = 'Debe seleccionar un archivo!';
            }
        }

        try{
                $files = ejecutarSQLPDO('SELECT gpx.id, upper(nombre) as nombre, gpx_file as archivo
                                    FROM cronogramas_gpx gpx
                                    JOIN cronogramas c on c.id = id_cronograma
                                    order by nombre', $conn);   //preg_grep('~\.(gpx)$~', scandir('./files/'));
                //sort($files);
                
                $sql = "SELECT c.id, upper(c.nombre) as nombre, gpx.id as gpx
                        FROM cronogramas c
                        LEFT JOIN cronogramas_gpx gpx ON gpx.id_cronograma = c.id
                        JOIN servicios s on s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura
                        where (id_cliente = 10 or (c.nombre like '%rondin%' and id_cliente = 13)) and c.id_estructura = 1 and c.activo and s.i_v = 'i' and not vacio
                        group by c.id
                        order by c.nombre";


                $result = ejecutarSQLPDO($sql, $conn);

                //print $sql;
        }
        catch (Exception $e)
        {
            print "Error: " . $e->getMessage;
        }
        mysqli_close($conn);

?>
<div class="container">
    <nav class="navbar navbar-light bg-light">
        <form enctype="multipart/form-data" class="text-right mt-4" method="POST" action=".">
            <div class="form-row mb-3">
                <input type="file" name="fichero_usuario" class="form-control col-lg-12"/>
            </div>
            <div class="form-row mb-3">
            <select class="form-control col-lg-12" name="cronograma">
                <option value="0">Seleccione un recorrido</option>
                <?php
                        while ($row = mysqli_fetch_array($result))
                        {
                            $class = '';//($row['gpx']?'uploaded':'');
                            print "<option class='$class' value='$row[id]'>$row[nombre]</option>";
                        }
                ?>
            </select>
        </div>
        <div class="form-row">
            <button class="btn btn-outline-success btn-block" type="submit" name="submit">Cargar</button>
        </div>
        </div>
        </form>
    </nav>
        <?php
            if ($error)
            {

                print '
                        <div class="container">
                        <div class="alert alert-danger" role="alert">
                          '.$error.'
                        </div>
                        </div>';
            }
            if ($success) 
            {
                print '
                        <div class="container">
                        <div class="alert alert-success" role="alert">
                          '.$success.'
                        </div>
                        </div>';
            }
        ?>
    <div id="map" class="container">
        <ul class="list-group">
            <?php
                //foreach ($files as $gpx)
                while ($gpx = mysqli_fetch_array($files))
                {
                    print '<li class="list-group-item">
                            <input data-id="'.$gpx['id'].'" data-file="'.$gpx['archivo'].'" type="button" class="btn btn-sm btn-danger delete" value="X"/> - '.$gpx['nombre'] .'    /    '.$gpx['archivo'].'</li>';
                }

            ?>
        </ul>
    </div>
</div>

    <script src="/recorridos/jquery-3.6.0.min.js"></script>
    <script src="/recorridos/popper.min.js"></script>
    <script src="/recorridos/bootstrap.min.js"></script>

</body>
<script>
        $(document).ready(function(){
                                        $('.delete').click(function(){  
                                                                        let btn = $(this);
                                                                        if (confirm('Seguro eliminar el archivo '+btn.data('file')+'?'))
                                                                        {
                                                                                $.post('/gpx/server.php',
                                                                                        {
                                                                                            filename : btn.data('file'),
                                                                                            gpxid: btn.data('id')
                                                                                         },
                                                                                        function(data){
                                                                                                        console.log(data);
                                                                                                        var data = $.parseJSON(data);
                                                                                                        if (data.ok)
                                                                                                        {
                                                                                                            $(location).attr('href','/gpx');
                                                                                                        }
                                                                                                        else
                                                                                                        {
                                                                                                            alert('Error al intentar eliminar el archivo');
                                                                                                        }
                                                                                        })
                                                                        }
                                        })
        })
</script>
</html>