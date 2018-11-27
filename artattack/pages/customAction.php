<?php
    session_start();
    require "incMySQLConnect.php";
    
    if($_SESSION["connected"]){

        $stmnt = $_POST["action"];

        if($conn->query($stmnt)){
            echo "SUCCESS";
        }else{
            echo "FAILURE: " . $stmnt;
        }
        $conn->close();
    }else{
        echo $stmnt;
    }
?>