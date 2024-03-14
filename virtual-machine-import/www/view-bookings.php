<?php
// Start the session
session_start();

// Check if the user is logged in, if not then redirect to the login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";

// Define and initialize variables to store search criteria
$search_date = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty(trim($_POST["search_date"]))) {
        $search_date = trim($_POST["search_date"]);
    }
}

// Start building the query
$query_parts = ["SELECT * FROM booking WHERE 1 = 1"];
$types = '';  // To hold parameter types
$params = []; // To hold the parameters

// Append conditions based on roles
if ($_SESSION["doctor"] == 1) {
    $query_parts[] = "doctor_id = ?";
    $types .= 'i';  // Integer
    $params[] = $_SESSION["id"];
} elseif ($_SESSION["patient"] == 1) {
    $query_parts[] = "patient_id = ?";
    $types .= 'i';  // Integer
    $params[] = $_SESSION["id"];
} elseif ($_SESSION["adminsecretary"] == 1) {
    // No specific condition needed for admin/secretary, they can view all
}

// Add search date to the conditions if provided
if (!empty($search_date)) {
    $query_parts[] = "date = ?";
    $types .= 's';  // String
    $params[] = $search_date;
}

// Combine all parts to form the final SQL query
$sql = implode(' AND ', $query_parts);

if ($stmt = mysqli_prepare($link, $sql)) {
    // Bind parameters if there are any
    if ($types && !empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    // Execute the query
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    mysqli_stmt_close($stmt);
} else {
    echo "Error preparing statement: " . htmlspecialchars(mysqli_error($link));
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Bookings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font: 14px sans-serif; text-align: center; }
        .wrapper { width: 95%; padding: 20px; margin: auto; }
        table { width: 100%; margin-top: 20px; }
        th, td { text-align: left; padding: 8px; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .btn-custom { margin-top: 20px; } /* Additional styling for custom buttons */
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Bookings</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="search_date">Filter by date:</label>
                <input type="date" id="search_date" name="search_date" value="<?php echo $search_date; ?>">
                <input type="submit" class="btn btn-primary" value="Search">
            </div>
        </form>
        <table>
            <tr>
                <th>Booking ID</th>
                <th>Doctor ID</th>
                <th>Patient ID</th>
                <th>Date</th>
                <th>Start Time</th>
                <th>End Time</th>
            </tr>
            <?php
            if (isset($result) && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['booking_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['doctor_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['patient_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['end_time']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No bookings found for the selected date.</td></tr>";
            }
            ?>
        </table>
        <!-- Buttons for Welcome Page and Signing Out -->
        <a href="welcome.php" class="btn btn-info btn-custom">Home Page</a>
        <a href="logout.php" class="btn btn-danger btn-custom">Sign Out</a>
    </div>    
</body>
</html>

