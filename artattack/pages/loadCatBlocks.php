<?php
    require $dbconPath;
    
    if(isset($_SESSION["searcherid"])){
        $searcherId = $_SESSION["searcherid"];    
    }else{
        $searcherId = 0;
    }

$root = $_SESSION["root"];
    if($_SESSION["connected"]){                    
        //get all categories
        $stmnt = "SELECT ClassID, Name, SuperClass AS Super, (SELECT SuperClass FROM `myclass` WHERE ClassID = Super) AS ReturnClass FROM `myclass` ORDER BY SuperClass";
        $result = $conn->query($stmnt);

        if($result->num_rows > 0){
            $currentSuper = null; 
?>
            <div id="catsuper<?php echo $currentSuper; ?>" class="flexgrid"> <!--first catblock start-->
<?php 
            $returnSuper = null;
            while($row = $result->fetch_assoc()){
//change to next superclass div when necessary
                if($row["Super"] != $currentSuper){
                    if($currentSuper != null){ 
?>
                        <button class="prevcatbtn" onclick="gotoPrevCatBlock(<?php echo $currentSuper . ",'" . $returnSuper; ?>')">Go back</button>
<?php
                    }
                    $currentSuper = $row["Super"];
                    $returnSuper = "#catsuper" . $row["ReturnClass"];
?>
            </div>
            <div id="catsuper<?php echo $currentSuper; ?>" style="display:none">
<?php
                }
//add individual category div element 
?>
                <div id="cb<?php echo $row["ClassID"];?>" onclick="gotoNextCatBlock('#catsuper<?php echo $currentSuper; ?>','#catsuper<?php echo $row["ClassID"]; ?>')"><?php echo $row["Name"]; ?></div>                       
<?php
            }
?>  
                <button class="prevcatbtn btn" onclick="gotoPrevCatBlock(<?php echo $currentSuper; ?>, '<?php echo $returnSuper; ?>')">Go back</button>
            </div><!--catblock end-->
<?php
        }else{
            echo "EMPTY";
        }
        $conn->close();
    }
?>
<script>
    function gotoNextCatBlock(thisSuper, nextSuper){
        if($(nextSuper).length == 0){//no further categories down
            
            showProducts(nextSuper.slice(9),"category","<?php echo $root; ?>",<?php echo $searcherId; ?>);

        }else{//display child categories
            $(thisSuper).css("display","none");
            $(nextSuper).css("display","flex");    
        }
    }

    function gotoPrevCatBlock(thisSuper,prevSuper){
        $(prevSuper).css("display","flex");
        var superId = "#catsuper" + thisSuper;
        $(superId).css("display","none");

    }

</script>