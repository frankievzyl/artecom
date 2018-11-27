<script>
    function signupS(){
        var name = ssignupform.snamefld;
        var username = ssignupform.susernamefld;
        var pass = ssignupform.spswdfld;
        var passC = ssignupform.sconfpswdfld;

        var validFullName = validateFullName(name,"#snameerr");
        var validUsername = validateUsername(username,"#susernameerr");
        if(validUsername){
            validUsername = isUniqueUsername(username,"#susernameerr");
        }
        var validPassword = validatePassword(pass,"#spswderr");
        if(validPassword){
            validPassword = isUniquePassword(pass,"#spswderr");
        }
        var validConfirmPassword = false;
        if(validPassword){//only check if a password is entered
            validConfirmPassword = validateConfirmPassword(passC, pass,"#sconfpswderr");
        }
        //submit form if all inputs are correct

        if(validFullName && validEmail && validPassword && validConfirmPassword){
            var statement = "INSERT INTO `staff` (username, password, fullname) VALUES ('" + username.value + "','" + pass.value + "','" + name.value + "')";
            $.post("customAction.php",{action: statement},function(data){

                if(data.trim() == "SUCCESS"){
                    $.post("customQuery.php",{query: "SELECT StaffID FROM `staff` WHERE password = '" + pass.value + "'", resultkind: "singleval"}, function(data){
                        var result = JSON.parse(data);
                        if(result != "EMPTY"){
                            $.post("sessions.php", {stafflogin: result});
                        }
                    });
                }
            });
        }
    }
</script>
<form name="ssignupform">
    <fieldset>
        <legend>Staff Registration</legend>
        <div id="sname">
            <label class="formlabels">Full name&#58;</label>
            <input type="text" name="snamefld" required size="50" maxlength="50" autofocus 
                   pattern="^[A-Z][A-z]+(\s[A-z]+)+$"
                   onchange="validateFullName(this,'#snameerr')">
            <span id="snameerr" class="formerrors"></span>
        </div>
        <div id="susername">
            <label class="formlabels">Username&#58;</label>
            <input type="text" name="susernamefld" required size="50" maxlength="50"
                   pattern="/^[^\s]+$/"
                    onchange="validateUsername(this,'#susernameerr')">
            <span id="susernameerr" class="formerrors"></span>
        </div>
        <div id="spswd">
            <label class="formlabels">Password&#58;</label>
            <input type="password" name="spswdfld" required size="16" maxlength="16"
                   pattern="^.{8,16}$" onkeyup="validatePassword(this, '#spswderr'); showPasswordStrength(this.value, '#spswderr')">
            <span id="spswderr" class="formerrors"></span>
        </div>
        <div id="sconfpswd">
            <label class="formlabels">Confirm password&#58;</label>
            <input type="password" name="sconfpswdfld" required size="16" maxlength="16"
                   pattern="^.{8,16}$">
            <span id="sconfpswderr" class="formerrors"></span>
        </div>
        <button type="button" name="ssignupformsubmit" onclick="signUpS()">Submit</button>
        <button type="button" onclick="contextSwitch('#accounts')">Cancel</button>
        <details class="hint">
            <summary>Show valid password hints</summary>
            <ul>
                <li>Is 8 - 16 characters long</li>
                <li>Has any punctuation character</li>
                <li>Has 2 uppercase letters</li>
                <li>Has 2 digits</li>
                <li>Has 2 special characters</li>
            </ul>
        </details>
    </fieldset>
</form>
