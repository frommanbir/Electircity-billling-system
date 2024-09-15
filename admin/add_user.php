<?php
require_once('head_html.php');
require_once('../Includes/config.php');
require_once('../Includes/session.php');
require_once('../Includes/admin.php');

// Initialize flag
$showPopup = false;

// Handle user addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $meterNo = $_POST['meter_no']; // Updated to meter_no
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    
    // Validate input
    if (!empty($name) && !empty($meterNo) && !empty($contact) && !empty($address)) {
        // Updated SQL query to use meter_no instead of email
        $stmt = $con->prepare("INSERT INTO user (name, meter_no, phone, address) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $meterNo, $contact, $address);
        if ($stmt->execute()) {
            $showPopup = true; // Set flag to show popup
        }
        $stmt->close();
    }
}

// Fetch users
$id = $_SESSION['aid'];
$query1 = "SELECT COUNT(*) FROM user";
$result1 = mysqli_query($con, $query1);
$row1 = mysqli_fetch_row($result1);
$numrows = $row1[0];
include("paging1.php");
$result = retrieve_users_detail($_SESSION['aid'], $offset, $rowsperpage);
?>
<!DOCTYPE html>
<head>
    <title></title>
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
                        New
                        <small>Customer</small>
                    </h1>

                    <!-- Add User Form -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Add New Customer
                        </div>
                        <div class="panel-body">
                            <form method="POST" action="">
                                <div class="form-group">
                                    <label for="name">Name:</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="form-group">
                                    <label for="meter_no">Meter No.:</label>
                                    <input type="text" class="form-control" id="meter_no" name="meter_no" required 
                                    pattern="\d{12}" title="Please enter exactly 12 digits">
                                </div>
                                <div class="form-group">
                                    <label for="contact">Contact:</label>
                                    <input type="text" class="form-control" id="contact" name="contact" required 
                                    pattern="\d{10}" title="Please enter exactly 10 digits">
                                </div>
                                <div class="form-group">
                                    <label for="address">Address:</label>
                                    <input type="text" class="form-control" id="address" name="address" required>
                                </div>
                                <button type="submit" class="btn btn-primary" name="add_user">Add Customer</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Popup for success message -->
<div id="successPopup" class="popup">
    <p>Account created successfully</p>
    <button onclick="closePopup()">Close</button>
</div>

<script>
    // Show popup if flag is set
    document.addEventListener('DOMContentLoaded', function() {
        <?php if ($showPopup): ?>
            document.getElementById('successPopup').classList.add('active');
        <?php endif; ?>
    });

    function closePopup() {
        document.getElementById('successPopup').classList.remove('active');
    }
</script>
</body>
</html>

<style>
    .popup {
        display: none;
        position: fixed;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        background-color: white;
        border: 1px solid #ccc;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .popup.active {
        display: block;
    }
</style>
