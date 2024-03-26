<?php
// Initialize the session
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: sign-in.php");
    exit;
}

// Include config file
require_once "config.php";

// Attempt to fetch user information
$user_id = $_SESSION["id"];
$sql = "SELECT name, firstName, middleName, lastName, localaddress, city, stateprovince, zippostalcode, fulladdress, emailaddress, phonenumber, DATE_FORMAT(birthdate, '%Y-%m-%d') as birthdate, gender FROM users WHERE id = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 600px; padding: 20px; margin: auto; }
        .form-group { margin-bottom: 10px; }
        .btn-custom { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Profile</h2>
        <div class="form-group">
            <label><b>Full Name:</b> <?php echo htmlspecialchars($user['name']); ?></label>
        </div>
        <div class="form-group">
            <label><b>First Name:</b> <?php echo htmlspecialchars($user['firstName']); ?></label>
        </div>
        <div class="form-group">
            <label><b>Middle Name:</b> <?php echo htmlspecialchars($user['middleName']); ?></label>
        </div>
        <div class="form-group">
            <label><b>Last Name:</b> <?php echo htmlspecialchars($user['lastName']); ?></label>
        </div>
        <div class="form-group">
            <label><b>Local Address:</b> <?php echo htmlspecialchars($user['localaddress']); ?></label>
        </div>
        <div class="form-group">
            <label><b>City:</b> <?php echo htmlspecialchars($user['city']); ?></label>
        </div>
        <div class="form-group">
            <label><b>State/Province:</b> <?php echo htmlspecialchars($user['stateprovince']); ?></label>
        </div>
        <div class="form-group">
            <label><b>Zip/Postal Code:</b> <?php echo htmlspecialchars($user['zippostalcode']); ?></label>
        </div>
        <div class="form-group">
            <label><b>Full Address:</b> <?php echo htmlspecialchars($user['fulladdress']); ?></label>
        </div>
        <div class="form-group">
            <label><b>Email Address:</b> <?php echo htmlspecialchars($user['emailaddress']); ?></label>
        </div>
        <div class="form-group">
            <label><b>Phone Number:</b> <?php echo htmlspecialchars($user['phonenumber']); ?></label>
        </div>
        <div class="form-group">
            <label><b>Birthdate:</b> <?php echo htmlspecialchars($user['birthdate']); ?></label>
        </div>
        <div class="form-group">
            <label><b>Gender:</b> <?php echo htmlspecialchars($user['gender']); ?></label>
        </div>
        <!-- Update Profile button -->
        <a href="update-profile.php" class="btn btn-primary btn-custom">Update Profile</a>

        <!-- Home Page button -->
        <a href="home-page.php" class="btn btn-info btn-custom">Home Page</a>

        <!-- Sign Out button -->
        <a href="sign-out.php" class="btn btn-danger btn-custom">Sign Out</a>
    </div>    
</body>
</html>
