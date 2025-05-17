<?php
// Start session
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login_patient'])) {
    // Collect form data
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);

    // Validate inputs
    if (empty($phone) || empty($password)) {
        $error = "Phone number and password are required.";
    } else {
        // Prepare SQL statement
        $query = "SELECT * FROM patients WHERE PhoneNumber = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $patient = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $patient['Password'])) {


                $_SESSION['patient_id'] = $patient['PatientID'];
                $_SESSION['patient_name'] = $patient['Name'];

                // Redirect to patient dashboard
                header('Location: index.php');
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with this phone number.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Login</title>
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
                </div>
            </div>
        </nav>
    </div>

    <!-- Patient Login Form -->
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-6 shadow rounded mb-4 w-96">
            <h2 class="text-xl font-bold mb-4 text-center">Patient Login</h2>

            <!-- Error message -->
            <?php if (!empty($error)) : ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-center">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number:</label>
                    <input type="number" id="phone" name="phone" class="w-full border border-gray-300 rounded p-2" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                    <input type="password" id="password" name="password" class="w-full border border-gray-300 rounded p-2" required>
                </div>
                <div class="text-center">
                    <button type="submit" name="login_patient" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Login</button>
                </div>
            </form>
            <div class="mt-4 text-center">
                <p class="text-sm">Not registered yet? <a href="register_patient.php" class="text-indigo-500 hover:text-indigo-700">Register</a></p>
            </div>
        </div>
    </div>
</body>

</html>