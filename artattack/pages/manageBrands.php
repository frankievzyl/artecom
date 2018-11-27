<?php
    $dbconPath = "incMySQLConnect.php";
?>
<table class="xtratable">

    <tr>
        <th>Brand Name</th><th>Logo</th><th>Update</th><th>Insert</th><th>Delete</th>
    </tr>
<?php
    require $dbconPath;

    if($_SESSION["connected"]){

        $stmnt = "SELECT * FROM `brand`";
        $result = $conn->query($stmnt);
        if($result->num_rows > 0){

            while($row = $result->fetch_assoc()){ 
?>
    <tr>
        <td >
            <input class="brandcol"
               type="text" 
               size="30" 
                   required
               maxlength="30" 
               pattern="^[A-Z][A-z]+(\s?[A-z]+)+$"
               onchange="validateBrand(this,$(this).next('span')); setChanged(this);"
                   value="<?php echo $row["Name"]; ?>">
            <span class="brandnameerr tableerrors"></span>
        </td>
        <td >
            <input class="logocol"
                type="file" 
                accept="image/*" 
                onchange="uploadImage(this, $(this).siblings('img'), $(this).next(), false); setChanged(this);updateImg(event,$(this).siblings('img'));">
            <span class="brandlogoerr"></span>
            <img class="imgcol"
                 src="..<?php echo $row["Logo"]; ?>" 
                 alt="No logo selected for brand.">
        </td>
        <td><input class="updatechk" type="checkbox" value="<?php echo $row["BrandID"]; ?>"></td>
        <td><input class="insertchk" type="checkbox"></td>
        <td><input class="deletechk" type="checkbox" value="<?php echo $row["BrandID"]; ?>"></td>
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
            
            var statement = "DELETE * FROM `brand` WHERE BrandID IN (" + deleteList + ")";
        
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
        
        var failure = false;
        var cont;
        var valueList;
        $(".insertchk:checked").parents("tr").each(function(){
                    
            valuesList = "'" + $(this).find(".brandcol").val() + "','/images/" + $(this).find(".logocol").val() + "'";
            
            cont = uploadImage($(this).find(".logocol"),$(this).find(".imgcol"), $(this).find(".brandlogoerr"), true);
                        
            if(cont){
                var statement = "INSERT INTO `brand` (`Name`, `Logo`) VALUES (" + valuesList + ")";
                $.post("customAction.php", {action: statement}, function(data){
                    failure = data.trim() == "SUCCESS";
                });
            }
        });//get array of rows marked for insertion
        
        if(failure){
            $("#insstat").text("Insertion of row(s) in the table successful.").css("color", "green");     
        }else{
            $("#insstat").text("Insertion of row(s) in the table failed.").css("color","red");
        }
    }
    
    function updateRows(){
        
        var failure = false;
        var cont;
        $(".updatechk:checked").parents("tr").each(function(){
            var name = $(this).find(".brandcol").val();
            var logo = $(this).find(".logocol").val();
            var id = $(this).find(".updatechk").val();
            
            cont = uploadImage($(this).find(".logocol"),$(this).find(".imgcol"), $(this).find(".brandlogoerr"),true);
            
            if(cont){
                var statement = "UPDATE `brand` SET Name = '" + name + "', Logo = '" + logo + "' WHERE BrandID = " + id;
                $.post("customAction.php", {action: statement}, function(data){
                    failure = data.trim() != "SUCCESS";                
                });
            }
        });
        
        if(failure){
            $("#updstat").text("One or more changes failed to be applied.").css("color","text");
        }else{
            $("#updstat").text("All changes were applied successfully.").css("color","text");
        }
     
    }
    
    function addRow(){
        $.post("makeTableRow.php",{tablename: "brand"},function(data){
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