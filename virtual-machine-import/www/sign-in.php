<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect him to the welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: home-page.php");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = $sign_in_error = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement to fetch all relevant fields
        $sql = "SELECT id, username, password, patient, doctor, adminsecretary, name, firstName, lastName, middleName, fulladdress, emailaddress, phonenumber, localaddress, city, stateprovince, zippostalcode, birthdate, gender FROM users WHERE username = ?";

        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password, $patient, $doctor, $adminsecretary, $name, $firstName, $lastName, $middleName, $fulladdress, $emailaddress, $phonenumber, $localaddress, $city, $stateprovince, $zippostalcode, $birthdate, $gender);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, so start a new session
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            $_SESSION["patient"] = $patient;
                            $_SESSION["doctor"] = $doctor;
                            $_SESSION["adminsecretary"] = $adminsecretary;
                            // Store additional fields in session variables
                            $_SESSION["name"] = $name;
                            $_SESSION["firstName"] = $firstName;
                            $_SESSION["lastName"] = $lastName;
                            $_SESSION["middleName"] = $middleName;
                            $_SESSION["fulladdress"] = $fulladdress;
                            $_SESSION["emailaddress"] = $emailaddress;
                            $_SESSION["phonenumber"] = $phonenumber;
                            $_SESSION["localaddress"] = $localaddress;
                            $_SESSION["city"] = $city;
                            $_SESSION["stateprovince"] = $stateprovince;
                            $_SESSION["zippostalcode"] = $zippostalcode;
                            $_SESSION["birthdate"] = $birthdate;
                            $_SESSION["gender"] = $gender;
                            
                            // Redirect user to welcome page
                            header("location: home-page.php");
                        } else {
                            // Password is not valid, display a generic error message
                            $sign_in_error = "Invalid username or password.";
                        }
                    }
                } else {
                    // Username doesn't exist, display a generic error message
                    $sign_in_error = "Invalid username or password.";
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign in</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font: 14px sans-serif; }
        .wrapper { width: 360px; padding: 20px; margin: auto; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Sign in</h2>
        <p>Please fill in your credentials to sign in.</p>
        
        <?php 
        if (!empty($sign_in_error)) {
            echo '<div class="alert alert-danger">' . $sign_in_error . '</div>';
        }        
        ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                <span class="invalid-feedback"><?php echo $username_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                <span class="invalid-feedback"><?php echo $password_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Sign in">
            </div>
            <p>Don't have an account? <a href="sign-up.php">Sign up now</a>.</p>
			<p><a href="reset-password-request.php">Forgot Password?</a></p> <!-- Add Forgot Password link -->
        </form>
    </div>
</body>
</html>
