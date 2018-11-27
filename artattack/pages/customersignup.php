<script>
    function signupC(){

        var name = csignupform.cnamefld;
        var email = csignupform.cemailfld;
        var mobile = csignupform.cmobilefld;
        var pass = csignupform.cpswdfld;
        var passC = csignupform.cconfpswdfld;

        var validFullName = validateFullName(name,"#cnameerr");
        var validEmail = validateEmail(email,"#cemailerr");
        if(validEmail){
            validEmail = isUniqueEmail(email,"#cemailerr");
        }
        var validMobile = validateMobile(mobile,"#cmobileerr");
        if(validMobile){
            validMobile = isUniqueMobile(mobile,"#cmobileer");
        }
        var validPassword = validatePassword(pass,"#cpswderr");
        if(validPassword){
            validPassword = isUniquePassword(pass,"#cpswderr");
        }
        var validConfirmPassword = false;
        if(validPassword){//only check if a password is entered
            validConfirmPassword = validateConfirmPassword(passC, pass,"#cconfpswderr");
        }
        //submit form if all inputs are correct

        if(validFullName && validEmail && validMobile && validPassword && validConfirmPassword){
            var statement = "INSERT INTO `customer` (Name, Email, MobileNumber, Password) VALUES ('" + name.value + "','" + email.value + "','" + mobile.value + "','" + pass.value + "')";
            $.post("customAction.php",{action: statement},function(data){

                if(data.trim() == "SUCCESS"){
                    $.post("customQuery.php",{query: "SELECT CustomerID FROM `customer` WHERE Password = '" + password.value + "'", resultkind: "singleval"}, function(data){
                        var result = JSON.parse(data);
                        if(result != "EMPTY"){
                            $.post("sessions.php", {customerlogin: result});
                        }
                    });
                }
            });
        }
    }
</script>
<form name="csignupform">
    <fieldset>
        <legend>Customer Registration</legend>
        <div id="cname">
            <label class="formlabels">Full name&#58;</label>
            <input type="text" name="cnamefld" required size="50" maxlength="50" autofocus 
                   pattern="^[A-Z][A-z]+(\s[A-z]+)+$"
                   onchange="validateFullName(this,'#cnameerr')">
            <span id="cnameerr" class="formerrors"></span>
        </div>
        <div id="cemail">
            <label class="formlabels">Email address&#58;</label>
            <input type="email" name="cemailfld" required="true" size="50" maxlength="50"
                   pattern="^.([A-z]|[0-9]|\.|_|-)+@([A-z]|[0-9]|\.|_|-)+(\.[a-z]{2,3}){1,2}$"
                   onchange="validateEmail(this, '#cemailerr')">
            <span id="cemailerr" class="formerrors"></span>
        </div>
        <div id="cmobile">
            <label class="formlabels">Mobile number&#58;</label>
            <input type="text" name="cmobilefld" required size="17" maxlength="17" pattern="^(\d{3}\s|\(\+\d{2}\)\s?\d{2}\s|\+\d{2}\s\d{2}\s)\d{3}\s\d{4}$"
                   onchange="validateMobile(this, '#cmobileerr')">
            <span id="cmobileerr" class="formerrors"></span>
        </div>
        <div id="cpswd">
            <label class="formlabels">Password&#58;</label>
            <input type="password" name="cpswdfld" required size="16" minlength="8" maxlength="16" autocomplete="off" pattern="^.{8,16}$" onkeyup="validatePassword(this,'#cpswderr'); showPasswordStrength(this.value,'#cpswderr');">
            <span id="cpswderr" class="formerrors"></span>

        </div>
        <div id="cconfpswd">
            <label class="formlabels">Confirm password&#58;</label>
            <input type="password" name="cconfpswdfld" required size="16" minlength="8" maxlength="16" autocomplete="off" pattern="^.{8,16}$">
            <span id="cconfpswderr" class="formerrors"></span>
        </div>
        <button type="button" name="csignupformsubmit" onclick="signupC()">Submit</button>
        <button type="button" onclick="contextSwitchUser('#accounts')">Cancel</button>
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