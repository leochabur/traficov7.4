<?php session_start();




      if ((md5($_POST['pwd']) == md5('ty20mb15')) && ($_POST['usr'] == 'usertyt')){
         $_SESSION['auth'] = 1;
         header('location:/toyota/drawgraph.php');
      }
      else{
           header('location:/toyota/index.php');
      }

?>

