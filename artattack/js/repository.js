function showSearch(){
    $("#search").toggle();
    $("#shiftbuttons #searchpnlbtn i").toggle();
}

function showCatbar(){
    $("#categories").toggle();
    $("#shiftbuttons #catpnlbtn i").toggle();
}

function showCart(){
    $("#cartsuper").toggle();
    $("#shiftbuttons #cartpnlbtn i").toggle();
}

function getCat(){
    var selectedCat = $("#categories").children("select").find(":selected").val();
    selectedCat = selectedCat.slice(9);
    return selectedCat;
}

function refreshLists(targetOptions, myLevel){
    $("#categories").children("select").css("display","none");//hide all select lists
    var i;
    for(i = 0; i <= myLevel; i++){//show all select lists below and this one
        var selectId = "#pcatfld" + i;
        $(selectId).css("display","inline-flex");
    }
    var selectId = "#pcatfld" + (myLevel + 1);
    if($(selectId).children(targetOptions).length > 0){//if there are child options to select from for this super category
        $(selectId).css("display","inline-flex");
        $(selectId).children().css("display","none");//hide all options in new select list
        $(selectId).children().prop("selected",false);//deselect all options
        $(selectId).children(targetOptions).css("display","block");//show only the options that are children of selected value in previous list
        $(selectId).children(targetOptions).first().prop("selected",true);//sets first viable option as being selected
        $(selectId).val($(selectId).children(targetOptions).first().val());//update list selected display
    }

}

function contextSwitchUser(contextid){
    //return overall control
    if($("#store").css("display") == "none"){
        $("#store").css("display", "block");
        $(contextid).css("display", "none");
        $("button").prop("disabled", false);
        $("input").prop("disabled", false);
        $("select").prop("disabled", false);
    }else{
        //limit control to current context
        $("#store").css("display", "none");
        $(contextid).css("display", "block");
        $("button").prop("disabled", true);
        $("input").prop("disabled", true);
        $("select").prop("disabled", true);
    }   
}

function showProducts(category, sKind, toRoot, custId){
    
    if(document.getElementById("catchk").checked == false){//if search by category is OFF
        category = null;
        if(sKind == "category"){
            sKind = "normal";//ensure categories are excluded
        }
    }else{
        sKind = "category";//ensure categories are included
    }
    if($("[name=specialchk]").prop("checked")){//if special still on
        if(sKind == "category"){//search for special in category
            sKind = "special" + sKind;
        }else{
            sKind = "special";
        }
    }
    if($("[name=voucherchk]").prop("checked")){//if voucher still on
            sKind = "voucher";
    }
    console.log("cat: " + category + " kind: " + sKind);
    //sKind : normal, special, voucher, category
    var sForm = document.forms["searchform"];
    //part of name to be searched for in product name
    var sNamePart = sForm["searchfld"].value;
    
    //boolean array for by name, by price, by rating
    var sSort = [   sForm["alphachk"].checked,
                    sForm["pricechk"].checked,
                    sForm["ratechk"].checked];
    
    //boolean array for ascending(false) ,descending(true) each sort
    var sOrder = [  sForm["alphadownrad"].checked,
                    sForm["pricedownrad"].checked,
                    sForm["ratedownrad"].checked];
    
    //boolean array[price range, stock]
    var sFilters = [    sForm["pricerangechk"].checked,
                        sForm["stockchk"].checked];
    //low range price
    var sMinP = sForm["pricerangemin"].value;
    //high range price
    var sMaxP = sForm["pricerangemax"].value;
    var minLimit = sForm["pricerangemin"].getAttribute("min");
    var maxLimit = sForm["pricerangemax"].getAttribute("max");
    var priceFlag = true;
    //while(priceFlag){//corrects, pushing max value to lower limit
        //ensure prices are within limits
        if(sMinP < minLimit){
            sMinP = minLimit;
            sForm["pricerangemin"].value = minLimit;
            setMinOnMax(sForm["pricerangemin"]);
        }
        if(sMaxP > maxLimit){
            sMaxP = maxLimit;
            sForm["pricerangemax"].value = maxLimit;
            setMaxOnMin(sForm["pricerangemax"]);
        }
        priceFlag = false;
        //ensure prices are relationally correct
        if(sMinP > sMaxP){
            sMinP = minLimit;
            sForm["pricerangemin"].value = minLimit;
            setMinOnMax(sForm["pricerangemin"]);
            priceFlag = true;
        }
    //}
    //all (1)[default], available only(2), out of stock only(3)
    var sStock = 1;
    if(!sForm["stockallrad"].checked){//all stock not selected
        if(sForm["stockinrad"].checked){//see only in stock
            sStock = 2;
        }else{//out of stock must be selected
            sStock = 3;
        }
    }
    var tempArr;
    var i;
    //array of selected brands
    var sBrands = [];
    //array of selected ranges
    var sRanges = [];
    //array of selected colours
    var sColours = [];
    tempArr = $("#brands").children("div").children(":checkbox").toArray();
    for(i = 0; i < tempArr.length; i++){
        if(tempArr[i].checked){
            sBrands.push(tempArr[i].value);
        }
    }
    if(sBrands.length > 0){//if any brands were selected
        tempArr = $("#ranges").children("div").children(":checkbox").toArray();
        for(i = 0; i < tempArr.length; i++){
            if(tempArr[i].checked){
                sRanges.push(tempArr[i].value);
            }
        }
        if(sRanges.length > 0){//if any ranges were selected
            tempArr = $("#colours").children("div").children(":checkbox").toArray();
            for(i = 0; i < tempArr.length; i++){
                if(tempArr[i].checked){
                    sColours.push(tempArr[i].value);
                }
            }
            if(sColours.length == 0){
                sColours[0] = 0;
            }
        }else{
            sRanges[0] = 0;
            sColours[0] = 0;
        }
    }else{
        sBrands[0] = 0;
        sRanges[0] = 0;
        sColours[0] = 0;
    }
    //change element where contents will be posted based on whether on index and store pages or management page
    
    var theDiv = "#store";
    if(custId == "staff"){
        theDiv = "#inventory";
    }
    
    $.post(toRoot + "/pages/searchProducts.php",{ catId : category,
                                        kind: sKind,
                                        namepart: sNamePart,
                                        sort: sSort,
                                        order: sOrder,
                                        minP: sMinP,
                                        maxP: sMaxP,
                                        stock: sStock,
                                        filters: sFilters,
                                        brands: sBrands,
                                        ranges: sRanges,
                                        colours: sColours,
                                        cID: custId },
           function(data){
                $(theDiv).html(data);
            });    
}

