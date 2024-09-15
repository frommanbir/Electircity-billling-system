<?php
require_once("Includes/config.php");
require_once("Includes/session.php");

// Handle admin redirection based on login status
if (isset($_SESSION['logged']) && $_SESSION['logged'] == true) {
    if ($_SESSION['account'] == "admin") {
        header("Location:admin/index.php");
        exit(); // Important to stop further script execution after redirection
    } else {
        // If not admin, redirect to an error page or home
        header("Location:../index.php");
        exit(); // Important to stop further script execution after redirection
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $email = $_POST['email'];
    $pass = $_POST['pass'];

    // Basic validation (can be extended)
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($pass)) {
        // Example login logic for admin
        if ($email === 'admin@example.com' && $pass === 'password') {
            $_SESSION['logged'] = true;
            $_SESSION['account'] = 'admin';
            header("Location:admin/index.php");
            exit();
        } else {
            $err_login = "Invalid credentials";
        }
    } else {
        $err_login = "Please enter a valid email and password";
    }
}
?>
<!DOCTYPE html> 
<html lang="en">
<head>
    <title>E-bill System</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/css/dash_admin.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
   
</head>
<body>
    <div class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php"><b>Billing System</b></a>
            </div>
            <div class="navbar-collapse collapse">
                <!-- Login Form -->
                <form action="index.php" class="navbar-form navbar-right" role="form" method="post">
                    <div class="form-group">
                        <input type="text" placeholder="Email" name="email" id="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="password" placeholder="Password" name="pass" id="pass" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-success">Sign In</button>
                    <?php if (isset($err_login)) { echo "<div class='alert alert-danger' role='alert'>$err_login</div>"; } ?>
                </form>
            </div>
        </div>
    </div>

    <div class="headerwrap">
        <div class="container">
            <h1>Welcome</h1>
            <img src="./image/hydro.jpg" alt="Electricity Billing System">
        </div>
    </div>

    <script>
        function validateForm() {
            var x = document.forms["myForm"]["email"].value;
            var atpos = x.indexOf("@");
            var dotpos = x.lastIndexOf(".");
            if (atpos < 1 || dotpos < atpos + 2 || dotpos + 2 >= x.length) {
                alert("Not a valid e-mail address");
                return false;
            }
        }
    </script>
</body>
</html>

 <style>
        body {
            background-color: #f0f8ff; /* Light blue background */
            font-family: Arial, sans-serif;
        }
        .headerwrap {
            padding: 80px 0;
            background-color: #ffffff; /* White background for a cleaner look */
            text-align: center;
        }
        .headerwrap h1 {
            color: #333; /* Darker color for better contrast */
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        .headerwrap img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow effect */
        }
        .navbar {
            border-bottom: 1px solid #ddd; /* Subtle border */
        }
        .navbar-brand {
            font-weight: bold;
        }
        .navbar-collapse {
            text-align: right; /* Align login to the right */
        }
        .navbar-nav {
            display: inline-block;
        }
    </style>
