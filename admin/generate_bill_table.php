<div class="table-responsive">
    <table class="table table-hover table-striped table-bordered table-condensed">
        <thead>
            <tr>
                <th>USER</th>
                <th>METER NO.</th> <!-- Added Meter No. header -->
                <th>UNITS</th>
                <th>BILL DATE</th>
                <th>DUE DATE</th>
                <th>GENERATE</th>                                        
            </tr>
        </thead>
        <tbody>
            <?php 
            // SQL query to count the total number of users
            $query1 = "SELECT COUNT(*) FROM user";
            $result1 = mysqli_query($con, $query1);
            $row1 = mysqli_fetch_row($result1);
            $numrows = $row1[0];
            include("paging1.php");                       

            // Retrieve bill data with pagination
            $result = retrieve_bill_data($offset, $rowsperpage);

            while($row = mysqli_fetch_assoc($result)){
                // Get the meter number for the current user
                $user_id = $row['uid'];
                $query_meter = "SELECT meter_no FROM user WHERE id = $user_id";
                $result_meter = mysqli_query($con, $query_meter);
                $user_info = mysqli_fetch_assoc($result_meter);
                $meter_no = $user_info['meter_no'];
            ?>
                <tr>
                    <form action="generate_bill.php" method="post" name="form_gen_bill_<?php echo $row['uid']; ?>" onsubmit="return checkInp(this)">
                    <?php
                        // Query to check if a bill has already been generated for the given date
                        $query3 = "SELECT bdate as bdate1 FROM bill WHERE uid={$row['uid']} ORDER BY id DESC";
                        $result3 = mysqli_query($con, $query3);
                        $flag = 0;
                        while($row2 = mysqli_fetch_assoc($result3)){
                            if($row2['bdate1'] == $row['bdate']) {
                                $flag = 1;
                                break; // Exit loop early if a match is found
                            }
                        }
                        
                        if($flag == 0) {
                    ?>
                        <input type="hidden" name="uid" value="<?php echo htmlspecialchars($row['uid']); ?>">
                        <input type="hidden" name="uname" value="<?php echo htmlspecialchars($row['uname']); ?>">
                        
                        <td height="50">
                            <?php echo htmlspecialchars($row['uname']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($meter_no); ?> <!-- Display meter number -->
                        </td>
                        <td>                                                  
                            <input class="form-control" type="tel" name="units" placeholder="ENTER UNITS">
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['bdate']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['ddate']); ?>
                        </td>
                        <td>
                            <button type="submit" name="generate_bill" class="btn btn-success form-control">GENERATE BILL</button>
                        </td>
                    <?php 
                        } 
                    ?>
                    </form>
                </tr>                
                <?php 
                    } 
                ?>
            </tbody>                
        </table>
        <?php include("paging2.php"); ?>
    </div>
    
<script>
     function checkInp(form)
     {
           var units = form["units"].value;
           if (isNaN(units) || units.trim() === "") 
           {
             alert("Please input a valid number for units");
             return false;
           }
           return true;
     }
</script>
