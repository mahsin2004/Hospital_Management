<?php
include 'db.php'; // Database connection

// Enable exceptions for mysqli
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $labTestID = intval($_GET['id']);

    // Delete the lab test
    try {
        $stmt = $conn->prepare("DELETE FROM LabTests WHERE LabTestID = ?");
        $stmt->bind_param("i", $labTestID);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            echo "<p style='color: green;'>Lab Test Deleted successfully!</p>";
            echo "<a href='add_lab_test.php'>Back to Lab Test List</a>";
        } else {
            echo "<p style='color: green;'>Lab Test not found!</p>";
            echo "<a href='add_lab_test.php'>Back to Lab Test List</a>";
        }
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        echo "<p style='color: red;'>Error deleting lab test: " . $e->getMessage() . "</p>";
        echo "<a href='add_lab_test.php'>Back to Lab Test List</a>";
    }
} else {
    echo "<p style='color: red;'>Invalid Lab Test ID!</p>";
}
