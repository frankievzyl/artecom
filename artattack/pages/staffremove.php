<script>
    function deleteS(){

        var username = sremovalform.susernamefld;
        var password = sremovalform.spswdfld;
        
        var validUsername = validateUsername(username,"#susernameerr");
        var validPassword = validatePassword(password,"#spswderr");

        //submit form if all inputs are correct

        if(validUsername && validPassword){
            var statement = "DELETE FROM `staff` WHERE password = '" + password.value + "' AND username = '" + username.value + "'";
            $.post("customAction.php",{action: statement},function(data){
                var result = data.trim();
                if(result != "FAILURE"){
                    alert("Account deleted.");
                }else{
                    alert("Failed to remove account.");
                }
                contextSwitch("#accounts");
            });
        }
    }
</script>
<form name="sremovalform" autocomplete>
    <fieldset>
        <legend>Staff Removal</legend>
        <div id="susername">
            <label class="formlabels">Username&#58;</label>
            <input type="text" name="susernamefld" required size="50" maxlength="50"
                   pattern="/^[^\s]+$/"
                   onchange="validateUsername(this,'#susernameerr')">
            <span id="susernameerr" class="formerrors"></span>
        </div>
        <div id="spswd">
            <label class="formlabels">Password&#58;</label>
            <input type="password" name="spswdfld" required 
                   size="16" autocomplete="off" maxlength="16" pattern="^.{8,16}$"
                   onchange="validatePassword(this, '#spswderr')">
            <span id="spswderr" class="formerrors"></span>
        </div>
        <button type="button" name="sremovalformsubmit" onclick="deleteS()">Submit</button>
        <button type="button" onclick="contextSwitch('#accounts')">Cancel</button>
    </fieldset>
</form>