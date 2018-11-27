<?php
    $dbconPath = "incMySQLConnect.php";
?>
<table class="xtratable">

    <tr>
        <th>Name</th><th>Parent Brand</th><th>Update</th><th>Insert</th><th>Delete</th>
    </tr>
<?php
    require $dbconPath;

    if($_SESSION["connected"]){
        
        //make list of all brands
        $stmnt = "SELECT * FROM `brand`";
        $result = $conn->query($stmnt);
        if($result->num_rows > 0){
            $brandList = array();
            while($row = $result->fetch_assoc()){
                array_push($brandList,$row);
            }
        }    
        
        $stmnt = "SELECT * FROM `myrange`";
        $result = $conn->query($stmnt);
        if($result->num_rows > 0){

            while($row = $result->fetch_assoc()){ 
?>
    <tr>
        <td>
            <input class="rangecol"
                type="text" 
                   required
                size="30" 
                maxlength="30" 
                pattern="^[A-Z][A-z]+(\s?[A-z]+)+$"
                onchange="validateRange(this,$(this).next('span')); setChanged(this);"
                   value="<?php echo $row["Name"]; ?>">
            <span class="rangenameerr tableerrors"></span>
        </td>
        <td>
            <select class="ofbrand"
                    onchange="setChanged(this)">
<?php
     $echoNull = true;
     if(isset($brandList)){
         foreach($brandList as $brand){
             if($brand["BrandID"] == $row["BrandID"]){
                echo "<option value='" . $brand["BrandID"] . "' selected>" . $brand["Name"] . "</option>";
             }else if($row["BrandID"] == null){
                 echo "<option value='null' selected>NULL</option>";
                 $echoNull = false;
             }else{
                echo "<option value='" . $brand["BrandID"] . "'>" . $brand["Name"] . "</option>";
             }
         }
     }
     if($echoNull){
         echo "<option value='null'>NULL</option>";
     }
 ?>
            </select>
        </td>
        <td><input class="updatechk" type="checkbox" value="<?php echo $row["RangeID"]; ?>"></td>
        <td><input class="insertchk" type="checkbox"></td>
        <td><input class="deletechk" type="checkbox" value="<?php echo $row["RangeID"]; ?>"></td>
    </tr>
<?php   
            }
        }else{ 
?>
    <tr>
        <td colspan="5">This table has no data</td>
    </tr>
<?php       
         }
        $conn->close();
    }
?>   
</table>
<script>
    
    function removeRows(){
        
        var deleteList = "";
        var deletions = $(".deletechk:checked").toArray(); //get all items to delete
        //make string for statement
        for(var item in deletions){
            deleteList += deletions[item].value + ","; //add to string
        }
             
        if(deleteList != ""){
            deleteList = deleteList.substr(0,deleteList.length - 1);//remove last comma
            
            var statement = "DELETE * FROM `myrange` WHERE RangeID IN (" + deleteList + ")";
        
            $.post("customAction.php", {action: statement}, function(data){
                if(data.trim() == "SUCCESS"){
                    $("#delstat").text("Deletion of row(s) in the table successful.").css("color","green");     
                }else{
                    $("#delstat").text("Deletion of row(s) in the table failed.").css("color","red");
                }
            });       
        }     
    }
    
    function insertRows(){
        
        var valuesList = "";
        $(".insertchk:checked").parents("tr").each(function(){
            
            valuesList += "('" + $(this).find(".rangecol").val() + "', " + $(this).find(".ofbrand").val() + "),";
        });//get array of rows marked for insertion
        
        
        if(valuesList != ""){
            valuesList = valuesList.substr(0,valuesList.length - 1);
            var statement = "INSERT INTO `myrange` (`Name`, `BrandID`) VALUES (" + valuesList + ")";
            $.post("customAction.php", {action: statement}, function(data){
                if(data.trim() == "SUCCESS"){
                    $("#insstat").text("Insertion of row(s) in the table successful.").css("color", "green");     
                }else{
                    $("#insstat").text("Insertion of row(s) in the table failed.").css("color","red");
                }
            });    
        }
    }
    
    function updateRows(){
        
        var failure = false;
        $(".updatechk:checked").parents("tr").each(function(){
            var name = $(this).find(".rangecol").val();
            var brand = $(this).find(".ofbrand").val();
            var id = $(this).find(".updatechk").val();
            
            var statement = "UPDATE `myrange` SET Name = '" + name + "', BrandID = " + brand + " WHERE RangeID = " + id;
            $.post("customAction.php", {action: statement}, function(data){
                failure = data.trim() != "SUCCESS";                
            });
        });
        if(failure){
            $("#updstat").text("One or more changes failed to be applied.").css("color","text");
        }else{
            $("#updstat").text("All changes were applied successfully.").css("color","text");
        }
     
    }
    
    function addRow(){
        $.post("makeTableRow.php",{tablename: "myrange"},function(data){
            $("table").append(data);   
        });
        
    }               
    
    function processData(){
        removeRows();
        insertRows();
        updateRows();
        $("#cambtns").children().toggle();
    }
    
    function setChanged(item){
        $(item).parents("tr").find(".updatechk").prop("checked", true);
    }
    
    function confirm(){
        contextSwitch("#editor");
        $("#editor").empty();
    }
    
</script>
<span id="delstat"></span>
<span id="insstat"></span>
<span id="updstat"></span>
<div id="cambtns">
    <button type="button" onclick="addRow()">Add New</button>
    <button type="button" onclick="processData()">Commit Changes</button>
    <button type="button" onclick="contextSwitch('#editor'); $('#editor').empty();">Cancel</button>
    <button type="button" style="display:none" onclick="confirm()">Confirm</button>
</div>