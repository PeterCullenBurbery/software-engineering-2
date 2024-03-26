<?php
// Start the session
session_start();

// Check if the user is logged in and is a patient, otherwise redirect
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["patient"] != 1) {
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";

// Define and initialize variables to store form data
$doctor_id = $date = $start_time = $end_time = "";
$date_err = $time_err = $doctor_err = "";
$booking_message = ""; // Variable to store booking messages

// Fetch list of doctors
$doctors = [];
$sql = "SELECT id, username FROM users WHERE doctor = 1";
if ($result = mysqli_query($link, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $doctors[] = $row;
    }
    mysqli_free_result($result);
} else {
    echo "Error: Could not execute $sql. " . mysqli_error($link);
}

// Process the form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate the doctor selection
    if (empty(trim($_POST["doctor_id"]))) {
        $doctor_err = "Please select a doctor.";
    } else {
        $doctor_id = trim($_POST["doctor_id"]);
    }

    // Validate date
    if (empty(trim($_POST["date"]))) {
        $date_err = "Please enter a date.";
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($_POST["date"]))) {
        $date_err = "Please enter a valid date in YYYY-MM-DD format.";
    } else {
        $date = trim($_POST["date"]);
    }

    // Validate start and end time
    if (empty(trim($_POST["start_time"])) || empty(trim($_POST["end_time"]))) {
        $time_err = "Please enter both start and end times.";
    } else {
        $start_time = date('H:i:s', strtotime(trim($_POST["start_time"])));
        $end_time = date('H:i:s', strtotime(trim($_POST["end_time"])));
        if ($start_time >= $end_time) {
            $time_err = "Start time must be earlier than end time.";
        }
    }

    // Check if the doctor is available at the given time
    if (empty($date_err) && empty($time_err) && empty($doctor_err)) {
        $sql = "SELECT booking_id FROM booking WHERE doctor_id = ? AND date = ? AND NOT (start_time >= ? OR end_time <= ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "isss", $doctor_id, $date, $end_time, $start_time);

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);

                if (mysqli_stmt_num_rows($stmt) == 0) {
                    // Doctor is available
                    $insertSql = "INSERT INTO booking (doctor_id, patient_id, date, start_time, end_time) VALUES (?, ?, ?, ?, ?)";

                    if ($insertStmt = mysqli_prepare($link, $insertSql)) {
                        mysqli_stmt_bind_param($insertStmt, "iisss", $doctor_id, $_SESSION["id"], $date, $start_time, $end_time);

                        if (mysqli_stmt_execute($insertStmt)) {
                            $booking_message = "Appointment booked successfully.";
                        } else {
                            $booking_message = "Error: Could not execute $insertSql. " . mysqli_error($link);
                        }
                        mysqli_stmt_close($insertStmt);
                    }
                } else {
                    $booking_message = "The doctor is not available at the selected time.";
                }
                mysqli_stmt_close($stmt);
            } else {
                $booking_message = "Error: Could not execute $sql. " . mysqli_error($link);
            }
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
    <title>Book an Appointment</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font: 14px sans-serif; }
        .wrapper { width: 350px; padding: 20px; margin: auto; }
        .booking-message { text-align: center; margin: 20px 0; } /* Adjusted styling for messages */
    </style>
</head>
<body>
    <div class="wrapper">
        <h2>Book an Appointment</h2>
        <p>Please fill in the details to book an appointment.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Doctor</label>
                <select name="doctor_id" class="form-control <?php echo (!empty($doctor_err)) ? 'is-invalid' : ''; ?>">
                    <?php foreach($doctors as $doctor): ?>
                        <option value="<?php echo $doctor["id"]; ?>" <?php echo ($doctor_id == $doctor["id"]) ? 'selected' : ''; ?>><?php echo htmlspecialchars($doctor["username"]); ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="invalid-feedback"><?php echo $doctor_err; ?></span>
            </div>    
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" class="form-control <?php echo (!empty($date_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $date; ?>">
                <span class="invalid-feedback"><?php echo $date_err; ?></span>
            </div>
            <div class="form-group">
                <label>Start Time</label>
                <input type="time" name="start_time" class="form-control <?php echo (!empty($time_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $start_time; ?>">
            </div>
            <div class="form-group">
                <label>End Time</label>
                <input type="time" name="end_time" class="form-control <?php echo (!empty($time_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $end_time; ?>">
                <span class="invalid-feedback"><?php echo $time_err; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Book Appointment">
            </div>
            <!-- Display booking message -->
            <?php if (!empty($booking_message)): ?>
                <div class="alert alert-info booking-message"><?php echo $booking_message; ?></div>
            <?php endif; ?>
        </form>
        <!-- Navigation Buttons -->
        <div class="navigation-btns">
            <a href="welcome.php" class="btn btn-info">Home Page</a>
            <a href="logout.php" class="btn btn-danger">Sign Out</a>
        </div>
    </div>    
</body>
</html>
