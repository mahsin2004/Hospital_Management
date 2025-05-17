<?php
$pageTitle = "Upload Report";
ob_start(); // Start output buffering

// Include database connection
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['report_file'])) {
    // Retrieve and sanitize form data
    $labTestID = htmlspecialchars($_POST['lab_test_id']);

    // Ensure upload directory exists
    $uploadDir = "uploads/reports/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle file upload
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

                // Insert report into LabTestReports table
                $stmt = $conn->prepare("INSERT INTO LabTestReports (LabTestID, ReportFilePath) VALUES (?, ?)");
                $stmt->bind_param("is", $labTestID, $uploadFile);
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

                echo "Lab report successfully uploaded, and lab test status updated to 'Completed'!<br><br>";
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

// Fetch all LabTestReports
try {
    $sql = "SELECT LabTestReports.ReportID, LabTestReports.LabTestID, LabTestReports.ReportFilePath, LabTests.TestName 
            FROM LabTestReports 
            INNER JOIN LabTests ON LabTestReports.LabTestID = LabTests.LabTestID";
    $result = $conn->query($sql);
} catch (mysqli_sql_exception $e) {
    echo "<p style='color: red;'>Error fetching reports: " . $e->getMessage() . "</p>";
    exit;
}

// Fetch LabTestID values for the select dropdown
try {
    $labTestsQuery = "SELECT LabTestID, TestName FROM LabTests";
    $labTestsResult = $conn->query($labTestsQuery);
} catch (mysqli_sql_exception $e) {
    echo "<p style='color: red;'>Error fetching lab tests: " . $e->getMessage() . "</p>";
    exit;
}
?>

<!-- HTML Form -->
<!DOCTYPE html>
<html>

<head>
    <title>Upload Lab Report</title>
</head>

<body>
    <div class="container">

        <!-- Upload Form -->
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="lab_test_id" class="block text-sm font-medium text-gray-700">Lab Test ID</label>
                <select id="lab_test_id" name="lab_test_id" class="w-full border border-gray-300 rounded p-2" required>
                    <option value="">Select Lab Test</option>
                    <?php if ($labTestsResult && $labTestsResult->num_rows > 0): ?>
                        <?php while ($row = $labTestsResult->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($row['LabTestID']) ?>">
                                <?= htmlspecialchars($row['TestName']) ?> (ID: <?= htmlspecialchars($row['LabTestID']) ?>)
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="">No lab tests available</option>
                    <?php endif; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="report_file" class="block text-sm font-medium text-gray-700">Upload Report</label>
                <input type="file" id="report_file" name="report_file" class="w-full bg-white border border-gray-300 rounded p-2" required />
            </div>

            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Upload Report</button>
        </form>

        <!-- Display Uploaded Reports -->
        <div class="bg-white p-6 shadow rounded my-6">
            <h2 class="text-xl font-bold mb-4">Uploaded Lab Reports</h2>

            <?php if ($result && $result->num_rows > 0): ?>
                <table class="w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-200 p-2">Report ID</th>
                            <th>Lab Test ID</th>
                            <th>Test Name</th>
                            <th>Report File</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="border border-gray-200 p-2 text-center"><?= htmlspecialchars($row['ReportID']) ?></td>
                                <td class="border border-gray-200 p-2 text-center"><?= htmlspecialchars($row['LabTestID']) ?></td>
                                <td class="border border-gray-200 p-2 text-center"><?= htmlspecialchars($row['TestName']) ?></td>
                                <td class="border border-gray-200 p-2 text-center">
                                    <a href="<?= htmlspecialchars($row['ReportFilePath']) ?>" target="_blank">
                                        View Report
                                    </a>
                                </td>
                                <td class="border border-gray-200 p-2 text-center">
                            <a href="edit_upload_lab_report.php?id=<?php echo $row['ReportID']; ?>" class="text-blue-500 hover:underline">Edit</a>
                            <a href="delete_upload_lab_report.php?id=<?php echo $row['ReportID']; ?>" class="text-red-500 hover:underline ml-2">Delete</a>
                        </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="border border-gray-200 p-2 text-center">No lab test reports found.</td>
                </tr>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>

<?php
$content = ob_get_clean(); // Capture the output and assign it to $content
include 'layout.php'; // Include the layout file
?>