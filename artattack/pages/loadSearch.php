<?php 
$root = $_SESSION["root"];
if(isset($_SESSION["searcherid"])){
    $searcherId = "" . $_SESSION["searcherid"];
}else{
    $searcherId = "0";
}

$dbconPath = $root . "/pages/incMySQLConnect.php";
?>

<form name="searchform" id="searchform">
    <button class="btn" type="button" name="searchfiltersubmit" onclick="showProducts(getCat(), 'normal', '<?php echo $root; ?>','<?php echo $searcherId;?>')">
        Search <i class="fas fa-search"></i></button>

    <label for="searchfld">Product Name</label><input type="search" name="searchfld" id="searchfld">
    <fieldset id="sort">
        <legend>SORT</legend>
        <input type="checkbox" name="alphachk" value="alphaon" id="alphachk" checked><label for="alphachk"><i class="far fa-square"></i><i class="far fa-check-square"></i>alphabetically</label>
        <div id="alphagrp">
            <input type="radio" name="alpharad" value="alphaup" id="alphauprad" checked><label for="alphauprad"><i class="far fa-circle"></i><i class="far fa-dot-circle"></i><i class="fas fa-sort-alpha-down"></i></label>
            <input type="radio" name="alpharad" value="alphadown" id="alphadownrad"><label for="alphadownrad"><i class="far fa-circle"></i><i class="far fa-dot-circle"></i><i class="fas fa-sort-alpha-up"></i></label>
        </div>
        <input type="checkbox" name="pricechk" value="priceon" id="pricechk"><label for="pricechk"><i class="far fa-square"></i><i class="far fa-check-square"></i>by price</label>
        <div id="pricegrp">
            <input type="radio" name="pricerad" value="priceup" id="priceuprad" checked><label for="priceuprad"><i class="far fa-circle"></i><i class="far fa-dot-circle"></i><i class="fas fa-sort-numeric-down"></i></label>
            <input type="radio" name="pricerad" value="pricedown" id="pricedownrad"><label for="pricedownrad"><i class="far fa-circle"></i><i class="far fa-dot-circle"></i><i class="fas fa-sort-numeric-up"></i></label>
        </div>
        <input type="checkbox" name="ratechk" value="rateon" id="ratechk"><label for="ratechk"><i class="far fa-square"></i><i class="far fa-check-square"></i>by rating</label>
        <div id="rategrp">
            <input type="radio" name="raterad" value="rateup" id="rateuprad" checked><label for="rateuprad"><i class="far fa-circle"></i><i class="far fa-dot-circle"></i><i class="fas fa-sort-numeric-down"></i></label>
            <input type="radio" name="raterad" value="ratedown" id="ratedownrad"><label for="ratedownrad"><i class="far fa-circle"></i><i class="far fa-dot-circle"></i><i class="fas fa-sort-numeric-up"></i></label>
        </div>
