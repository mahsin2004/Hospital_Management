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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register_doctor'])) {
    // Collect and sanitize form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $specialization = mysqli_real_escape_string($conn, $_POST['specialization']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Validate phone number (basic validation)
    if (!preg_match("/^[0-9]{10,15}$/", $contact)) {
        $error = "Please enter a valid phone number (10-15 digits).";
    } else {
        // Check if the contact number already exists
        $query = "SELECT * FROM doctors WHERE PhoneNumber = '$contact'";
        $result = mysqli_query($conn, $query);
        if (mysqli_num_rows($result) > 0) {
            $error = "This phone number is already registered. Please try another one.";
        } else {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert data into the database
            $query = "INSERT INTO doctors (Name, Specialization, PhoneNumber, Password) 
                      VALUES ('$name', '$specialization', '$contact', '$hashed_password')";

            if (mysqli_query($conn, $query)) {
                // Get the last inserted doctor ID
                $doctor_id = mysqli_insert_id($conn);

                // Set session variables after successful registration
                $_SESSION['success'] = "Registration successful! Please log in.";
                $_SESSION['doctor_id'] = $doctor_id;
                $_SESSION['doctor_name'] = $name;
                $_SESSION['doctor_contact'] = $contact;
                $_SESSION['logged_in'] = true;

                // Redirect to login page after successful registration
                header('Location: index.php'); 
                exit();
            } else {
                // Registration failed
                $error = "Registration failed: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Navbar -->
    <div class="bg-gray-800">
        <nav class="text-white">
            <div class="container mx-auto flex items-center justify-between py-4 px-6">
                <img src="https://i.postimg.cc/jSvW9XfQ/output-onlinepngtools-2.png" alt="Logo" class="w-12 h-12 object-contain">
                <div>
                    <a href="index.php" class="text-white hover:text-indigo-400 px-4">Home</a>
                </div>
            </div>
        </nav>
    </div>

    <!-- Registration Form -->
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-6 shadow rounded-lg w-full max-w-md">
            <h2 class="text-xl font-bold mb-4 text-center text-gray-800">Register as a Doctor</h2>

            <!-- Display success message or error message -->
            <?php if (isset($error)): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-center">
                    <?php echo htmlspecialchars($error); // Escape error message ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-center">
                    <?php echo htmlspecialchars($_SESSION['success']); ?>
                    <?php unset($_SESSION['success']); ?> <!-- Unset session success after displaying it -->
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-medium">Name:</label>
                    <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Enter your full name" aria-label="Name" required>
                </div>
                <div class="mb-4">
                    <label for="specialization" class="block text-gray-700 font-medium">Specialization:</label>
                    <input type="text" id="specialization" name="specialization" class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Enter your specialization" aria-label="Specialization" required>
                </div>
                <div class="mb-4">
                    <label for="contact" class="block text-gray-700 font-medium">Phone Number:</label>
                    <input type="number" id="contact" name="contact" class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Enter your phone number" aria-label="Phone Number" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 font-medium">Password:</label>
                    <input type="password" id="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:outline-none" placeholder="Enter a password" minlength="6" title="Password must be at least 6 characters long" aria-label="Password" required>
                </div>
                <div class="text-center">
                    <button type="submit" name="register_doctor" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500">Register</button>
                </div>
            </form>
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600">Already registered? <a href="login_doctor.php" class="text-indigo-500 hover:text-indigo-700">Login</a></p>
            </div>
        </div>
    </div>
</body>

</html>