function showSpecials(sbtn,root, cid){
    //turn off vouchers if ON
    if($("[name=voucherchk]").prop("checked")){//is already ON
        $("#voucherbtn").css("opacity",0.5);
        $("[name=voucherchk]").prop("checked",false);//turn OFF
    }
    //manage specials
    if($("[name=specialchk]").prop("checked")){//is already ON
        sbtn.style.opacity = 0.5;
        $("[name=specialchk]").prop("checked",false);//turn OFF
        showProducts(getCat(),"category",root, cid);
    }else{//already OFF
        sbtn.style.opacity = 1;
        $("[name=specialchk]").prop("checked",true);//turn ON
        showProducts(getCat(),"special",root, cid);
    }
}

function showVouchers(vbtn,root, cid){
    //turn off specials if ON
    if($("[name=specialchk]").prop("checked")){//is already ON
        $("#specialbtn").css("opacity",0.5);
        $("[name=specialchk]").prop("checked",false);//turn OFF
    }
    //manage vouchers
    if($("[name=voucherchk]").prop("checked")){//is already ON
        vbtn.style.opacity = 0.5;
        $("[name=voucherchk]").prop("checked",false);//turn OFF
        showProducts(getCat(),"category",root, cid);
    }else{//already OFF
        vbtn.style.opacity = 1;
        $("[name=voucherchk]").prop("checked", true);//turn ON
        showProducts(getCat(),"voucher",root, cid);
    }
}

function viewProduct(barcode, root){
    
    contextSwitchUser("#viewer");
    $.post(root+"/pages/viewproduct.php",{thisProduct: barcode},function(data){
        $("#viewer").html(data);
    });  
}

//allows the customer to review a product
function showReviewInput(barcode, userid){
    $("#submitReview").attr("onclick","postReview('" + barcode + "'," + userid + ")");
}

function decRating(){
    $(".rratingfld").text(function(index, original){
        if(original != "0"){
            return Number(original) - 1; 
        }
        return 0;
    });
    refreshStars();
}

function incRating(){
    $(".rratingfld").text(function(index, original){
        if(original != "10"){
            return Number(original) + 1; 
        }
        return 10;
    });
    refreshStars();
}

function refreshStars(){
    var rating = Number($(".rratingfld").text());
    $("#reditingstars").empty();
    while(rating > 0){
        if(rating - 2 >= 0){
            $("#reditingstars").append("<i class='fas fa-star'></i>");
            rating -= 2;

        }else if(rating- 1 >= 0 || rating < 1 && rating > 0){
            $("#reditingstars").append("<i class='fas fa-star-half'></i>"); 
            rating = 0;
        }
    }
}

function hideReviewInput(){
    $("#reviewform").css("display","none");
    $("#disabilitycloak").css("display","none");
    $("#makereview").css("display","none");
}