<?php if($searcherId == "staff"){ ?>
        <input type="checkbox" name="sortchk" value="saleson" id="saleschk"><label for="saleschk"><i class="far fa-square"></i><i class="far fa-check-square"></i>number of sales</label>
        <div id="salegrp">
            <input type="radio" name="salesrad" value="salesup" id="salesuprad" checked><label for="salesuprad"><i class="far fa-circle"></i><i class="far fa-dot-circle"></i><i class="fas fa-sort-numeric-down"></i></label>
            <input type="radio" name="salesrad" value="salesdown" id="salesdownrad"><label for="salesdownrad"><i class="far fa-circle"></i><i class="far fa-dot-circle"></i><i class="fas fa-sort-numeric-up"></i></label>
        </div>
<?php   } ?>
    </fieldset>
    <fieldset id="filter">
        <legend>FILTER</legend>
            <?php
                require $dbconPath;
                $pricemax = 10000;
                $pricemin = 0;

                if($_SESSION["connected"]){

                    $result = $conn->query("SELECT MAX(Price) AS maxPrice, MIN(Price) AS minPrice FROM `product`");

                    if($result->num_rows > 0){
                        $row = $result->fetch_assoc();
                        $pricemax = $row["maxPrice"];
                        $pricemin = $row["minPrice"];
                    }
                    $conn->close();
                }
                $average = floor(($pricemax + $pricemin) / 2);
            ?>
        <input type="checkbox" name="pricerangechk" value="pricerangeon" id="pricerangechk"><label for="pricerangechk"><i class="far fa-square"></i><i class="far fa-check-square"></i>price between</label>
        <div id="pricerangegrp">
            <span class="currency">R </span><input type="number" name="pricerangemin" id="pricerangeminfld" <?php echo "min= $pricemin value= $pricemin"; ?> onchange="setMinOnMax(this)"><label for="pricerangeminfld">min</label>
            <span>and</span>
            <span class="currency">R </span><input type="number" name="pricerangemax" id="pricerangemaxfld" <?php echo "max= $pricemax value= $pricemax"; ?> onchange="setMaxOnMin(this)"><label for="pricerangemaxfld">max</label>
        </div>
        <script>//limit price fields
            function setMaxOnMin(maxfld){ 
                var newMax = maxfld.value;
                $("#pricerangeminfld").attr("max", newMax);
            }

            function setMinOnMax(minfld){
                var newMin = minfld.value;
                $("#pricerangemaxfld").attr("min", newMin);
            }
        </script>
        <input type="checkbox" name="stockchk" value="stockon" id="stockchk" ><label for="stockchk"><i class="far fa-square"></i><i class="far fa-check-square"></i>stock</label>
        <div id="stockgrp">
            <input type="radio" name="stockrad" value="stockall" id="stockallrad" checked><label for="stockallrad"><i class="far fa-circle"></i><i class="far fa-dot-circle"></i>all stock</label>
            <input type="radio" name="stockrad" value="stockin" id="stockinrad"><label for="stockinrad"><i class="far fa-circle"></i><i class="far fa-dot-circle"></i>available only</label>
<?php   if($searcherId == "staff"){ ?>
            <input type="radio" name="stockrad" value="stockout" id="stockoutrad"><label for="stockoutrad"><i class="far fa-circle"></i><i class="far fa-dot-circle"></i>out of stock</label>
<?php   } ?>
        </div>
