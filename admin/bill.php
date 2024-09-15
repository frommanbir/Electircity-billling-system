<?php 
require_once('head_html.php'); 
require_once('../Includes/config.php'); 
require_once('../Includes/session.php'); 
require_once('../Includes/admin.php'); 
?>

<body>

    <div id="wrapper">

        <?php 
            require_once("nav.php");
            require_once("sidebar.php");
        ?>

        <!-- Page Content -->
        <div id="page-content-wrapper">

            <div class="container-fluid">

                <!-- Page Heading -->
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">
                        Generate<small> New Bill</small>
                        </h1>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div class="tab-pane fade in active" id="generate">
                                <?php
                                    // Corrected SQL query to use CURDATE()
                                    $sql = "SELECT CURDATE()";
                                    $result = mysqli_query($con, $sql);

                                    if (!$result) {
                                        echo "Error executing query: " . mysqli_error($con);
                                    } else {
                                        $row = mysqli_fetch_row($result);
                                        if ($row[0]) {
                                            include("generate_bill_table.php");
                                        } else {
                                            echo "<div class=\"text-danger text-center\" style=\"padding-top:100px; font-size: 30px;\">No bills to generate</div>";
                                            include("generate_bill_table.php");
                                        }
                                    }
                                ?>
                            </div> 

                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <?php require_once("js.php"); ?>
</body>

</html>
