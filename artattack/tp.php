<?php
    $testArr = array("berry"=>"wordberry", "barley"=>"wordbarley", "crayon"=>"wordcrayon");
?>
<!DOCTYPE html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<html>
<body>
    <script>
    
        function showSelect(){
            $('#showurl').text("Pick up" == $('[name=deliverymethodfld]').val());
        }
         function printTestArr(){
             
             var field = null;
                var testarr = <?php echo json_encode($testArr); ?> ;
                /*if(testarr[0] != field){
                    alert(field);
                    return;
                }*/
                var index = testarr.length;
                testarr.berry = "wordapple";
                testarr.push = {banana:"wordbanana"};
             
                //for(var i in testarr){
                    
                        console.log("same is " + testarr.berry);
                    
                //}
            }
    </script>
    <p><input type="file"  accept="image/*" name="image" id="file"  onchange="loadFile(event)" style="display: none;"></p>
<p><label for="file" style="cursor: pointer;">Upload Image</label></p>
<p><img id="output" width="200" /></p>

    
    <div id="deliverymethod">
                    <label class="formlabels">Delivery method&#58;</label>
                    <select name="deliverymethodfld">
                        <option value="Post Office CtC">Post Office Counter&#45;to&#45;Counter</option>
                        <option value="Cape Town Courier">Cape Town Courier</option>
                        <option selected value="Courier DtD">Courier Door&#45;to&#45;Door</option>
                        <option value="Pick up">Pick up at store</option>
                    </select>
                    <span id="deliverymethoderr" class="formerrors"></span>
                </div>
    <button type="button" onclick="showSelect()">show select</button>
<script>
var loadFile = function(event) {
	var image = document.getElementById('output');
    var urlrec = URL.createObjectURL(event.target.files[0]);
	image.src = urlrec;
    var para = document.getElementById("showurl");
    para.textContent = urlrec;
};
</script>
    <p id="showurl"></p>
    </body>
</html>

<!--function imageTester(){
	var myimg = document.getElementById("myversion");
    var prev = document.getElementById("preview");
    var imgVal = myimg.value.split("\\");
    var ft = imgVal[imgVal.length -1];
    prev.textContent = ft;
}-->

else if($hasCustomer){ ?>
            
                 //hides cart for illegal logins or non-regiestered customers
                $("#cart").ready(function(){                    
                        $("#cart").load("loadCart.php");  
                    });
            <?php }else{ ?>
                        $("#cart").html("<span id=\"cartMsg\">You must be logged in to see your cart.</span>");
                <?php } ?>