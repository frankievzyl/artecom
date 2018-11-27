<?php
    session_start();
    if(isset($_POST["reportType"])){
        
        $report = $_POST["reportType"];
        
        
        if(isset($_POST["startDate"])){ //isset to be removed once period input fields are made
            $startDate = date("Y/m/d",$_POST["startDate"]);    
        }else{
            $startDate = date("Y/m/d",strtotime("1 January 2000"));
        }        
        if(isset($_POST["endDate"])){
            $endDate = date("Y/m/d",$_POST["endDate"]);
        }else{
            $endDate = date("Y/m/d"); //current time
        }
            
        require "incMySQLConnect.php";
        
        if($_SESSION["connected"]){
            switch($report){
                case "inventory":
                    $stmnt = "SELECT * FROM `inventoryreport` WHERE TrDateTime BETWEEN '$startDate' AND '$endDate'";
                    
                    $result = $conn->query($stmnt);
                    
                    if($result->num_rows > 0){?>
                        
                        <div id="tablescroller">
                        <table class="reporttable">
                            <colgroup>
                                <col class="bcclm"><col class="nameclm"><col class="thinclm"><col class="thinclm"><col class="dateclm">
                            </colgroup>
                            <tr><th>BarCode</th><th>Product Name</th><th>Inventory Level</th><th>Items Sold</th><th>Transaction Date</th></tr>
                    <?php
                        while($row = $result->fetch_assoc()){
                            
                                echo "<tr><td>" . $row["BarCode"] . "</td><td style='text-align:left;'>" . $row["Name"] . "</td><td>" . $row["StockLevel"] . "</td><td>" . $row["TotalSoldThisPeriod"] . "</td><td>" . date("d/m/Y",strtotime($row["TrDateTime"])) . "</td></tr>";
                        }
                        echo "</table></div>";
                    }else{
                        echo "<span>No information to fill this table could be found.</span>";
                    }
                    break;
                case "incomesales":
                    $stmnt = "SELECT * FROM `incomesalesreport` WHERE TrDateTime BETWEEN '$startDate' AND '$endDate'";
                    
                    $result = $conn->query($stmnt);
                    
                    if($result->num_rows > 0){ ?>
                         <div id="tablescroller">
                        <table class='reporttable'>
                            <colgroup>
                                <col class="bcclm"><col class="nameclm"><col class="moneyclm"><col class="thinclm"><col class="moneyclm"><col class="moneyclm">
                            </colgroup>
                            <tr><th>BarCode</th><th>Product Name</th><th>Current Price</th><th>Items Sold</th><th>Sub Total Now</th><th>Total on Transaction</th></tr>
                            <?php
                        while($row = $result->fetch_assoc()){
                            
                                echo "<tr><td>" . $row["BarCode"] . "</td><td>" . $row["Name"] . "</td><td>R " . $row["Price"] . "</td><td>" . $row["Quantity"] . "</td><td><i class='currency'>R</i> " . $row["Quantity"] * $row["Price"] . "</td><td>" . $row["TotalAmount"] . "</td></tr>";
                        }
                        echo "</table></div>";
                    }else{
                        echo "<span>No information to fill this table could be found.</span>";
                    }
                    break;
                case "transaction":
                    $stmnt = "SELECT * FROM `transactionreport` WHERE TrDateTime BETWEEN '$startDate' AND '$endDate'";
                    
                    $result = $conn->query($stmnt);
                    
                    if($result->num_rows > 0){ ?>
                        <div id="tablescroller">
                        <table class='reporttable'>
                            <colgroup>
                                <col class="thinclm"><col class="dateclm"><col class="timeclm"><col class="moneyclm"><col class="bcclm"><col class="nameclm"><col class="thinclm">
                            </colgroup>
                            <tr><th>Transaction ID</th><th>Transaction Date</th><th>Transaction Time</th><th>Total Amount</th><th>Product Barcode</th><th>Product Name</th><th>Units Sold</th></tr>
                            <?php
                        while($row = $result->fetch_assoc()){
                            
                                echo "<tr><td>" . $row["TransactionID"] . "</td><td>" . date("d/m/Y",strtotime($row["TrDateTime"])) . "</td><td>" . date("h:i:s - a",strtotime($row["TrDateTime"])) . "</td><td><i class='currency'>R</i> " . $row["TotalAmount"] ."</td><td>" . $row["BarCode"] ."</td><td>" . $row["Name"] . "</td><td>" . $row["Quantity"] . "</td></tr>";
                        }
                        echo "</table></div>";
                    }else{
                        echo "<span>No information to fill this table could be found.</span>";
                    }
                    break;
            }
        }else{
            echo "<span>A connection to the database could not be established.<br>Please try again.</span>";
        }
        $conn->close();
    }else{
        echo "<span>An error occured and the report cannot be loaded.<br>Please try again.</span>";
    }
?>
                            <button type="button" onclick="contextSwitch('#reports')">Return</button>
