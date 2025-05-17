<?php
// Include database connection
include 'db.php';

if (isset($_GET['id'])) {
    $reportID = $_GET['id'];

    try {
        // Fetch the report file path before deletion
        $stmt = $conn->prepare("SELECT ReportFilePath FROM LabTestReports WHERE ReportID = ?");
        $stmt->bind_param("i", $reportID);
        $stmt->execute();
        $stmt->bind_result($reportFilePath);
        $stmt->fetch();
        $stmt->close();

        // Delete the report file from the server
        if ($reportFilePath && file_exists($reportFilePath)) {
            unlink($reportFilePath);
        }

        // Delete the report record from the database
        $deleteStmt = $conn->prepare("DELETE FROM LabTestReports WHERE ReportID = ?");
        $deleteStmt->bind_param("i", $reportID);
        $deleteStmt->execute();
        $deleteStmt->close();

      
        echo "<p style='color: green;'>Lab report successfully deleted!</p>";
        echo "<a href='upload_lab_report.php'>Back to Lab Test Report List</a>";
        exit();
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
