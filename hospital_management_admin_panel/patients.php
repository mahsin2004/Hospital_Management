<?php
$pageTitle = "Patients";
ob_start(); // Start output buffering

// Include database connection file (update with your connection details)
include 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_patient'])) {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Hash the password before storing it
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert patient data into the database
    try {
        $stmt = $conn->prepare("INSERT INTO Patients (Name, Age, PhoneNumber, Password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siss", $name, $age, $phone, $hashedPassword);
        $stmt->execute();
        $stmt->close();
        $message = "Patient added successfully!";
    } catch (mysqli_sql_exception $e) {
        // Check if it's a duplicate entry error
        if ($e->getCode() == 1062) {
            $error_message = "This phone number is already registered.";
        } else {
            $error_message = "An error occurred. Please try again.";
        }
    }
}

// Fetch patients from the database
$sql = "SELECT * FROM Patients";
$result = $conn->query($sql);

// Display success or error messages
if (isset($_GET['message']) && $_GET['message'] === 'PatientDeleted') {
    echo "<p class='text-green-500'>Patient successfully deleted.</p>";
}
if (isset($_GET['error'])) {
    if ($_GET['error'] === 'DeletionFailed') {
        echo "<p class='text-red-500'>Failed to delete patient. Please try again.</p>";
    } elseif ($_GET['error'] === 'NoIDProvided') {
        echo "<p class='text-red-500'>No patient ID provided.</p>";
    }
}
?>

<!-- Add Patient Form -->
<div class="bg-white p-6 shadow rounded mb-4">
    <h2 class="text-xl font-bold mb-4">Add New Patient</h2>
    <form action="patients.php" method="POST">
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
        <button type="submit" name="add_patient" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Add Patient</button>
    </form>

    <?php
    // Display success message
    if (isset($message)) {
        echo "<p class='text-green-500 mt-4'>$message</p>";
    }

    // Display error message
    if (isset($error_message)) {
        echo "<p class='text-red-500 mt-4'>$error_message</p>";
    }
    ?>
</div>

<!-- Patients List -->
<div class="bg-white p-6 shadow rounded mb-4">
    <h2 class="text-xl font-bold mb-4">Patients List</h2>
    <table class="w-full border-collapse border border-gray-200">
        <thead>
            <tr class="bg-gray-100">
                <th class="border border-gray-200 p-2">ID</th>
                <th class="border border-gray-200 p-2">Name</th>
                <th class="border border-gray-200 p-2">Age</th>
                <th class="border border-gray-200 p-2">Phone</th>
                <th class="border border-gray-200 p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td class="border border-gray-200 p-2 text-center"><?php echo $row['PatientID']; ?></td>
                        <td class="border border-gray-200 p-2 text-center"><?php echo $row['Name']; ?></td>
                        <td class="border border-gray-200 p-2 text-center"><?php echo $row['Age']; ?></td>
                        <td class="border border-gray-200 p-2 text-center"><?php echo $row['PhoneNumber']; ?></td>
                        <td class="border border-gray-200 p-2 text-center">
                            <a href="edit_patient.php?id=<?php echo $row['PatientID']; ?>" class="text-blue-500 hover:underline">Edit</a>
                            <a href="delete_patient.php?id=<?php echo $row['PatientID']; ?>" class="text-red-500 hover:underline ml-2">Delete</a>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="5" class="border border-gray-200 p-2 text-center">No patients found.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean(); // Capture the output and assign it to $content
include 'layout.php'; // Include the layout file
?>
