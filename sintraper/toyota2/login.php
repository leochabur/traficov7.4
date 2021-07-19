<?php session_start();




      if ((md5($_POST['pwd']) == md5('ty2015mb')) && ($_POST['usr'] == 'usrtoyota')){
         $_SESSION['auth'] = 1;
         header('location:/toyota2/drawgraph.php');
      }
      else{
           header('location:/toyota2/index.php');
      }

?>

