<html>

<head>

        <link rel="stylesheet" href="../bootstrap.min.css"/>
    <style>
        #map {
            height: 100%;
        }
    </style>
</head>

<body>

<?php

        if (isset($_POST['submit']))
        {
        $dir_subida = '../';
        $fichero_subido = $dir_subida . basename($_FILES['fichero_usuario']['name']);

        if (move_uploaded_file($_FILES['fichero_usuario']['tmp_name'], $fichero_subido)) {
            echo "El fichero es válido y se subió con éxito.\n";
        } else {
            echo "¡Posible ataque de subida de ficheros!\n";
        }

        
        }
        $files = preg_grep('~\.(gpx)$~', scandir('../'));
        sort($files);
         //echo 'SDDDDDDDDDDDDDD?'.var_dump($files);

?>
    <nav class="navbar navbar-light bg-light">
        <form enctype="multipart/form-data" class="form-inline text-right" method="POST" action="#">
            <input type="file" name="fichero_usuario" class="form-control"/>
            <button class="btn btn-outline-success" type="submit" name="submit">Cargar</button>
        </form>
        <a class="navbar-brand" href="#">
            <img src="../toyota.png" width="100" alt="">
        </a>
    </nav>
    <div id="map">
    <ul class="list-group">
        <?php
            foreach ($files as $gpx)
            {
                print '<li class="list-group-item"><input data-file="'.$gpx.'" type="button" class="btn btn-sm btn-danger delete" value="X"/> - '.$gpx.'</li>';
            }

        ?>
    </ul>
    </div>

    <script src="../jquery-3.6.0.min.js"></script>
    <script src="../popper.min.js"></script>
    <script src="../bootstrap.min.js"></script>

</body>
<script>
        $(document).ready(function(){
                                        $('.delete').click(function(){  
                                                                        let btn = $(this);
                                                                        if (confirm('Seguro eliminar el archivo '+btn.data('file')+'?'))
                                                                        {
                                                                                $.post('/recorridos/upload/server.php',
                                                                                        {filename : btn.data('file')},
                                                                                        function(data){
                                                                                                        var data = $.parseJSON(data);
                                                                                                        if (data.ok)
                                                                                                        {
                                                                                                            location.reload();
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