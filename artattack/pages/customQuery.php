<?php
    session_start();
    require "incMySQLConnect.php";
    
    if($_SESSION["connected"]){

        $arrOut = array();
        $row = array();
        
        $stmnt = $_POST["query"];
        $resultKind = $_POST["resultkind"];
        
        $result = $conn->query($stmnt);

        if($result->num_rows > 0){
            
            switch ($resultKind){
                case "hasempty": 
                    echo json_encode("HAS"); 
                    break;
                case "singleval": 
                    $row = $result->fetch_array(); 
                    echo json_encode($row[0]); 
                    break;
                case "singlerow": 
                    $row = $result->fetch_assoc();
                    echo json_encode($row);
                    break;
                case "multiple": 
                    while($row = $result->fetch_assoc()){
                        array_push($arrOut, $row);
                    } 
                    echo json_encode($arrOut); 
                    break;
                default: echo json_encode("FAILURE");
            }
        }else{
            echo json_encode("EMPTY");
        }
        $conn->close();
    }else{
        echo json_encode("NOCONNECT");
    }
?>
    
    
    
    
