<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$email = "";
$email_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate email input
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email address.";
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty($email_err)) {
        // Prepare a select statement to fetch the user ID using the email address
        $sql = "SELECT id FROM users WHERE emailaddress = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_email = $email;

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 1) {
                    mysqli_stmt_bind_result($stmt, $userId);
                    mysqli_stmt_fetch($stmt);

                    // Generate a unique token for password reset
                    $token = bin2hex(random_bytes(32));
                    // Set expiration time for the token
                    $expires = date("Y-m-d H:i:s", time() + 1800); // Token expires after 30 minutes

                    // Insert the token into your database along with user ID and expiration time
                    $sqlInsert = "INSERT INTO password_resets (reset_uuid, user_id, token, expires) VALUES (UUID_TO_BIN(UUID()), ?, ?, ?)";
                    if ($stmtInsert = mysqli_prepare($link, $sqlInsert)) {
                        mysqli_stmt_bind_param($stmtInsert, "iss", $userId, $token, $expires);
                        if (mysqli_stmt_execute($stmtInsert)) {
                            // Prepare and send the email
                            $to = $email;
                            $subject = "Password Reset Request";
                            // Update the link to point to your localhost setup
                            $resetLink = "http://localhost/reset-password.php?token=" . $token;
                            $message = "You requested a password reset. Click the link to reset your password: " . $resetLink;
                            $headers = "From: noreply@yourwebsite.com";
                            mail($to, $subject, $message, $headers);

                            echo "If your email address is in our database, you will receive a password reset link shortly.";
                        } else {
                            echo "Oops! Something went wrong while processing your request.";
                        }
                        mysqli_stmt_close($stmtInsert);
                    }
                } else {
                    echo "If your email address is in our database, you will receive a password reset link shortly.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password Request</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font: 14px sans-serif; }
        .wrapper { width: 360px; padding: 20px; margin: auto; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Reset Password Request</h2>
        <p>Please enter your email address to receive a password reset link.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $email_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Send Reset Link">
            </div>
        </form>
    </div>    
</body>
</html>

