<?php 
session_start(); 

$rndCustomer = 0;
$dbconPath = "pages/incMySQLConnect.php";
$root = ".";
$_SESSION["root"] = ".";

$_SESSION["customerid"] = null;
$_SESSION["staffid"] = null;
$_SESSION["searcherid"] = $rndCustomer;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Art Attack: Home</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/main.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">
        <script src="js/repository.js"></script>
        <script src="js/validation.js"></script>
        <script>

            function refreshListsIndex(tgtOpt, myLvl){
                refreshLists(tgtOpt, myLvl);
                $("#catchk").prop("checked",true);//set to search for categories
                showProducts(tgtOpt.slice(9), "category","<?php echo $root; ?>",<?php echo $rndCustomer; ?>);
            }
            
            function setAccAction(caction){
                $.post("sessions.php",{inorup: caction});
                window.location.href = "http://localhost/artattack/pages/store.php";
            }
        </script>
    </head>
    <body>
        <div id="pagegrid" class="container-fluid">
            <?php require $root . "/pages/header.php"; ?>        
            <div class="row">
                <div id="search" class="col-sm-2">
<?php require "pages/loadSearch.php"; ?>
                </div>
                <div class="col-sm-10">
                    <div class="container-fluid">
                        
                        <div id="categories" class="flexline row">
<?php
    require "./pages/incMySQLConnect.php";

    if($_SESSION["connected"]){                    
        //get all classes
        $stmnt = "SELECT * FROM `myclass` ORDER BY ClassLevel, SuperClass";
        $result = $conn->query($stmnt);

        if($result->num_rows > 0){
            $currentLevel = 0;
            echo "<select id=\"pcatfld$currentLevel\" onclick=\"refreshListsIndex(this.value, $currentLevel)\">";
            while($row = $result->fetch_assoc()){
                //change to next level select item if necessary
                if($row["ClassLevel"] != $currentLevel){
                    $currentLevel = $row["ClassLevel"];
                    echo "</select><select id=\"pcatfld$currentLevel\" style=\"display:none\" onclick=\"refreshListsIndex(this.value, $currentLevel)\">"; 
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
                            <input type="checkbox" name="catchk" value="caton" id="catchk" checked><label for="catchk">Search in Category</label>
                
                        </div>
                        <div id="special" class="row">
                            <div class="col-xs-2 filler"></div>
                            <div id="specialbtn" class="col-xs-3" onclick="showSpecials(this,'<?php echo $root; ?>',<?php echo $rndCustomer; ?>)"><i class="fas fa-tags"></i><input type="checkbox" name="specialchk" style="display:none"></div>
                            <div class="col-xs-2 filler"></div>
                            <div id="voucherbtn" class="col-xs-3" onclick="showVouchers(this,'<?php echo $root; ?>',<?php echo $rndCustomer; ?>)"><i class="fas fa-money-check-alt"></i><input type="checkbox" name="voucherchk" style="display:none"></div>
                            <div class="col-xs-2 filler"></div>
                        </div>
                        <div id="mainactivity" class="row">    
                            <div id="store" class="col-xs-12">
                                <div id="catblocks">
                                <?php
                                    require $root . "/pages/loadCatBlocks.php";
                                ?>
                                </div>
                            </div>
                            <div id="viewer" class="col-xs-12"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include $root . "/pages/footer.php"; ?>
        </div>
    </body>
</html>