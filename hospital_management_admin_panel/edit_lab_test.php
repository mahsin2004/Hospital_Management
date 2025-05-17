<?php
$pageTitle = "Edit Lab Test";
ob_start(); // Start output buffering
include 'db.php'; // Database connection

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $labTestID = intval($_GET['id']);

    // Fetch the lab test details
    try {
        $stmt = $conn->prepare("SELECT * FROM LabTests WHERE LabTestID = ?");
        $stmt->bind_param("i", $labTestID);
        $stmt->execute();
        $result = $stmt->get_result();
        $labTest = $result->fetch_assoc();
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        echo "<p style='color: red;'>Error fetching lab test: " . $e->getMessage() . "</p>";
        exit;
    }

    // Check if lab test exists
    if (!$labTest) {
        echo "<p style='color: red;'>Lab Test not found!</p>";
        exit;
    }
} else {
    echo "<p style='color: red;'>Invalid Lab Test ID!</p>";
    exit;
}

// Handle form submission for updating the lab test
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $testName = htmlspecialchars($_POST['test_name']);
    $testDate = htmlspecialchars($_POST['test_date']);
    $status = htmlspecialchars($_POST['status']);

    try {
        $stmt = $conn->prepare("UPDATE LabTests SET TestName = ?, TestDate = ?, Status = ? WHERE LabTestID = ?");
        $stmt->bind_param("sssi", $testName, $testDate, $status, $labTestID);
        $stmt->execute();
        $stmt->close();
        
        echo "<p style='color: green;'>Lab Test updated successfully!</p>";
        echo "<a href='add_lab_test.php'>Back to Lab Test List</a>";
        exit();
    } catch (mysqli_sql_exception $e) {
        echo "<p style='color: red;'>Error updating lab test: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Lab Test</title>
</head>

<body>
    <form method="POST" action="">

        <div class="mb-4">
            <label for="test_name" class="block text-sm font-medium text-gray-700">Test Name:</label>
            <input type="text" id="test_name" class="w-full border border-gray-300 rounded p-2" name="test_name" value="<?php echo htmlspecialchars($labTest['TestName']); ?>" required>
        </div>
        <div class="mb-4">
            <label for="test_date" class="block text-sm font-medium text-gray-700">Test Date:</label>
            <input type="date" id="test_date" class="w-full border border-gray-300 rounded p-2" name="test_date" value="<?php echo htmlspecialchars($labTest['TestDate']); ?>" required>
        </div>
        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Status:</label>
            <select id="status" name="status" class="w-full border border-gray-300 rounded p-2" required>
                <option value="Pending" <?php echo ($labTest['Status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="Completed" <?php echo ($labTest['Status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
            </select>
        </div>
        <button class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600" type="submit">Update Lab Test</button>
    </form>
</body>

</html>

<?php
$content = ob_get_clean(); // Capture the output and assign it to $content
include 'layout.php'; // Include the layout file
?>