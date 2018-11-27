<script>
    function loginS(){

        var username = sloginform.susernamefld;
        var password = sloginform.spswdfld;
        
        var validUsername = validateUsername(username,"#susernameerr");
        var validPassword = validatePassword(password,"#spswderr");

        //submit form if all inputs are correct

        if(validUsername && validPassword){
            var statement = "SELECT StaffID FROM `staff` WHERE password = '" + password.value + "' AND username = '" + username.value + "'";
            $.post("customQuery.php",{query: statement, resultkind: "singleval"},function(data){
                var result = JSON.parse(data);
                if(result != "EMPTY"){
                    $.post("sessions.php",{stafflogin: result});
                    contextSwitch("#accounts");
                    if(result != 1){
                        $(".admin").css("display","none");
                    }
                }else{
                    alert("Failed to login.");
                    sloginform.reset();
                }
            });
        }
    }
</script>
<form name="sloginform" autocomplete>
    <fieldset>
        <legend>Staff Login</legend>
        <div id="susername">
            <label class="formlabels">Username&#58;</label>
            <input type="text" name="susernamefld" required size="50" maxlength="50"
                   
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
        <button type="button" name="sloginformsubmit" onclick="loginS()">Submit</button>
        <button type="button" onclick="window.location ='http://localhost/artattack/index.php';">Cancel</button>
    </fieldset>
</form>