<?php 
    session_start();
    $dbconPath = "incMySQLConnect.php";
    $root = "..";
?>

<script>
    function validateVoucherForm(){
        var validBarCode = validateBarCode(addvoucherform.pbarcodefld, "#pbarcodeerr");
        var validPrice;
        if(validBarCode){
            validPrice = validatePrice(addvoucherform.ppricefld, "#ppriceerr");
        }
        
        return validBarCode && validPrice;
    }
    
    function insertVoucher(){
        if(validateVoucherForm()){
            var statement = "INSERT INTO `product` (BarCode, Price, StockLevel, Name, Description) VALUES ('" + addvoucherform.pbarcodefld.value + "', " + addvoucherform.ppricefld.value + ", 999, '" + $("#pvouchernamelbl").text() + "', 'voucher')";
            $.post("customAction.php", {action: statement}, function(data){
                if(data.trim() == "FAILURE"){
                 alert("Failed to add the new voucher.\nPlease try again.");
            }else{
                alert("The new voucher has been added successfully.");
            }
            });
            contextSwitch("#editor");
        }
    }
        
    function updateVName(fld){
        $("#pvouchernamelbl").text("Voucher R" + fld.trim());
    }
</script>
<form name="addvoucherform">
    <legend>Add Voucher</legend>
    <div id="pbarcode">
        <label class="formlabels">Barcode&#58;</label>
        <input type="text" name="pbarcodefld" required size="13" maxlength="13" pattern="^[0-9]{13}$">
        <span id="pbarcodeerr" class="formerrors"></span>
    </div>
    <div id="pprice">
        <label class="formlabels">Price&#58;</label><span class="currency">R </span>
        <input type="text" name="ppricefld" required size="9" max="9" pattern="^\d{1,6}\.\d{2}$" onchange="fixDecimals(this); updateVName(this.value);">
        <span id="ppriceerr" class="formerrors"></span>
    </div>
    <span id="pvouchernamelbl"></span>
    <button type="button" name="addvoucherformsubmit" onclick="insertVoucher()">Submit</button>
    <button type="button" name="addvoucherformcancel" onclick="contextSwitch('#editor')">Cancel</button>
</form>
