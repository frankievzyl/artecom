<script>
    var mobile = null;
    var custid = null;
    var OTP = null;
    //validate email and check if it exists
    function resetOne(){

        var email = cresetform1.cemailfld;
        if(validateEmail(email,"cemailerr")){
             $.post("customQuery.php",{query: "SELECT CustomerID, MobileNumber FROM `customer` WHERE Email = '" + email.value + "'", resultkind: "singlerow"}, function(data){
                var result = JSON.parse(data);
                if(result != "EMPTY"){
                    custid = result.CustomerID; 
                    mobile = result.MobileNumber;
                    $("#fieldsettwo").prop("disabled", false);
                    $("#fieldsetone").prop("disabled", true);
                    resendOTP();

                }else{
                    $("#cemailerr").text("The supplied email could not be found.");
                }
            });
        }
    }

    //validate second form
    function resetTwo(){
        var pass = cresetform2.cpswdfld;
        var passC = cresetform2.cconfpswdfld;
        var validPassword = validatePassword(pass,"#cpswderr");
        if(validPassword){
            validPassword = isUniquePassword(pass,"#cpswderr");
        }
        var validConfirmPassword = false;
        if(validPassword){
            validConfirmPassword = validateConfirmPassword(passC, pass,"#cconfpswderr");
        }
        var validOTP = false;
        if(OTP != null){//only if not undefined
            if(OTP == cresetform2.otpfld.value){
                validOTP = true;
            }else{
                $("#otperr").text("The given pin number does not match the sent OTP.\nPlease retry and\or request a resend.");
            }
        }
        if(validPassword && validConfirmPassword && validOTP){
            var reset = "UPDATE `customer` SET Password = '" + pass.value + "' WHERE CustomerID = " + custid; 
            $.post("customAction.php",{action: reset},function(data){
                if(data.trim() == "SUCCESS"){
                        $("#accounts").load("customerlogin.php");
                }else{
                        $("#accounts").html("<p>Password reset was unsuccessful</p><button type='button' onclick='contextSwitch(\"#accounts\")'");
                }
            });
        }
    }

    function resendOTP(){

        if(mobile != null){
            OTP = Math.floor((Math.random() * 10000)+1);
            console.log("OPT sent to: " + mobile + "\nValue sent is: " + OTP);
            $("#otperr").text("An OTP was sent to your mobile number.");
        }
    }
</script>
<form name="cresetform1" autocomplete>
    <legend>Provide Email</legend>
    <fieldset id="fieldsetone">              
    <div id="cemail">
        <label class="formlabels">Email address&#58;</label>
        <input type="email" name="cemailfld" required size="50" maxlength="50" pattern="^.([A-z]|[0-9]|\.|_|-)+@([A-z]|[0-9]|\.|_|-)+(\.[a-z]{2,3}){1,2}$" onchange="validateEmail(this, '#cemailerr')">
        <span id="cemailerr" class="formerrors"></span>
    </div>
    <button type="button" name="cresetform1submit" onclick="resetOne()">Submit</button>
    </fieldset>
</form>
<form name="cresetform2">
    <fieldset id="fieldsettwo" disabled>        
        <legend>Reset Password</legend>
        <div id="OTP">
            <label class="formlabels">Enter OTP&#58;</label>
            <input type="text" name="otpfld" required>
            <span id="otperr" class="formerrors"></span>
        </div>
        <button type="button" id="sendotpbtn" onclick="resendOTP()">Resend OTP</button>
        <div id="cpswd">
            <label class="formlabels">Password&#58;</label>
            <input type="password" name="cpswdfld" required size="16" minlength="8" maxlength="16" pattern="^.{8,16}$" onkeyup="validatePassword(this,'#cpswderr'); showPasswordStrength(this.value,'#cpswderr');">
            <span id="cpswderr" class="formerrors"></span>
        </div>
        <div id="cconfpswd">
            <label class="formlabels">Confirm password&#58;</label>
            <input type="password" name="cconfpswdfld" required size="16" maxlength="16" pattern="^.{8,16}$">
            <span id="cconfpswderr" class="formerrors"></span>
        </div>
        <button type="button" name="cresetform2submit" onclick="resetTwo()">Submit</button>
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
<button type="button" onclick="contextSwitchUser('#accounts')">Cancel</button>

