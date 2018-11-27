<?php 
    session_start();
    $dbconPath = "incMySQLConnect.php";
    $root = "..";
    $_SESSION["colourRange"] = null;
    $_SESSION["colourProdName"] = null;

    $instruction = $_POST["ins"]; //editproduct, insertproduct, addcolour, deleteproduct
        $thisProduct = "";
    if($instruction != "insertproduct"){
        //only set product id if one was sent, not in the case of a new product
        $thisProduct = $_POST["productid"][0];
    }    

    if($instruction == "deleteproduct"){
        
        //make string of all product ids for queries
        $productList = "";
        $products = $_POST["productid"];
        for($i = 0; $i < count($products); $i++){
            $productList .= "'$products[$i]', ";
        }
        $productList = rtrim($productList, ", ");
        
        //does not remove product data, as other tables depend on this data, rather sets as not for sale
        require $dbconPath;

        if($_SESSION["connected"]){

            $stmnt = "UPDATE `product` SET ForSale = FALSE WHERE BarCode IN ($productList)";
            if($conn->query($stmnt)){
                    echo "SUCCESS";
                }else{
                    echo "FAILURE";
                }
                $conn->close();      
        }else{
            echo "FAILURE";
        }

    }else if($instruction == "editproduct" || $instruction == "addcolour"){
        //gather data to fill in form with existing data

        require $dbconPath;

        if($_SESSION["connected"]){
            $stmnt = "SELECT 
                        `productdetails`.*,
                        `colour`.ColourID,
                        `colour`.Name AS cName,
                        `colour`.ColourCode AS cCode,
                        `colour`.ColourHex AS cHex 
                        FROM `productdetails` 
                        LEFT JOIN `colour` ON `productdetails`.ColourID = `colour`.ColourID 
                        WHERE BarCode = '$thisProduct'";

            $result = $conn->query($stmnt);

            if($result->num_rows > 0){
                $basicData = $result->fetch_assoc();

            }
            $conn->close();
        }
    }

    $notNew = isset($basicData);
    
?>
<script type=text/javascript>

    function resetPage(){
        var origAction = "<?php echo $instruction; ?>";
        mngProd(origAction);
        contextSwitch("#editor");
    }
    
    function doWithNewColour(){
<?php
     if($instruction == "editproduct"){
         echo "editProduct();";
     }else if($instruction != "deleteproduct"){
         echo "insertProduct();";
     }
?>    
        alert("Colour added. You may now make changes and add another.");
    }
    
    //makes higher level category options reflect changes in lower level ones
    function refreshListsInEditor(targetOptions, myLevel){
        
        $("#pcat").children().css("display","none");//hide all select lists
        var i;
        for(i = 0; i <= Number(myLevel); i++){//show all select lists below and this one
            var selectId = "#pcatfld" + i;
            $("#pcat").find(selectId).css("display","inline-flex");
        }
        var selectId = "#pcatfld" + (Number(myLevel) + 1);
        if($("#pcat").find(selectId).children(targetOptions).length > 0){
            //if there are child options to select from for this super category
            $("#pcat").find(selectId).css("display","inline-flex");
            $("#pcat").find(selectId).children().css("display","none");//hide all options in new select list
            $("#pcat").find(selectId).children(".nosuper").css("display","block"); //show null options
            $("#pcat").find(selectId).children().prop("selected",false);//deselect all options
            $("#pcat").find(selectId).children(targetOptions).css("display","block");//show only the options that are children of selected value in previous list
            $("#pcat").find(selectId).children(targetOptions).first().prop("selected",true);//sets first viable option as being selected
            $("#pcat").find(selectId).val($(selectId).children(targetOptions).first().val());//update list selected display

        }else{
            $("#pcat").find(selectId).children().css("display","block");
            $("#pcat").find(selectId).children(".nosuper").prop("selected",true);
        }
        
    }
        
    function validateProduct(){
        
        var validBarCode = validateBarCode(productform.pbarcodefld,"#pbarcodeerr","<?php echo $thisProduct; ?>");
        var validPrice = validatePrice(productform.ppricefld, "#ppriceerr");
        var validPName = validateProductName(productform.pnamefld, "#pnameerr");
        var validDesc = validateProductDesc(productform.pdescfld, "#pdescerr");
        
        return validBarCode && validPrice && validPName && validDesc;
        
    }
    
   /* function isNew(statement){
        var isnew = false;
        $.post("customQuery.php",{query: statement, resultkind: "hasempty"}, function(data){
            var value = JSON.parse(data);
            isnew = value == "EMPTY";
        });
        return isnew;
    }*/
