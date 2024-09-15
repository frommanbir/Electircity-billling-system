<?php 
require_once('head_html.php'); 
require_once('../Includes/config.php'); 
require_once('../Includes/session.php'); 
require_once('../Includes/admin.php'); 

// Check if bill_id is provided
if (isset($_GET['bill_id'])) {
    $bill_id = mysqli_real_escape_string($con, $_GET['bill_id']);

    // Fetch the bill details
    $query = "SELECT b.id AS bill_id, b.amount, b.ddate, u.name AS user_name 
              FROM bill b 
              JOIN user u ON b.uid = u.id 
              WHERE b.id='$bill_id' AND b.status='PAID'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $bill = mysqli_fetch_assoc($result);
    } else {
        $error = 'Invalid bill ID or the bill is not paid.';
    }
} else {
    $error = 'No bill ID provided.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Bill</title>
    <link rel="stylesheet" href="path/to/your/css/styles.css"> <!-- Update with your CSS path -->
    
</head>
<body>
    <div id="wrapper">
        <?php 
            require_once("nav.php");
            require_once("sidebar.php");
        ?>

        <div id="page-content-wrapper">
            <div class="container-fluid">
                <h1 class="page-header">Print Bill</h1>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (isset($bill)): ?>
                    <div class="bill-details">
                        <p><strong>Bill ID:</strong> <?php echo htmlspecialchars($bill['bill_id']); ?></p>
                        <p><strong>User:</strong> <?php echo htmlspecialchars($bill['user_name']); ?></p>
                        <p><strong>Amount:</strong> <?php echo htmlspecialchars($bill['amount']); ?></p>
                        <p><strong>Due Date:</strong> <?php echo htmlspecialchars($bill['ddate']); ?></p>
                    </div>

                    <div class="button-group">
                        <!-- Print Bill Button -->
                        <button class="print-button" onclick="window.print()">Print Bill</button>

                        <!-- Back to Bill List Button -->
                        <a href="pay_bill.php" class="button-group back-button">Back to Bill List</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
<style>
        .button-group {
            margin-top: 20px;
        }
        .button-group button, .button-group a {
            margin-right: 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            display: inline-block;
            text-align: center;
        }
        .print-button {
            background-color: #007bff;
        }
        .back-button {
            background-color: #6c757d;
        }
        .history-button {
            background-color: #28a745;
        }
        .done-button {
            background-color: #ffc107;
            color: black;
        }
    </style>
