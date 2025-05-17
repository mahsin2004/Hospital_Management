<?php
include 'db.php'; // Include database connection

// Check if 'id' is passed for deletion
if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];

    // Check if the appointment ID is valid (numeric check)
    if (!is_numeric($appointment_id)) {
        // If it's not a valid number, redirect with an error message
        header("Location: appointments.php?message=Invalid appointment ID");
        exit;
    }

    // Delete the appointment from the database
    $sql = "DELETE FROM appointments WHERE AppointmentID = ?";  // Ensure column name is 'AppointmentID' or the correct one
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $appointment_id);
        $stmt->execute();

        // Check if the query was successful
        if ($stmt->affected_rows > 0) {
            // Redirect to the appointments page with a success message
            echo "<p style='color: green;'>Appointment Deleted successfully!</p>";
            echo "<a href='appointments.php'>Back to Appointment List</a>";
        } else {
            // If no rows were affected (e.g., appointment not found), redirect with an error message
            echo "<p style='color: green;'>Appointment not found!</p>";
            echo "<a href='appointments.php'>Back to Appointment List</a>";
        }
        $stmt->close();
    } else {
        // If the query failed to prepare, show an error message
        header("Location: appointments.php?message=Error deleting appointment.");
    }

    exit;
} else {
    // If 'id' is not passed, redirect back to the appointments page
    header("Location: appointments.php?message=Appointment ID missing.");
    exit;
}