<?php if($instruction == "insertproduct" || $instruction == "addcolour"){  ?> 
    function insertProduct(){
        if(validateProduct()){
            var bc = productform.pbarcodefld.value;
            //check if product barcode is unique
            $.post("customQuery.php",{query: "SELECT BarCode FROM `product` WHERE BarCode = '" + bc + "'", resultkind: "hasempty"}, function(data){
                var value = JSON.parse(data);
                    if(value == "EMPTY"){
                        var cont;
                    var imgpath = productform.pimgfld.value;
                    if($("#imgprev").attr("src") != null){
                        imgpath = "/images/" + imgpath;
                        cont = uploadImage($("[name=pimgfld]"), $("#pimgprev"), $("#pimgerr"), true);
                    }else{
                        imgpath = "";
                        cont = false;
                    }

                    $.post("customAction.php",{action: "INSERT INTO `product` (`BarCode`, `Price`, `Name`, `Description`, `Image`, `BrandID`, `RangeID`, `ColourID`) VALUES ('" + bc + "', " + productform.ppricefld.value + ", '" + productform.pnamefld.value + "', '" + productform.pdescfld.value.trim() + "', '" + imgpath + "', " + $("#pbrandfld").val() + ", " + $("#prangefld").val() + ", " + $("#pcolouridfld").text() + ")"}, function(data){
                        if(data.trim() == "SUCCESS"){
                            insertCategory(bc);
                            insertAttributes(bc);        
                        }else{
                            alert("Product was not inserted.\nPlease try again.");
                        }
                    });
                    
                }else{
                     $("#pbarcodeerr").text("This barcode is already in use. Please use a new one.");
                }
            });
        }
    }
    
    function refreshColours(rangeid){
        
        $.post("colourSelector.php", {rangeid: rangeid}, function(data){
            $("#pcolour").html(data); 
        });       
    }
<?php }else if($instruction == "editproduct"){ ?>
    
    function editProduct(){
        if(validateProduct()){
            var bc = productform.pbarcodefld.value;
            $.post("customQuery.php",{query: "SELECT BarCode FROM `product` WHERE BarCode = '" + bc + "'", resultkind: "hasempty"}, function(data){
                var value = JSON.parse(data);
                if(value == "HAS"){
                    $.post("customAction.php", {action: "UPDATE `product` SET `BarCode` = '" + bc + "' WHERE `BarCode` = '<?php echo $thisProduct; ?>'"}, function(data){
                       if(data.trim() == "SUCCESS"){
                           editRest();
                       }else{
                           alert("Product was not updated.\nPlease try again.");
                       }
                    });
                }else{
                    editRest();
                }
            });
        }
    }
    
    function editRest(){
        var imgpath = productform.pimgfld.value;
        if(imgpath != null){
            imgpath = "/images/" + imgpath;
        }else{
            imgpath = "<?php $basicData["Image"]; ?>";
        }

        $.post("customAction.php",{action: "UPDATE `product` SET `Price` = " + productform.ppricefld.value + ", `Name` = '" + productform.pnamefld.value + "', `Description` = '" + productform.pdescfld.value.trim() + "', `Image` = '" + imgpath + "', BrandID = " + $("#pbrandfld").val() + ", RangeID = " + $("#prangefld").val() + ", ColourID = " + $("#pcolouridfld").text() + " WHERE `BarCode` = '" + bc + "'"}, function(data){
            if(data.trim() == "SUCCESS"){
                $.post("customAction.php",{action: "DELETE * FROM `attributemeasurement` WHERE `BarCode` = '<?php echo $thisProduct; ?>'"},function(data){
                    if(data.trim() == "SUCCESS"){
                        insertCategory(bc);
                        insertAttributes(bc);
                    }
                });        
            }else{
                alert("Product was not updated.\nPlease try again.");
            }
        });
    }
    
    function refreshColours(rangeid){
        
        $.post("colourSelector.php", {productname: "<?php echo $basicData["Name"]; ?>", rangeid: rangeid}, function(data){
            $("#pcolour").html(data); 
        });       
    }
<?php } ?>    
    
    function insertCategory(barcode){
        var catID = null;
        var temp;
        var catSelects = $("#pcat select").toArray();
        
        for(var cat in catSelects){
            temp = catSelects[cat].value;
            if(temp == "null"){
                break;
            }else{
                catID = temp;
            }
        }
        
        if(catID != null){
            $.post("customAction.php",{action: "INSERT INTO `classification` (`ClassID`, `BarCode`) VALUES (" + catID + ", '" + barcode + "')"}, function(data){
               if(data.trim != "SUCCESS"){
                   alert("Category was not added to the database.");
               } 
            });    
        }

    }
    
    function insertAttributes(barcode){
        var insertList = "";
        
        $("#attrcontainer").each(function(){
            
            insertList  += "('" + barcode + "', " 
                            + $(this).find(".pattrnamefld").val() + ", " 
                            + $(this).find(".pattramntfld").val() + ", " 
                            + $(this).find(".pattrunitfld").val() + ", "
                            + $(this).find(".pattradjfld").val() + "),";
        });
        
        if(insertList != ""){
            insertList = insertList.substr(0, insertList.length - 1);
            $.post("customAction.php",{action: "INSERT INTO `attributemeasurement` (`BarCode`,`AttributeID`, `Amount`,`UnitID`,`WAmountID`) VALUES (" + insertList +")"}, function(data){
                if(data.trim != "SUCCESS"){
                    alert("Failed to add attributes to database.");
                }
            });
        }
    }
    
    function removeAttr(attrgrpid){
        //only removes children
        var id = "#pattr" + attrgrpid;
        $(id).children().remove();
    }
    
    function newAttrFields(iter){
        $.post("attrSet.php",{iterator:iter},function(data){
            $("#attrcontainer").append(data); 
        });
        $("[name=addattrbtn]").attr("onclick","newAttrFields(" + ++iter + ")");
    }
    
    
