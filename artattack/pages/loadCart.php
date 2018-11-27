<?php
    require "incMySQLConnect.php";

    if(!isset($_SESSION["customerid"])){ //load customer id with received data
        if(isset($_POST["cID"])){
            $thisCustomer = $_POST["cID"];
        }else{
            $thisCustomer = 0;//no customer
        }
    }else{
        $thisCustomer = $_SESSION["customerid"]; //otherwise use existing customer id
    }
    
    if(isset($_POST["newbarcode"])){ //requesting a single object (added to cart)
        $newBC = $_POST["newbarcode"];
        $stmnt = "SELECT
                        product.BarCode,
                        product.Price,
                        product.StockLevel,
                        product.Name,
                        product.Image,
                        product.SpecialPrice,
                        product.SpecialEnd,
                        (NOT product.Description <=> 'voucher') AS NotVoucher,
                        customerproduct.Quantity
                    FROM
                        `product`
                    JOIN `customerproduct` ON product.BarCode = customerproduct.BarCode
                    WHERE
                        customerproduct.CustomerID = $thisCustomer AND customerproduct.BarCode = '$newBC' AND customerproduct.TransactionID IS NULL";
    }else{ //otherwise load entire cart (page reload)
        $stmnt =    "SELECT
                        product.BarCode,
                        product.Price,
                        product.StockLevel,
                        product.Name,
                        product.Image,
                        product.SpecialPrice,
                        product.SpecialEnd,
                        (NOT product.Description <=> 'voucher') AS NotVoucher,
                        customerproduct.Quantity
                    FROM
                        `product`
                    JOIN `customerproduct` ON product.BarCode = customerproduct.BarCode
                    WHERE
                        customerproduct.CustomerID = $thisCustomer AND customerproduct.TransactionID IS NULL";
    }
    if($_SESSION["connected"] && $thisCustomer != 0){ //ensures there is still a customer

        $result = $conn->query($stmnt);

        if($result->num_rows > 0){

            while($row = $result->fetch_assoc()){
                $shortBC = $row["BarCode"]; ?>
                
                <div id="cartitem_<?php echo $shortBC; ?>" class="cartitem row">
                    <div class="container-fluid">
                        <div class="row">
                            <img class="pimg col-xs-3" src="..<?php echo $row["Image"]; ?>" alt="<?php echo $row["Name"]; ?>">
                            <div class="genitemgrp col-xs-9 container-fluid">
                                <div class="pname row"><?php echo $row["Name"]; ?></div>
                                <div class="row">
                                    <div class="wholeprice col-xs-6">
                            <?php if($row["SpecialPrice"] != null){ ?>
                                        <div class="pspecial">Special: R <?php echo $row["SpecialPrice"]; ?></div>
                                        <div class="pspecialdate">Ends: <?php echo $row["SpecialEnd"]; ?></div>
                            <?php }else{ ?>
                                        <div class="pprice">R <?php echo $row["Price"]; ?></div>
                            <?php } ?>
                                    </div>

                    <?php   if($row["NotVoucher"]){ ?>
                                    <div class="pstock col-xs-6">Available&#58; 
                                        <div id="pstock_$shortBC" <?php if($row["StockLevel"] <= 0){ ?> style="color:red" <?php } ?>>
                                            <?php echo $row["StockLevel"]; ?>
                                        </div>
                                    </div>
                <?php       }else{echo "<div class='filler col-xs-6'></div>";} ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="cartcount col-xs-8">
                                <button class="downcountbtn" onclick="minusCount('<?php echo $shortBC; ?>')">
                                    <i class="fas fa-minus-circle"></i>
                                </button>
                                <span id="count_<?php echo $shortBC; ?>" class="count"><?php echo $row["Quantity"]; ?></span>
                                <button class="upcountbtn" onclick="plusCount('<?php echo $shortBC; ?>')">
                                    <i class="fas fa-plus-circle"></i>
                                </button>
                            </div>
                            <button class="removefromcartbtn col-xs-2" onclick="removeFromCart('<?php echo $shortBC; ?>')"><i class="fas fa-times"></i></button>
                            <input type="checkbox" class="itemchk col-xs-2" value="<?php echo $shortBC; ?>">
                        </div>
                    </div>
                </div>
        <?php    }
        }else{ ?>
                <span id="cartMsg">You have no items in your cart.</span>
        <?php }
        $conn->close();
    }
?>