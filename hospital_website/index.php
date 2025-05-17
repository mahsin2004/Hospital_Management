<?php
// Include the database connection file
include 'db.php';

// Start the session
session_start();

// Check if the patient or doctor is logged in
$isPatientLoggedIn = isset($_SESSION['patient_id']);
$patientName = $isPatientLoggedIn ? $_SESSION['patient_name'] : null;

$isDoctorLoggedIn = isset($_SESSION['doctor_id']);
$doctorName = $isDoctorLoggedIn ? $_SESSION['doctor_name'] : null;

// Query to get the doctors list
$sql = "SELECT * FROM Doctors";
$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
    // Display error message if query fails
    die("Error: " . $conn->error);
}

// Fetch doctors data into an array
$doctors = ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="bg-gray-800">
        <!-- Navbar -->
        <nav class="text-white">
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

        <!-- Website Banner Section -->
        <div class="relative text-white">
            <div class="container mx-auto py-20 px-6 text-center">
                <h1 class="text-4xl font-bold mb-4">Welcome to Our Hospital</h1>
                <p class="text-lg mb-8">Your health is our priority. Book your appointment with our expert doctors today!</p>
            </div>
        </div>
    </div>

    <!-- Doctor List Section -->
    <div class="container mx-auto py-12 px-6">
        <h2 class="text-2xl font-bold mb-6 text-center">Our Doctors</h2>
        <?php if ($isPatientLoggedIn): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (!empty($doctors)): ?>
                    <?php foreach ($doctors as $doctor): ?>
                        <div class="bg-white shadow rounded p-6">
                            <h3 class="text-xl font-bold mb-2"><?= htmlspecialchars($doctor['Name']) ?></h3>
                            <p class="text-gray-600 mb-4"><?= htmlspecialchars($doctor['Specialization']) ?></p>
                            <a href="book-appointment.php?doctor_id=<?= urlencode($doctor['DoctorID']) ?>"
                                class="bg-indigo-500 text-white py-2 px-4 rounded hover:bg-indigo-600">
                                Book Appointment
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-gray-700">No doctors found.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="text-center bg-yellow-100 border border-yellow-400 text-yellow-700 py-4 px-6 rounded">
                <p>You need to log in as a patient to view and book appointments with our doctors.</p>
                <a href="login_patient.php" class="bg-indigo-500 text-white py-2 px-4 rounded hover:bg-indigo-600 mt-4 inline-block">
                    Log in as Patient
                </a>
            </div>
        <?php endif; ?>
    </div>

    <section class="bg-cover bg-center text-white py-20" style="background-image: url('https://images.unsplash.com/photo-1724786594231-c8bfd41c0bfa?q=80&w=1793&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'); ">
        <div class=" container mx-auto text-center px-6">
        <blockquote class="text-3xl italic font-semibold mb-6">
            "The only way to do great work is to love what you do."
        </blockquote>
        <p class="text-xl font-semibold">- Steve Jobs</p>
        <div class="mt-8">
            <a href="" class="bg-indigo-400 text-white px-6 py-2 rounded-sm hover:bg-indigo-500">
                Learn More
            </a>
        </div>
        </div>
    </section>


    <footer class="bg-gray-800 text-white py-6">
        <div class="container mx-auto text-center">
            <p>&copy; <?php echo date("Y"); ?> Your Company Name. All rights reserved.</p>
            <div class="mt-4">
                <a href="privacy-policy.php" class="text-indigo-400 hover:underline">Privacy Policy</a> |
                <a href="terms-of-service.php" class="text-indigo-400 hover:underline">Terms of Service</a> |
                <a href="contact.php" class="text-indigo-400 hover:underline">Contact Us</a>
            </div>
            <div class="mt-4">
                <p>Follow us on:</p>
                <div class="flex justify-center space-x-4">
                    <a href="#" class="text-white hover:text-indigo-400"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="text-white hover:text-indigo-400"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-white hover:text-indigo-400"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" class="text-white hover:text-indigo-400"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>


</body>

</html>