</script>
<form name="productform">
    <fieldset id="editproduct">
<?php
    if($notNew){ ?>
        <legend>Edit 
            <i>
                <?php 
                    echo $basicData["Name"]; 
                ?>
            </i> &#40;
            <span id="bc">
                <?php
                    echo $thisProduct;
                ?>
            </span>&#41;
        </legend>
<?php
    }else{ ?>
        <legend>Add Product</legend>
<?php
    } ?>
        <div id="pbarcode">
            <label class="formlabels">Barcode&#58;</label>
            <input 
<?php
   if($notNew){ 
        echo " value=\"$thisProduct\" ";
   }
?>
            type="text" name="pbarcodefld" required 
            size="13" maxlength="13" pattern="^[0-9]{13}$" 
            onchange="validateBarCode(this,'#pbarcodeerr')">
            <span id="pbarcodeerr" class="formerrors"></span>
        </div>
        <div id="pprice">
            <label class="formlabels">Price&#58;</label><span class="currency">R </span>
            <input 
<?php
   if($notNew){
        echo " value=\"" . $basicData["Price"] . "\" ";    
   }
?>       
            type="text" name="ppricefld" required 
            size="9" max="9" pattern="^\d{1,6}\.\d{2}$" 
            onchange="fixDecimals(this); validatePrice(this,'#ppriceerr')"> 
            <span id="ppriceerr" class="formerrors"></span>
        </div>
        <div id="pname">
            <label class="formlabels">Name&#58;</label>
            <input 
<?php
    if($notNew){
        echo " value=\"" . $basicData["Name"] . "\" ";    
    }
?>       
            type="text" name="pnamefld" required 
            size="50" maxlength="50" pattern="^([0-9]|[A-z])+(\s?([0-9]|[A-z])+)+.?$"
            onchange="validateProductName(this,'#pnameerr')">
            <span id="pnameerr" class="formerrors"></span>
        </div>
        <div id="pdesc">
            <label class="formlabels">Description&#58;</label>
            <textarea name="pdescfld" maxlength="256" rows="5" cols="51"
                      onchange="validateProductDesc(this,'#pdescerr')">
<?php
    if($notNew){
        echo $basicData["Description"];    
    }
?>
            </textarea>
            <span id="pdescerr" class="formerrors"></span>
        </div>
        <div id="pimg">
            <label class="formlabels">Image&#58;</label>
            <input       
            type="file" accept="image/*" name="pimgfld" 
            onchange="uploadImage(this, $('#pimgprev'), $('#pimgerr'), false); updateImg(event, $('#pimgprev'))" required>
            <img id="pimgprev" 
