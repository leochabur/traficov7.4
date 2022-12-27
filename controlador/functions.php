<?php

   ///////////////redefinir conexciones PDO

   function mysql_query($sql, $conn)
   {
        try
        {
            return mysqli_query($conn, $sql);
        }
        catch (Exception $e) { throw $e; }
   }

   function mysql_num_rows($result)
   {
        try
        {
            return mysqli_num_rows($result);
        }
        catch (Exception $e) { throw $e; }   
   }

   function mysql_fetch_array($result)
   {
        try
        {
            return $result->fetch_array();
        }
        catch (Exception $e) { throw $e; }   
   }

   function mysql_close($conn)
   {
        try
        {
            mysqli_close($conn);
        }
        catch (Exception $e) { throw $e; }   
   }

   function mysql_errno($conn)
   {
        try
        {
            return mysqli_errno($conn);
        }
        catch (Exception $e) { throw $e; }
   }

   function mysql_free_result($result)
   {
        try
        {
            mysqli_free_result($result);
        }
        catch (Exception $e) { throw $e; }
   }   

   function mysql_insert_id($conn)
   {
        try
        {
            return mysqli_insert_id($conn);
        }
        catch (Exception $e) { throw $e; }
   }

   function mysql_fetch_row($result)
   {
        try
        {
            return mysqli_fetch_row($result);
        }
        catch (Exception $e) { throw $e; }
   }

   function mysql_error($conn)
   {
        try
        {
            return mysqli_error($conn);
        }
        catch (Exception $e) { throw $e; }
   }


?>