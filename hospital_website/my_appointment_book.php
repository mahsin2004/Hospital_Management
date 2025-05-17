<?php
// Include the database connection
include('db.php');
session_start();

// Check if the patient or doctor is logged in
$isPatientLoggedIn = isset($_SESSION['patient_id']);
$patientName = $isPatientLoggedIn ? $_SESSION['patient_name'] : null;

$isDoctorLoggedIn = isset($_SESSION['doctor_id']);
$doctorName = $isDoctorLoggedIn ? $_SESSION['doctor_name'] : null;

// Initialize variables
$appointments = [];
$error = "";

// Check user type
if (isset($_SESSION['patient_id'])) {
    // Fetch appointments for the logged-in patient
    $patient_id = $_SESSION['patient_id'];
    $stmt = $conn->prepare("SELECT a.AppointmentID, a.AppointmentDate, a.Status, d.Name AS DoctorName 
                            FROM Appointments a
                            JOIN Doctors d ON a.DoctorID = d.DoctorID
                            WHERE a.PatientID = ?");
    $stmt->bind_param("i", $patient_id);
} elseif (isset($_SESSION['doctor_id'])) {
    // Fetch appointments for the logged-in doctor
    $doctor_id = $_SESSION['doctor_id'];
    $stmt = $conn->prepare("SELECT a.AppointmentID, a.AppointmentDate, a.Status, p.Name AS PatientName 
                            FROM Appointments a
                            JOIN Patients p ON a.PatientID = p.PatientID
                            WHERE a.DoctorID = ?");
    $stmt->bind_param("i", $doctor_id);
} else {
    $error = "You must be logged in to view your appointments.";
}

if (!empty($stmt)) {
    $stmt->execute();
    $result = $stmt->get_result();
    $appointments = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="text-white bg-gray-800">
        <div class="container mx-auto flex items-center justify-between py-2 px-6">
            <img src="https://i.postimg.cc/jSvW9XfQ/output-onlinepngtools-2.png" alt="Logo" class="w-16 h-16 object-contain">
            <div class="flex gap-2 items-center">
                <a href="index.php" class="text-white hover:text-indigo-400 px-4">Home</a>
                <a href="lab_tests.php" class="text-white hover:text-indigo-400 px-4">Lab Tests</a>
                <a href="my_appointment_book.php" class="text-white hover:text-indigo-400 px-4">My Appointments</a>
                <?php if ($isPatientLoggedIn): ?>
                    <!-- Show patient name and logout -->
                    <span class="text-white font-bold">Hello, <?= htmlspecialchars($patientName) ?></span>
                    <a href="logout.php" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">Logout</a>
                <?php elseif ($isDoctorLoggedIn): ?>
                    <!-- Show doctor name and logout -->
                    <span class="text-white font-bold">Hello, Dr. <?= htmlspecialchars($doctorName) ?></span>
                    <a href="logout.php" class="bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">Logout</a>
                <?php else: ?>
                    <!-- Show join buttons if not logged in -->
                    <a href="login_patient.php" class="bg-indigo-500 text-white py-2 px-4 rounded hover:bg-indigo-600">
                        Join as Patient
                    </a>
                    <a href="login_doctor.php" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">
                        Join as Doctor
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>


    <div class="container mx-auto py-10">
        <h2 class="text-2xl font-bold text-center mb-6">My Appointments</h2>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php elseif (!empty($appointments)): ?>
            <table class="min-w-full bg-white shadow-md rounded">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Appointment ID</th>
                        <th class="py-2 px-4 border-b"><?= isset($_SESSION['patient_id']) ? "Doctor Name" : "Patient Name" ?></th>
                        <th class="py-2 px-4 border-b">Date</th>
                        <th class="py-2 px-4 border-b">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appointment): ?>
                        <tr>
                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($appointment['AppointmentID']) ?></td>
                            <td class="py-2 px-4 border-b">
                                <?= isset($_SESSION['patient_id']) ? htmlspecialchars($appointment['DoctorName']) : htmlspecialchars($appointment['PatientName']) ?>
                            </td>
                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($appointment['AppointmentDate']) ?></td>
                            <td class="py-2 px-4 border-b"><?= htmlspecialchars($appointment['Status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center text-gray-600">No appointments found.</p>
        <?php endif; ?>
    </div>
</body>

</html>