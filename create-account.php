<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/animations.css">  
    <link rel="stylesheet" href="css/main.css">  
    <link rel="stylesheet" href="css/signup.css">
        
    <title>Create Account</title>
    <style>
        .container {
            animation: transitionIn-X 0.5s;
        }
    </style>
</head>
<body>
<?php
// Start session and reset user data
session_start();
$_SESSION["user"] = "";
$_SESSION["usertype"] = "";

// Set timezone
date_default_timezone_set('Asia/Karachi');
$date = date('Y-m-d');
$_SESSION["date"] = $date;

// Import database
include("connection.php");

if ($_POST) {
    $name = $_POST['name'];
    $email = $_POST['newemail'];
    $newpassword = $_POST['newpassword'];
    $cpassword = $_POST['cpassword'];
    
    if ($newpassword == $cpassword) {
        $result = $database->query("SELECT * FROM webuser WHERE email='$email';");
        if ($result->num_rows == 1) {
            $error = '<label class="form-label" style="color:rgb(255, 62, 62); text-align:center;">An account with this email already exists.</label>';
        } else {
            $database->query("INSERT INTO patient (pemail, ppassword, pname) VALUES ('$email', '$newpassword', '$name');");
            $database->query("INSERT INTO webuser VALUES ('$email', 'p')");

            // Redirect to login page after successful account creation
            header('Location: login.php');
            exit();
        }
    } else {
        $error = '<label class="form-label" style="color:rgb(255, 62, 62); text-align:center;">Password confirmation does not match.</label>';
    }
} else {
    $error = '<label class="form-label"></label>';
}
?>

<center>
    <div class="container">
        <table border="0" style="width: 50%;">
            <form action="" method="POST">
                <tr>
                    <td colspan="2">
                        <p class="header-text">Create Your Account</p>
                        <p class="sub-text">Join us today and get started.</p>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="name" class="form-label">Full Name</label>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <input type="text" name="name" class="input-text" placeholder="Your Name" required>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="newemail" class="form-label">Email Address</label>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <input type="email" name="newemail" class="input-text" placeholder="Enter your email" required>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="newpassword" class="form-label">Password</label>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <input type="password" name="newpassword" class="input-text" placeholder="Enter password" required>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <label for="cpassword" class="form-label">Confirm Password</label>
                    </td>
                </tr>
                <tr>
                    <td class="label-td" colspan="2">
                        <input type="password" name="cpassword" class="input-text" placeholder="Confirm password" required>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php echo $error ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="reset" value="Reset" class="login-btn btn-primary-soft btn">
                    </td>
                    <td>
                        <input type="submit" value="Sign Up" class="login-btn btn-primary btn">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <br>
                        <label class="sub-text">Already have an account? </label>
                        <a href="login.php" class="hover-link1 non-style-link">Login</a>
                        <br><br><br>
                    </td>
                </tr>
            </form>
        </table>
    </div>
</center>
</body>
<script src="script/jquery.min.js"></script>
<script src="script/common.js"></script>
</html>
