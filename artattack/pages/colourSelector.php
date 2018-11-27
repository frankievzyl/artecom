<?php
    $dbconPath = "incMySQLConnect.php";

    if(isset($_SESSION["colourRange"])){
        $temp = $_SESSION["colourRange"];
        if($temp != null){
            $rangeID = $temp;
        }
    }
    if(isset($_SESSION["colourProdName"])){
        $productName = $_SESSION["colourProdName"];
    }

    if(isset($_POST["productname"])){ //used on view product page
        $productName = $_POST["productname"];
    }

    if(isset($_POST["rangeid"])){ //used on manage product page
        $temp = $_POST["rangeid"];
        if($temp != "null"){
            $rangeID = $temp;
        }
    }
    
    require $dbconPath;
    
    if($_SESSION["connected"]){
        
        if(isset($rangeID)){
            $stmnt = "SELECT * FROM `colour` WHERE `RangeID` = $rangeID";
        }else if(isset($productName)){
            $stmnt = "SELECT `colour`.* FROM `colour` JOIN `product` ON `colour`.`ColourID` = `product`.`ColourID` WHERE `product`.`Name` = '$productName'";    
        }else{
            $stmnt = "SELECT * FROM `colour`";
        }
        
        if(isset($stmnt)){
            $result = $conn->query($stmnt);

            $colourData = array();
            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    array_push($colourData, $row);
                }
            }
        }
        $conn->close();
    }else{
        echo "FAILURE";
    }
    
    //make list
if(isset($stmnt)){
?>
<div class="dropdown">
    <button type="button" 
            class="btn btn-primary dropdown-toggle"
            data-toggle="dropdown">Choose Colour
        <i class="fas fa-palette"></i>
    </button>
    <ul id="colourselect"
        class="dropdown-menu">
<?php
    foreach($colourData as $colour){ 
?>
    <li id="<?php echo $colour["ColourID"]; ?>"
         onclick="updateColourField(
                  <?php
                        echo $colour["ColourID"] . ",";
                        echo "'" . $colour["Name"] . "',";
                        echo "'" . $colour["ColourCode"] . "',";
                        echo "'" . $colour["ColourHex"] . "'";
                  ?>)"
         class="cfullprev">
        <div class="chexprev"
             style="background-color:#<?php echo $colour["ColourHex"]; ?>;">
        </div>
        <span><?php echo $colour["Name"]; ?></span>
    </li>
<?php
    } echo "</ul></div>";
}?>