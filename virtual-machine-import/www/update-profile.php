<?php
// Initialize the session
session_start();

// Check if the user is logged in, otherwise redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";

// Attempt to fetch current user information
$sql = "SELECT name, firstName, lastName, middleName, fulladdress, emailaddress, phonenumber, localaddress, city, stateprovince, zippostalcode, birthdate, gender FROM users WHERE id = ?";
if($stmt = mysqli_prepare($link, $sql)){
    // Bind session ID to statement
    mysqli_stmt_bind_param($stmt, "i", $_SESSION["id"]);
    
    // Execute statement
    if(mysqli_stmt_execute($stmt)){
        // Store result
        $result = mysqli_stmt_get_result($stmt);
        
        // Fetch user data
        if($row = mysqli_fetch_assoc($result)){
            // Populate variables with user data
            $userData = $row;
        } else {
            // Handle case where user data could not be fetched
            echo "Failed to fetch user information.";
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    
    // Close statement
    mysqli_stmt_close($stmt);
}

// Define an array for potential updates
$updates = [];
$params = [];
$param_types = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fields = [
        "name" => "s", "firstName" => "s", "lastName" => "s", "middleName" => "s",
        "fulladdress" => "s", "emailaddress" => "s", "phonenumber" => "s",
        "localaddress" => "s", "city" => "s", "stateprovince" => "s",
        "zippostalcode" => "s", "birthdate" => "s", "gender" => "s"
    ];

    foreach ($fields as $field => $type) {
        if (isset($_POST[$field]) && !empty(trim($_POST[$field]))) {
            $updates[] = "$field = ?";
            $params[] = $_POST[$field];
            $param_types .= $type;
        }
    }

    if (count($updates) > 0) {
        $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
        $params[] = $_SESSION["id"];
        $param_types .= "i";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, $param_types, ...$params);
            if (mysqli_stmt_execute($stmt)) {
                header("location: profile.php");
                exit;
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
    <title>Update Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 700px; padding: 20px; margin: auto; }
        .form-group { margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Update Profile</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($userData['name'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="firstName" class="form-control" value="<?php echo htmlspecialchars($userData['firstName'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Middle Name</label>
                <input type="text" name="middleName" class="form-control" value="<?php echo htmlspecialchars($userData['middleName'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="lastName" class="form-control" value="<?php echo htmlspecialchars($userData['lastName'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Local Address</label>
                <input type="text" name="localaddress" class="form-control" value="<?php echo htmlspecialchars($userData['localaddress'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" class="form-control" value="<?php echo htmlspecialchars($userData['city'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>State/Province</label>
                <input type="text" name="stateprovince" class="form-control" value="<?php echo htmlspecialchars($userData['stateprovince'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Zip/Postal Code</label>
                <input type="text" name="zippostalcode" class="form-control" value="<?php echo htmlspecialchars($userData['zippostalcode'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Full Address</label>
                <input type="text" name="fulladdress" class="form-control" value="<?php echo htmlspecialchars($userData['fulladdress'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="emailaddress" class="form-control" value="<?php echo htmlspecialchars($userData['emailaddress'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phonenumber" class="form-control" value="<?php echo htmlspecialchars($userData['phonenumber'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Birthdate</label>
                <input type="date" name="birthdate" class="form-control" value="<?php echo htmlspecialchars($userData['birthdate'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select name="gender" class="form-control">
                    <option value="">Select Gender</option>
                    <option value="Male" <?php echo (isset($userData['gender']) && $userData['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo (isset($userData['gender']) && $userData['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>
            <div class="form-group">
                <input type="submit" name="update" class="btn btn-primary" value="Update">
                <a class="btn btn-link" href="profile.php">Cancel</a>
            </div>
        </form>
    </div>    
</body>
</html>
