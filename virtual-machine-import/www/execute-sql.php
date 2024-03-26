<?php
// Start the session and include config
session_start();
require_once "config.php";

// Verify user's permission
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: sign-in.php");
    exit;
}

// Initialize variables
$sql = $results = "";
$sql_err = "";

// Process submitted form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["sql"]))) {
        $sql_err = "Please enter an SQL query.";
    } elseif (stripos(trim($_POST["sql"]), "SELECT") !== 0) {
        // Prevent non-SELECT queries for security reasons
        $sql_err = "Only SELECT queries are allowed.";
    } else {
        $sql = trim($_POST["sql"]);

        // Execute SQL query
        if ($result = mysqli_query($link, $sql)) {
            $results = mysqli_fetch_all($result, MYSQLI_ASSOC);
            mysqli_free_result($result);
        } else {
            $sql_err = "Could not execute query.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Execute SQL</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; text-align: center; }
        .wrapper{ width: 700px; margin: auto; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Execute SQL Query</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>SQL Query</label>
                <textarea name="sql" class="form-control <?php echo (!empty($sql_err)) ? 'is-invalid' : ''; ?>"><?php echo $sql; ?></textarea>
                <span class="invalid-feedback"><?php echo $sql_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Execute">
            </div>
        </form>
        <?php if (!empty($results)): ?>
            <h3>Results</h3>
            <pre><?php print_r($results); ?></pre>
        <?php endif; ?>
    </div>    
</body>
</html>