<?php
    if($notNew){
        echo " src=\"" . $root . $basicData["Image"] . "\" ";    
    }
?>     
            alt="No image selected for product.">
            <span id="pimgerr" class="formerrors"></span>
        </div>
        <div id="pcat" class="flexline">
            <label class="formlabels">Category&#58;</label>
            <?php
                require $dbconPath;

                if($_SESSION["connected"]){         
                    
                    $prodCats = array();//holds product category tree
                    $hasSuper = false;
                    if($notNew){ 
                        //get category tree for product starting with child category
                        array_push($prodCats, $basicData["CatID"]);                        
                                                
                        //get rest of classes going up if not already at base class
                        $hasSuper = $prodCats[0] != null;
                        $catIndex = 0;
                        while($hasSuper){
                            $stmnt = "SELECT SuperClass FROM `myclass` WHERE ClassID = " . $prodCats[$catIndex];
                            
                            $result = $conn->query($stmnt);
                            if($result->num_rows > 0){
                                $row = $result->fetch_assoc();
                                array_push($prodCats,$row["SuperClass"]);
                            }
                            $hasSuper = $prodCats[++$catIndex] != null;
                        }
                    }
                    array_pop($prodCats); //remove null from end
                    $tree = array_reverse($prodCats);//reverse to mirror select results
                    
                    $existingCats = array();
                    //get all classes
                    $stmnt = "SELECT * FROM `myclass` ORDER BY ClassLevel, SuperClass";
                    $result = $conn->query($stmnt);
                    
                    
                    if($result->num_rows > 0){
                        $currentLevel = 0; //mirrors index for category tree array
                        echo "<select id=\"pcatfld$currentLevel\" onchange=\"refreshListsInEditor(this.value, $currentLevel)\">";
                        
                        $echoNull = true;;
                        while($row = $result->fetch_assoc()){
                            
                            array_push($existingCats, $row);
                            //change to next level select item if necessary
                            if($row["ClassLevel"] != $currentLevel){
                                
                                $currentLevel = $row["ClassLevel"];
                                if($echoNull){
                                    echo "<option value='null' class='nosuper'>NULL</option>";
                                }
                                $echoNull = true;
                                if(count($tree) < $currentLevel + 1){
                                    echo "</select><select id=\"pcatfld$currentLevel\"  onchange=\"refreshListsInEditor(this.value, $currentLevel)\" style=\"display:none\">";
                                }else{
                                    echo "</select><select id=\"pcatfld$currentLevel\"  onchange=\"refreshListsInEditor(this.value, $currentLevel)\">";
                                }
                            }
                            //add option element ?>
                            
<?php                       
                            if(!$hasSuper && $echoNull){
                                    $echoNull = false;
            ?>
                                <option value='null' selected class='nosuper'>NULL</option>
            <?php
                            }
                            if(count($tree) >= $currentLevel + 1){
                                 
                                if($tree[$currentLevel] == $row["ClassID"]){ 
?>
                                <option selected value=".optsuper<?php echo $row["ClassID"]; ?>" class="optsuper<?php echo $row["SuperClass"]; ?>"><?php echo $row["Name"]; ?></option>
<?php       
                               }else{ 
?>
                                  <option value=".optsuper<?php echo $row["ClassID"]; ?>" class="optsuper<?php echo $row["SuperClass"]; ?>"><?php echo $row["Name"]; ?></option>  
                                    
<?php                          }
                            }else{
                                ?>
                                <option value=".optsuper<?php echo $row["ClassID"]; ?>" class="optsuper<?php echo $row["SuperClass"]; ?>"><?php echo $row["Name"]; ?></option>
                                <?php
                            }
                              
                        }
                        if($echoNull){
                            echo "<option value='null' class='nosuper'>NULL</option></select>";
                        }else{
                                echo "</select>";
                        }
                        
                    }
                    
                    $conn->close();
                }
            ?>
            <span id="pcaterr" class="formerrors">
                <?php 
                    if(count($tree) == 0){ 
                        echo "This product has no category yet! Please select one."; 
                    }
                ?>
            </span>
        </div>
        <div id="pbrand">
            <label class="formlabels">Brand&#58;</label>
            <select id="pbrandfld">
