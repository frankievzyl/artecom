<?php 
    session_start(); 
    $dbconPath = "incMySQLConnect.php";
    $root = "..";
    $_SESSION["root"] = "..";
    
    if(isset($_SESSION["accAction"]) && !(isset($_SESSION["customerid"]))){
        $accountFunction = $_SESSION["accAction"];//login or signup
        $thisCustomer = 0;
        $hasCustomer = false;
        $_SESSION["accAction"] = null;
    }else{
        if(isset($_SESSION["customerid"])){
            //creates synonym for customer id session object
            $thisCustomer = $_SESSION["customerid"];
            $hasCustomer = true;
            $accountFunction = false;
            $_SESSION["searcherid"] = $thisCustomer;
            
        }else{
            $thisCustomer = 0;
            $hasCustomer = false;
            $accountFunction = "login";
            
        }
    }
    
    
    $_SESSION["currentTransBCS"] = null;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Art Attack: Store<?php echo $thisCustomer; ?></title>
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
            
            <?php if($accountFunction == "login"){ //loads login screen?>
                $("#accounts").ready(function(){
                    $("#accounts").load("customerlogin.php");
                    contextSwitchUser("#accounts"); 
                });
            
            <?php }else if($accountFunction == "signup"){ ?>
                $("#accounts").ready(function(){
                    $("#accounts").load("customersignup.php");
                    contextSwitchUser("#accounts");
                });
            <?php } ?>
            
            //select all items in the cart
            function selectAll(){
                $(".itemchk").each(function(){
                    this.checked = true;
                });
            }
            
            //sets all items in the cart to unselected
            function deSelectAll(){
                $(".itemchk").each(function(){
                    this.checked = false;
                });
            }
            
            //subtracts 1 from the number of this item in the cart
            function minusCount(barcode){
             
                var itemcount = Number($("#count_"+barcode).text());
                
                if(itemcount == 0){//already have 0 of this item selected
                    alert("Cannot have less than 0 of an item in the cart.\n Click on the 'remove' button to take it out of the cart.");
                }else{
                    // 1 or more of this item in cart
                        
                    var remAction = "UPDATE `customerproduct` SET Quantity = Quantity - 1 WHERE BarCode = '" + barcode + "' AND CustomerID = <?php echo $thisCustomer; ?> AND TransactionID IS NULL";        

                    $.post("customAction.php", {action: remAction}, function(data){

                        if(data.trim() == "SUCCESS"){//statement executed successfully
                            itemcount--;//decrease item count in cart
                            $("#count_"+barcode).text(itemcount);//set new count on page    
                        }
                        //else nothing as there is no need to update

                    });
                }
            }
            
            //adds 1 to the number of this item in the cart{
            function plusCount(barcode){
                var itemcount = Number($("#count_"+barcode).text());
                var proceed = true;
                
                if($("#pstock_"+barcode).length > 0){//if the item is NOT a voucher
                    var available =  Number($("#pstock_"+barcode).text());//get number in stock
                    
                    proceed = available - (itemcount + 1) >= 0;
                    //true if enough in stock to do transaction (potentially)
                    if(!proceed){
                        alert("Not enough stock to complete the request.");
                    }
                }//else a voucher has infinite stock available; no check necessary
                if(proceed){
                    var remAction = "UPDATE `customerproduct` SET Quantity = Quantity + 1 WHERE BarCode = '" + barcode + "' AND CustomerID = <?php echo $thisCustomer; ?> AND TransactionID IS NULL";        

                    $.post("customAction.php", {action: remAction}, function(data){

                        if(data.trim() == "SUCCESS"){//statement executed successfully
                            itemcount++;//increase item count in cart
                            $("#count_"+barcode).text(itemcount);//set new count on page    
                        }
                        //else nothing as there is no need to update

                    });
                }
            }
    
            //removes the given item from the customer's cart
            function removeFromCart(barcode){
                var remAction = "DELETE FROM `customerproduct` WHERE CustomerID = <?php echo $thisCustomer; ?> AND BarCode = '" + barcode + "' AND TransactionID IS NULL";
                
              $.post("customAction.php", {action: remAction}, function(data){//removes cart items
                    
                    var msg = data.trim();console.log(msg);
                    if(msg == "SUCCESS"){//item removed from cart in database
                        $("#cartitem_"+barcode).remove();//remove item from cart on page
                    }else{
                        alert("Failed to remove the item from the cart.\nPlease try again.");
                    } 
                });
            }
            
            //removes the selected items from the cart
            function removeSelected(){
                var removalList = "";
                var removals = $(".itemchk:checked").toArray(); //get all selected cart items
                var remAction = ""; 
                
                if(removals.length > 0){
                    if(removals.length == 1){
                        remAction = "DELETE FROM `customerproduct` WHERE CustomerID = <?php echo $thisCustomer; ?> AND BarCode = '" + removals.pop().value + "' AND TransactionID IS NULL";
                        
                    }else{
                        for(var item in removals){
                            removalList += "'" + removals[item].value + "',";//add each to a string
                        }
                        removalList = removalList.substr(0,removalList.length - 1);//remove last comma

                        remAction = "DELETE FROM `customerproduct` WHERE CustomerID = <?php echo $thisCustomer; ?> AND BarCode IN (" + removalList + ") AND TransactionID IS NULL";
                    }
                    
                    $.post("customAction.php", {action: remAction}, function(data){//removes cart items

                        var msg = data.trim();
                        if(msg == "SUCCESS"){
                            $(".itemchk:checked").parent().remove();//remove selected items
                        }else{
                            alert("Failed to remove the selected items from cart.\nPlease try again.");
                        } 
                    });
                }else{
                    alert("No items selected.");
                }
            }
            
            //checks out the selected items in the cart
            function checkoutSelected(){
                var checkouts = $(".itemchk:checked").toArray();
                var checkoutList = [];
                
                if(checkouts.length > 0){// 1+ items selected
                    for(var bc in checkouts){
                        checkoutList.push(checkouts[bc].value);
                    }    
                    //post to transaction page and then go to it
                    $.post("transaction.php",{checkoutBCS : checkoutList},function(){
                        window.location = "http://localhost/artattack/pages/transaction.php";
                    });
                }else{
                    alert("No items selected to check out.");
                }
            }
            
            function doAccount(filename){
                contextSwitchUser("#accounts");
               $("#accounts").load("<?php echo $root; ?>/pages/" + filename);
            }
            
            function logoutCust(){
                $.post("<?php echo $root; ?>/pages/sessions.php", {customerlogout: "logout"});
                window.location = "http://localhost/artattack/index.php";
            }
        </script>
    </head>
    <body>
        <div id="pagegrid" class="container-fluid">
            <?php require $root . "/pages/header.php"; ?>
            <div class="row">
                <div id="search" class="col-sm-2">
                    <div id="special">
                        <div id="specialbtn" onclick="showSpecials(this,'<?php echo $root; ?>',<?php echo $thisCustomer; ?>)"><i class="fas fa-tags"></i><input type="checkbox" name="specialchk" style="display:none"></div>
                        <div id="voucherbtn" onclick="showVouchers(this,'<?php echo $root; ?>',<?php echo $thisCustomer; ?>)"><i class="fas fa-money-check-alt"></i><input type="checkbox" name="voucherchk" style="display:none"></div>
                    </div>
