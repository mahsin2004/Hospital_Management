<?php
$pageTitle = "Edit upload lab report";
ob_start();
// Include database connection
include 'db.php';

// Check if ID is passed
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $reportID = $_GET['id'];

    // Fetch the current report details from the database
    try {
        $stmt = $conn->prepare("SELECT ReportID, LabTestID, ReportFilePath FROM LabTestReports WHERE ReportID = ?");
        $stmt->bind_param("i", $reportID);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $report = $result->fetch_assoc();
            $reportFilePath = $report['ReportFilePath'];  // Initialize $reportFilePath with the existing file path
        } else {
            echo "Report not found.";
            exit;
        }

        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        echo "Error fetching report: " . $e->getMessage();
        exit;
    }
} else {
    echo "No report ID provided.";
    exit;
}

// Handle file upload and update logic when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['report_file'])) {
    $labTestID = htmlspecialchars($_POST['lab_test_id']);

    // Handle file upload
    $uploadDir = "uploads/reports/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($_FILES["report_file"]["name"]);
    $uploadFile = $uploadDir . $fileName;

    // Validate file type and size
    $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
    $fileType = mime_content_type($_FILES["report_file"]["tmp_name"]);
    $fileSize = $_FILES["report_file"]["size"];

    if (!in_array($fileType, $allowedTypes)) {
        echo "Invalid file type. Only PDF, JPEG, and PNG are allowed.";
    } elseif ($fileSize > 5 * 1024 * 1024) { // Limit file size to 5MB
        echo "File size exceeds 5MB limit.";
    } else {
        if (move_uploaded_file($_FILES["report_file"]["tmp_name"], $uploadFile)) {
            try {
                // Start transaction to ensure both operations succeed
                $conn->begin_transaction();

                // Update report in LabTestReports table
                $stmt = $conn->prepare("UPDATE LabTestReports SET LabTestID = ?, ReportFilePath = ? WHERE ReportID = ?");
                $stmt->bind_param("isi", $labTestID, $uploadFile, $reportID);
                $stmt->execute();
                $stmt->close();

                // Update LabTests table status to "Completed"
                $status = "Completed";
                $updateStmt = $conn->prepare("UPDATE LabTests SET Status = ? WHERE LabTestID = ?");
                $updateStmt->bind_param("si", $status, $labTestID);
                $updateStmt->execute();
                $updateStmt->close();

                // Commit transaction
                $conn->commit();

                echo "<p style='color: green;'>Lab report successfully updated, and lab test status updated to 'Completed'!</p>";
                echo "<a href='upload_lab_report.php'>Back to Lab Test Report List</a>";
                exit();
            } catch (mysqli_sql_exception $e) {
                // Rollback transaction in case of error
                $conn->rollback();
                echo "Error: " . $e->getMessage();
            }
        } else {
            echo "Error uploading the file. Please try again.";
        }
    }
}
?>

<!-- HTML Form for editing lab report -->
<!DOCTYPE html>
<html>

<head>
    <title>Edit Lab Report</title>
</head>

<body>
    <div class="container">


        <!-- Edit Report Form -->
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="lab_test_id" class="block text-sm font-medium text-gray-700">Lab Test ID</label>
                <select id="lab_test_id" name="lab_test_id" class="w-full border border-gray-300 rounded p-2" required>
                    <!-- Fetch available Lab Tests for the dropdown -->
                    <?php
                    $labTestsQuery = "SELECT LabTestID, TestName FROM LabTests";
                    $labTestsResult = $conn->query($labTestsQuery);

                    if ($labTestsResult && $labTestsResult->num_rows > 0):
                        while ($row = $labTestsResult->fetch_assoc()):
                    ?>
                            <option value="<?= htmlspecialchars($row['LabTestID']) ?>" <?= $row['LabTestID'] == $report['LabTestID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['TestName']) ?> (ID: <?= htmlspecialchars($row['LabTestID']) ?>)
                            </option>
                    <?php endwhile;
                    endif; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="report_file" class="block text-sm font-medium text-gray-700">Upload New Report</label>
                <input type="file" id="report_file" name="report_file" class="w-full bg-white border border-gray-300 rounded p-2" />
                <p>Current file: <a href="<?= htmlspecialchars($reportFilePath) ?>" target="_blank">View Report</a></p>
            </div>

            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Update Report</button>
        </form>
    </div>
</body>

</html>


<?php
$content = ob_get_clean(); // Capture the output and assign it to $content
include 'layout.php'; // Include the layout file
?>