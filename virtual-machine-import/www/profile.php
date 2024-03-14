<?php
// Initialize the session
session_start();

// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";

$user_name = ""; // Initialize the name as empty
$user_id = $_SESSION["id"];

$sql = "SELECT name FROM users WHERE id = ?";

if($stmt = mysqli_prepare($link, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $param_id);
    $param_id = $user_id;
    if(mysqli_stmt_execute($stmt)){
        mysqli_stmt_store_result($stmt);
        if(mysqli_stmt_num_rows($stmt) == 1){
            mysqli_stmt_bind_result($stmt, $user_name);
            mysqli_stmt_fetch($stmt);
        }
    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }
    mysqli_stmt_close($stmt);
}
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
        .wrapper{ width: 360px; padding: 20px; }
        .btn-custom { margin-bottom: 10px; } /* Custom class for spacing */
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Your Profile</h2>
        <?php if (!empty($user_name)): ?>
            <p>Welcome, <?php echo htmlspecialchars($user_name); ?>!</p>
        <?php else: ?>
            <p>Welcome, User! Your name is not set.</p>
            <a href="update-name.php" class="btn btn-warning btn-custom">Update Name</a>
        <?php endif; ?>
        <!-- Button to go to the welcome page -->
        <a href="welcome.php" class="btn btn-primary btn-custom">Go to Home Page</a>
        <!-- Button to log out -->
        <a href="logout.php" class="btn btn-danger btn-custom">Sign Out</a>
        <p>Here is your profile information.</p>
        <!-- Display more profile information here -->
    </div>    
</body>
</html>