<?php if($searcherId != "staff"){ //only customers will use brand, range and colour filtering ?>
        <fieldset id="brands">
            <legend>Brands</legend>
                <?php
                    require $dbconPath;

                    if($_SESSION["connected"]){
                        //get all brands for which there are products
                        $result = $conn->query("SELECT BrandID, Name 
                                                FROM `brand` 
                                                WHERE BrandID IN (  SELECT DISTINCT BrandID 
                                                                    FROM `product`)
                                                ORDER BY Name");

                        if($result->num_rows > 0){
                            while($row = $result->fetch_assoc()){
                                echo "<div>";
                                echo "<input type=\"checkbox\" id=\"brandchk" . $row["BrandID"]. "\" value=" . $row["BrandID"] . " onchange=\"refreshRanges(this)\">";
                                echo "<label for=\"brandchk" . $row["BrandID"] . "\"><i class='far fa-square'></i><i class='far fa-check-square'></i>" . $row["Name"] . "</label>";
                                echo "</div>";
                            }
                        }else{
                            echo "EMPTY";
                        }
                    }
                    $conn->close();
                ?>
        </fieldset>
        <fieldset id="ranges">
            <legend>Ranges</legend>
                <?php
                    require $dbconPath;

                    if($_SESSION["connected"]){
                        //get all ranges for which there are products
                        $result = $conn->query("SELECT DISTINCT `product`.`RangeID`, `myrange`.`Name` AS RName, `product`.`BrandID`, `brand`.`Name` AS BName
                                        FROM `product`
                                        JOIN `myrange` ON `product`.`RangeID` = `myrange`.`RangeID`
                                        JOIN `brand` ON `product`.`BrandID` = `brand`.`BrandID`
                                        ORDER BY BName, RName");

                        if($result->num_rows > 0){
                            $currentBrand = null;
                            $closeBrand = false;
                            while($row = $result->fetch_assoc()){
                                if($currentBrand != $row["BrandID"]){//when listing a new brand's ranges
                                    $currentBrand = $row["BrandID"];//change brands
                                    if($closeBrand){
                                        echo "</div>";
                                        $closeBrand = false;
                                    }
                                    echo "<div id=\"spbrand" . $row["BrandID"] . "\"><span class=\"rgrptitle\">" . $row["BName"] . "</span>";
                                    $closeBrand = true;
                                }
                                echo "<input type=\"checkbox\" id=\"rangechk" . $row["RangeID"]. "\" value=" . $row["RangeID"] . " onchange=\"refreshColours(this)\">";
                                echo "<label for=\"rangechk" . $row["RangeID"] . "\"><i class='far fa-square'></i><i class='far fa-check-square'></i>" . $row["RName"] . "</label>";

                            }
                            echo "</div>";
                        }else{
                            echo "EMPTY";
                        }
                    }
                    $conn->close();
                ?>
        </fieldset>
        <fieldset id="colours">
            <legend>Colours</legend>
                <?php
                    require $dbconPath;

                    if($_SESSION["connected"]){
                        //get all colours for which there are products
                        $result = $conn->query("SELECT DISTINCT `product`.`ColourID`, `colour`.`Name` AS CName, `product`.`RangeID`, `myrange`.`Name` AS RName
                                                FROM `product`
                                                JOIN `colour` ON `product`.`ColourID` = `colour`.`ColourID`
                                                JOIN `myrange` ON `product`.`RangeID` = `myrange`.`RangeID`
                                                ORDER BY RName, CName");

                        if($result->num_rows > 0){
                            $currentRange = null;
                            $closeRange = false;
                            while($row = $result->fetch_assoc()){
                                if($currentRange != $row["RangeID"]){//when listing  a new range's colours
                                    $currentRange = $row["RangeID"];//change ranges
                                    if($closeRange){
                                        echo "</div>";
                                        $closeRange = false;
                                    }
                                    echo "<div id=\"sprange" . $row["RangeID"] . "\"><span class=\"cgrptitle\">" . $row["RName"] . "</span>";
                                    $closeRange = true;
                                }
                                echo "<input type=\"checkbox\" id=\"colourchk" . $row["ColourID"]. "\" value=" . $row["ColourID"] . ">";
                                echo "<label for=\"colourchk" . $row["ColourID"] . "\"><i class='far fa-square'></i><i class='far fa-check-square'></i>" . $row["CName"] . "</label>";

                            }
                            echo "</div>";
                        }else{
                            echo "EMPTY";
                        }
                    }
                    $conn->close();
                ?>
        </fieldset>
<?php } ?>
    </fieldset>
        <button class="btn" type="button" name="searchfiltersubmit2"  onclick="showProducts(getCat(), 'normal', '<?php echo $root; ?>','<?php echo $searcherId; ?>')">
        Search <i class="fas fa-search"></i></button>
<?php if($searcherId != "staff"){ ?>
    <script>
        //initially hide all range and colour selector
        $("#ranges").children("div").hide();
        $("#colours").children("div").hide();

        function refreshRanges(brandbox){//show ranges for selected brands
            var selectedRange = "#spbrand" + brandbox.value;
            if(brandbox.checked){
                $(selectedRange).show();
            }else{
                $(selectedRange).hide();   
            }
            var rangeboxes = $(selectedRange).children(":checkbox").toArray();
            var i;
            for(i = 0; i < rangeboxes.length; i++){
                if(brandbox.checked && rangeboxes[i].checked){//parent selected and this child
                    rangeboxes[i].checked = true;//child now still selected
                }else{
                    rangeboxes[i].checked = false;//otherwise child deselected or remains so
                    refreshColours(rangeboxes[i]);//update colour children
                }
            }
        }

        function refreshColours(rangebox){//show colours for selected ranges
            var selectedColour = "#sprange" + rangebox.value;
            if(rangebox.checked){
                $(selectedColour).show();
            }else{
                $(selectedColour).hide();
            }
            var colourboxes = $(selectedColour).children(":checkbox").toArray();
            var i;
            for(i = 0; i < colourboxes.length; i++){
                if(rangebox.checked && colourboxes[i].checked){
                    colourboxes[i].checked = true;
                }else{
                    colourboxes[i].checked = false;
                }
            }
        }
    </script>
<?php } ?>
</form>