<?php

    $database= new mysqli("localhost","root","","mind_check");
    if ($database->connect_error){
        die("Connection failed:  ".$database->connect_error);
    }

?>
