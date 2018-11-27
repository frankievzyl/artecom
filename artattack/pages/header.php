<?php 
    if(isset($_SESSION["searcherid"])){
        $searcherId = "" . $_SESSION["searcherid"];    
    }else{
        $searcherId = "0";
    }
    
    $root = $_SESSION["root"];

    if($searcherId != "staff"){
?>
<header class="row">
    <img id="companylogo" class="col-sm-5" src="<?php echo $root; ?>/images/art_attack_logo.jpg" alt="ArtAttack Logo">
    <div class="col-sm-2 filler"></div>
    <div id="accountbtns" class="col-sm-5">                
    <?php

        if($searcherId != "0"){ ?>
            <button onclick="doAccount('profile.php')" id="custprofbtn" class="btn"><i class="fas fa-portait"></i>My Profile</button><button type="button" class="btn" id="custlogoutbtn" onclick="logoutCust()"><i class="fas fa-door-open"></i>Log out</button>
      <?php
        }else{ 
            if($root == ".."){ ?>

            <button onclick="doAccount('customerlogin.php')" id="custloginbtn" class="btn"><i class="fas fa-id-card"></i>Log in</button>
            <button onclick="doAccount('customersignup.php')" id="custsignupbtn" class="btn"><i class="fab fa-wpforms"></i>Register</button>
      <?php 
            }else{ ?>
             <button onclick="setAccAction('login')" id="custloginbtn" class="btn"><i class="fas fa-id-card"></i>Log in</button>
            <button onclick="setAccAction('signup')" id="custsignupbtn" class="btn"><i class="fab fa-wpforms"></i>Register</button>
        <?php
             }
        }
    ?>
     </div>
</header>
<?php   
  }
    if($root == "."){ //index page ?>
    <div id="shiftbuttons" class="row">
        <button type="button" id="searchpnlbtn" class="panelbtn btn col-xs-6" onclick="showSearch()"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i>Search and Filter</button>
        <button type="button" id="catpnlbtn" class="panelbtn col-xs-6 btn" onclick="showCatbar()"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i>Categories</button>
    </div>
<?php }else{
        if($searcherId == "staff"){ //staff page ?>
            <div id="shiftbuttons" class="row">
                    <button type="button" id="searchpblbtn" class="panelbtn btn" onclick="showSearch()"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i>Search and Filter</button>
                    <button type="button" id="catpnlbtn" class="panelbtn btn" onclick="showCatbar()"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i>Categories</button>
                    <button type="button" id="cmdspnlbtn" class="panelbtn btn" onclick="showCommands()"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i>Commands</button>
                </div>
                <div id="details" class="row">
                    <input type="checkbox" onchange="showDetails(this)" checked id="pbarcodechk"><label for="pbarcodechk"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i>barcode</label>
                    <input type="checkbox" onchange="showDetails(this)" checked id="ppricechk"><label for="ppricechk"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i>price</label>
                    <input type="checkbox" onchange="showDetails(this)" checked id="pnamechk"><label for="pnamechk"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i>name</label>
                    <input type="checkbox" onchange="showDetails(this)" checked id="pspecialpricechk"><label for="pspecialpricechk"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i>special price</label>
                    <input type="checkbox" onchange="showDetails(this)" checked id="pspecialdatechk"><label for="pspecialdatechk"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i>special end date</label>
                    <input type="checkbox" onchange="showDetails(this)" checked id="pcatchk"><label for="pcatchk"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i>category</label>
                    <input type="checkbox" onchange="showDetails(this)" checked id="pstockchk"><label for="pstockchk"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i>stock level</label>
                    <input type="checkbox" onchange="showDetails(this)" checked id="psoldchk"><label for="psoldchk"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i>number sold</label>
                    <input type="checkbox" onchange="showDetails(this)" checked id="pratingchk"><label for="pratingchk"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i>rating</label>
                </div>
    <?php }else{ //store page ?>
            <div id="shiftbuttons" class="row">
                <button type="button" id="searchpblbtn" class="panelbtn btn" onclick="showSearch()"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i>Search and Filter</button>
                <button type="button" id="catpnlbtn" class="panelbtn btn" onclick="showCatbar()"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i>Categories</button>
                <?php
                    if($searcherId != "0"){ ?>
                        <button type="button" id="cartpnlbtn" class="panelbtn btn" onclick="showCart()"><i class="far fa-eye"></i><i class="fas fa-eye-slash"></i><i class="fas fa-shopping-cart"></i></button>
        <?php
                    }
        ?>
            </div>
    <?php }

    }
?>
   



             