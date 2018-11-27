//validate customer or staff name
function validateFullName(name, errId){

    var valid = true;

    if(name.value == ""){
        $(errId).text("Please enter your full name.");
        valid = false;
    }else{
         $(errId).text("");
    }

    return valid;
}

//validate customer or staff email
function validateEmail(email, errId){

    var valid = true;
    if(email.value == ""){
        $(errId).text("Please enter your email address.");
        valid = false;
    }else if(email.validity.patternMismatch){
        $(errId).text("Please enter a valid email address");
        valid = false;
    }else{
        $(errId).text("");
    }        
    
    return valid;
}

//returns true if no pasword is found matching the on given
function isUniquePassword(password, errId){
    var unique = true;
    $.post("http://localhost/artattack/pages/customQuery.php",{query: "SELECT Password FROM `customer` WHERE Password = '" + password.value + "'", resultkind: "hasempty"},function(data){

                var result = JSON.parse(data);
                if(result == "HAS"){
                    unique = false;
                    $(errId).text("This password already exists in the database.");              
                }else{           
                    $(errId).text("");
                }
            });
    return unique;
}

//returns true if no email is found matching the on given
function isUniqueEmail(email, errId){
    var unique = true;
    $.post("http://localhost/artattack/pages/customQuery.php",{query: "SELECT Email FROM `customer` WHERE Email = '" + email.value + "'", resultkind: "hasempty"},function(data){

                var result = JSON.parse(data);
                if(result == "HAS"){
                    unique = false;
                    if(errId){
                        $(errId).text("This email already exists in the database.");
                    }
                }else{
                    if(errId){
                        $(errId).text("");
                    }
                }
            });
    return unique;
}

function isUniqueUsername(ufld, errId){
    var unique = true;
    $.post("http://localhost/artattack/pages/customQuery.php",{query: "SELECT username FROM `staff` WHERE username = '" + ufld.value + "'", resultkind: "hasempty"},function(data){

                var result = JSON.parse(data);
                if(result == "HAS"){
                    unique = false;
                    if(errId){
                        $(errId).text("This username already exists in the database.");
                    }
                }else{
                    if(errId){
                        $(errId).text("");
                    }
                }
            });
    return unique;
}

//returns true if no mobile number is found matching the on given
function isUniqueMobile(mobile, errId){
    var unique = true;
    $.post("http://localhost/artattack/pages/customQuery.php",{query: "SELECT MobileNumber FROM `customer` WHERE MobileNumber = '" + mobile.value + "'", resultkind: "hasempty"},function(data){

                var result = JSON.parse(data);
                if(result == "HAS"){
                    unique = false;
                    if(errId){
                        $(errId).text("This email already exists in the database.");
                    }
                }else{
                    if(errId){
                        $(errId).text("");
                    }
                }
            });
    return unique;
}

//validate customer mobile number
function validateMobile(mobile, errId){
    var valid = true;

    if(mobile.value == ""){
        $(errId).text("Please enter your mobile number.");
        valid = false;
    }else if(mobile.validity.patternMismatch){
        $(errId).text("Please enter a valid mobile number.");
        valid = false;
    }else{
        $(errId).text("");
    }
    return valid;
}

function validateUsername(ufld, errId){
    var valid = true;
    $(errId).text("");
    if(ufld.validity.valueMissing){
        $(errId).text("Please enter your username.");
        valid = false;
    }else if(ufld.validity.tooLong){
        $(errId).text("Please use a username no longer than 50 characters.");
        valid = false;
    }/*else if(ufld.validity.patternMismatch){
        $(errId).text("The username may not contain any spaces.");
        valid = false;
    }*/
    return valid;
}

function formatMobile(mobileField){
    if(/^\d{3}/.test(mobileField.value)){
        
        mobileField.value += " ";
    }else if(/^\(\+\d{2}\)/.test(mobileField.value)){
            mobileField.value += " ";
            if(/\(\+\d{2}\)\s?\d{2}/.test(mobileField.value)){
                mobileField.value += " ";
            }
        }else if(/\+\d{2}/.test(mobileField.value)){
            mobileField.value += " ";
            if(/\+\d{2}\s\d{2}/.test(mobileField.value)){
                mobileField.value += " ";
            }
        }
}

