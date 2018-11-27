<?php
    $dbconPath = "incMySQLConnect.php";
?>
<table class="xtratable">

    <tr>
        <th>Name</th><th>Category Level</th><th>Parent Category</th><th>Update</th><th>Insert</th><th>Delete</th>
    </tr>
<?php
    require $dbconPath;

    if($_SESSION["connected"]){

        $stmnt = "SELECT * FROM `myclass`";
        $result = $conn->query($stmnt);
        if($result->num_rows > 0){
            $catList = array();
            while($row = $result->fetch_assoc()){ 
                array_push($catList,$row);
            }
        }
        
        $maxlevel = 0;
        //get max level
        $stmnt = "SELECT max(ClassLevel) + 1 AS `maxlevel` FROM `myclass`";
        $result = $conn->query($stmnt);
        if($result->num_rows > 0){
            $maxlevel = $row["maxlevel"];
        }
        
        $conn->close();
    }
    
    if(isset($catList)){
        foreach($catList as $catOuter){
?>
    <tr>
        <td>
            <input class="catnamecol"
                type="text"  
                size="30"
                   required
                maxlength="30" 
                onchange="validateCatName(this,$(this).next('span'); setChanged(this);"
                   value="<?php echo $catOuter["Name"]; ?>">
            <span class="catnameerr tableerrors"></span>
        </td>
        <td >
            <input class="catlevelcol"
                type="number"
                   required
                min="0"
                max="<?php echo $maxlevel; ?>"
                   onchange="validateCatLevel(this, $(this).next('span')); setChanged(this);"
                   value="<?php echo $catOuter["ClassLevel"]; ?>">
            <span class="catlevelerr tableerrors"></span>
        </td>
        <td>
            <select class="ofcat"
                    onchange="setChanged(this)">
<?php
        $echoNull = true;
     foreach($catList as $catInner){
         if($catInner["ClassID"] == $catOuter["SuperClass"]){
            echo "<option value='" . $catInner["ClassID"] . "' selected>" . $catInner["Name"] . "</option>";
         }else if($catOuter["SuperClass"] == null){
             echo "<option value='null' selected>NULL</option>";
             $echoNull = false;
         }else{
            echo "<option value='" . $catIner["ClassID"] . "'>" . $catInner["Name"] . "</option>";
         }
     }
       if($echoNull){
          echo "<option value='null'>NULL</option>";
     }
 ?>
            </select>
        </td>
        <td><input class="updatechk" type="checkbox" value="<?php echo $row["ClassID"]; ?>"></td>
        <td><input class="insertchk" type="checkbox"></td>
        <td><input class="deletechk" type="checkbox" value="<?php echo $row["ClassID"]; ?>"></td>
    </tr>
<?php
        }
    }else{ 
?>
    <tr>
        <td colspan="6">This table has no data</td>
    </tr>
<?php
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
            
            var statement = "DELETE * FROM `myclass` WHERE ClassID IN (" + deleteList + ")";
        
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
            
            valuesList += "('" + $(this).find(".catnamecol").val() + "', " + $(this).find(".catlevelcol").val() + ", " + $(this).find(".ofcat").val() + "),";
        });//get array of rows marked for insertion
        
        
        if(valuesList != ""){
            valuesList = valuesList.substr(0,valuesList.length - 1);
            var statement = "INSERT INTO `myclass` (`Name`, `ClassLevel`, `SuperClass`) VALUES (" + valuesList + ")";
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
            var name = $(this).find(".catnamecol").val();
            var level = $(this).find(".catlevelcol").val();
            var parent = $(this).find(".ofcat").val();
            var id = $(this).find(".updatechk").val();
            
            var statement = "UPDATE `myclass` SET Name = '" + name + "', ClassLevel = " + level + ", SuperClass = " + parent + " WHERE ClassID = " + id;
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
        var max = Number("<?php echo $maxlevel; ?>");
         $.post("makeTableRow.php",{tablename: "myclass", maxlevel: max},function(data){
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