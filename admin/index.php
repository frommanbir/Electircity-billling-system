<?php 
require_once('head_html.php'); 
require_once('../Includes/config.php'); 
require_once('../Includes/session.php'); 
require_once('../Includes/admin.php'); 

// Fetch the required statistics for late users and total pending bills
list($lateUsersResult, $pendingBillsResult) = retrieve_users_defaulting($_SESSION['aid']);
$lateUsersCount = mysqli_fetch_row($lateUsersResult) ?: [0];
$pendingBillsCount = mysqli_fetch_row($pendingBillsResult) ?: [0];

// Fetch admin statistics for generated bills 
list($generatedBillsResult, $anotherResult) = retrieve_admin_stats($_SESSION['aid']);
$generatedBillsCount = mysqli_fetch_row($generatedBillsResult) ?: [0];
$anotherCount = mysqli_fetch_row($anotherResult) ?: [0];

// Check if any of the queries failed
if ($lateUsersResult === FALSE || $pendingBillsResult === FALSE || $generatedBillsResult === FALSE || $anotherResult === FALSE) {
    die("Error retrieving admin stats: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<head>
    <title>Dashboard</title>
</head>
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
                            Dashboard
                            <small> Overview</small>
                        </h1>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-xs-6">
                        <div class="panel panel-bolt">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-warning fa-3x"></i>
                                    </div>
                                    <div class="col-md-9 text-right">
                                        <div class="huge"><?php echo htmlspecialchars($lateUsersCount[0]); ?></div>
                                        <div>Late Customers</div>
                                    </div>
                                </div>
                            </div>
                            <a href="#" data-toggle="modal" data-target="#late"></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-xs-6">
                        <div class="panel panel-bolt2">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-spinner fa-3x"></i>
                                    </div>
                                    <div class="col-md-9 text-right">
                                        <div class="huge"><?php include('pendingcount.php'); ?></div>
                                        <div>Total Pending Bills</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                        <div class="panel panel-bolt">
                            <div class="panel-heading">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <i class="fa fa-file fa-3x"></i>
                                    </div>
                                    <div class="col-md-9 text-right">
                                        <div class="huge"><?php echo htmlspecialchars($generatedBillsCount[0]); ?></div>
                                        <div>Generated Bills</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php 
    require_once("js.php");
    ?>

</body>
</html>
