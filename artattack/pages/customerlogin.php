<script>
    
    function gotoReset(){
        $("#accounts").load("customerreset.php");
    }
    
    function login(){

        var email = cloginform.cemailfld;
        var password = cloginform.cpswdfld;
        
        var validEmail = validateEmail(email,"#cemailerr");
        var validPassword = validatePassword(password,"#cpswderr");

        //submit form if all inputs are correct

        if(validEmail && validPassword){
            var statement = "SELECT CustomerID FROM `customer` WHERE Password = '" + password.value + "' AND Email = '" + email.value + "'";
            
            $.post("customQuery.php",{query: statement, resultkind: "singleval"},function(data){ 
                var result = JSON.parse(data);
                if(result != "EMPTY" && result != "NOCONNECT"){ 
                    $.post("sessions.php",{customerlogin: result});
                    contextSwitchUser("#accounts");
                    $.post("loadCart.php", {cID: result}, function(data){
                        $("#cart").html(data);
                    });
                }else{
                    $("#accounts").html("<span>Could not perform login.<span><button type='button' onclick='document.location.reload();'>Retry</button><button type='button' onclick='window.location = \'http://localhost/artattack/index.php\';'>Return</button>");
                }
            });
        }
    }
</script>
<form name="cloginform" autocomplete>
    <fieldset>
        <legend>Customer Login</legend>
        <div id="cemail">
            <label class="formlabels">Email address&#58;</label>
            <input type="email" name="cemailfld" required size="50" maxlength="50" 
                   pattern="^.([A-z]|[0-9]|\.|_|-)+@([A-z]|[0-9]|\.|_|-)+(\.[a-z]{2,3}){1,2}$"
                   onchange="validateEmail(this, '#cemailerr')">
            <span id="cemailerr" class="formerrors"></span>
        </div>
        <div id="cpswd">
            <label class="formlabels">Password&#58;</label>
            <input type="password" name="cpswdfld" required size="16" maxlength="16" autocomplete="off" pattern="^.{8,16}$" onchange="validatePassword(this,'#cpswderr')">
            <span id="cpswderr" class="formerrors"></span>
        </div>
        <button type="button" onclick="gotoReset()">Reset password</button>
        <button type="button" name="cloginformsubmit" onclick="login()">Submit</button>
        <button type="button" onclick="window.location ='http://localhost/artattack/index.php';">Cancel</button>
    </fieldset>
</form>

