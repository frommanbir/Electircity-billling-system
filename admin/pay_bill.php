<?php 
    require_once('head_html.php'); 
    require_once('../Includes/config.php'); 
    require_once('../Includes/session.php'); 
    require_once('../Includes/admin.php'); 

    // Handle form submission for paying bills
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_bill'])) {
        $bill_id = $_POST['bill_id'];
        $amount = $_POST['amount'];

        // Validate and sanitize input
        if (!empty($bill_id) && !empty($amount) && is_numeric($amount) && $amount > 0) {
            $bill_id = mysqli_real_escape_string($con, $bill_id);
            $amount = mysqli_real_escape_string($con, $amount);

            // Check if the bill is pending
            $query = "SELECT amount FROM bill WHERE id='$bill_id' AND status='PENDING'";
            $result = mysqli_query($con, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $bill_amount = mysqli_fetch_assoc($result)['amount'];

                // Ensure the amount matches the bill amount
                if ($amount == $bill_amount) {
                    // Update bill status to 'PAID'
                    $query = "UPDATE bill SET status='PAID' WHERE id='$bill_id'";
                    if (mysqli_query($con, $query)) {
                        // Insert into transactions
                        $query = "INSERT INTO transactions (bill_id, amount) VALUES ('$bill_id', '$amount')";
                        if (mysqli_query($con, $query)) {
                            $success = 'Bill paid successfully. <a href="print_bill.php?bill_id=' . urlencode($bill_id) . '" target="_blank">Print Bill</a>';
                        } else {
                            $error = 'Error processing payment: ' . mysqli_error($con);
                        }
                    } else {
                        $error = 'Error updating bill status: ' . mysqli_error($con);
                    }
                } else {
                    $error = 'Amount does not match the bill amount.';
                }
            } else {
                $error = 'No pending bill found for this ID.';
            }
        } else {
            $error = 'Invalid input. Please provide a valid bill ID and amount.';
        }
    }

    // Fetch pending bills with meter numbers
    $query = "SELECT b.id AS bill_id, b.amount, b.ddate, u.name AS user_name, u.meter_no 
              FROM bill b 
              JOIN user u ON b.uid = u.id
              WHERE b.status='PENDING'";
    $result = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay Bill</title>
    <link rel="stylesheet" href="path/to/your/css/styles.css"> <!-- Update with your CSS path -->
    <style>
        .print-button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <?php 
            require_once("nav.php");
            require_once("sidebar.php");
        ?>

        <div id="page-content-wrapper">
            <div class="container-fluid">
                <h1 class="page-header">Pay Bills</h1>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Bill ID</th>
                                <th>User</th>
                                <th>Meter No.</th> <!-- Added Meter No. header -->
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['bill_id']); ?></td>
                                    <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['meter_no']); ?></td> <!-- Display meter number -->
                                    <td><?php echo htmlspecialchars($row['amount']); ?></td>
                                    <td><?php echo htmlspecialchars($row['ddate']); ?></td>
                                    <td>
                                        <form action="pay_bill.php" method="POST">
                                            <input type="hidden" name="bill_id" value="<?php echo htmlspecialchars($row['bill_id']); ?>">
                                            <input type="number" name="amount" placeholder="Amount" min="0" step="0.01" required>
                                            <button type="submit" name="pay_bill" class="btn btn-success">Pay</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="path/to/your/js/scripts.js"></script> <!-- Update with your JS path -->
</body>
</html>
