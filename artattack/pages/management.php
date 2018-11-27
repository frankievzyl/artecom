<?php
    session_start(); 
    $dbconPath = "incMySQLConnect.php";
    $_SESSION["root"] = "..";
    $root = "..";
    
    if(isset($_SESSION["staffid"])){//check if Staff is logged in
        //creates synonym for Staff id session object
        $thisStaff = $_SESSION["staffid"];
        $hasStaff = true;
        $loadLogin = false;
    }else{       
        $hasStaff = false;
        $loadLogin = true;
        $thisStaff = 0;
    }
    $_SESSION["searcherid"] = "staff";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Art Attack: Management</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
        <link rel="stylesheet" href="../css/main.css">
        <link rel="stylesheet" href="../css/staffStyle.css">
        <link rel="stylesheet" href="../css/LightMode.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="../js/repository.js"></script>
        <script src="../js/validation.js"></script>
        <script>
            
            <?php if($loadLogin){ //loads login screen?>
                $("#accounts").ready(function(){
                    $("#accounts").load("stafflogin.php");
                    contextSwitch("#accounts"); 
                });
            
            <?php }else{ ?>
            
             //hides commands not meant for regular staff
            $("#commands").ready(function(){                    
                    if(<?php echo $thisStaff; ?> != 1){
                        $(".admin").css("display","none");
                    }    
                });
            <?php } ?>
            
            function showDetails(checkitem){
                var id = $(checkitem).attr("id");
                $(checkitem).next().children("i").toggle();
                switch(id){
                    case "pbarcodechk":
                            $("#inventory").find(".pbarcode").toggle();
                        break;
                    case "ppricechk":
                            $("#inventory").find(".pprice").toggle();
                        break;
                    case "pnamechk":
                            $("#inventory").find(".pname").toggle();
                        break;
                    case "pspecialpricechk":
                            $("#inventory").find(".pspecial").toggle();
                        break;
                    case "pspecialdatechk":
                            $("#inventory").find(".pspecialdate").toggle();
                        break;
                    case "pcatchk":
                            $("#inventory").find(".pcat").toggle();
                        break;
                    case "pstockchk":
                            $("#inventory").find(".pstock").toggle();
                        break;
                    case "psoldchk":
                            $("#inventory").find(".psold").toggle();
                        break;
                    case "pratingchk":
                            $("#inventory").find(".rrating").toggle();
                        break;
                    default:
                }
            }
            
            function logoutStaff(){
                $.post("sessions.php", {stafflogout:"logout"});
                window.location = "http://localhost/artattack/index.php";
            }
            
            function doStaff(filename){
                contextSwitch("#accounts");
                $("#accounts").load("<?php echo $root; ?>/pages/" + filename);
            }
            
            //displays table containing inventory stats
            function showInvReport(){
                contextSwitch("#reports");
                $.post("reports.php",{reportType: "inventory"}, function(data){
                    $("#reports").html(data);
                });
            }
            
            //displays table containing income and sales stats
            function showISReport(){
                contextSwitch("#reports");
                $.post("reports.php",{reportType: "incomesales"}, function(data){
                    $("#reports").html(data);
                });
            }
            
            //displays table containing transaction stats
            function showTransReport(){
                contextSwitch("#reports");
                $.post("reports.php",{reportType: "transaction"}, function(data){
                    $("#reports").html(data);
                });
            }
            
            function refreshListsStaff(tgtOpt, myLvl){
                    refreshLists(tgtOpt, myLvl);
                    $("#catchk").prop("checked",true);//set to search for categories
                    showProducts(tgtOpt.slice(9), "category","..", "staff");
            } 
            
            function showCommands(){
                $("#commands").toggle();
                $("#shiftbuttons #cmdspnlbtn i").toggle();
            }
            
            function mngInventory(){
                var pList = [];
                pList = getProductList();
                if(pList != null){
                    contextSwitch("#editor");
                    $.post("manageStock.php", {setsize : pList.length, productids : pList},
                          function(data){
                                $("#editor").html(data); 
                    });
                }
            }
            
            function mngVoucher(){
                contextSwitch("#editor");
                $("#editor").load("manageVoucher.php");
            }
            
            function mngSpecial(){
                var pList = getProductList();
                if(pList != null){
                    contextSwitch("#editor");
                    $.post("manageSpecials.php", {ins: "editspecial",
                                                setsize : pList.length,
                                                productids : pList},
                            function(data){
                                $("#editor").html(data);    
                    });
                }
            }
            
            function delSpecial(){
                var pList = getProductList();
                if(pList != null){
                    $.post("manageSpecials.php", {ins: "removespecial",
                                                 setsize: pList.length,
                                                 productids : pList},
                          function(data){
                                if(data.trim() == "FAILURE"){
                                    alert("Failed to remove special(s) for item(s).\nPlease try again.");
                                }else{
                                    alert("Special(s) removed successfully.");
                                }    
                    });
                }
            }
            
            //get an array of barcodes of the checked inventory products, or a single product barcode
            function getProductList(){
                var prodArray = [];
                $(".itemchk").each(function(){ //add selected products to list
                
                    if($(this).prop("checked")){
                        prodArray.push($(this).val());
                    }
                });
              
                var listSize = prodArray.length;
                
                if(listSize < 1){//if none selected
                    alert("Please select one or more products.");
                    return null;
                }                   
                return prodArray;
            }
            
            function contextSwitch(contextid){ 
                
                if($("#inventory").css("display") == "none"){    
                    //reestablish overall control
                    $("#inventory").css("display", "block");
                    $(contextid).css("display", "none");
                    $("button, input[type='button']").prop("disabled", false);
                    $("input").prop("disabled", false);
                    $("select").prop("disabled", false);
                    
                }else{
                    //limit control to editor area
                    $("#inventory").css("display", "none");
                    $(contextid).css("display", "block");
                    $("button, input[type='button']").prop("disabled", true);
                    $("input").prop("disabled", true);
                    $("select").prop("disabled", true);
                }   
            }
            
            function mngProd(command){
                
                switch(command){
                    case "insertproduct":
                        contextSwitch("#editor");
                        $.post("manageProducts.php", {ins: command}, function(data){
                                $("#editor").html(data);
                        });
                        break;
                    case "deleteproduct":
                        var pList = getProductList();
                        if(pList != null){
                            $.post("manageProducts.php", {ins: command, productid: pList});
                        }
                        break;
                    case "editproduct":
                    case "addcolour":
                        var pList = getProductList();
                        if(pList != null){
                            contextSwitch("#editor");
                            $.post("manageProducts.php", {ins: command, productid: pList}, function(data){
                                $("#editor").html(data);
                            });
                        }
                        break;
                    default:
                }
            }
            
            function mngAttributes(){
                contextSwitch("#editor");
                $("#editor").load("manageAttributes.php");
            }
            
            function mngUnits(){
                contextSwitch("#editor");
                $("#editor").load("manageUnits.php");
            }
            
            function mngWordAmounts(){
                contextSwitch("#editor");
                $("#editor").load("manageWordAmounts.php");
            }
            
            function mngBrands(){
                contextSwitch("#editor");
                $("#editor").load("manageBrands.php");
            }
            
            function mngRanges(){
                contextSwitch("#editor");
                $("#editor").load("manageRanges.php");
            }
            
            function mngColours(){
                contextSwitch("#editor");
                $("#editor").load("manageColours.php");
            }
            
            function mngCategories(){
                contextSwitch("#editor");
                $("#editor").load("manageCategories.php");
            }
            
            function shiftCommands(btn){
                $(btn).children().toggle();
                $("#productcmds, #specialcmds, #reportcmds, #othercmds").toggle();   
            }
            /*
            function changeCSS(cssFile, cssLinkIndex) {

                var oldlink = document.getElementsByTagName("link").item(cssLinkIndex);

                var newlink = document.createElement("link");
                newlink.setAttribute("rel", "stylesheet");
                newlink.setAttribute("type", "text/css");
                newlink.setAttribute("href", cssFile);

                document.getElementsByTagName("head").item(0).replaceChild(newlink, oldlink);
            }*/
        </script>
    </head>
    <body>
        <div id="pagegrid" class="container-fluid">
            <div class="row">
                <div id="search" class="col-sm-2">
