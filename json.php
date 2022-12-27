<?php




$mysqli = mysqli_connect('167.114.128.232',  'leochabur', 'leo181979', 'gestionfaena');

$sql = "SELECT o.id as ori, d.id as de
FROM sp_st_mov_abst o
JOIN sp_st_mov_abst d ON o.id_mov_asoc = d.id
JOIN sp_proc_fan_day pfd ON pfd.id = d.id_proc_fan_day
WHERE o.id_fan_day = 565 and o.id_proc_fan_day = 2 AND d.id_proc_fan_day <> 2";

$result = mysqli_query($mysqli, $sql);


while ($row = mysqli_fetch_array($result))
{
        print "$row[ori],$row[de],";
}


        ?>
