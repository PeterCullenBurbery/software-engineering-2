<?php
// Initialize the session
// Ensure sessions are used securely over HTTPS
if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === "on") {
    ini_set("session.cookie_secure", 1);
}
ini_set("session.cookie_httponly", 1);
ini_set("session.use_only_cookies", 1);

session_start();


// Check if the user is already logged in, if not then redirect to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";

// Check the user's role
$is_patient = isset($_SESSION["patient"]) && $_SESSION["patient"] == 1;
$is_doctor = isset($_SESSION["doctor"]) && $_SESSION["doctor"] == 1;
$is_adminsecretary = isset($_SESSION["adminsecretary"]) && $_SESSION["adminsecretary"] == 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h1>
    </div>
    <p>
        <!-- Link to profile page -->
        <a href="profile.php" class="btn btn-primary">Go to Profile</a>

        <!-- Link for patients to book an appointment -->
        <?php if ($is_patient): ?>
            <a href="book-an-appointment.php" class="btn btn-warning">Book an Appointment</a>
        <?php endif; ?>


		<!-- Link for doctors, patients, and admin secretaries to view their bookings -->
		<?php if ($is_doctor || $is_patient || $is_adminsecretary): ?>
			<a href="view-bookings.php" class="btn btn-success">View My Bookings</a>
		<?php endif; ?>

        <!-- Other user-specific links can be added here -->

        <!-- Common links -->
        <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
        <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    </p>
</body>
</html>
