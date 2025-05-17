<?php
// Start the session at the beginning of the file
session_start();

// Include database connection
include('db.php');

// Redirect if a patient or doctor is already logged in
if (isset($_SESSION['patient_id'])) {
    header('Location: index.php'); // Replace with the patient dashboard or homepage
    exit();
}

if (isset($_SESSION['doctor_id'])) {
    header('Location: index.php'); // Replace with the doctor dashboard or homepage
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_patient'])) {
    // Collect and sanitize form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $age = mysqli_real_escape_string($conn, $_POST['age']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if phone number already exists
    $query_check = "SELECT * FROM patients WHERE PhoneNumber = '$phone'";
    $result_check = mysqli_query($conn, $query_check);

    if (mysqli_num_rows($result_check) > 0) {
        $error = "A patient with this phone number is already registered.";
    } else {
        // Insert patient into the database
        $query_insert = "INSERT INTO patients (Name, Age, PhoneNumber, Password) VALUES ('$name', '$age', '$phone', '$hashed_password')";
        if (mysqli_query($conn, $query_insert)) {
            // Get patient data after insertion to populate session variables
            $query_patient = "SELECT * FROM patients WHERE PhoneNumber = '$phone'";
            $result_patient = mysqli_query($conn, $query_patient);
            $patient = mysqli_fetch_assoc($result_patient);

            // Set session variables after successful registration
            $_SESSION['patient_id'] = $patient['PatientID'];
            $_SESSION['patient_name'] = $patient['Name'];
            $_SESSION['logged_in'] = true;

            // Redirect to homepage or dashboard
            header('Location: index.php');
            exit();
        } else {
            $error = "An error occurred while registering. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="bg-gray-800">
        <nav class="text-white">
            <div class="container mx-auto flex items-center justify-between py-2 px-6">
                <img src="https://i.postimg.cc/jSvW9XfQ/output-onlinepngtools-2.png" alt="Logo" class="w-16 h-16 object-contain">
                <a href="index.php" class="text-white hover:text-indigo-400 px-4">Home</a>
            </div>
        </nav>
    </div>

    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-6 shadow rounded mb-4 w-96">
            <h2 class="text-xl font-bold mb-4 text-center">Register as a Patient</h2>

            <!-- Error Message -->
            <?php if (!empty($error)) : ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-center">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name:</label>
                    <input type="text" id="name" name="name" class="w-full border border-gray-300 rounded p-2" required>
                </div>
                <div class="mb-4">
                    <label for="age" class="block text-sm font-medium text-gray-700">Age:</label>
                    <input type="number" id="age" name="age" class="w-full border border-gray-300 rounded p-2" required>
                </div>
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number:</label>
                    <input type="number" id="phone" name="phone" class="w-full border border-gray-300 rounded p-2" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                    <input type="password" id="password" name="password" class="w-full border border-gray-300 rounded p-2" required>
                </div>
                <div class="text-center">
                    <button type="submit" name="register_patient" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Register</button>
                </div>
            </form>
            <div class="mt-4 text-center">
                <p class="text-sm">Already registered? <a href="login_patient.php" class="text-indigo-500 hover:text-indigo-700">Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>
