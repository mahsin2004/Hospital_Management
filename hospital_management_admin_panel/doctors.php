<?php
$pageTitle = "Doctors";
ob_start(); // Start output buffering

// Include database connection file (update with your connection details)
include 'db.php';

// Add doctor functionality
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_doctor'])) {
    $name = $_POST['name'];
    $specialization = $_POST['specialization'];
    $phone = $_POST['contact']; // Rename 'contact' to 'phone' for clarity
    $password = $_POST['password']; // Password input field

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement to prevent SQL injection
    $sql = "INSERT INTO Doctors (Name, Specialization, PhoneNumber, Password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $specialization, $phone, $hashed_password);

    // Execute the statement and handle possible errors
    try {
        $stmt->execute();
        $message = "Doctor added successfully!";
    } catch (mysqli_sql_exception $e) {
        // Handle duplicate entry error (for phone number)
        if ($e->getCode() == 1062) {
            $errorMessage = "Error: This phone number is already in use. Please use a different one.";
        } else {
            $errorMessage = "An error occurred while adding the doctor. Please try again.";
        }
    }
    $stmt->close();
}

// Fetch doctors from the database
$sql = "SELECT * FROM Doctors";
$result = $conn->query($sql);

// Display success or error messages for doctor deletion
if (isset($_GET['message']) && $_GET['message'] === 'DoctorDeleted') {
    echo "<p class='text-green-500'>Doctor successfully deleted.</p>";
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'DeletionFailed') {
        echo "<p class='text-red-500'>Failed to delete doctor. Please try again.</p>";
    } elseif ($_GET['error'] === 'NoIDProvided') {
        echo "<p class='text-red-500'>No doctor ID provided.</p>";
    }
}

?>

<!-- Add Doctor Form -->
<div class="bg-white p-6 shadow rounded mb-6">
    <h2 class="text-xl font-bold mb-4">Add New Doctor</h2>
    <form method="POST" action="doctors.php">
        <div class="mb-4">
            <label for="name" class="block text-gray-700">Name:</label>
            <input type="text" id="name" name="name" class="w-full px-4 py-2 border border-gray-300 rounded" required>
        </div>
        <div class="mb-4">
            <label for="specialization" class="block text-gray-700">Specialization:</label>
            <input type="text" id="specialization" name="specialization" class="w-full px-4 py-2 border border-gray-300 rounded" required>
        </div>
        <div class="mb-4">
            <label for="contact" class="block text-gray-700">Phone Number:</label>
            <input type="number" id="contact" name="contact" class="w-full px-4 py-2 border border-gray-300 rounded" required>
        </div>
        <div class="mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
            <input type="password" id="password" name="password" class="w-full border border-gray-300 rounded p-2" required>
        </div>
        <button type="submit" name="add_doctor" class="bg-blue-500 text-white px-4 py-2 rounded">Add Doctor</button>
    </form>
    <?php
    if (isset($message)) {
        echo "<p class='text-green-500 mt-4'>$message</p>";
    }
    if (isset($errorMessage)) {
        echo "<p class='text-red-500 mt-4'>$errorMessage</p>";
    }
    ?>
</div>

<!-- Doctors List Table -->
<div class="bg-white p-6 shadow rounded">
    <h2 class="text-xl font-bold mb-4">Doctors List</h2>
    <table class="w-full border-collapse border border-gray-200">
        <thead>
            <tr class="bg-gray-100">
                <th class="border border-gray-200 p-2 text-center">ID</th>
                <th class="border border-gray-200 p-2 text-center">Name</th>
                <th class="border border-gray-200 p-2 text-center">Specialization</th>
                <th class="border border-gray-200 p-2 text-center">Phone Number</th>
                <th class="border border-gray-200 p-2 text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td class="border border-gray-200 p-2 text-center"><?php echo $row['DoctorID']; ?></td>
                        <td class="border border-gray-200 p-2 text-center"><?php echo $row['Name']; ?></td>
                        <td class="border border-gray-200 p-2 text-center"><?php echo $row['Specialization']; ?></td>
                        <td class="border border-gray-200 p-2 text-center"><?php echo $row['PhoneNumber']; ?></td>
                        <td class="border border-gray-200 p-2 text-center">
                            <a href="edit_doctor.php?id=<?php echo $row['DoctorID']; ?>" class="text-blue-500 hover:underline">Edit</a>
                            <a href="delete_doctor.php?id=<?php echo $row['DoctorID']; ?>" class="text-red-500 hover:underline ml-2">Delete</a>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="5" class="border border-gray-200 p-2 text-center">No doctors found.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean(); // Capture the output and assign it to $content
include 'layout.php'; // Include the layout file
?>
