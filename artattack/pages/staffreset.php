<script>

    var staffid = null;

    //validate email and check if it exists
    function resetOne(){

        var username = sresetform1.susernamefld;
        if(validateUsername(username,"#susernameerr")){
             $.post("customQuery.php",{query: "SELECT StaffID FROM `staff` WHERE username = '" + username.value + "'", resultkind: "singleval"}, function(data){
                var result = JSON.parse(data);
                if(result != "EMPTY"){
                    staffid = result;

                    $("#fieldsettwo").prop("disabled", false);
                    $("#fieldsetone").prop("disabled", true);

                }else{
                    $("#susernamefld").text("The supplied username could not be found.");
                }
            });
        }
    }

    //validate second form
    function resetTwo(){
        var pass = sresetform2.spswdfld;
        var passC = sresetform2.sconfpswdfld;
        var validPassword = validatePassword(pass,"#spswderr");
        if(validPassword){
            validPassword = isUniquePassword(pass,"#spswderr");
        }
        var validConfirmPassword = false;
        if(validPassword){
            validConfirmPassword = validateConfirmPassword(passC, pass,"#sconfpswderr");
        }
    
        if(validPassword && validConfirmPassword){
            var reset = "UPDATE `staff` SET password = '" + pass.value + "' WHERE StaffID = " + staffid;
            $.post("customAction.php",{action: reset},function(data){
                if(data.trim() == "SUCCESS"){
                    $("#accounts").load("stafflogin.php");
                    contextSwitch("#accounts");
                    
                }else{
                    $("#accounts").html("<p>Password reset was unsuccessful</p><button type='button' onclick='contextSwitch(\"#accounts\")'");
                }
            });
        }
    }

</script>
<form name="sresetform1">
    <fieldset id="fieldsetone">
        <legend>Provide Username</legend>
        <div id="susername">
            <label class="formlabels">Username&#58;</label>
            <input type="text" name="susernamefld" required size="50" maxlength="50"
                   pattern="/^[^\s]+$/"
                onchange="validateUsername(this,'#susernameerr')">
            <span id="susernameerr" class="formerrors"></span>
        </div>
        <button type="button" name="sresetform1submit" onclick="resetOne()">Submit</button>
    </fieldset>
</form>
<form name="sresetform2">
    <fieldset id="fieldsettwo">
        <legend>Reset Password</legend>
        <div id="spswd">
            <label class="formlabels">Password&#58;</label>
            <input type="password" name="spswdfld" required size="16" maxlength="16"
                   onkeyup="validatePassword(this,'#spswderr'); showPasswordStrength(this.value,'#spswderr');">
            <span id="spswderr" class="formerrors"></span>
        </div>
        <div id="sconfpswd">
            <label class="formlabels">Confirm password&#58;</label>
            <input type="password" name="sconfpswdfld" required size="16" maxlength="16">
            <span id="sconfpswderr" class="formerrors"></span>
        </div>
        <button type="button" name="sresetform2submit" onclick="resetTwo()">Submit</button>
    </fieldset>
</form>
<button type="button" onclick="contextSwitch('#accounts')">Cancel</button>
