<?php session_start();

if(isset($_POST["stafflogin"])){
    $_SESSION["staffid"] = $_POST["stafflogin"];
    $_SESSION["searcherid"] = "staff";
    //header("Location: http://localhost/artattack/pages/management.php");
    
}else if(isset($_POST["stafflogout"])){
    $_SESSION["staffid"] = null;
    $_SESSION["searcherid"] = 0;
}else if(isset($_POST["customerlogin"])){
    $_SESSION["customerid"] = $_POST["customerlogin"];
    $_SESSION["searcherid"] = $_POST["customerlogin"];
}else if(isset($_POST["customerlogout"])){
    $_SESSION["customerid"] = null;
    $_SESSION["searcherid"] = 0;
}else if(isset($_POST["inorup"])){
    $_SESSION["accAction"] = $_POST["inorup"];
}

?>