function postReview(pBarcode, userid){
    
    var reviewText = $("#rtextfld").text().trim();
    
    if(reviewText != ""){    
        var stReview = "INSERT INTO `review` Text, Rating VALUES('" + reviewText + "', " + Number($("#rratingfld").text()) + ")";
        $.post("customAction.php",{action:stReview}, function(data){
            if(data.trim() == "SUCCESS"){
                var reviewID = null;
                var getReviewId = "SELECT ReviewID FROM `review` WHERE Text = '" + reviewText + "' AND RvDateTime > DATE SUB(NOW(), INTERVAL 5 MINUTE)";
                $.post("../pages/customQuery.php", {query: getReviewId, resultkind: "singleval"}, function(data){
                    reviewID = JSON.parse(data);
                    if(reviewID != "EMPTY"){
                        var linkToCust = "UPDATE `customerproduct` SET ReviewID = " + reviewID + " WHERE BarCode = '" + pBarcode + "' AND CustomerID = " + userid + " AND TransactionID IS NOT NULL AND ReviewID IS NULL";
                        $.post("customAction.php",{action: linkToCust}, function(data){
                            if(data.trim() != "SUCCESS"){
                                $("#rtextflderr").text("Failed to post review. Please try again.");
                            }else{
                                viewProduct(pBarcode,"..");
                            }
                        });
                    }
                });
            }
        });
    }else{
        $("#rtextflderr").text("Please enter a comment.");
    }
}

//adds the selected item to the current customer's cart
function addToCart(barcode, custid){
    if($("#cartitem_"+barcode).length == 0){//this item in not yet in the customer's cart
        var remAction = "INSERT INTO `customerproduct` (CustomerID, BarCode) VALUES (" + custid + ",'" + barcode + "')";
        $.post("customAction.php",{action: remAction},function(data){

            if(data.trim() == "SUCCESS"){
                //inserted item, now load in from database
                $.post("loadCart.php", {cID : custid, newbarcode : barcode}, function(data){

                    $("#cart").append(data);//add new item to cart 
                });
            }else{
                alert("Failed to add the item to your cart.\nPlease try again.\n" + data);
            }
        });
    }else{
        alert("This item is already in your cart.");
    }
}

//generate code matching JS pattern ^([a-z][0-9]-){3}[A-Z]$
function generateVoucherCode(){
    
    var code = "";
    var alphaSecs, alphaNum, numSecs, nums;
    var c;
    var loops = 3;
    var start = 10;
    for(c = start; c <= start * loops * loops; c *= loops){
        
        //random numbers to generate alphabet characters a - z (97 - 122) 
        alphaSecs = (new Date().getSeconds() + 1) * c; //10-600
        alphaNum = Math.floor((Math.random() * 100) + alphaSecs); //10 -699
        alphaNum = alphaNum % 25 + 97;// 97-122

        //random number to generate numbers 0 - 9 (48 - 57)
        numSecs = (new Date().getSeconds() + 1) * c;
        nums = Math.floor((Math.random() * 100) + numSecs);  
        nums = nums % 9 + 48;
        
        //put code together
        code += String.fromCharCode(alphaNum) + String.fromCharCode(nums) + "-"; 
    }
    
    //capital letter to end A-Z (65 - 90)
    code += String.fromCharCode((alphaNum + nums) % 25 + 65);
    return code;
}

function fixDecimals(pfld){
                
    var oldPrice = pfld.value;
    if(!/\.\d{1,2}$/.test(oldPrice)){
        var after = /\.\d{1,2}$/.exec(oldPrice);
        var before = /^\d{1,6}/.exec(oldPrice);
        if(before == null){
            before = "0";
        }
        if(after == null){
            after = ".00";
        }
        pfld.value = before + after;
    }
}

function implPercent(original, percfld, pricefld){
    pricefld.value = Number(percfld.value) * original;
}

function updateImg(event, imgtagid){
    var recUrl = URL.createObjectURL(event.target.files[0]);
    $(imgtagid).attr("src", recUrl);
    $(imgtagid).attr("alt", recUrl);
}

function uploadImage(imginput, imgoutput, errId, doupload){
    if($(imginput).get(0).files[0] != null){
        var name = $(imginput).get(0).files[0].name;
        var fd = new FormData();
        $(errId).text("");
        var upload = true;
        //check extension
        var ext = name.split('.').pop().toLowerCase();
        if($.inArray(ext, ["png","jpg","jpeg"]) == -1){
            $(errId).text("Invalid image type");
            upload = false;
        }

        var fr = new FileReader();
        fr.readAsDataURL($(imginput).get(0).files[0]);
        var file = $(imginput).get(0).files[0];
        var fsize = file.size || file.fileSize;
        if(fsize > 2000000){
            $(errId).text("Image is too large");
            upload = false;
        }
        upload = doupload && upload;

        if(upload){
            fd.append("file",file);
            $.ajax({
                url:"uploadImg.php",
                method: "POST",
                data: fd,
                contentType: false, processData: false,
                success: function(data){
                    $(imgoutput).prop("src",data);
                    $(errId).css("color","green").text("Image Uploaded");
                }
            });
        }
        return upload;
    }else{
        return true;
    } 
}

function updateColourField(id, name, code, hex){
    $("#pcolouridfld").text(id);
    $("#pcolournamefld").text(name);
    $("#pcolourcodefld").text(code);
    $("#pcolourprev").css("background-color","#" + hex);
}