<?php
require_once('head_html.php');
require_once('../Includes/config.php');
require_once('../Includes/session.php');
require_once('../Includes/admin.php');

// Initialize variables
$username = '';
$transactions = [];
$totalAmount = 0.00; // Initialize total amount
$error = '';

// Get current date
$currentDate = date('Y-m-d');

// Handle search
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Determine the date range based on the selected option
    $dateRange = isset($_GET['range']) ? $_GET['range'] : 'all'; // Default to 'all'

    // Set start and end dates based on the selected range
    switch ($dateRange) {
        case 'today':
            $startDate = date('Y-m-d', strtotime('monday this week'));
            $endDate = date('Y-m-d', strtotime('sunday this week'));
        case 'week':
            $startDate = date('Y-m-d', strtotime('monday this week'));
            $endDate = date('Y-m-d', strtotime('sunday this week'));
            break;
        case 'month':
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
            break;
        default:
            $startDate = date('Y-m-01');
            $endDate = date('Y-m-t');
            break;
    }

    // Handle user search
    if (isset($_GET['search']) && !empty($_GET['username'])) {
        $username = mysqli_real_escape_string($con, $_GET['username']);

        // Fetch user IDs based on the username
        $userQuery = "SELECT id FROM user WHERE name LIKE '%$username%'";
        $userResult = mysqli_query($con, $userQuery);

        if ($userResult && mysqli_num_rows($userResult) > 0) {
            $userIds = [];
            while ($user = mysqli_fetch_assoc($userResult)) {
                $userIds[] = $user['id'];
            }
            $userIdsList = implode(',', $userIds);

            // Fetch transactions for the searched user IDs and within the date range
            $transactionQuery = "SELECT t.id AS transaction_id, t.bill_id, t.amount AS transaction_amount, t.date AS transaction_date, 
                                        b.amount AS bill_amount, b.ddate, u.name AS user_name 
                                 FROM transactions t 
                                 JOIN bill b ON t.bill_id = b.id 
                                 JOIN user u ON b.uid = u.id 
                                 WHERE u.id IN ($userIdsList) AND t.date BETWEEN '$startDate' AND '$endDate'";
            $transactions = mysqli_query($con, $transactionQuery);

            if (!$transactions) {
                $error = 'Error fetching transactions: ' . mysqli_error($con);
            }
        } else {
            $error = 'No users found with that username.';
        }
    } else {
        // Fetch all transactions within the date range
        $transactionQuery = "SELECT t.id AS transaction_id, t.bill_id, t.amount AS transaction_amount, t.date AS transaction_date, 
                                    b.amount AS bill_amount, b.ddate, u.name AS user_name 
                             FROM transactions t 
                             JOIN bill b ON t.bill_id = b.id 
                             JOIN user u ON b.uid = u.id 
                             WHERE t.date BETWEEN '$startDate' AND '$endDate'";
        $transactions = mysqli_query($con, $transactionQuery);

        if (!$transactions) {
            $error = 'Error fetching transactions: ' . mysqli_error($con);
        }
    }

    // Calculate total amount from the fetched transactions
    if ($transactions && mysqli_num_rows($transactions) > 0) {
        // Calculate total amount directly from the fetched transactions
        while ($transaction = mysqli_fetch_assoc($transactions)) {
            $totalAmount += $transaction['transaction_amount'];
        }
        
        // Reset the result pointer to the start
        mysqli_data_seek($transactions, 0);
    }
}
?>

<!DOCTYPE html>
<head>
    <title>Transaction History</title>
</head>
<body>
    <div id="wrapper">
        <?php 
            require_once("nav.php");
            require_once("sidebar.php");
        ?>

        <div id="page-content-wrapper">
            <div class="container-fluid">
                <h1 class="page-header">Transaction History</h1>

                <div class="date-filter">
                    <form action="transaction_history.php" method="GET">
                        <label><input type="radio" name="range" value="today" <?php echo ($dateRange === 'today') ? 'checked' : ''; ?>> Today</label>
                        <label><input type="radio" name="range" value="week" <?php echo ($dateRange === 'week') ? 'checked' : ''; ?>> This Week</label>
                        <label><input type="radio" name="range" value="month" <?php echo ($dateRange === 'month') ? 'checked' : ''; ?>> This Month</label>
                        <label><input type="radio" name="range" value="all" <?php echo ($dateRange === 'all') ? 'checked' : ''; ?>> All Time</label>
                        <button type="submit">Filter</button>
                    </form>
                </div>

                <div class="search-form">
                    <form action="transaction_history.php" method="GET">
                        <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Search by username">
                        <button type="submit" name="search">Search</button>
                        <input type="hidden" name="range" value="<?php echo htmlspecialchars($dateRange); ?>">
                    </form>
                </div>

                <?php if (isset($error) && $error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (isset($transactions) && mysqli_num_rows($transactions) > 0): ?>
                    <div class="transaction-table">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Bill ID</th>
                                    <th>User</th>
                                    <th>Bill Amount</th>
                                    <th>Due Date</th>
                                    <th>Transaction Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($transaction = mysqli_fetch_assoc($transactions)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($transaction['transaction_id']); ?></td>
                                        <td><?php echo htmlspecialchars($transaction['bill_id']); ?></td>
                                        <td><?php echo htmlspecialchars($transaction['user_name']); ?></td>
                                        <td><?php echo htmlspecialchars($transaction['bill_amount']); ?></td>
                                        <td><?php echo htmlspecialchars($transaction['ddate']); ?></td>
                                        <td><?php echo htmlspecialchars($transaction['transaction_date']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                        <div class="total-amount">
                            Total Amount: Rs.<?php echo number_format($totalAmount, 2); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">No transactions found for the given criteria.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php require_once("js.php"); ?>
</body>
</html>
