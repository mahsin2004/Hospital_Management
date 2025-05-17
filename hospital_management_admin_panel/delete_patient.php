<?php
include 'db.php';

if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];

    try {
        // Delete the patient from the database
        $sql = "DELETE FROM Patients WHERE PatientID = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("i", $patient_id);
            $stmt->execute();
            $stmt->close();

            echo "<p style='color: green;'>Patient deleted successfully!</p>";
            echo "<a href='patients.php'>Back to Patient List</a>";
        } else {
            throw new Exception("Failed to prepare the SQL statement.");
        }
    } catch (mysqli_sql_exception $e) {
        // Display a custom error message for foreign key constraint
        if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
            echo "<p style='color: red;'>Cannot delete patient because they are associated with existing appointments. Delete related appointments first.</p>";
        } else {
            // Display a generic database error
            echo "<p style='color: red;'>An error occurred while deleting the patient. Please try again later.</p>";
        }
        echo "<a href='patients.php'>Back to Patient List</a>";
    } catch (Exception $e) {
        // Catch any other exceptions
        echo "<p style='color: red;'>An unexpected error occurred: " . $e->getMessage() . "</p>";
        echo "<a href='patients.php'>Back to Patient List</a>";
    }
} else {
    // Redirect if no ID is provided
    header("Location: patients.php?error=NoIDProvided");
    exit();
}