<?php require "loadSearch.php"; ?>
                </div>
                <div class="col-sm-8">
                    <div class="container-fluid">
                        <?php include "header.php"; ?>
                        <div id="categories" class="flexline row">
                            <?php
                                require $dbconPath;

                                if($_SESSION["connected"]){                    
                                    //get all classes
                                    $stmnt = "SELECT * FROM `myclass` ORDER BY ClassLevel, SuperClass";
                                    $result = $conn->query($stmnt);

                                    if($result->num_rows > 0){
                                        $currentLevel = 0;
                                        echo "<select id=\"pcatfld$currentLevel\" onclick=\"refreshListsStaff(this.value, $currentLevel)\">";
                                        while($row = $result->fetch_assoc()){
                                            //change to next level select item if necessary
                                            if($row["ClassLevel"] != $currentLevel){
                                                $currentLevel = $row["ClassLevel"];
                                                echo "</select><select id=\"pcatfld$currentLevel\" style=\"display:none\" onclick=\"refreshListsStaff(this.value, $currentLevel)\">"; 
                                            }
                                            //add option element
                                            echo "<option value=\".optsuper" . $row["ClassID"] . "\" class=\"optsuper" . $row["SuperClass"] ."\">" . $row["Name"] . "</option>";
                                        }
                                        echo "</select>";
                                    }else{
                                        echo "EMPTY";
                                    }
                                    $conn->close();
                                }
                            ?>
                            <input type="checkbox" name="catchk" value="caton" id="catchk"><label for="catchk">Search in Category</label>
                        </div>

                        <div id="inventory" class="row"></div>
                        <div id="editor" class="row"></div>
                        <div id="accounts" class="row"></div>
                        <div id="reportSuper" class="row">
                             <div id="reports">
                                <!--start and end date selectors to define period reports are showing information on. Then report table.-->
                            </div>
                        </div>
                    </div>
                </div>
                <div id="commands" class="col-sm-2">
                    <div id="productcmds">
                        <span>PRODUCTS</span>
                        <button type="button" onclick="mngProd('insertproduct')">add new product</button>
                        <button type="button" class="admin" onclick="mngProd('editproduct')">edit product</button>
                        <button type="button" onclick="mngProd('addcolour')">add different colour&#40;s&#41;</button>
                        <button type="button" onclick="mngProd('deleteproduct')">delete product&#40;s&#41;</button>
                        <button type="button" onclick="mngInventory()">add to inventory</button>
                        <button type="button" class="admin" onclick="mngVoucher()">add new voucher</button>
                    </div>
                    <div id="specialcmds">
                        <span class="admin">SPECIALS</span>
                        <button type="button" class="admin" onclick="mngSpecial()">make special&#40;s&#41;</button>
                        <button type="button" class="admin" onclick="mngSpecial()">edit special&#40;s&#41;</button>
                        <button type="button" class="admin" onclick="delSpecial()">remove special&#40;s&#41;</button>
                    </div>
                    <div id="reportcmds">
                        <span>REPORTS</span>
                        <button type="button" onclick="showInvReport()">inventory</button>
                        <button type="button" class="admin" onclick="showISReport()" >income&amp;sales</button>
                        <button type="button" onclick="showTransReport()">transaction</button>
                        <button type="button" class="admin" onclick="">site overview</button>
                        <button type="button" class="admin" onclick="">customer behaviour</button>
                    </div>
                    <div id="othercmds" class="admin" style="display:none">
                        <span>MANAGE EXTRAS</span>
                        <button type="button" onclick="mngAttributes()">attributes</button>
                        <button type="button" onclick="mngUnits()">units</button>
                        <button type="button" onclick="mngWordAmounts()">adjectives</button>
                        <button type="button" onclick="mngBrands()">brands</button>
                        <button type="button" onclick="mngRanges()">ranges</button>
                        <button type="button" onclick="mngColours()">colours</button>
                        <button type="button" onclick="mngCategories()">categories</button>
                    </div>
                    <button type="button" class="btn admin" onclick="shiftCommands(this)"><i style="display:none" class="fas fa-arrow-alt-circle-left"></i>manage extras<i class="fas fa-arrow-alt-circle-right"></i></button>
                    <div id="accountcmds">
                        <span>ACCOUNTS</span>
                        <button type="button" onclick="logoutStaff()">Log out</button>
                        <button type="button" class="admin" onclick="doStaff('staffsignup.php')">add new staff</button>
                        <button type="button" class="admin" onclick="doStaff('staffremove.php')">remove staff</button>
                        <button type="button" class="admin" onclick="doStaff('staffreset.php')">reset staff password</button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>