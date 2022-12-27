<html>

<head>

    <link rel="stylesheet" href="leaflet.css"/>
        <link rel="stylesheet" href="bootstrap.min.css"/>
        <link rel="stylesheet" href="bootstrap-select.min.css"/>
    <style>
        #map {
            height: 100%;
        }
    </style>
</head>

<body>

<?php
            $gpxs = [];
            if (isset($_POST) && (isset($_POST['gpxs'])))
            {
                $gpxs = $_POST['gpxs'];
            }
           // print (var_dump($_POST['gpxs']));


         $files = preg_grep('~\.(gpx)$~', scandir('./'));

         //echo 'SDDDDDDDDDDDDDD?'.var_dump($files);

?>
    <nav class="navbar navbar-light bg-light">
        <form class="form-inline text-right" method="POST" action="#">
            <select name="gpxs[]" data-width="auto" multiple data-actions-box="true" class="form-control multiple-select" data-live-search="true" multiple multiple data-selected-text-format="count > 1">

                <?php
                    foreach ($files as $f)
                    {
                        $selected = '';
                        if (in_array($f, $gpxs))
                        {
                            $selected = "selected='selected'";
                        }
                        print "<option value='$f' $selected>". substr($f, 0, -4)."</option>";
                    }
                ?>

                </select>
            <button class="btn btn-outline-success" type="submit">Cargar</button>
        </form>
        <a class="navbar-brand" href="#">
            <img src="toyota.png" width="100" alt="">
        </a>
    </nav>
    <div id="map">
    </div>
    <script src="leaflet.js"></script>
    <script src='leaflet-omnivore.min.js'></script>
    <script src="gpx.min.js"></script>
    <script src="jquery-3.2.1.slim.min.js"></script>
    <script src="popper.min.js"></script>
    <script src="bootstrap.min.js"></script>
    <script src="bootstrap-select.min.js"></script>
    <script src="defaults-es_ES.min.js"></script>
    <script>
        $('.multiple-select').selectpicker();
        var map = L.map('map', {
            zoom: 12,
            center: [-34.13404590170442, -59.04310993169572]
        });
        L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; <a href="http://www.osm.org">OpenStreetMap</a>'
        }).addTo(map);

        <?php
            if (count($gpxs))
            {
                $color = ['red', 'green', 'yellow', 'black', 'orange'];
                $i = 0;
                foreach ($gpxs as $gpx)
                {
                    ?>
                    new L.GPX('<?php echo $gpx; ?>', {
                                async: true,
                                polyline_options: {
                                                        color: '<?php echo $color[$i]; ?>',
                                                        weight: 5
                                                    },
                                                    marker_options: {
                                                                    wptIconUrls: {
                                                                    '': 'point.png',
                                                                    },
                                                                    startIconUrl: 'point.png',
                                                                    endIconUrl: 'point.png',
                                                                    
                                                                }
                            }).addTo(map);
                    <?php
                    $i++;
                }
            }
        ?>

    </script>
</body>

</html>