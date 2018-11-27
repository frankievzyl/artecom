<?php 
    session_start();
    $dbconPath = "incMySQLConnect.php";
    $root = "..";
    $instruction = $_POST["ins"]; //editspecial, removespecial

    $productSet = $_POST["setsize"];
    if($productSet == 1){ //if only 1 product chosen to insert, update or delete

        $productList = $_POST["productids"][0];

        if($instruction == "editspecial"){ //only gather data when creating/editing special
            require $dbconPath;

            if($_SESSION["connected"]){
                $stmnt = "SELECT Name, Price, SpecialPrice, SpecialEnd
                            FROM `product`
                            WHERE `product`.`BarCode` = '$productList'";

                $result = $conn->query($stmnt);

                if($result->num_rows > 0){
                    $specialData = $result->fetch_assoc();
                }
                $conn->close();
            }
        }
    }else{//more than one product's special is being created/updated to be the same
        //make string of all product ids for queries
        $productList = "";
        $products = $_POST["productids"];
        for($i = 0; $i < count($products); $i++){
            $productList .= "'$products[$i]', ";
        }
        $productList = rtrim($productList, ", ");
    }       
?>
<script>
    function validateSpecialForm(){
        var price = pspecialform.pspecialpricefld;
        var perc = pspecialform.ppercfld;
        var validAmount = false;

        if(price.value != ""){
            validAmount = validatePrice(price, "#pspecialpriceerr");
        }else if(perc != ""){
            validAmount = validatePercentage(perc, "#ppercerr");
        }else{
            $("#ppercerr").text("Please enter either an new amount or a percentage of the original price.")
        }
        var validDate = validateSpecialDate(pspecialform.pspecialdatefld, "#pspecialdateerr");

        return validAmount && validDate;
    }

    function updateSpecial(){
        if(validateSpecialForm()){

            var end = pspecialform.pspecialdatefld.value;
            var statement = "UPDATE `product` SET SpecialPrice = " + pspecialform.pspecialpricefld.value + ", SpecialEnd = '" + end + "' WHERE BarCode IN (<?php echo $productList; ?>)";

            $.post("customAction.php", { action: statement}, function(data){
                if(data.trim() == "FAILURE"){
                     alert("Failed to update the product(s) with a special.\nPlease try again.");
                }else{
                    alert("The product(s) have been updated with the special details.");
                }
            });
            contextSwitch("#editor");
        }
    }
</script>
<?php
    switch ($instruction){
        case "editspecial":
            if(isset($specialData)){
                $hasData = ($specialData["SpecialPrice"] != null);
                if($hasData){
                    $percent = $specialData["SpecialPrice"] / $specialData["Price"] * 100;
                }
            }else{
                $hasData = false;
            }
?>
<form name="pspecialform">          
    <?php 
        if($hasData){
            echo "<legend>Edit Special for <i>" . $specialData["Name"] . "</i></legend>"; 
        }else{
            if($productSet == 1){
                echo "<legend>Add Special for <i>" . $specialData["Name"] . "</i></legend>";
            }else{
                echo "<legend><i>Edit special for product set</i></legend>";
            }
        }
    ?>
    <div id="pspecialprice"><!--require either price or percentage-->
        <label class="formlabels">Special price&#58;</label><span class="currency">R </span>
        <input type="text" 
               name="pspecialpricefld" 
               required 
               size="9" 
               max="9" 
               pattern="^\d{1,6}\.\d{2}$" 
               onchange="fixDecimals(this)"
               <?php 
                    if($hasData){ echo " value=\"" . $specialData["SpecialPrice"] . "\" "; } 
               ?>
        >
        <span id="pspecialpriceerr" class="formerrors"></span>
    </div>
    <?php if($hasData){ //cannot use percentage of many products?>
        <div id="pperc">
            <label class="formlabels">Percentage discount&#58;</label>
            <input type="text" 
                   name="ppercfld" size="5" 
                   maxlength="5" 
                   pattern="^(0|[1-9][0-9]?|100)(\.\d{1,2})?$" 
                   value="<?php echo $percent; ?>"
                   onchange="implPercent(<?php echo $specialData["Price"]; ?>, this, pspecialform.pspecialpricefld)"><span>&#37;</span>
            <span id="ppercerr" class="formerrors"></span>
        </div>
    <?php } ?>
    <div id="pspecialdate">
        <label class="formlabels">Special end date&#58;</label>
        <input type="date" 
               required
               name="pspecialdatefld"
               min="<?php echo date("Y-m-d",strtotime("tomorrow")); ?>"
               <?php
                    if($hasData){
                        echo " value=\"" . date("Y-m-d", strtotime($specialData["SpecialEnd"])) . "\" ";
                    }
               ?>
        >
        <span id="pspecialdateerr" class="formerrors"></span>
    </div>
    <button type="button" name="pspecialformsubmit" onclick="updateSpecial()">Submit</button>
    <button type="button" name="pspecialformcancel" onclick="contextSwitch('#editor');">Cancel</button> 
</form>
        <?php
            break;
        case "removespecial":
            require $dbconPath;

            if($_SESSION["connected"]){

                $stmnt = "UPDATE `product` SET SpecialPrice = NULL, SpecialEnd = NULL WHERE BarCode IN ($productList)";
                if($conn->query($stmnt)){
                    echo "SUCCESS";
                }else{
                    echo "FAILURE";
                }
                $conn->close();      
            }else{
                echo "FAILURE";
            }
            break;
        default: ?> <span>Error: Reload</span><?php
    } 
?>