<?php
    require $dbconPath;

    $echoNull = true;
    
    if($_SESSION["connected"]){
        
        $stmnt = "SELECT * FROM `brand`";
        $result = $conn->query($stmnt);
        
        
        if($result->num_rows > 0){
            
            while($row = $result->fetch_assoc()){
                if($notNew){
                    if($basicData["BrandID"] == $row["BrandID"]){
                        echo "<option value='" . $row["BrandID"] . "' selected>" . $row["Name"] . "</option>";
                    }else if($basicData["BrandID"] == null){
                        echo "<option value='null' selected>NULL</option>";
                        $echoNull = false;
                    }else{
                        echo "<option value='" . $row["BrandID"] . "'>" . $row["Name"] . "</option>";
                    }
                 }else{
                    echo "<option value='" . $row["BrandID"] . "'>" . $row["Name"] . "</option>";
                 }
            }
        }
        $conn->close();
    }
    if($echoNull){
         echo "<option value='null'>NULL</option>";
    }
?>
            </select>
            <span id="pbranderr" class="formerrors"></span>
        </div>
        <div id="prange">
            <label class="formlabels">Range&#58;</label>
            <select id="prangefld" onchange="refreshColours(this.value)">
<?php

    require $dbconPath;

    $echoNull = true;
    
    if($_SESSION["connected"]){
        
        $stmnt = "SELECT * FROM `myrange`";
        $result = $conn->query($stmnt);
        
        
        if($result->num_rows > 0){
            
            while($row = $result->fetch_assoc()){
                if($notNew){
                    if($basicData["RangeID"] == $row["RangeID"]){
                        echo "<option value='" . $row["RangeID"] . "' selected>" . $row["Name"] . "</option>";
                    }else if($basicData["RangeID"] == null){
                        echo "<option value='null' selected>NULL</option>";
                        $echoNull = false;
                    }else{
                        echo "<option value='" . $row["RangeID"] . "'>" . $row["Name"] . "</option>";
                     }
                 }else{
                    echo "<option value='" . $row["RangeID"] . "'>" . $row["Name"] . "</option>";
                 }
            }
        }
        $conn->close();
    }
    if($echoNull){
         echo "<option value='null'>NULL</option>";
    }
?>
            </select>
            <span id="prangeerr" class="formerrors"></span>
        </div>
    </fieldset>
    <fieldset>
        <legend>Colour</legend>
        <div id="pcolour">            
            <?php 
                if($notNew){
                    $_SESSION["colourRange"] = $basicData["RangeID"];
                    $_SESSION["colourProdName"] = $basicData["Name"];
                }
            include "colourSelector.php";
            ?>
        </div>
        <div id="pcolourdetails">
            <label class="formlabels">Selected Colour&#58;</label>
            <span id="pcolouridfld"><?php if($notNew){echo $basicData["ColourID"];}else{echo "null";} ?></span>
            <label class="formlabels">Colour name&#58;</label>
            <span id="pcolournamefld"><?php if($notNew){echo $basicData["cName"];}else{echo "null";} ?></span>
            <label class="formlabels">Colour code&#58;</label>
            <span id="pcolourcodefld"><?php if($notNew){echo $basicData["cCode"];}else{echo "null";} ?></span>
            <div id="pcolourprev" <?php if($notNew){echo "style='background-color:#" . $basicData["cCode"] . "'";}?>></div>
        </div>
    </fieldset>
    <fieldset id="advanceddetails">
        <legend>Detailed Attributes</legend>
            <div id="attrcontainer">
