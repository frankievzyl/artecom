<?php 
    session_start();
    $dbconPath = "incMySQLConnect.php";
    $root = "..";

    $productSet = $_POST["setsize"];
    if($productSet == 1){ //if only 1 product chosen to add stock for
        $productList = $_POST["productids"][0];
    }else{//more than one product's stock is going to be added to by the same amount
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
    function updateStock(){
        if(validateStock(addpstockform.pstockfld, "#addpstockerr")){

            var statement = "UPDATE `product` SET StockLevel = StockLevel + " + addpstockform.pstockfld.value + " WHERE BarCode IN (<?php echo $productList; ?>)";
            $.post("customAction.php", {action: statement}, function(data){
                if(data.trim() == "FAILURE"){
                 alert("Failed to add to inventory for the selected product(s).Please try again.");
                }else{
                    alert("The inventory of the product(s) has been added to.");
                } 
            });
            contextSwitch("#editor");
            
        }
    }
</script>
<form name="addpstockform">
    <div id="pstock">
        <label class="formlabels">Number of items to add&#58;</label>
        <input type="text" name="pstockfld" size="3" maxlength="3" pattern="^\d{1,3}$">
        <span id="addpstockerr" class="formerrors"></span>
    </div>
    <button type="button" name="addpstockformsubmit" onclick="updateStock()">Submit</button>
    <button type="button" name="addpstockformcancel" onclick="contextSwitch('#editor')">Cancel</button>
</form>
