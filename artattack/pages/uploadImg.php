<?php

if($_FILES["file"]["name"] != ''){
     
    $test = explode('.', $_FILES["file"]["name"]);
    $ext = end($test);
    $name = rand(100, 999) . '.' . $ext;
    $location = '../images/' . $_FILES["file"]["name"];  
    move_uploaded_file($_FILES["file"]["tmp_name"], $location);
    echo $location;
}
?>