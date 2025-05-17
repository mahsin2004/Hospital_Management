<?php
session_start();
include 'db.php'; // Include the database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords and Confirm Passwords do not match.";
    } else {
        // Hash the password before storing
        $hashed_password = md5($password); // Use a stronger hashing algorithm like bcrypt in production

        // Check if the email is already taken
        $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
        if ($result->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            // Insert new user into the database
            $conn->query("INSERT INTO users (email, password) VALUES ('$email', '$hashed_password')");
            $_SESSION['logged_in'] = true;
            $_SESSION['email'] = $email;
            header("Location: index.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded shadow-md w-96">
        <h2 class="text-2xl font-bold mb-4 text-center">Admin Register</h2>

        <form action="register.php" method="POST">
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md border pl-3 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm h-10" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md border pl-3 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm h-10" required>
            </div>
            <div class="mb-4">
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="mt-1 block w-full border-gray-300 rounded-md border pl-3 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm h-10" required>
            </div>
            <?php if (isset($error)): ?>
                <p class="text-red-500 text-sm mb-4"><?php echo $error; ?></p>
            <?php endif; ?>
            <button type="submit" class="w-full bg-indigo-500 text-white py-2 px-4 rounded-md hover:bg-indigo-600 ">Register</button>
        </form>
        <div class="mt-4 text-center">
            <p class="text-sm">Already registered? <a href="login.php" class="text-indigo-500 hover:text-indigo-700">Login</a></p>
        </div>
    </div>
</body>

</html>