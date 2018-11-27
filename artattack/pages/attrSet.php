<?php
    if(isset($_POST["iterator"])){
        $indiv = $_POST["iterator"];
    }else{
        $indiv = 0;
    }

require "incMySQLConnect.php";

    if($_SESSION["connected"]){
?>

<div id="pattr<?php echo $indiv; ?>">
    <div class="pattrnd">
        <label class="formlabels">Attribute Name&#58;</label>
        <select class="pattrnamefld">
            <option value="null" selected>NULL</option>
<?php
        //get all attributes
        $stmnt = "SELECT * FROM `attribute`";
        $result = $conn->query($stmnt);

        if($result->num_rows > 0){

            while($row = $result->fetch_assoc()){
                echo "<option value='" . $row["AttributeID"] . "'>" . $row["Name"] . "</option>";
            }
        }
?>
        </select>
        <span class="pattrnameerr formerrors"></span>

    </div>
    <div class="pattrau">
        <span><h4>Quantitative</h4></span>
        <label class="formlabels">Amount&#58;</label>
        <input type="number" class="pattramntfld" 
               min="1" max="1000"
               onchange="validateAttrAmount(this,$(this).next('span'))">
        <span class="pattramnterr formerrors"></span>
        <label class="formlabels">Unit Symbol&#58;</label>
        <select class="pattrunitfld">
            <option value="null" selected>NULL</option>
<?php
        //get all units
        $stmnt = "SELECT * FROM `unit`";
        $result = $conn->query($stmnt);
        if($result->num_rows > 0){

            while($row = $result->fetch_assoc()){
                echo "<option value='" . $row["UnitID"] . "'>" . $row["Symbol"] . "</option>";
            }
        }
?>
        </select>
        <span class="pattruniterr formerrors"></span>
    </div>
    <div class="pattradj">
        <span><h4>Qualitative</h4></span>
        <label class="formlabels">Adjective&#58;</label>
         <select class="pattradjfld">
            <option value="null" selected>NULL</option>
<?php
        //get all adjectives
        $stmnt = "SELECT * FROM `wordamount`";
        $result = $conn->query($stmnt);
        if($result->num_rows > 0){

            while($row = $result->fetch_assoc()){
                echo "<option value='" . $row["WAmountID"] . "'>" . $row["Name"] . "</option>";
            }
        }
?>
        </select>
        <span class="pattradjerr formerrors"></span>

    </div>
    <button type="button" onclick="removeAttr(<?php echo $indiv; ?>)" class="removeattrbtn">Remove Attribute</button>
</div>
<?php
        $conn->close();
    }else{
        echo "Database connection failed.";
    }
    
?>
 
