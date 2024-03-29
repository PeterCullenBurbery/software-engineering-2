<?php
// Initialize the session
session_start();

// Check if the user is logged in, otherwise redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: sign-in.php");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$new_name = "";
$new_name_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate new name
    if(empty(trim($_POST["new_name"]))){
        $new_name_err = "Please enter the new name.";     
    } else{
        $new_name = trim($_POST["new_name"]);
    }
        
    // Check input errors before updating the database
    if(empty($new_name_err)){
        // Prepare an update statement
        $sql = "UPDATE users SET name = ? WHERE id = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "si", $param_name, $param_id);
            
            // Set parameters
            $param_name = $new_name;
            $param_id = $_SESSION["id"];
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Name updated successfully. Redirect to welcome page
                header("location: home-page.php");
                exit();
            } else{
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
    <title>Update Name</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 360px; padding: 20px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Update Name</h2>
        <p>Please fill out this form to update your name.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="form-group">
                <label>New Name</label>
                <input type="text" name="new_name" class="form-control <?php echo (!empty($new_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $new_name; ?>">
                <span class="invalid-feedback"><?php echo $new_name_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link ml-2" href="home-page.php">Cancel</a>
            </div>
        </form>
    </div>    
</body>
</html>