<?php require "loadSearch.php"; ?>
                    </div>
                <div class="col-sm-7">
                    <div class="container-fluid">
                        <div id="categories" class="flexline row">
                        <?php
                            require $dbconPath;

                            if($_SESSION["connected"]){                    
                                //get all classes
                                $stmnt = "SELECT * FROM `myclass` ORDER BY ClassLevel, SuperClass";
                                $result = $conn->query($stmnt);

                                if($result->num_rows > 0){
                                    $currentLevel = 0;
                                    echo "<select id=\"pcatfld$currentLevel\" onclick=\"refreshListsStore(this.value, $currentLevel)\">";
                                    while($row = $result->fetch_assoc()){
                                        //change to next level select item if necessary
                                        if($row["ClassLevel"] != $currentLevel){
                                            $currentLevel = $row["ClassLevel"];
                                            echo "</select><select id=\"pcatfld$currentLevel\" style=\"display:none\" onclick=\"refreshListsStore(this.value, $currentLevel)\">"; 
                                        }
                                        //add option element
                                        echo "<option value=\".optsuper" . $row["ClassID"] . "\" class=\"optsuper" . $row["SuperClass"] ."\">" . $row["Name"] . "</option>";
                                    }
                                    echo "</select>";
                                }else{
                                    echo "EMPTY";
                                }
                                $conn->close();
                            }
                        ?>
                        <input type="checkbox" name="catchk" value="caton" id="catchk" checked><label for="catchk">Search in Category</label>
                        <script>

                            function refreshListsStore(tgtOpt, myLvl){
                                refreshLists(tgtOpt, myLvl);
                                $("#catchk").prop("checked",true);//set to search for categories
                                showProducts(tgtOpt.slice(9), "category","<?php echo $root; ?>", <?php echo $thisCustomer; ?>);
                            }

                        </script>
                    </div>
                        <div id="mainactivity" class="row">
                            <div id="store" class="col-xs-12">
                                <div id="catblocks">
                                    <?php 
                                        if($accountFunction == "false"){
                                            require "loadCatBlocks.php"; 
                                        }
                                    ?>
                                </div>
                            </div>
                                
                            <div id="accounts" class="col-xs-12"></div>
                            <div id="viewer" class="col-xs-12"></div>
                        </div>
                    </div>    
                </div>
                <div id="cartsuper" class="col-sm-3">
<?php if($hasCustomer){ ?>
                    <fieldset id="topbtns">
                        <button class="cartbtns" onclick="selectAll()">Select All</button>
                        <button class="cartbtns" onclick="deSelectAll()">Deselect All</button>
                        <button class="cartbtns" onclick="checkoutSelected()">Checkout Selected</button>
                        <button class="cartbtns" onclick="removeSelected()">Remove Selected</button>
                    </fieldset>
<?php } ?>
                    <div id="cart" class="container-fluid">
                        <?php
                            //loads cart with customer's cart items
                            if($hasCustomer){
                                require "loadCart.php";
                            }else{
                                echo "<span id=\"cartMsg\">You must be logged in to see your cart.</span>";
                            }
                        ?>
                    </div>
<?php if($hasCustomer){ ?>
                    <fieldset id="bottombtns">
                        <button class="cartbtns" onclick="selectAll()">Select All</button>
                        <button class="cartbtns" onclick="deSelectAll()">Deselect All</button>
                        <button class="cartbtns" onclick="checkoutSelected()">Checkout Selected</button>
                        <button class="cartbtns" onclick="removeSelected()">Remove Selected</button>
                    </fieldset>
<?php } ?>
                </div>   
            </div>
            <?php include $root . "/pages/footer.php"; ?>
        </div>
        <?php include "makeReview.php"; ?>
    </body>
</html>