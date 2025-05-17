<?php
// Include database connection
include('db.php');
session_start();

// Redirect if a patient or doctor is already logged in
if (isset($_SESSION['patient_id'])) {
    header('Location: index.php'); // Replace with the patient dashboard or homepage
    exit();
}

if (isset($_SESSION['doctor_id'])) {
    header('Location: index.php'); // Replace with the doctor dashboard or homepage
    exit();
}


$error = ""; // Initialize error message

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_doctor'])) {
    // Collect form data
    $contact = trim($_POST['contact']);
    $password = trim($_POST['password']);

    // Validate input
    if (!empty($contact) && !empty($password)) {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT * FROM Doctors WHERE PhoneNumber = ?");
        $stmt->bind_param("s", $contact);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();

        if ($doctor) {
            // Verify password
            if (password_verify($password, $doctor['Password'])) {
                // Start session and set session variables
                $_SESSION['doctor_id'] = $doctor['DoctorID'];
                $_SESSION['doctor_name'] = $doctor['Name'];
                $_SESSION['doctor_contact'] = $doctor['PhoneNumber'];

                // Redirect to doctor dashboard
                header('Location: index.php');
                exit();
            } else {
                // Invalid password message
                $error = "Invalid password. Please try again.";
            }
        } else {
            // No doctor found with the provided contact
            $error = "Doctor not found. Please check your contact number.";
        }
        $stmt->close();
    } else {
        $error = "Both phone number and password are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-6 shadow rounded mb-4 w-96">
            <h2 class="text-xl font-bold mb-4 text-center">Doctor Login</h2>

            <!-- Display error message -->
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-center">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-4">
                    <label for="contact" class="block text-sm font-medium text-gray-700">Phone Number:</label>
                    <input type="number" id="contact" name="contact" class="w-full border border-gray-300 rounded p-2" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                    <input type="password" id="password" name="password" class="w-full border border-gray-300 rounded p-2" required>
                </div>
                <div class="text-center">
                    <button type="submit" name="login_doctor" class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600">Login</button>
                </div>
            </form>
            <div class="mt-4 text-center">
                <p class="text-sm">Not registered yet? <a href="register_doctor.php" class="text-indigo-500 hover:text-indigo-700">Register</a></p>
            </div>
        </div>
    </div>
</body>

</html>
