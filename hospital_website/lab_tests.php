<?php
$pageTitle = "Lab Test Reports";

// Include the database connection
include('db.php');
session_start();

// Check if the user is logged in
$isPatientLoggedIn = isset($_SESSION['patient_id']);
$patientID = $isPatientLoggedIn ? intval($_SESSION['patient_id']) : null;

$isDoctorLoggedIn = isset($_SESSION['doctor_id']);
$doctorID = $isDoctorLoggedIn ? intval($_SESSION['doctor_id']) : null;

if (!$isPatientLoggedIn && !$isDoctorLoggedIn) {
    header("Location: index.php");
    exit;
}

// Variables to store lab tests and reports
$labTests = [];
$error_message = null;

try {
    if ($isPatientLoggedIn) {
        // Fetch lab tests for the logged-in patient
        $stmtLabTests = $conn->prepare("
            SELECT LabTestID, TestName, TestDate, Status
            FROM LabTests
            WHERE PatientID = ?
            ORDER BY TestDate DESC
        ");
        $stmtLabTests->bind_param("i", $patientID);
    } elseif ($isDoctorLoggedIn) {
        // Fetch all lab tests for doctors
        $stmtLabTests = $conn->prepare("
            SELECT LabTestID, TestName, TestDate, Status
            FROM LabTests
            ORDER BY TestDate DESC
        ");
    }

    // Execute the query
    $stmtLabTests->execute();
    $resultLabTests = $stmtLabTests->get_result();
    $labTests = $resultLabTests->fetch_all(MYSQLI_ASSOC);
    $stmtLabTests->close();
} catch (Exception $e) {
    $error_message = "An error occurred: " . htmlspecialchars($e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto py-10 px-6">
        <h1 class="text-3xl font-bold text-center mb-6"><?php echo htmlspecialchars($pageTitle); ?></h1>

        <!-- Error Message -->
        <?php if (isset($error_message)): ?>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <!-- Lab Tests -->
        <?php if (!empty($labTests)): ?>
            <div class="bg-white shadow-md rounded p-6">
                <h2 class="text-xl font-semibold mb-4">Lab Tests</h2>
                <table class="table-auto w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-4 py-2">Test Name</th>
                            <th class="border border-gray-300 px-4 py-2">Test Date</th>
                            <th class="border border-gray-300 px-4 py-2">Status</th>
                            <th class="border border-gray-300 px-4 py-2">Reports</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($labTests as $test): ?>
                            <tr class="text-center">
                                <td class="border border-gray-300 px-4 py-2">
                                    <?php echo htmlspecialchars($test['TestName']); ?>
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <?php echo htmlspecialchars($test['TestDate']); ?>
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <?php echo htmlspecialchars($test['Status']); ?>
                                </td>
                                <td class="border border-gray-300 px-4 py-2">
                                    <?php
                                    try {
                                        // Fetch reports for this lab test
                                        $stmtReports = $conn->prepare("
                                            SELECT ReportFilePath, UploadDate
                                            FROM LabTestReports
                                            WHERE LabTestID = ?
                                            ORDER BY UploadDate DESC
                                        ");
                                        $stmtReports->bind_param("i", $test['LabTestID']);
                                        $stmtReports->execute();
                                        $resultReports = $stmtReports->get_result();
                                        $reports = $resultReports->fetch_all(MYSQLI_ASSOC);
                                        $stmtReports->close();
                                    } catch (Exception $e) {
                                        echo "Error fetching reports";
                                        continue;
                                    }
                                    ?>
                                    <?php if (!empty($reports)): ?>
                                        <ul>
                                            <?php foreach ($reports as $report): ?>
                                                <li>
                                                    <a href="hospital_management/uploads/<?php htmlspecialchars($report['ReportFilePath']); ?>"
                                                        target="_blank" class="text-indigo-500 hover:underline">
                                                        View Report (Uploaded: <?php echo htmlspecialchars($report['UploadDate']); ?>)
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-gray-500">No reports available</p>
                                    <?php endif; ?>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-700 text-center">No lab tests available.</p>
        <?php endif; ?>

        <div class="mt-6 text-center">
            <a href="index.php" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">
                Back to Dashboard
            </a>
        </div>
    </div>
</body>

</html>