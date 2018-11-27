<?php
    $dbconPath = "incMySQLConnect.php";
?>
<table class="xtratable">

    <tr>
        <th>Name</th><th>Code</th><th>Hex Colour</th><th>Parent Range</th><th>Update</th><th>Insert</th><th>Delete</th>
    </tr>
<?php
    require $dbconPath;

    if($_SESSION["connected"]){

        //make list of all ranges
        $stmnt = "SELECT * FROM `myrange`";
        $result = $conn->query($stmnt);
        if($result->num_rows > 0){
            $rangeList = array();
            while($row = $result->fetch_assoc()){
                array_push($rangeList,$row);
            }
        }  
        
        $stmnt = "SELECT * FROM `colour`";
        $result = $conn->query($stmnt);
        if($result->num_rows > 0){

            while($row = $result->fetch_assoc()){ 
?>
    <tr>
       <td >
            <input class="colournamecol"
                type="text"  
                size="30" 
                   required
                maxlength="30" 
                pattern="^[A-Z][A-z]+(\s?[A-z]+)+$"
                onchange="validateColourName(this,$(this).next('span'); setChanged(this);"
                   value="<?php echo $row["Name"]; ?>">
            <span class="colournameerr tableerrors"></span>
        </td>
        <td >
            <input class="colourcodecol"
                type="text" 
                size="10" 
                maxlength="10" 
                pattern="^(([A-z]|[0-9])+(-|#|\s)?)+([A-z0-9]+#?)*$"
                onchange="validateColourCode(this,$(this).next('span')); setChanged(this);"
                   value="<?php echo $row["ColourCode"]; ?>">
            <span class="colourcodeerr tableerrors"></span>
        </td>
        <td >
            <input class="colourhexcol" 
                   type="color"
                   required
                   onchange="setChanged(this)"
                   value="#<?php echo $row["ColourHex"]; ?>">
            <span class="colourhexerr tableerrors"></span>
        </td>
        <td>
            <select class="ofrange"
                    onchange="setChanged(this)">
<?php
     $echoNull = true;
     if(isset($rangeList)){
         foreach($rangeList as $range){
             if($range["RangeID"] == $row["RangeID"]){
                echo "<option value='" . $range["RangeID"] . "' selected>" . $range["Name"] . "</option>";
             }else if($row["RangeID"] == null){
                 echo "<option value='null' selected>NULL</option>";
                 $echoNull = false;
             }else{
                echo "<option value='" . $range["RangeID"] . "'>" . $range["Name"] . "</option>";
             }
         }
     }
     if($echoNull){
          echo "<option value='null'>NULL</option>";
     }
 ?>
            </select>
        </td>
        <td><input class="updatechk" type="checkbox" value="<?php echo $row["ColourID"]; ?>"></td>
        <td><input class="insertchk" type="checkbox"></td>
        <td><input class="deletechk" type="checkbox" value="<?php echo $row["ColourID"]; ?>"></td>
    </tr>
<?php   
            }
        }else{ 
?>
    <tr>
        <td colspan="7">This table has no data</td>
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
            
            var statement = "DELETE * FROM `colour` WHERE ColourID IN (" + deleteList + ")";
        
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
            
            valuesList += "('" + $(this).find(".colournamecol").val() + "','" + $(this).find(".colourcodecol").val() + "','" + $(this).find(".colourhexcol").val() + "', " + $(this).find(".ofrange").val() + "),";
        });//get array of rows marked for insertion
        
        
        if(valuesList != ""){
            valuesList = valuesList.substr(0,valuesList.length - 1);
            var statement = "INSERT INTO `colour` (`Name`, `ColourCode`, `ColourHex`, `RangeID`) VALUES (" + valuesList + ")";
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
            var name = $(this).find(".colournamecol").val();
            var code = $(this).find(".colourcodecol").val();
            var hex = $(this).find(".colourhexcol").val();
            var range = $(this).find(".ofrange").val();
            var id = $(this).find(".updatechk").val();
            
            var statement = "UPDATE `colour` SET Name = '" + name + "', ColourCode = '" + code + "', ColourHex = '" + hex + "', RangeID = " + range + " WHERE ColourID = " + id;
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
        $.post("makeTableRow.php",{tablename: "colour"},function(data){
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