<?php
$iterator = 0;
if($notNew){
    require $dbconPath;

    if($_SESSION["connected"]){

        $attrArr = array();
        $unitArr = array();
        $adjArr = array();

        //get all attributes
        $stmnt = "SELECT * FROM `attribute`";
        $result = $conn->query($stmnt);
        if($result->num_rows > 0){

            while($row = $result->fetch_assoc()){
                array_push($attrArr, $row);
            }
        }

        //get all units
        $stmnt = "SELECT * FROM `unit`";
        $result = $conn->query($stmnt);
        if($result->num_rows > 0){

            while($row = $result->fetch_assoc()){
                array_push($unitArr, $row);
            }
        }

        //get all adjectives
        $stmnt = "SELECT * FROM `wordamount`";
        $result = $conn->query($stmnt);
        if($result->num_rows > 0){

            while($row = $result->fetch_assoc()){
                array_push($adjArr, $row);
            }
        }

        //get attributes of which there are multiple
        $stmnt = "SELECT * FROM `attributemeasurement` WHERE `BarCode` = '$thisProduct'";

        $result = $conn->query($stmnt);
        
        if($result->num_rows > 0){

            while($row = $result->fetch_assoc()){ 
 ?>
                    <div id="pattr<?php echo $iterator; ?>">
                        <div class="pattrnd">
                            <label class="formlabels">Attribute Name&#58;</label>
                                <select class="pattrnamefld">
<?php
     $echoNull = true;
     if(isset($attrArr)){
         foreach($attrArr as $attr){
             if($attr["AttributeID"] == $row["AttributeID"]){
                echo "<option value='" . $attr["AttributeID"] . "' selected>" . $attr["Name"] . "</option>";
             }else if($row["AttributeID"] == null){
                 echo "<option value='null' selected>NULL</option>";
                 $echoNull = false;
             }else{
                echo "<option value='" . $attr["AttributeID"] . "'>" . $attr["Name"] . "</option>";
             }
         }
     }
     if($echoNull){
         echo "<option value='null'>NULL</option>";
     }
 ?>
                                </select>
                            <span class="pattrnameerr formerrors"></span>
                        </div>
                        <div class="pattrau">
                            <span><h4>Quantitative</h4></span>
                            <label class="formlabels">Amount&#58;</label>
                            <input type="number" class="pattramntfld" min="1" max="1000" 
                                   value="<?php if($row["Amount"] == null){echo "";}else{echo $row["Amount"];} ?>"
                                   onchange="validateAttrAmount(this, $(this).next('span'))">
                            <span class="pattramnterr formerrors"></span>
                            <label class="formlabels">Unit Symbol&#58;</label>
                                <select class="pattrunitfld">
<?php
     $echoNull = true;
     if(isset($unitArr)){
         foreach($unitArr as $unit){
             if($unit["UnitID"] == $row["UnitID"]){
                echo "<option value='" . $unit["UnitID"] . "' selected>" . $unit["Symbol"] . "</option>";
             }else if($row["UnitID"] == null){
                 echo "<option value='null' selected>NULL</option>";
                 $echoNull = false;
             }else{
                echo "<option value='" . $unit["UnitID"] . "'>" . $unit["Symbol"] . "</option>";
             }
         }
     }
     if($echoNull){
         echo "<option value='null'>NULL</option>";
     }
 ?>
                                    </select>
                                <span class="pattruniterr formerrors"></span>
                        </div>
                        <div class="pattradj">
                            <span><h4>Qualitative</h4></span>
                            <label class="formlabels">Adjective&#58;</label>
                                <select class="pattradjfld">
<?php
     $echoNull = true;
     if(isset($adjArr)){
         foreach($adjArr as $adj){
             if($adj["WAmountID"] == $row["WAmountID"]){
                echo "<option value='" . $adj["WAmountID"] . "' selected>" . $adj["Name"] . "</option>";
             }else if($row["WAmountID"] == null){
                 echo "<option value='null' selected>NULL</option>";
                 $echoNull = false;
             }else{
                echo "<option value='" . $adj["WAmountID"] . "'>" . $adj["Name"] . "</option>";
             }
         }
     }
     if($echoNull){
         echo "<option value='null'>NULL</option>";
     }
 ?>
                                </select>
                            <span class="pattradjerr formerrors"></span>

                        </div>

                        <button type="button" onclick="removeAttr(<?php echo $iterator; ?>)" class="removeattrbtn">Remove Attribute</button>
                    </div>
<?php   
                ++$iterator; 
            }
        }
        else{
            echo "<span class=\"previewMsg\">This product has no special attributes.</span>";
        }
        $conn->close();
    }
}
?>
        </div>
        <input type="button" name="addattrbtn" value="Add attribute" onclick="newAttrFields(<?php echo $iterator; ?>)">
    </fieldset>
</form>
<button type="button" name="productformsubmit" 
        onclick="
<?php
     if($instruction == "editproduct"){
         echo "editProduct()";
     }else if($instruction != "deleteproduct"){
         echo "insertProduct()";
     }
?>">Submit</button>
<button type="button" name="productformreset" onclick="resetPage()">Reset</button>
<button type="button" name="addcolourbtn" onclick="doWithNewColour()">Submit and add another Colour</button>
    <button type="button" name="productformcancel" onclick="contextSwitch('#editor'); $('#editor').empty();">Cancel</button>