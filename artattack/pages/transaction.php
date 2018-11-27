<?php 
    session_start(); 
    $dbconPath = "incMySQLConnect.php";
    
    if(isset($_SESSION["customerid"])){//creates synonym for customer id session object
        $thisCustomer = $_SESSION["customerid"];
        $hasCustomer = true;
    }else{
        $hasCustomer = false;
        $thisCustomer = 0;
    }

    //get selected barcodes from post and place in session
    if(isset($_POST["checkoutBCS"])){
        $_SESSION["currentTransBCS"] = $_POST["checkoutBCS"];
    }

    if(isset($_SESSION["currentTransBCS"])){
        $bcList = $_SESSION["currentTransBCS"];

        if(count($bcList) > 1){ //create string joining barcodes
            $bcString = "customerproduct.BarCode IN (";
            foreach($bcList as $bc){
                $bcString .= "'" . $bc . "',";
            }
            $bcString = rtrim($bcString,",");
            $bcString .= ")";
        }else{
            $bcString = "customerproduct.BarCode = '$bcList[0]'";
        }
        
        require $dbconPath;

        if($_SESSION["connected"]){
            //fetch information about cart items
            $stmnt = "SELECT
                        IF(product.SpecialEnd >= NOW(), product.SpecialPrice * customerproduct.Quantity, product.Price * customerproduct.Quantity) AS ActualCost,
                        IF(product.SpecialEnd >= NOW(), product.SpecialPrice, product.Price) AS UsedPrice,
                        product.StockLevel,
                        product.Name,
                        (NOT product.Description <=> 'voucher') AS NotVoucher,
                        customerproduct.Quantity,
                        customerproduct.BarCode
                    FROM
                        `product`
                    JOIN `customerproduct` ON product.BarCode = customerproduct.BarCode
                    WHERE
                        customerproduct.CustomerID = $thisCustomer AND $bcString AND customerproduct.TransactionID IS NULL";

            $result = $conn->query($stmnt);

            if($result->num_rows > 0){
                
                $cartVouchers = array();//holds only to be bought vouchers
                $cartItems = array();//holds all products to be bought
                
                while($row = $result->fetch_assoc()){

                    if($row["NotVoucher"]){ //add all products to items array
                        array_push($cartItems,$row);
                    }else{ //also add to voucher array if it's a voucher
                        array_push($cartItems,$row);
                        array_push($cartVouchers,$row);
                    }
                }
            }

            //fetch information about client address

            $stmnt = "SELECT Address, City, PostCode, Country, RegionState FROM `customer` WHERE CustomerID = $thisCustomer";

            $result = $conn->query($stmnt);

            if($result->num_rows > 0){
                $customerAddr = $result->fetch_assoc();
            }
            
            //fetch all the vouchers a customer can still use to help pay during checkout
            $stmnt = "SELECT VoucherCode, Amount FROM `voucher` WHERE SentTo = (SELECT Email FROM `customer` WHERE CustomerID = $thisCustomer) AND Amount > 0";
            
            $result = $conn->query($stmnt);
            
            if($result->num_rows > 0){
                $ownVouchers = array();
                while($row = $result->fetch_assoc()){
                    array_push($ownVouchers, $row);
                }
            }
            $conn->close();
        }
    }
    $hasAddr = isset($customerAddr);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Art Attack: Checkout</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
        <link rel="stylesheet" href="../css/main.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="../js/repository.js"></script>
        <script src="../js/validation.js"></script>
        <script>
                        
            function disableStep(stepId){
                $(stepId).prop("disabled",true).css("opacity","0.6");
            }
            
            function enableStep(stepId){
                $(stepId).prop("disabled",false).css("opacity","1");
            }
            
            //end of step1: validates delivery details
            function submitDelivery(){
                
                var proceed = false;
                
                if($("[name=deliverymethodfld]").val() == "Pick up"){
                    proceed = true;
                }else{
                    var address = validateTransField($("[name=addressfld]").get(0),$("#addresserr").get(0),"address");
                    var city = validateTransField($("[name=cityfld]").get(0), $("#cityerr").get(0),"city");
                    var postcode = validateTransField($("[name=postcodefld]").get(0),$("#postcodeerr").get(0),"postcode");
                    var country = validateTransField($("[name=countryfld]").get(0),$("#countryerr").get(0),"country");
                    var regstate = validateTransField($("[name=regionstatefld]").get(0),$("#regionstateerr").get(0),"regstate");
                    proceed = address && city && postcode && country && regstate;
                }

                if(proceed){
                    
                    //clear final delivery display
                    $("#delivery").children("span").remove();
                                    
                    //create span elements holding entered details
                    var addrSpan = $("<span></span>").html("<i>Address: </i>" + $("[name=addressfld]").val());
                    var citySpan = $("<span></span>").html("<i>City: </i>" + $("[name=cityfld]").val());
                    var pcSpan = $("<span></span>").html("<i>Post Code: </i>" + $("[name=postcodefld]").val());
                    var cntySpan = $("<span></span>").html("<i>Country: </i>" + $("[name=countryfld]").val());
                    var rsSpan = $("<span></span>").html("<i>Region or State: </i>" + $("[name=regionstatefld]").val());
                    
                    //add elements to final display 
                    $("#delivery").append(addrSpan);
                    $("#delivery").append(citySpan);
                    $("#delivery").append(pcSpan);
                    $("#delivery").append(rsSpan);
                    $("#delivery").append(cntySpan);
                                        
                    disableStep("#step1");
                    //determine whether a voucher was bought
                    if(<?php 
                        if(isset($cartVouchers)){ 
                            if(count($cartVouchers) > 0){
                                echo "true";
                            }else{
                                echo "false";
                            }
                        }else{
                            echo "false";
                        }?>){
                        /*if so, set field to give recipient emails for vouchers to true,
                        setting it as the next step to be completed and validated*/
                        enableStep("#step1A");
                    }else{
                        //otherwise step2 is the next set of values to enter and validate
                        enableStep("#step2");
                    }   
                }
            }
            
            //end of step1A
            function submitSendVoucher(){
                //loop through all submitted recipient emails
                
                var recipients = $("[name=remailfld]").get();
                var allcorrect = true;
                
                //validate each
                for(var email in recipients){
                    allcorrect = validateEmail(recipients[email],$(recipients[email]).next(".remailerr").get(0));
                    if(! allcorrect){ //an email was invalid
                       break; 
                    }
                }
                if(allcorrect){ //all emails correct, go to next step
                    
                    disableStep("#step1A"); 
                    enableStep("#step2");
                }
            }
            
            //return to delivery information field
            function cancelSendVoucher(){
                disableStep("#step1A");
                enableStep("#step1");
            }
            
            //part of step2, unused if customer owns no vouchers
            function addVoucher(){
                disableStep("#step2");
                enableStep("#step2A");
            }
            
            //part of step2, unused if customer owns no vouchers
            function removeVoucher(){
                
                //update total value
                var optfld = $("#usedvouchersfld").children(":selected").val(); //selected voucher
                var amnt = $("#totalfld").text(); //now total

                var moreAmnt = Math.fround(Number(amnt) + Number(optfld));
                $("#totalfld").text("" + moreAmnt);
                
                //remove the selected voucher from the list
                $("#usedvouchersfld").children(":selected").remove();
                
            }
            
            //part of step 2, validates given voucher and adds to list
            function submitGiveVoucher(){
                var validCode = validateVoucherCode($("[name=vouchercodefld]").get(0), $("#vouchercodeerr"), $("#totalfld"));
                
                var vCode = $("[name=vouchercodefld]").val();
                
                if(validCode){
                    //check for existence, whether emails match and and whether any amount left
                    var stAmount = "SELECT Amount FROM `voucher` WHERE SentTo = (SELECT Email FROM `customer` WHERE CustomerID = <?php echo $thisCustomer; ?> AND VoucherCode = '" + vCode + "' AND Amount != 0 AND Claimant IS NULL)";

                    $.post("customQuery.php",{query: stAmount, resultkind: "singleval"}, function(data){ 
                        var vAmount = JSON.parse(data);
                        if(vAmount == "EMPTY"){
                            $("#vouchercodeerr").html("<span>The given voucher does not exist, or does not belong to you.</span>");
                        }else if(vAmount == 0){
                            $("#vouchercodeerr").html("<span>This voucher has already been used. (Single use only).</span>");
                        }else{
                            $("#vouchercodeerr").text("");
                            var newOption = $("<option></option>");
                            newOption.text(vCode);
                            newOption.val(vAmount);
                            $("#usedvouchersfld").append(newOption);
                            
                            //update total value
                            var amnt = $("#totalfld").text();
                            var lessAmnt = Math.fround(Number(amnt) - vAmount);
                            $("#totalfld").text("" + lessAmnt);
                            cancelGiveVoucher();
                        }
                    });
                }
            }
            
            function cancelGiveVoucher(){
                disableStep("#step2A");
                enableStep("#step2");
                $("[name=vouchercodefld]").val("");
            }
            
            //end of step2: sets up to either fill in card or account info 
            function submitPayment(){
                //disable step2
                $("#step2").prop("disabled",true);
                var paymenttype = $("[name=paymenttypefld]").val();
                if(paymenttype == "Credit Card"){
                    $("#step3A").prop("disabled",false).css("opacity","1");
                }else{
                    $("#step3B").prop("disabled",false).css("opacity","1");
                }
                $("#finaltotal").text($("#totalfld").text());
            }
            
            function cancelPayment(){
                disableStep("#step2");
                enableStep("#step1");
            }
            
            //step3A: validates card details
            function submitCard(){
                var card = validateTransField($("[name=cardnumberfld]").get(0),$("#cardnumbererr").get(0),"card");
                var name = validateTransField($("[name=cardholdernamefld]").get(0),$("#cardholdernameerr").get(0),"cardholder");
                var xm = validateTransField($("[name=expirymfld]").get(0),$("#expiryerr").get(0),"exm");
                var xy = validateTransField($("[name=expiryyfld]").get(0),$("#expiryerr").get(0),"exy");
                var cvv = validateTransField($("[name=cvvfld]").get(0),$("#cvverr").get(0), "cvv");
                
                if(card && name && xm && xy && cvv){
                    $("#step3A").prop("disabled",true);
                    $("#step4").prop("disabled",false).css("opacity","1");
                    
                    //clear final payment display
                    $("#payment").children("span").remove();
                    //create new elements
                    var cardSpan = $("<span></span>").html("<i>Card Number: </i>" + $("[name=cardnumberfld]").val());
                    var holderSpan = $("<span></span>").html("<i>Cardholder Name: </i>" + $("[name=cardholdernamefld]").val());
                    var expSpan = $("<span></span>").html("<i>Expiry: </i>" + $("[name=expirymfld]").val() + "/" + $("[name=expiryyfld]").val());
                    var cvvSpan = $("<span></span>").html("<i>CVV: </i>" + $("[name=cvvfld]").val());
                    //add new elements
                    $("#payment").append(cardSpan);
                    $("#payment").append(holderSpan);
                    $("#payment").append(expSpan);
                    $("#payment").append(cvvSpan);
                }
            }
            
            function cancelCardInfo(){
                enableStep("#step2");
                disableStep("#step3A")
            }
            
            //step3B: validates bank account details
            function submitAccount(){
                var name = validateTransField($("[name=accholdernamefld]").get(0),$("#accholdernameerr").get(0),"accholder");
                var routenum = validateTransField($("[name=routingnumberfld]").get(0),$("#routingnumbererr").get(0),"routenum");
                var accnum = validateTransField($("[name=accnumberfld]").get(0),$("#accnumbererr").get(0), "accnum");
                
                if(name && routenum && accnum){
                    $("#step3B").prop("disabled",true);
                    $("#step4").prop("disabled",false).css("opacity","1");
                    
                    //clear final payment display
                    $("#payment").children("span").remove();
                    //create new elements
                    var nameSpan = $("<span></span>").html("<i>Account Holder: </i>" + $("[name=accholdernamefld]").val());
                    var routeSpan = $("<span></span>").html("<i>Routing Number: </i>" + $("[name=routingnumberfld]").val());
                    var accSpan = $("<span></span>").html("<i>Account Number: </i>" + $("[name=accnumberfld]").val());
                    //add new elements
                    $("#payment").append(nameSpan);
                    $("#payment").append(routeSpan);
                    $("#payment").append(accSpan);
                    
                }
            }
            
            function cancelAccInfo(){
                enableStep("#step2");
                disableStep("#step3B");
            }
            
            //step4: final acknowledgement of transaction
            function sendCommit(){
                             
                var goOn = true;
                
                var addr, city, pc, cnty, rs;
                //get values
                    addr = $("[name=addressfld]").val();
                    city = $("[name=cityfld]").val();
                    pc = $("[name=postcodefld]").val();
                    cnty = $("[name=countryfld]").val();
                    rs = $("[name=regionstatefld]").val();
                
                //if set as new default address is checked and the delivery method is not pickup
                if($("#addresschk").prop("checked") && $("[name=deliverymethodfld]").val() != "Pick up"){
                    
                    var stAddr = "UPDATE `customer` SET Address = '" + addr + "', City = '" + city + "', PostCode = '" + pc + "', Country = '" + cnty + "', RegionState = '" + rs + "' WHERE CustomerID = " + <?php echo $thisCustomer; ?>;
                    //update customer address details
                    $.post("customAction.php",{action: stAddr},function(data){
                        if(data == "FAILURE"){
                            $("#commiterr").html(function(i,old){
                               return old + "<br>Failed to update address details."; 
                            });
                            alert("Failed to update address details.");
                            goOn = false;
                        }
                    });
                }
                
                //if address not updated OR done so successfully
                if(goOn){ 
                    //get vouchers that were bought
                    var boughtVouchers = [];
                    boughtVouchers = <?php 
                        if(isset($cartVouchers)){ 
                            $separateVs = array();
                            foreach($cartVouchers as $vc){ 
                                for($c = 1; $c <= $vc["Quantity"]; $c++){
                                    array_push($separateVs, $vc); //add unique instance of voucher to array
                                }
                            }
                            echo json_encode($separateVs);
                        }else{
                            echo "[]";
                        } 
                        ?>;

                    var vSize = boughtVouchers.length;
                    if(vSize > 0){ //if vouchers were bought

                        //get recipient emails
                        var recipients = $("[name=remailfld]").toArray();

                        //start insert statement
                        var stVouch = "INSERT INTO `voucher` (VoucherCode, Amount, SentTo) VALUES ";

                        //loop through both arrays
                        for(var c = 0; c < vSize; c++){
                            stVouch += "('" + generateVoucherCode() + "', " + boughtVouchers[c].UsedPrice + ", '" + recipients[c].value + "')";
                            if(c < vSize - 1){ //not on last loop
                                stVouch += ", "; //add comma to separate different voucher entries
                            }
                        }

                        $.post("customAction.php",{action: stVouch},function(data){
                            if(data == "FAILURE"){
                                $("#commiterr").html(function(i ,old){
                                    return old + "Failed to add bought vouchers to database."; 
                                });
                                alert("Failed to add bought vouchers to database.\nPlease try again.");
                                goOn = false;
                            }
                        });
                    }
                }
                
                //In ideal solution, send emails with voucher code to recipients
                
                if(goOn){ //reduce used vouchers and sets as being claimed
                    var stUpdateVoucher = "";
                    $("#usedvouchersfld").children().each(function(){
                        var thistext = $(this).text();
                        stUpdateVoucher = "UPDATE `voucher` SET Amount = 0, Claimant = <?php echo $thisCustomer; ?> WHERE VoucherCode = '" + thistext + "'"; 
                       $.post("customAction.php",{action: stUpdateVoucher}, function(data){
                            if(data.trim() == "FAILURE"){
                                $("#commiterr").html(function(i,old){
                                    return old + "<br>Failed to claim use of voucher " + thistext + ".";
                                });
                                goOn = false;
                            } 
                       }); 
                    });
                }
                
                if(goOn){ // if vouchers were bought and successfull OR no vouchers
                    //get transaction details to store in db

                    var deliv = $("[name=deliverymethodfld]").val();
                    var pay = $("[name=paymenttypefld]").val();
                    var fullAddress = addr + ", " + city + ", " + pc + ", " + rs + ", " + cnty;

                    var stTrans = "INSERT INTO `mytransaction` (TotalAmount, ShipAddress, Payment, Delivery) VALUES ( " + $("#finaltotal").text().trim() + ", '" + fullAddress + "', '" + pay + "','" + deliv + "')";

                    //insert transaction details

                    $.post("customAction.php", {action: stTrans}, function (data){
                        if(data.trim() == "FAILURE"){
                            $("#commiterr").html(function(i,old){
                                return old + "<br>Failed to execute transaction.";
                            });
                            goOn = false;
                            alert("Failed to execute transaction.\nPlease try again.");
                        }
                    });
                }                
                
                if(goOn){
                    //created a transaction , now update the customerproduct table, linking product, quantity, customer and transaction
                    
                    //get id of newly created transaction
                    var stGetTrans = "SELECT TransactionID FROM `mytransaction` WHERE TotalAmount = " + $("#finaltotal").text().trim() + " AND ShipAddress = '" + fullAddress + "' AND TrDateTime > DATE_SUB(NOW(), INTERVAL 5 MINUTE)";
                    
                    $.post("customQuery.php",{query: stGetTrans, resultkind: "singleval"},function(data){
                        var transID = JSON.parse(data);
                        if(transID != "EMPTY"){
                            
                            var stCart = "UPDATE `customerproduct` SET TransactionID = " + transID + " WHERE CustomerID = <?php echo $thisCustomer . " AND " . $bcString ; ?> AND TransactionID IS NULL";
                            
                            $.post("customAction.php",{action: stCart}, function(data){
                                if(data.trim() == "SUCCESS"){
                                    
                                    var cnt;
                                    var cartItemsArr = [];
                                    cartItemsArr = 
                                        <?php if(isset($cartItems)){
                                                echo json_encode($cartItems);
                                            }else{
                                                echo "[]"; 
                                        }?> ;
                                    var stStockUpdate = "";
                                    var stPriceUpdate = "";
                                    for(cnt = 0; cnt < cartItemsArr.length; cnt++){
                                        //update product count
                                         stStockUpdate = "UPDATE `product` SET StockLevel = StockLevel - " + cartItemsArr[cnt].Quantity + " WHERE BarCode = '" + cartItemsArr[cnt].BarCode + "'";
                                        $.post("customAction.php",{action: stStockUpdate}, function(data){
                                            if(data.trim() == "FAILURE"){
                                                $("#commiterr").html(function(i, old){
                                                    return old + "<br>" + "Failed to update stock for " + cartItemsArr[cnt].Name;
                                                    goOn = false;
                                                });
                                            }else{
                                                alert("Failed to update stock level.")
                                            }
                                        });
                                        //set total product cost for each product on transaction
                                        stPriceUpdate = "UPDATE `customerproduct` SET ProductTotal = " + cartItemsArr[cnt].ActualCost + " WHERE TransactionID = " + transID + " AND BarCode = '" + cartItemsArr[cnt].BarCode + "'";
                                        $.post("customAction.php", {action: stPriceUpdate}, function(data){
                                            if(data.trim() == "FAILURE"){
                                                $("#commiterr").html(function(i, old){
                                                    return old + "<br>" + "Failed to update price total for " + cartItemsArr[cnt].Name;
                                                    goOn = false;
                                                });
                                            }else{
                                                alert("Failed to update product total.")
                                            } 
                                        });
                                    }
                                    alert("Transaction was successful!");
                                    window.location = "http://localhost/artattack/pages/store.php";
                                }
                            });
                        }else{
                            $("#commiterr").html(function(i, old){
                                return old + "<br>" + "Failed to find transaction ID";
                                goOn = false;
                            });
                        }
                    });
                }
                if(goOn){
                   
                }
            }
            
            function cancelCommit(){
                window.history.back();
            }
            
        </script>
    </head>
    <body>
        <!--ADDRESS and DELIVERY INFO-->
        <div class="container-fluid">
            <fieldset id="step1" class="row">
                <legend>Delivery Information</legend>
                <div id="address">
                    <label class="formlabels">Address&#58;</label>
                    <input type="text" name="addressfld" required size="50" maxlength="50" 
                           pattern="^\d+(\s[A-Z]([a-z]|[^\d]+))(,\s[A-Z]([a-z]|[^\d])+)+$"
                           onchange="validateTransField(this,$('#addresserr').get(0),'address')"
                           <?php if($hasAddr){echo " value='" . $customerAddr["Address"] . "'";} ?>>
                    <span id="addresserr" class="formerrors"></span>
                </div>
                <div id="city">
                    <label class="formlabels">City&#58;</label>
                    <input type="text" name="cityfld" required size="20" maxlength="20" 
                           pattern="^[A-Z][A-z]+(\s?[A-z]+)+$"
                           onchange="validateTransField(this,$('#cityerr').get(0),'city')"
                           <?php if($hasAddr){echo " value='" . $customerAddr["City"] . "'";} ?>>
                    <span id="cityerr" class="formerrors"></span>
                </div>
                <div id="postalcode">
                    <label class="formlabels">Postal code&#58;</label>
                    <input type="text" name="postcodefld" required size="5" 
                           pattern="^[0-9]{4}$"
                           onchange="validateTransField(this,$('#postcodeerr').get(0),'postcode')"
                           <?php if($hasAddr){echo " value='" . $customerAddr["PostCode"] . "'";} ?>>
                    <span id="postcodeerr" class="formerrors"></span>
                </div>
                <div id="regionstate">
                    <label class="formlabels">Region or State&#58;</label>
                    <input type="text" name="regionstatefld" required size="40" maxlength="40" 
                           pattern="^[A-Z][A-z]+(\s?[A-z]+)+$"
                           onchange="validateTransField(this,$('#regionstateerr').get(0),'regstate')"
                           <?php if($hasAddr){echo " value='" . $customerAddr["RegionState"] . "'";} ?>>
                    <span id="regionstateerr" class="formerrors"></span>
                </div>
                <div id="country">
                    <label class="formlabels">Country&#58;</label>
                    <input type="text" name="countryfld" required size="30" maxlength="30" 
                           pattern="^[A-Z][A-z]+(\s?[A-z]+)+$"
                           onchange="validateTransField(this,$('#countryerr).get(0),'country')"
                           <?php if($hasAddr){echo " value='" . $customerAddr["Country"] . "'";} ?>>
                    <span id="countryerr" class="formerrors"></span>
                </div>
                <div id="deliverymethod">
                    <label class="formlabels">Delivery method&#58;</label>
                    <select name="deliverymethodfld">
                        <option value="Post Office CtC">Post Office Counter&#45;to&#45;Counter</option>
                        <option value="Cape Town Courier">Cape Town Courier</option>
                        <option selected value="Courier DtD">Courier Door&#45;to&#45;Door</option>
                        <option value="Pick up">Pick up at store</option>
                    </select>
                    <span id="deliverymethoderr" class="formerrors"></span>
                </div>
                <div id="makedefaultdelivery">
                    <input type="checkbox" name="makedefaultaddresschk" value="setdefault" id="addresschk"><label for="addresschk">set as new default address</label>
                </div>
                <input type="button" name="addressformsubmit" onclick="submitDelivery()" value="Submit Delivery Details">
            <!--RECIPIENT OF VOUCHER EMAIL INPUT-->
            </fieldset>
            <?php if(isset($cartVouchers)){ ?>
            <fieldset id="step1A" style="opacity:0.6" class="row">
                <legend>Voucher Recipients</legend>
                <div id="remail">
                    <?php
                        foreach($cartVouchers as $vc){ 
                            for($c = 1; $c <= $vc["Quantity"]; $c++){ ?>
                                <span class="voucherinquestion">
                                    <?php echo "For " . $vc["Name"] . " ($c) please give:";?>
                                </span>
                                <label class="formlabels">Recipient email&#58;</label>
                                <input type="email" name="remailfld" required size="50" maxlength="50" pattern="^.([A-z]|[0-9]|\.|_|-)+@([A-z]|[0-9]|\.|_|-)+(\.[a-z]{2,3}){1,2}$"
                                       onchange="validateEmail(this, $(this).next('span'))">
                            <span class="formerrors remailerr"></span>
                    <?php 
                            }//close for loop
                        } //close for each loop ?>
                </div>
                <input type="button" name="sendvoucherformsubmit" onclick="submitSendVoucher()" value="Submit Recipient Email">
                <input type="button" name="sendvoucherformcancel" onclick="cancelSendVoucher()" value="Cancel">
            </fieldset>
            <?php } //exclude step1A ?>
            <!--PAYMENT TYPE AND USE OF VOUCHERS-->
            <fieldset id="step2" disabled style="opacity:0.6" class="row">
                <legend>Payment Methods</legend>
                <label class="formlabels">Payment type&#58;</label>
                <select name="paymenttypefld">
                    <option selected value="Credit Card">Credit Card</option>
                    <option value="EFT">EFT</option>
                </select>
                <span id="transactiontotalfld">Total Cart Cost:<br>R<span id="totalfld"> 
                    <?php
                        $total = 0.00;
                        if(isset($cartItems)){
                            foreach($cartItems as $item){
                                $total += $item["ActualCost"];
                            }
                        }
                        echo "" . $total;
                    ?></span>
                    </span>
                <?php if(isset($ownVouchers)){ ?>
                <div id="usevoucher">
                    <input type="button" name="givevoucherbtn" value="Add Voucher" onclick="addVoucher()">
                    <input type="button" name="takebackvoucherbtn" value="Remove Voucher" onclick="removeVoucher()">
                    <select id="usedvouchersfld" size="0"></select>
                </div>
                <?php } //excludes option to load step2A ?>
                <input type="button" name="paymentmethodformsubmit" onclick="submitPayment()" value="Submit">
                <input type="button" name="paymentmethodformcancel" onclick="cancelPayment()" value="Cancel">
            <!--ENTER VOUCHER CODES TO USE FOR PAYMENT-->
            </fieldset>
            <?php if(isset($ownVouchers)){ ?>
            <fieldset id="step2A" disabled style="opacity:0.6" class="row">
                <legend>Claim Voucher</legend>
                <div id="vouchercode">
                    <label class="formlabels">Enter voucher code&#58;</label>
                    <input type="text" name="vouchercodefld" required size="10" maxlength="10" 
                           pattern="^([a-z][0-9]-){3}[A-Z]$"
                           onchange="validateVoucherCode(this,$('#vouchercodeerr'))">
                    <span id="vouchercodeerr" class="formerrors"></span>
                </div>
                <input type="button" name="givevoucherformsubmit" onclick="submitGiveVoucher()" value="Submit Code">
                <input type="button" name="givevoucherformcancel" onclick="cancelGiveVoucher()" value="Cancel">
            </fieldset>
            <?php } //excludes step2A ?>
            <div class="row">
                <!--IN THE CASE OF CREDIT CARD PAYMENT-->
                <fieldset id="step3A" disabled style="opacity:0.6" class="col-md-6">
                    <legend>Credit Card Information</legend>
                    <div id="cardtype">
                        <label class="formlabels">Credit card type&#58;</label>
                        <select name="cardtypefld">
                            <option value="MasterCard">MasterCard</option>
                            <option selected value="Visa">Visa</option>
                        </select>
                        <span id="cardtypeerr" class="formerrors"></span>
                    </div>
                    <div id="cardnumber">
                        <label class="formlabels">Credit card number&#58;</label>
                        <input type="text" name="cardnumberfld" required size="19" maxlength="19" pattern="^(([0-9]{4}\s)([0-9]{4}\s){2}([0-9]{4})|([0-9]{16}))$"
                               onchange="validateTransField(this, $('#cardnumbererr').get(0),'card')">
                        <span id="cardnumbererr" class="formerrors"></span>
                    </div>
                    <div id="cardholdername">
                        <label class="formlabels">Name on card&#58;</label>
                        <input type="text" name="cardholdernamefld" required size="30" maxlength="30" pattern="^[A-Z]+(\s[A-Z]+)+.$"
                               onchange="validateTransField(this, $('#cardholdernameerr').get(0),'cardholder')">
                        <span id="cardholdernameerr" class="formerrors"></span>
                    </div>
                    <div id="expiry">
                        <label class="formlabels">Expiration date&#58;</label>
                        <input type="text" name="expirymfld" required size="2" maxlength="2" 
                               pattern="^(0[1-9]|1[0-2])$"
                               onchange="validateTransField(this,$('#expiryerr').get(0),'exm')">
                        <input type="text" name="expiryyfld" required size="2" maxlength="2" 
                               pattern="^[0-9]{2}$"
                               onchange="validateTransField(this,$('#expiryerr').get(0),'exy')">
                        <span id="expiryerr" class="formerrors"></span>
                    </div>
                    <div id="cvv">
                        <label class="formlabels">CVV code&#58;</label>
                        <input type="text" name="cvvfld" required size="3" maxlength="3" 
                               pattern="^[0-9]{3}$"
                               onchange="validateTransField(this,$('#cvverr').get(0),'cvv')">
                        <span id="cvverr" class="formerrors"></span>
                    </div>
                    <input type="button" name="cardinfoformsubmit" onclick="submitCard()" value="Submit Card Details">
                     <input type="button" name="cardinfoformcancel" onclick="cancelCardInfo()" value="Cancel">
                </fieldset>
                <!--IN THE CASE OF EFT PAYMENT-->
                <fieldset id="step3B" disabled style="opacity:0.6" class="col-md-6">
                    <legend>Bank Account Information</legend>
                    <div id="acctype">
                        <label class="formlabels">Account type&#58;</label>
                        <select name="acctypefld">
                            <option value="checking" >Checking</option>
                            <option value="deposit" >Deposit</option>
                            <option value="transact" >Transactional</option>
                        </select>
                        <span id="acctypeerr" class="formerrors"></span>
                    </div>
                    <div id="accholdername">
                        <label class="formlabels">Name on account&#58;</label>
                        <input type="text" name="accholdernamefld" required size="30" maxlength="30" 
                               pattern="^[A-Z]+(\s[A-Z]+)+.$"
                               onchange="validateTransField(this,$('#accholdernameerr').get(0),'accholder')">
                        <span id="accholdernameerr" class="formerrors"></span>
                    </div>
                    <div id="routingnumber">
                        <label class="formlabels">Routing number&#58;</label>
                        <input type="text" name="routingnumberfld" required size="20" maxlength="20" 
                               pattern="^(((\d+\s?\d+)+)|((\d+-?\d+)+))$"
                               onchange="validateTransField(this,$('#routingnumbererr').get(0),'routenum')">
                        <span id="routingnumbererr" class="formerrors"></span>
                    </div>
                    <div id="accnumber">
                        <label class="formlabels">Account number&#58;</label>
                        <input type="text" name="accnumberfld" required size="20" maxlength="20" 
                               pattern="^(((\d+\s?\d+)+)|((\d+-?\d+)+))$"
                               onchange="validateTransField(this,$('#accnumbererr').get(0),'accnum')">
                        <span id="accnumbererr" class="formerrors"></span>
                    </div>
                    <input type="button" name="accinfoformsubmit" onclick="submitAccount()" value="Submit Account Details">
                    <input type="button" name="accinforformcancel" onclick="cancelAccInfo()" value="Cancel">
                </fieldset>
            </div>
            <!--FINAL CONFIRMATION-->
            <fieldset id="step4" disabled style="opacity:0.6" class="row">
                <legend>Transaction Summary</legend>
                <fieldset id="delivery">
                    <legend>Delivery details</legend>
                </fieldset>
                <fieldset id="payment">
                    <legend>Payment details</legend>
                        
                </fieldset>
                <fieldset id="total">
                    <legend>Cost Breakdown</legend>
                    <?php
                        echo "<table><tr><th>Item Name</th><th>Unit Cost</th><th>Units</th><th>Subtotal</th></tr>";
                        if(isset($cartItems)){
                            foreach($cartItems as $item){
                                echo "<tr><td>" . $item["Name"] . "</td><td>" . $item["UsedPrice"] . "</td><td>" . $item["Quantity"] . "</td><td>" . $item["ActualCost"] . "</td></tr>";
                            }
                        } echo "</table>"; ?>
                    <span>Grand Total: R <span id="finaltotal"></span></span>
                    
                </fieldset>
                <button type="button" name="committransactionsubmit" onclick="sendCommit()">Confirm</button>
                <span id="commiterr" class="formerrors"></span>
            </fieldset>
        <button type="button" name="committransactioncancel" onclick="cancelCommit()">Cancel</button>
            <?php
                if(!isset($_SESSION["currentTransBCS"])){
                    echo "<span>Something went wrong. We apologize. Please return to the store.</span>";
                    echo "<a href=\"store.php\" class=\"linkbtn\">Return to Store</a>";
                }
            ?>
        </div>
    </body>
</html>