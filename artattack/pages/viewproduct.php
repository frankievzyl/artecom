<?php 
    session_start(); 
    $root = $_SESSION["root"];
    $dbconPath = "incMySQLConnect.php";

    if(isset($_SESSION["customerid"])){//creates synonym for customer id session object
        $thisCustomer = $_SESSION["customerid"];
        $hasCustomer = true;
    }else{
        $thisCustomer = 0;
        $hasCustomer = false;
    }

    //get selected barcodes from post and place in session
    if(isset($_POST["thisProduct"])){
        $thisProduct = $_POST["thisProduct"];
    }
    
?>

<?php
    require $dbconPath;

    if($_SESSION["connected"]){
        //get basic data of which there is only 1
        $stmnt = "SELECT
                        `product`.`Name` AS ProdName,
                        `product`.`Price`,
                        `product`.`SpecialPrice`,
                        `product`.`SpecialEnd`,
                        `product`.`StockLevel`,
                        `product`.`Description`,
                        `product`.`Image`,
                        `product`.`BrandID`,
                        `brand`.`Name` AS BrandName,
                        `brand`.`Logo`,
                        `product`.`RangeID`,
                        `myrange`.`Name` AS RangeName,
                        `product`.`ColourID`,
                        `colour`.`Name` AS ColourName,
                        `colour`.`ColourCode`,
                        `colour`.`ColourHex`
                    FROM
                        `product`
                    LEFT JOIN `brand` ON `product`.`BrandID` = `brand`.`BrandID`
                    LEFT JOIN `myrange` ON `product`.`RangeID` = `myrange`.`RangeID`
                    LEFT JOIN `colour` ON `product`.`ColourID` = `colour`.`ColourID`
                    WHERE
                        `product`.`BarCode` = '$thisProduct'";

        $result = $conn->query($stmnt);

        if($result->num_rows > 0){
            $loaded = true;
            $basicData = $result->fetch_assoc();

        }else{
            $loaded = false;
        }
    }

  if($loaded){  //only loads the page if data was found
?>
<div class="container-fluid">
    <div id="primary" class="row">
        <?php

            if($basicData["Image"] != null){
                echo "<img id=\"pimg\" class=\"col-sm-5 col-xs-6\" src=\"" . $root . $basicData["Image"] . "\" alt=\"No image preview\">";
            }
            echo "<div class=\"col-sm-7 col-xs-6>\"";
            echo "<span id=\"pname\">" . $basicData["ProdName"] . "</span><span id=\"pprice\">R " . $basicData["Price"] . "</span>";
            if($basicData["SpecialPrice"] != null){
                $dt = strtotime($basicData["SpecialEnd"]);
                echo "<span id=\"pspecialprice\"><i>On Special: R </i>" . $basicData["SpecialPrice"] . "</span><span id=\"pspecialdate\"><i>Ends on: </i>" . date("d/m/Y",$dt) . " <i>at: </i>" . date("h:ia",$dt) . "</span>";
            }
            echo "<span id=\"pstock\" style=\"color:";
                if($basicData["StockLevel"] == 0){
                    echo "red";
                }else{
                    echo "green";
                }
            echo "\" ><i style='color:black' >Available: </i>" . $basicData["StockLevel"] . "</span>";
            if($basicData["Description"] != null){
                echo "<pre id=\"pdesc\">" . $basicData["Description"] ."</pre>";
            }
            echo "</div>";
        ?>
    </div>

    <button type="button" data-toggle="collapse" data-target="#pheritage">
        Details <i class="fas fa-chevron-down"></i>
    </button>
    <div id="secondary" class="row">
        <?php if($basicData["BrandName"] != null){
            echo "<div id=\"pheritage\" class=\"col-sm-9 collapse\>";
            echo "<img id=\"pbrandlogo\" src=\"" . $root . $basicData["Logo"] . "\" alt=\"No logo available\"><span id=\"pbrandname\"><i>Brand: </i>" . $basicData["BrandName"] . "</span>";
            if($basicData["RangeName"] != null){
                echo "<span id=\"prangename\"><i>Range: </i>" . $basicData["RangeName"] . "</span>";
            }
            if($basicData["ColourHex"] != null){
                echo "<div id=\"pcolourprev\" style=\"background-color:#" . $basicData["ColourHex"] . "\"></div><span id=\"pcolourname\"><i>Colour name: </i>" . $basicData["ColourName"] . "</span><span id=\"pcolourcode\"><i>Colour code: </i>" . $basicData["ColourCode"] . "</span>";
                echo "</div><div id=\"changecolour\" class=\"col-sm-3\">";
                echo "<label for=\"pcolourhex\">Choose a different colour</label>";
                echo "<input type=\"color\" id=\"pcolourhex\" name=\"pcolourhexfld\" onchange=\"colourSwap()\">";
            }
        } ?>
    </div>
    <button type="button" data-toggle="collapse" data-target="#attributes">
        Attributes <i class="fas fa-chevron-down"></i>
    </button>
    <div id="tertiary" class="row">
        <div id="attributes" class="col-sm-12" collapse>
            <?php
                //get attributes of which there are multiple
                $stmnt = "SELECT
                                am.`Amount`,
                                `attribute`.`Name` AS AttrName,
                                `attribute`.`Description`,
                                `unit`.`Symbol`,
                                `wordamount`.`Name` AS WordName
                            FROM
                                `attributemeasurement` AS am
                            JOIN `attribute` ON am.`AttributeID` = `attribute`.`AttributeID`
                            LEFT JOIN `unit` ON am.`UnitID` = `unit`.`UnitID`
                            LEFT JOIN `wordamount` ON am.`WAmountID` = `wordamount`.`WAmountID`
                            WHERE
                                am.`BarCode` = '$thisProduct'";

                $result = $conn->query($stmnt);

                if($result->num_rows > 0){

                    while($row = $result->fetch_assoc()){ 

                        echo "<div class=\"attrib\">";
                        if($row["Symbol"] != null){ //if this is a quantitative attribute
                            echo "<span class=\"pattrname\">" . $row["AttrName"] . "</span><span class=\"pattramnt\">" . $row["Amount"] . "</span><span class=\"pattrunit\">" . $row["Symbol"] . "<span class=\"pattrdesc\">" . $row["Description"] . "</span></span>";
                        }elseif($row["WordName"] != null){ // if this is a qualitative attribute
                            echo "<span class=\"pattrname\">" . $row["AttrName"] . "</span><span class=\"pattradj\">" . $row["WordName"] . "</span>";
                        }
                        echo "</div>";
                    }
                }
                else{
                    echo "<span class=\"previewMsg\">This product has no special attributes.</span>";
                } 
            ?>
        </div>
    </div>
    <?php if($thisCustomer != 0 && $thisCustomer != null){ //ensures that only logged in customers can make a review ?>
        <button class="btn btn-primary row" id="addreviewbtn" data-toggle="modal" data-target="#makereview"
                onclick="showReviewInput('<?php echo $thisProduct . "'," . $thisCustomer; ?>)">
                        <i class="far fa-comment"></i><i class="fas fa-edit"></i>
        </button>
        <button class="addtocartbtn panelbuttons" onclick="addToCart('<?php echo $row["BarCode"] . "'," . $customerId; ?>)" <?php echo "disabled"; ?> >
            <i class="fas fa-cart-plus"></i>
        </button>
    <?php } ?>
    <button type="button" data-toggle="collapse" data-target="#previews">
        Reviews <span class="fas fa-chevron-down"><i class="fas fa-comments"></i></span>
    </button>
    <div id="quaternary" class="row">
        <div id="previews" collapse>
            <?php

                if($_SESSION["connected"]){ 

                    $stmnt = "SELECT
                                    `review`.`Text`,
                                    `review`.`Rating`,
                                    `review`.`RvDateTime`,
                                    `customer`.`Name`
                                FROM
                                    `review`
                                JOIN `customerproduct` ON `review`.`ReviewID` = `customerproduct`.`ReviewID`
                                JOIN `customer` ON `customer`.`CustomerID` = `customerproduct`.`CustomerID`
                                WHERE
                                    `customerproduct`.`BarCode` IN(
                                    SELECT
                                        BarCode
                                    FROM
                                        `product`
                                    WHERE Name
                                        = '" . $basicData["ProdName"] . "')";

                    $result = $conn->query($stmnt);

                    if($result->num_rows > 0){

                        while($row = $result->fetch_assoc()){
                            $dt = strtotime($row["RvDateTime"]);
                            $smRating = $row["Rating"]; ?>
                            <div class="newreview">;
                                <span class="rcustname"><i class="fas fa-user"></i><?php echo $row["Name"]; ?></span>;
                                <span class="rdate"><i class="far fa-calendar-alt"></i><?php echo date("d/m/Y",$dt); ?></span>;
                                <span class="rtime"><i class="far fa-clock"></i><?php echo date("h:ia",$dt); ?></span>;
                                <span class="rrating">
                                    <span class="rratingfld" style="display:none"><?php echo round($smRating,1); ?></span>
                                    <div class="rratingbase">
                                            <i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i><i class="far fa-star"></i>
                                        </div>
                                    <div class="rratingstars">
                                        <?php 
               
                                        while($smRating > 0){
                                            if($smRating - 2 >= 0){
                                                echo "<i class='fas fa-star'></i>";
                                                $smRating -= 2;

                                            }else if($smRating - 1 >= 0 || $smRating < 1 && $smRating > 0){
                                                echo "<i class='fas fa-star-half'></i>"; 
                                                $smRating = 0;

                                            }
                                        }
                                            ?> 
                                    </div>
                                </span>
                                <pre class="rtext"><?php echo $row["Text"]; ?></pre>;
                            </div>;
                    <?php
                        }
                    }else{
                        echo "<span class=\"previewsMsg\">There are no reviews for this product yet.</span>";
                    }
                }
            $conn->close();
            ?>
        </div>
    </div>
</div>
<?php }else{
  echo "<span class=\"previewMsg formerrors\">Failed to load product details. Please refresh the page.</span>";
} include "makeReview.php"; ?>
<button type="button" onclick="contextSwitchUser('#viewer')">Return</button>