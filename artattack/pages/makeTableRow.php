    <tr>
<?php
    $tableName = $_POST["tablename"];
        $dbconPath = "incMySQLConnect.php";
    
    switch($tableName){
        case "attribute":
?>
   
        <td>
            <input class="attrnamecol"
               type="text" 
               size="30" 
                   required
               maxlength="30"  
               pattern="^[A-Z][A-z]+(\s?[A-z]+)+$"
               onchange="validateAttrName(this,$(this).next('span'))">
            <span class="attrnameerr tableerrors"></span>
        </td>
        <td >
            <input class="attrdesccol"
               type="text" 
               size="60" 
               maxlength="60" 
               onchange="validateAttrDesc(this,$(this).next('span'))">
            <span class="attrdescerr tableerrors"></span>
        </td>
        
<?php
            break;
        case "unit":
?>
   
        <td >
            <input class="attrunitcol"
               type="text" 
               size="5"
                   required
               maxlength="5"
               pattern="^[A-z]{0,5}$"
                onchange="validateAttrUnit(this,$(this).next('span'))">    
            <span class="attruniterr tableerrors"></span>
        </td>
<?php
            break;
        case "wordamount":
?>
    
        <td >
            <input class="attradjcol"
                   type="text" 
                   size="20" 
                   required
                   maxlength="20"
                   pattern="^([A-z]+\s?)+$"
                   onchange="validateAttrAdj(this, $(this).next('span'))">
            <span class="attradjerr tableerrors"></span>
        </td>
<?php
            break;
        case "brand":
?>
  
        <td >
            <input class="brandcol"
               type="text" 
               size="30" 
                required
               maxlength="30" 
               pattern="^[A-Z][A-z]+(\s?[A-z]+)+$"
               onchange="validateBrand(this,$(this).next('span'))">
            <span class="brandnameerr tableerrors"></span>
        </td>
        <td >
            <input class="logocol"
                type="file" 
                accept="image/*" 
                onchange="uploadImage(this, $(this).siblings('img'), $(this).next(), false);updateImg(event,$(this).siblings('img')); ">
            <span id="brandlogoerr"></span>
            <img class="imgcol" alt="No logo selected for brand.">
        </td>
<?php
            break;
        case "myrange":
?>
    
        <td >
            <input class="rangecol"
                type="text" 
                size="30" 
                maxlength="30" 
                   required
                pattern="^[A-Z][A-z]+(\s?[A-z]+)+$"
                onchange="validateRange(this,$(this).next('span'))">
            <span class="rangenameerr tableerrors"></span>
        </td>
        <td>
            <select class="ofbrand">
                <option value="null" selected>NULL</option>
<?php
    require $dbconPath;

    if($_SESSION["connected"]){
        
        $stmnt = "SELECT * FROM `brand`";
        $result = $conn->query($stmnt);
        if($result->num_rows > 0){
            
            while($row = $result->fetch_assoc()){
                echo "<option value='" . $row["BrandID"] . "'>" . $row["Name"] . "</option>";
            }
        }
        $conn->close();
    }
?>
            </select>
        </td>
<?php
            break;
        case "colour":
?>
    
        <td >
            <input class="colournamecol"
                type="text"  
                size="30" 
                maxlength="30" 
                pattern="^[A-Z][A-z]+(\s?[A-z]+)+$"
                onchange="validateColourName(this,$(this).next('span')"
                   required>
            <span class="colournameerr tableerrors"></span>
        </td>
        <td >
            <input class="colourcodecol"
                type="text" 
                size="10" 
                maxlength="10" 
                pattern="^(([A-z]|[0-9])+(-|#|\s)?)+([A-z0-9]+#?)*$"
                onchange="validateColourCode(this,$(this).next('span'))">
            <span class="colourcodeerr tableerrors"></span>
        </td>
        <td >
            <input class="colourhexcol" type="color" required>
            <span class="colourhexerr tableerrors"></span>
        </td>
        <td>
            <select class="ofrange">
                <option value="null" selected>NULL</option>
<?php
    require $dbconPath;

    if($_SESSION["connected"]){

        $stmnt = "SELECT * FROM `myrange`";
        $result = $conn->query($stmnt);
        if($result->num_rows > 0){
            
            while($row = $result->fetch_assoc()){
                echo "<option value='" . $row["RangeID"] . "'>" . $row["Name"] . "</option>";
            }
        } 
        $conn->close();
    }
?>
            </select>
        </td>
<?php
            break;
        case "myclass":
?>
   
        <td >
            <input class="catnamecol"
                type="text"  
                size="30" 
                maxlength="30" 
                   required
                onchange="validateCatName(this,$(this).next('span')">
            <span class="catnameerr tableerrors"></span>
        </td>
        <td >
            <input class="catlevelcol"
                type="number"
                min="0"
                max="<?php echo $_POST["maxlevel"]; ?>"
                   required
                   onchange="validateCatLevel(this, $(this).next('span'))">
            <span class="catlevelerr tableerrors"></span>
        </td>
        <td>
            <select class="ofcat">
                <option value="null" selected>NULL</option>
<?php
    require $dbconPath;

    if($_SESSION["connected"]){

        $stmnt = "SELECT * FROM `myclass`";
        $result = $conn->query($stmnt);
        if($result->num_rows > 0){
            
            while($row = $result->fetch_assoc()){ 
                echo "<option value='" . $row["ClassID"] . "'>" . $row["Name"] . "</option>";
            }
        }
        $conn->close();
    }   
?>
            </select>
        </td>
<?php
            break;
        default:
    }
?>
        <td>
            <input class="updatechk" type="checkbox">
        </td>
        <td>
            <input class="insertchk" type="checkbox" checked>
        </td>
        <td>
            <input class="deletechk" type="checkbox"> 
        </td>
    </tr>