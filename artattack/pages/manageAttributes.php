<?php
    $dbconPath = "incMySQLConnect.php";
?>
<table class="xtratable">
    <caption>Attributes denote a characteristic of a product e.g. length, weight, hardness</caption>
    <tr>
        <th>Attribute Name</th><th>Attribute Description</th><th>Update</th><th>Insert</th><th>Delete</th>
    </tr>
<?php
    require $dbconPath;

    if($_SESSION["connected"]){

        $stmnt = "SELECT * FROM `attribute`";
        $result = $conn->query($stmnt);
        if($result->num_rows > 0){

            while($row = $result->fetch_assoc()){ 
?>
    <tr>
        
        <td >
            <input class="attrnamecol"
               type="text" 
               size="30" 
               required
               maxlength="30"  
               pattern="^[A-Z][A-z]+(\s?[A-z]+)+$"
               onchange="validateAttrName(this,$(this).next('span')); setChanged(this);"
                value="<?php echo $row["Name"]; ?>">
            <span class="attrnameerr tableerrors"></span>
        </td>
        <td >
            <input class="attrdesccol"
               type="text" 
               size="60" 
               maxlength="60" 
               onchange="validateAttrDesc(this,$(this).next('span')); setChanged(this);"
                value="<?php echo $row["Description"]?>">
            <span class="attrdescerr tableerrors"></span>
        </td>
        <td><input class="updatechk" type="checkbox" value="<?php echo $row["AttributeID"]; ?>"></td>
        <td><input class="insertchk" type="checkbox"></td>
        <td><input class="deletechk" type="checkbox" value="<?php echo $row["AttributeID"]; ?>"></td>
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
            
            var statement = "DELETE * FROM `attribute` WHERE AttributeID IN (" + deleteList + ")";
        
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
            //for checkbox parent's(td) parent (tr)
            valuesList += "('" + $(this).find(".attrnamecol").val() + "','" + $(this).find(".attrdesccol").val() + "'),";
        });//get array of rows marked for insertion
        
        
        if(valuesList != ""){
            valuesList = valuesList.substr(0,valuesList.length - 1);
            var statement = "INSERT INTO `attribute` (`Name`, `Description`) VALUES (" + valuesList + ")";
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
            var name = $(this).find(".attrnamecol").val();
            var desc = $(this).find(".attrdesccol").val();
            var id = $(this).find(".updatechk").val();
            
            var statement = "UPDATE `attribute` SET Name = '" + name + "', Description = '" + desc + "' WHERE AttributeID = " + id;
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
        $.post("makeTableRow.php",{tablename: "attribute"},function(data){
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