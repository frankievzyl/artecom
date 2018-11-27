<?php
    $servername = "localhost";
    $username = "root";
    $password = ""; 
    $dbname = "artack_db";
    
    //Create Connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    //Check Connection
    if($conn->connect_error){
        echo "Connection failed: " . $conn->connect_error;
    }else{
        $_SESSION["connected"] = true;
    }
   
?>