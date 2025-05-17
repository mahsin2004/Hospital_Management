<?php
$pageTitle = "Book Appointment";

session_start(); // Start the session

// Include database connection file
include 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit;
}

// Check if the patient or doctor is logged in
$isPatientLoggedIn = isset($_SESSION['patient_id']);
$patientName = $isPatientLoggedIn ? $_SESSION['patient_name'] : null;

$isDoctorLoggedIn = isset($_SESSION['doctor_id']);
$doctorName = $isDoctorLoggedIn ? $_SESSION['doctor_name'] : null;

// Retrieve the patient ID from the session
$patientID = $_SESSION['patient_id'];

// Get the doctor ID from query parameters if available
$selectedDoctor = isset($_GET['doctor_id']) ? htmlspecialchars($_GET['doctor_id']) : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize user inputs
    $doctorID = htmlspecialchars($_POST['doctor']);
    $appointmentDate = htmlspecialchars($_POST['appointment_date']);
    $appointmentTime = htmlspecialchars($_POST['appointment_time']);
    $status = 'Scheduled'; // Default status

    try {
        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("INSERT INTO Appointments (DoctorID, PatientID, AppointmentDate, AppointmentTime, Status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iisss", $doctorID, $patientID, $appointmentDate, $appointmentTime, $status);
        $stmt->execute();
        $stmt->close();
        $message = "Appointment booked successfully!";
    } catch (mysqli_sql_exception $e) {
        $error_message = "An error occurred: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <nav class="bg-gray-800 text-white">
        <div class="container mx-auto flex items-center justify-between py-2 px-6">
            <img src="https://i.postimg.cc/jSvW9XfQ/output-onlinepngtools-2.png" alt="Logo" class="w-16 h-16 object-contain">
            <div>
                <a href="index.php" class="text-white hover:text-indigo-400 px-4">Home</a>
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

    <!-- Appointment Booking Form Section -->
    <div id="book-appointment" class="container mx-auto py-12 px-6">
        <h2 class="text-2xl font-bold mb-6 text-center">Book an Appointment</h2>
        <form action="" method="POST" class="bg-white shadow rounded p-6 max-w-md mx-auto">
            <div class="mb-4">
                <!-- Hidden Doctor ID field -->
                <input type="hidden" id="doctor" name="doctor" value="<?php echo $selectedDoctor; ?>" />

                <div class="mb-4">
                    <label for="appointment_date" class="block text-gray-700 font-medium mb-2">Appointment Date</label>
                    <input
                        type="date"
                        id="appointment_date"
                        name="appointment_date"
                        class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required />
                </div>
                <div class="mb-4">
                    <label for="appointment_time" class="block text-gray-700 font-medium mb-2">Appointment Time</label>
                    <input
                        type="time"
                        id="appointment_time"
                        name="appointment_time"
                        class="w-full border border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required />
                </div>

                <button
                    type="submit"
                    class="w-full bg-indigo-500 text-white py-3 px-6 rounded hover:bg-indigo-600 focus:ring-2 focus:ring-indigo-500">
                    Book Appointment
                </button>

                <?php
                if (isset($message)) {
                    echo "<p class='text-green-500 mt-4'>$message</p>";
                }

                if (isset($error_message)) {
                    echo "<p class='text-red-500 mt-4'>$error_message</p>";
                }
                ?>
        </form>
    </div>

</body>

</html>