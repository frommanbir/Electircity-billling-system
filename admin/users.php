<?php 
    require_once('head_html.php'); 
    require_once('../Includes/config.php'); 
    require_once('../Includes/session.php'); 
    require_once('../Includes/admin.php');

    // Handle user removal
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_user'])) {
        $user_name = $_POST['user_name'];
        
        // Validate input
        if (!empty($user_name)) {
            $user_name = mysqli_real_escape_string($con, $user_name); // Escape user input
            $query = "DELETE FROM user WHERE name = '$user_name'";
            mysqli_query($con, $query);
        }
    }
?>

<body>
    <div id="wrapper">
        <?php 
            require_once("nav.php");
            require_once("sidebar.php");
        ?>

        <div id="page-content-wrapper">
            <div class="container-fluid">
                <h1 class="page-header">Customer<small>Details</small></h1>
                <ol class="breadcrumb">
                  <li>User</li>
                  <li class="active">Details</li>
                </ol>

                <!-- Add User Link -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <a href="add_user.php" target="_blank" class="btn btn-primary">Add New Customer</a>
                    </div>
                </div>

                <!-- User Table -->
                <div class="table-responsive" style="padding-top: 0">
                    <table class="table table-hover table-bordered table-condensed">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Meter No.</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $id = $_SESSION['aid'];
                                $query1 = "SELECT COUNT(*) FROM user";
                                $result1 = mysqli_query($con, $query1);
                                $row1 = mysqli_fetch_row($result1);
                                $numrows = $row1[0];
                                include("paging1.php");
                                $result = retrieve_users_detail($_SESSION['aid'], $offset, $rowsperpage);

                                $cnt = 1;
                                while ($row = mysqli_fetch_assoc($result)) {
                            ?>
                                <tr>
                                    <td height="50"><?php echo $cnt; ?></td>
                                    <td><?php echo $row['name'] ?></td>
                                    <td><?php echo $row['meter_no'] ?></td>
                                    <td><?php echo $row['phone'] ?></td>
                                    <td><?php echo $row['address'] ?></td>  
                                    <td>
                                        <button type="button" class="btn btn-danger" onclick="showMessageBox('<?php echo htmlspecialchars($row['name']); ?>')">Remove</button>
                                    </td>                                                    
                                </tr>
                            <?php 
                                $cnt++; 
                                } 
                            ?>
                        </tbody>
                    </table>
                    <?php include("paging2.php"); ?>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Confirmation Message Box -->
    <div id="messageBox" class="message-box" style="display: none;">
        <div class="message-box-content">
            <p>Are you sure you want to remove this user?</p>
            <form id="confirmForm" method="POST" action="">
                <input type="hidden" name="user_name" id="messageBoxUserName">
                <button type="button" class="btn btn-secondary" onclick="hideMessageBox()">Cancel</button>
                <button type="submit" class="btn btn-danger" name="remove_user">Yes, Remove</button>
            </form>
        </div>
    </div>

    <!-- JavaScript for handling message box visibility -->
    <script type="text/javascript">
        function showMessageBox(userName) {
            document.getElementById('messageBoxUserName').value = userName;
            document.getElementById('messageBox').style.display = 'block';
        }

        function hideMessageBox() {
            document.getElementById('messageBox').style.display = 'none';
        }
    </script>

    <!-- CSS for Message Box -->

    <style>
        .message-box {
            position: fixed;
            top: 60%;
            left: 74.8%;
            transform: translate(-50%, -50%);
            width: 300px;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            text-align: center;
        }

        .message-box-content {
            margin: 20px;
        }

        .message-box button {
            margin: 5px;
        }
    </style>
