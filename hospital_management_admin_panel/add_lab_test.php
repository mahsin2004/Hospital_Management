<?php
$pageTitle = "Add Lab Test";
ob_start(); // Start output buffering
// Include database connection
include 'db.php';

// Fetch patients
$patients = [];
try {
    $stmt = $conn->prepare("SELECT PatientID, Name FROM Patients ORDER BY Name ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    echo "<p style='color: red;'>Error fetching patients: " . $e->getMessage() . "</p>";
}

// Fetch doctors
$doctors = [];
try {
    $stmt = $conn->prepare("SELECT DoctorID, Name FROM Doctors ORDER BY Name ASC");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    echo "<p style='color: red;'>Error fetching doctors: " . $e->getMessage() . "</p>";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $patientID = htmlspecialchars($_POST['patient_id']);
    $testName = htmlspecialchars($_POST['test_name']);
    $testDate = htmlspecialchars($_POST['test_date']);
    $doctorID = htmlspecialchars($_POST['doctor_id']);

    try {
        // Insert lab test record
        $stmt = $conn->prepare("INSERT INTO LabTests (PatientID, TestName, TestDate, Status) VALUES (?, ?, ?, 'Pending')");
        $stmt->bind_param("iss", $patientID, $testName, $testDate);
        $stmt->execute();
        echo "<p style='color: green;'>Lab Test successfully added!</p>";
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}

// Fetch lab tests from the database
$labTests = [];
try {
    $stmt = $conn->prepare("SELECT LabTestID, PatientID, TestName, TestDate, Status FROM LabTests ORDER BY TestDate DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $labTests[] = $row;
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    echo "<p style='color: red;'>Error fetching lab tests: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Lab Test</title>
</head>

<body>
    <div class="bg-white p-6 shadow rounded mb-4">

        <form method="POST" action="">
            <div class="mb-4">
                <label for="patient_id" class="block text-sm font-medium text-gray-700">Patient</label>
                <select id="patient_id" name="patient_id" class="w-full border border-gray-300 rounded p-2" required>
                    <option value="" disabled selected>Select a Patient</option>
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?php echo htmlspecialchars($patient['PatientID']); ?>">
                            <?php echo htmlspecialchars($patient['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="doctor_id" class="block text-sm font-medium text-gray-700">Doctor</label>
                <select id="doctor_id" name="doctor_id" class="w-full border border-gray-300 rounded p-2" required>
                    <option value="" disabled selected>Select a Doctor</option>
                    <?php foreach ($doctors as $doctor): ?>
                        <option value="<?php echo htmlspecialchars($doctor['DoctorID']); ?>">
                            <?php echo htmlspecialchars($doctor['Name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="test_name" class="block text-sm font-medium text-gray-700">Test Name</label>
                <input type="text" id="test_name" class="w-full border border-gray-300 rounded p-2" name="test_name" required />
            </div>

            <div class="mb-4">
                <label for="test_date" class="block text-sm font-medium text-gray-700">Test Date</label>
                <input type="date" id="test_date" class="w-full border border-gray-300 rounded p-2" name="test_date" required />
            </div>

            <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Add Lab Test</button>
        </form>
    </div>

    <div class="bg-white p-6 shadow rounded">
        <h2 class="text-xl font-bold mb-4">Lab Test List</h2>
        <?php if (count($labTests) > 0): ?>
            <table class="min-w-full border-collapse border border-gray-300">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-300 p-2">Lab Test ID</th>
                        <th class="border border-gray-300 p-2">Patient ID</th>
                        <th class="border border-gray-300 p-2">Test Name</th>
                        <th class="border border-gray-300 p-2">Test Date</th>
                        <th class="border border-gray-300 p-2">Status</th>
                        <th class="border border-gray-200 p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($labTests as $test): ?>
                        <tr>
                            <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($test['LabTestID']); ?></td>
                            <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($test['PatientID']); ?></td>
                            <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($test['TestName']); ?></td>
                            <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($test['TestDate']); ?></td>
                            <td class="border border-gray-300 p-2"><?php echo htmlspecialchars($test['Status']); ?></td>
                            <td class="border border-gray-200 p-2 text-center">
                                <a href="edit_lab_test.php?id=<?php echo htmlspecialchars($test['LabTestID']); ?>" class="text-blue-500 hover:underline">Edit</a>
                                <a href="delete_lab_test.php?id=<?php echo htmlspecialchars($test['LabTestID']); ?>" class="text-red-500 hover:underline ml-2">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No lab tests found.</p>
        <?php endif; ?>
    </div>
</body>

</html>

<?php
$content = ob_get_clean(); // Capture the output and assign it to $content
include 'layout.php'; // Include the layout file
?>