//validate entered password
function validatePassword(pass, errId){
    var valid = true;

    if(pass.value == ""){
        $(errId).text("Please enter a valid password.");
        valid = false;
    }else{
        $(errId).text("");
    }
    return valid;
}

function showPasswordStrength(crntText, errFieldId){
   if(/^.{8,16}$/g.test(crntText)){
       if(/\s+|;|,|:|`|'|"|\|/g.test(crntText)){
           if(/[A-Z]{2}/g.test(crntText)){
               if(/\d{2}/g.test(crntText)){
                   if(/\W{2}/g.test(crntText)){
                       $(errFieldId).text("Very Strong").css("color","#80f");
                   }else{
                       $(errFieldId).text("Strong").css("color","#08f");
                   }
               }else{
                   $(errFieldId).text("Good").css("color","#4f4");
               }
           }else{
               $(errFieldId).text("Weak").css("color","#f80");
           }
       }else{
           $(errFieldId).text("Very Weak").css("color","#f44");
       }
   }else{
       $(errFieldId).text("Please enter a valid password.").css("color","#222");
   }    
}


//checks that passwords match
function validateConfirmPassword(confpass, pass, errId){
    var valid = true;

    if(confpass.value == ""){
        $(errId).text("Please confirm your email address.");
        valid = false;
    }else if(confpass.value != pass.value){
        valid = false;
        $(errId).text("This password does not match the first. Please re-enter.");
    }else{
        $(errId).text("");
    }
    return valid;   
}

//returns true if address part is valid, false otherwise and sets appropriate error message
function validateTransField(field,error, type){
    
    var valid = true;
    error.innerHTML = "";
    if(field.validity.valueMissing){
        valid = false;
        error.innerHTML += "<span>Please fill in this field.</span>"; 
    }
    if(field.validity.tooLong){
        valid = false;
        error.innerHTML += "<span>Too long, please use no more than " + field.maxlength + " characters.</span>";
    }
    if(field.validity.patternMismatch){
        valid = false;
        switch (type){
            case "address":
                error.innerHTML += "<span>Wrong format, should be e.g. <i>[number] [Street name] [Street type], [Suburb]</i></span>";
                break;
            case "postcode":
            error.innerHTML += "<span>Wrong format. Must be 4 digit code e.g. <i>####</i></span>";
            break;
            case "city":            
            case "country":
            case "regstate":
                error.innerHTML += "<span>Wrong format. Check capitalization and special characters like <b>&#39;</b> and <b>&#45;</b>.</span>";
                break;
            case "card":
                error.innerHTML += "<span>Wrong format. Must be 16 numbers with or without spaces.</span>";
                break;
            case "cardholder":
                error.innerHTML += "<span>Wrong format. Must be initials as on card in uppercase.</span>";
                break;
            case "exm":
                error.innerHTML += "<span>Wrong format. Enter a month from 01 to 12.</span>";
                break;
            case "exy":
                error.innerHTML += "<span>Wrong format. Enter only last 2 digits of year.</span>";
                break;
            case "cvv":
                error.innerHTML += "<span>Wrong format. Enter only the 3 digits as on card.</span>";
                break;
            case "accholder":
                error.innerHTML += "<span>Wrong format. Must be initials associated with account in uppercase.</span>";
                break;
            case "routenum":
            case "accnum":
                error.innerHTML += "<span>Wrong format. 20 max digits with spaces or dashes(-).</span>";        
                break;
            default: error.innerHTML = "";      
        }
    }
    return valid;
}

function validateReview(){ return; }

function validateVoucherCode(field, errId){
    var valid = true;
    $(errId).text("");
    if(field.validity.valueMissing){
        var valid = false;
        $(errId).html("<span>Please fill in this field.</span>"); 
    }
    if(field.validity.tooLong){
        var valid = false;
        $(errId).html("<span>Too long, please use no more than " + field.maxlength + " characters.</span>");
    }
    if(field.validity.patternMismatch){
        var valid = false;
        $(errId).html("<span>Wrong format. Make sure the code is entered correctly with no spaces.</span>");
    }
    return valid;
}
 
function validateBarCode(bcfld, errId,original){
    var valid = true;
    $(errId).text("");
    if(bcfld.validity.valueMissing){
        $(errId).text("Please enter a barcode.");
        valid = false;
    }else if(bcfld.validity.tooLong){
        valid = false;
        $(errId).text("Please ensure the barcode is 13 characters long.");
    }else if(bcfld.validity.patternMismatch){
        valid = false;
        $(errId).text("Wrong formant. Please enter a valid EAN_13 code.");
    }else if(bcfld.value != original){
        var statement = "SELECT BarCode FROM `product` WHERE BarCode = '" + bcfld.value + "'";
        $.post("http://localhost/artattack/pages/customQuery.php", {query: statement, resultkind:"hasempty"}, function(data){
            var result = JSON.parse(data);
            if(result == "HAS"){
                valid = false;
                $(errId).text("Please enter a barcode that is unused.");
            }//otherwise the result is empty and the barcode unique, valid is still true
        });
    }
    return valid;
}

function validatePrice(pricefld, errId){
    var valid = true;
    $(errId).text("");
    if(pricefld.validity.valueMissing){
        $(errId).text("Please enter a price.");
        valid = false;
    }else if(pricefld.validity.tooLong){
        $(errId).text("Please enter a value from 1 to 999 999.");
        valid = false;
    }else if(pricefld.validity.patterMismatch){
        $(errId).text("Please enter a value matching the format 999.99.");
        valid = false;
    }
    return valid;
}

function validateProductName(namefld, errId){
    var valid = true;
    $(errId).text("");
    if(namefld.validity.valueMissing){
        $(errId).text("Please enter the product title.");
        valid = false;
    }else if(namefld.validity.tooLong){
        $(errId).text("Please change the name to be 50 characters or less.");
        valid = false;
    }else if(namefld.validity.patterMismatch){
        $(errId).text("Please use only numbers and letters. No special characters.");
        valid = false;
    }
    return valid;
}

function validateProductDesc(descfld, errId){
    var valid = true;
    $(errId).text("");
    if(descfld.validity.tooLong){
        $(errId).text("The maximum number of characters is 256.");
        valid = false;
    }
    return valid;
}

function validateCategory(){
    return true;
}

function validateBrand(brandfld, errId){
    var valid = true;
        $(errId).text("");
    if(brandfld.validity.patternMismatch){
        $(errId).text("Please use only letters.");
        valid = false;
    }else if(brandfld.validity.tooLong){
        $(errId).text("Please enter a brand name of 30 characters or less.");
        valid = false;
    }else if(brandfld.validity.missingValue){
        $(errId).text("Enter a brand name.");
    }
    return valid;
}

function validateRange(rangefld, errId){
    var valid = true;
    $(errId).text("");
    if(rangefld.validity.patternMismatch){
        $(errId).text("Please use only letters.");
        valid = false;
    }else if(rangefld.validity.tooLong){
        $(errId).text("Please enter a range name of 30 characters or less.");
        valid = false;
    }else if(rangefld.validity.missingValue){
        $(errId).text("Enter a range name.");
    }
    return valid;
}

function validateColourName(cname, nameErr){
    var valid = true;
    $(nameErr).text("");
    if(cname.validity.tooLong){
        nameErr.innerHTML = "Choose a name no longer than 30 characters.";
        valid = false;
    }else if(cname.validity.patternMismatch){
        nameErr.innerHTML = "Please don't use any numbers or special characters.";
        valid = false;
    }else if(cname.validity.missingValue){
        $(errId).text("Enter a colour name");
    }
    return valid;
}

function validateColourCode(ccode, codeErr){
    var valid = true;
    $(codeErr).text("");
    if(ccode.validity.tooLong){
        codeErr.innerHTML = "Choose a code no longer than 10 characters.";
        valid = false;
    }else if(ccode.validity.patternMismatch){
        codeErr.innerHTML = "Enter a code that has letters and numbers with -, # or \ inbetween.";
        valid = false;
    }
    return valid;
}

function validateAttrName(namefld, nameErr){
    var valid = true; 
    $(nameErr).text("");
    if(namefld.value == ""){
        $(nameErr).text("Please enter a name for the attribute.");
        valid = false;
    }else if(namefld.validity.tooLong){
        $(nameErr).text("Enter a name no more than 30 characters long.");
        valid = false;
    }else if(namefld.validity.patternMismatch){
        $(nameErr).text("Please use only letters in the name.");
        valid = false;
    }
    return valid; 
}

function validateAttrDesc(descfld, descErr){
    var valid = true;
    $(descErr).text("");
    if(descfld.value != "" && descfld.validity.tooLong){
        $(descErr).text("Enter a description no more than 60 characters long.");
    }
    return valid;
}

function validateAttrUnit(unitfld, unitErr){
    var valid = true;
    $(unitErr).text("");
    if(amntfld.validity.patternMismatch){
        $(amntErr).text("Please use only letters for the unit.");
        valid = false;
    }

    if(unitfld.validity.tooLong){
        $(unitErr).text("Enter a unit no longer than 5 characters.");
        valid = false;
    }
    return valid;
}

function validateAttrAmount(amntfld, amntErr){
    var valid = true;
    $(amntErr).text("");
    if(amntfld.value != ""){
        if(amntfld.validity.rangeOverflow || amntfld.validity.rangeUnderflow){
            $(amntErr).text("Please enter an amount between 0 and 1000.");
            valid = false;
        }
    }
    return valid;
}

function validateAttrAdj(adjfld, adjErr){
    var valid = true;
    $(adjErr).text("");
    if(adjfld.value != ""){
        
        if(adjfld.validity.tooLong){
            $(adjErr).text("Enter an adjective no longer than 20 characters.");
            valid = false;
        }else if(adjfld.validity.patternMismatch){
            $(adjErr).text("Please use only words with no special characters or numbers.");
            valid = false;
        }
    }//else an adjective is not required
    return valid;
}

function validateStock(stockfld, errId){
     var valid = true;
$(errId).text("");
    if(stockfld.value == ""){
        $(errId).text("Please enter an amount to add to inventory.");
        valid = false;
    }else if(stockfld.validity.patternMismatch){
        $(errId).text("Please enter an amount from 1 to 999.");
        valid = false;
    }else{
        $(errId).text("");
    }
    return valid;
}

function validatePercentage(percfld, errId){
    var valid = true;
    $(errId).text("");
    if(percfld.value != ""){
        if(percfld.validity.tooLong){
            $(errId).text("Enter a percentage from 0.00 to 100.00.");
            valid = false;
        }else if(percfld.validity.patternMismatch){
            $(errId).text("Enter a number with no more than 2 decimal places.");
            valid = false;
        }
    }
    return valid;
}

function validateSpecialDate(datefld, errId){
    var valid = true;
    $(errId).text("");
    if(datefld.validity.valueMissing){
        $(errId).text("Please select an end date for the special.");
        valid = false;
    }else if(datefld.validity.valueUnderflow){
        $(errId).text("The end date must be at least 1 day from now.");
        valid = false;
    }
    
    return valid;
}

function validateCatName(namefld, errId){
    $(errId).text("");
    var valid = true;
    if(namefld.validity.missingValue){
        $(errId).text("Enter a category name.");
    }else if(namefld.validity.tooLong){
        $(errId).text("The name must be fewer than 30 characters.");
    }    
    return valid;
}

function validateCatLevel(levelfld, errId){
    var valid = true;
    $(errId).text("");
    if(levelfld.validity.rangeOverflow){
        $(errId).text("The level can only be 1 higher than the current highest.");
    }else if(levelfld.validity.rangeUnderflow){
        $(errId).text("The level cannot be lower than 0.");
    }else if(levelfld.validity.valueMissing){
        $(errId).text("Specify the category level in the hierarchy.");
    }
    return valid;    
}