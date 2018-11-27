<?php
    session_start();
    require "incMySQLConnect.php";
    
    if($_SESSION["connected"]){

        $catId = $_POST["catId"];//current category
        $kind = $_POST["kind"];//normal, special, voucher
        $namepart = $_POST["namepart"];//string to be included in product name
        $sort = $_POST["sort"];//boolean array for by name, by price, by rating
        $order = $_POST["order"];//boolean array for ascending(false) ,descending(true) each sort
        $minP = $_POST["minP"];//low range price
        $maxP = $_POST["maxP"];//high range price
        $stock = $_POST["stock"];//boolean: all(1)[default], available only(2), out of stock only(3)
        $filters = $_POST["filters"];//boolean array[price range, stock]
        $brands = $_POST["brands"];//array of selected brands
        $ranges = $_POST["ranges"];//array of selected ranges
        $colours = $_POST["colours"];//array of selected colours
        $root = $_SESSION["root"];
        $customerId = $_POST["cID"];
        
        $stmnt = "SELECT * FROM `productdetails`";
        
        if($kind == "category"){
            $stmnt = "SELECT * FROM `productdetails` WHERE CatID = " . $catId;
            
            //somehow get all products for main category going down, all inclusive, narrowing as descending
        }elseif($kind == "specialcategory"){
            $stmnt = "SELECT * FROM `productdetails` WHERE CatID = " . $catId . " AND NOT SpecialPrice IS NULL";   
        }elseif($kind == "normal"){
            if($customerId == "staff"){ //modified so staff sees all products
                $stmnt = "SELECT * FROM `productdetails`";
            }else{
                $stmnt = "SELECT * FROM `productdetails` WHERE (NOT Description = 'voucher' OR Description IS NULL) ";
            }
        }elseif($kind == "special"){
            $stmnt = "SELECT * FROM `productdetails` WHERE NOT SpecialPrice IS NULL";
        }else{
            $stmnt = "SELECT * FROM `productdetails` WHERE Description = 'voucher'";
        }
        
        //SEARCHING for string in product name
        if($namepart != null){
            $stmnt .= " AND Name LIKE '%" . $namepart . "%'";
        }
        //FILTERING continuing WHERE clause
        if(json_decode($filters[0])){//use price range
            $stmnt .=  " AND (Price BEWTEEN $minP AND $maxP)";
        }
        if(json_decode($filters[1])){//use stock level
            if($stock == 2){//see only in-stock
                $stmnt .= " AND Stock > 0";
            }elseif($stock == 3){//see only out-of-stock
                $stmnt .= " AND Stock <= 0";
            }
        }
        if($brands[0] != 0){//selected brands
            $stmnt .= " AND BrandID IN (";
            foreach ($brands as $b){
                $stmnt .= $b . ",";
            }
            $stmnt = rtrim($stmnt,",");
            $stmnt .= ")";
        }
        if($ranges[0] != 0){//selected ranges
            $stmnt .= " AND RangeID IN (";
            foreach ($ranges as $r){
                $stmnt .= $r . ",";
            }
            $stmnt = rtrim($stmnt,",");
            $stmnt .= ")";
        }
        if($colours[0] != 0){//selected colours
            $stmnt .= " AND ColourID IN (";
            foreach ($colours as $c){
                $stmnt .= $c . ",";
            }
            $stmnt = rtrim($stmnt,",");
            $stmnt .= ")";
        }
            
        //SORTING after WHERE clause
        if(json_decode($sort[0])){//by name
            $stmnt .= " ORDER BY Name";
            if(json_decode($order[0])){// and descending
                $stmnt .= " DESC";
            }
            if(json_decode($sort[1])){//and by price
                $stmnt .= ",Price";
                if(json_decode($order[1])){// and descending
                    $stmnt .= " DESC";
                }
                if(json_decode($sort[2])){// and by rating
                    $stmnt .= ",avgRating";
                    if(json_decode($order[2])){//and descending
                        $stmnt .= " DESC";
                    }
                } 
            }elseif(json_decode($sort[2])){//not by price, but by rating also
                $stmnt .= ",avgRating";
                if(json_decode($order[2])){//and descending
                    $stmnt .= " DESC";
                }
            }
        }elseif(json_decode($sort[1])){//not by name, but by price
            $stmnt .= " ORDER BY Price";
            if(json_decode($order[1])){//and descending
                $stmnt .= " DESC";
            }
            if(json_decode($sort[2])){//and by rating
                $stmnt .= ",avgRating";
                if(json_decode($order[2])){
                    $stmnt .= " DESC";//and descending
                }
            }    
        }elseif(json_decode($sort[2])){//only by rating
            $stmnt .= " ORDER BY avgRating";
            if(json_decode($order[2])){
                $stmnt .= " DESC";//and descending
            }
        }
        
        $disablePart = $customerId != 0 && $customerId != null;//ensures only a logged in customer can add to cart and make reviews
       // echo "<p>$stmnt</p>";
        if($customerId != "staff"){ //showing store page products
            $result = $conn->query($stmnt);
            if($result->num_rows > 0){ 
                while($row = $result->fetch_assoc()){ ?>
                        
                    <div id="sp_<?php echo $row["BarCode"]; ?>" class="storeproduct">
                        <div class="panelbuttonsgrplarge">
                            <?php if($disablePart){ ?>
                            <button type="button" class="addtocartbtn panelbuttons" onclick="addToCart('<?php echo $row["BarCode"] . "'," . $customerId; ?>)">
                                <i class="fas fa-cart-plus"></i>
                            </button>

                            <?php } ?>
                            <button type="button" class="viewprodbtn panelbuttons" onclick="viewProduct('<?php echo $row["BarCode"] . "', '" . $root; ?>')">
                                View Product Details
                            </button>
                            <?php if($disablePart){ ?>
                            <button type="button" class="reviewbtn panelbuttons" onclick="showReviewInput('<?php echo $row["BarCode"] . "'," . $customerId; ?>)">
                                Review Product
                            </button>
                            <?php } ?>
                        </div>
                        <!--container for smaller rows-->
                        <div class="container-fluid pushback">
                            <!--top row for image and buttons-->
                            <div class="row">
                                <img class="pimg col-xs-4 col-md-12" src="<?php echo $root . $row["Image"]; ?>" alt="<?php echo $row["Name"]; ?>">
                                <!--buttons div-->
                                <div class="container-fluid col-xs-8  col-md-12">
                                    <div class="panelbuttonsgrpsmall">
                                        <div class="row">
                                            <?php if($disablePart){ ?>
                                            <button type="button" class="addtocartbtn panelbuttons col-xs-12" onclick="addToCart('<?php echo $row["BarCode"] . "'," . $customerId; ?>)">
                                                <i class="fa fa-cart-plus"></i>
                                            </button>
                                        </div>
                                        <div class="row">
                                            <?php } ?>
                                            <button type="button" class="viewprodbtn panelbuttons col-xs-6 col-md-12" onclick="viewProduct('<?php echo $row["BarCode"] . "', '" . $root; ?>')">
                                                View Product Details
                                            </button>
                                            <?php if($disablePart){ ?>
                                            <button type="button" class="reviewbtn panelbuttons col-xs-6 col-md-12" onclick="showReviewInput('<?php echo $row["BarCode"] . "'," . $customerId; ?>)">
                                                Review Product
                                            </button>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--row for rest of data-->
                            <div class="row">
                                <div class="score col-xs-4 col-md-12">
                                    <div class="pname"><?php echo $row["Name"]; ?></div>
                                    <div class="pprice">R <?php echo $row["Price"]; ?></div>
                                </div>
                                <div class="sspecial col-xs-4 col-md-12">
                            <?php 
                                 if($row["SpecialPrice"] != null){ ?>
                                    <div class="pspecial">Special: R <?php echo $row["SpecialPrice"]; ?></div>
                                    <div class="pspecialdate">Ends: <?php echo $row["SpecialEnd"]; ?></div>
                            <?php
                                }else{ 
                                    echo "<div class='filler'></div>";
                                } 
                            ?>
                                </div>
                                <div class="sstock col-xs-4 col-md-12">
                            <?php
                                if($row["Description"] != "voucher"){ ?>
                                    <div class="pstock"><?php echo $row["StockLevel"]; ?></div>
                                    <div class="prating"><?php echo $row["avgRating"]; ?></div>
                            <?php 
                                }else{
                                    echo "<div class='filler'></div>";
                                } ?>
                                </div>
                            </div>                        
                        </div>
                    </div>
            <?php       
                }
            }else{
                echo "EMPTY";
            }
        }else if($customerId == "staff"){// if showing staff page products (layout different)
            $result = $conn->query($stmnt);

            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){ 
                    $smRating = $row["avgRating"]?>
                    <div id="ip_<?php echo $row["BarCode"]; ?>" class="invproduct col-md-12 col-sm-6 col-xs-12">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="icore col-md-2 col-sm-12">
                                    <div class="pbarcode"><?php echo $row["BarCode"]; ?></div>
                                    <div class="pprice"><span class="currency">R </span><?php echo $row["Price"]; ?></div> 
                                </div>
                                <div class="ispecial col-md-2 col-sm-12">
                                    <div class="pspecial">
                                        <?php
                                            if($row["SpecialPrice"] != null){
                                                echo "<span class='currency'>R </span>";
                                                echo $row["SpecialPrice"]; 
                                        ?>
                                    </div>
                                    <div class="pspecialdate">
                                        <?php 
                                                echo date("d/m/Y", strtotime($row["SpecialEnd"])); 
                                            } 
                                        ?>
                                    </div>
                                </div>
                                <div class="istock col-md-2 col-sm-12">
                                    <div class="pstock">
                                        <?php 
                                            if($row["Description"] != "voucher"){ 
                                                echo $row["StockLevel"]; 
                                        ?>
                                    </div>
                                    <div class="psold">
                                        <?php 
                                                echo $row["TotalSold"];
                                            } 
                                        ?>
                                    </div>
                                </div>
                                <div class="inamcat col-md-3 col-sm-5">
                                    <div class="pname"><?php echo $row["Name"]; ?></div>
                                    <div class="pcat"><?php echo $row["CatID"]; ?></div>
                                </div>
                                <div class="irate col-md-2 col-sm-12">
                                    <span class="rrating">
                                        <span class="rratingfld"><?php echo round($smRating,1); ?></span>
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
                                            }           ?>
                                            
                                        </div>
                                    </span>
                                </div>
                                <div class="iselect col-md-1 col-sm-12">
                                    <input type="checkbox" class="itemchk" value="<?php echo $row["BarCode"]; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
               <?php }
            }else{
                echo "EMPTY";
            }
        }
        $conn->close();
    }
?>

