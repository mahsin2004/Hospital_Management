<?php
include 'db.php';

// Enable mysqli exceptions for better error handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $doctor_id = intval($_GET['id']);

    try {
        // Prepare and execute delete statement
        $sql = "DELETE FROM Doctors WHERE DoctorID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $stmt->close();

        // Redirect with success message
        // header("Location: doctors.php?success=DoctorDeleted");
        echo "<p style='color: green;'>Doctor deleted successfully!</p>";
        echo "<a href='doctors.php'>Back to Doctor List</a>";
        exit();
    } catch (mysqli_sql_exception $e) {
        // Handle foreign key constraint error
        if (strpos($e->getMessage(), "a foreign key constraint fails") !== false) {
            echo "<p style='color: red;'>Cannot delete doctor because they are associated with existing appointments. Delete related appointments first.</p>";
        } else {
            // Handle other database errors
            echo "<p style='color: red;'>An error occurred while deleting the doctor. Please try again later.</p>";
        }
        echo "<a href='doctors.php'>Back to Doctor List</a>";
    }
} else {
    // Redirect if no ID is provided or invalid ID
    header("Location: doctors.php?error=InvalidID");
    exit();
}
?>
