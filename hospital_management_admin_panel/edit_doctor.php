<?php
$pageTitle = "Edit Doctor Info";
ob_start();
include 'db.php';

// Enable mysqli exceptions for better error handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $doctor_id = intval($_GET['id']);

    try {
        // Fetch doctor details
        $sql = "SELECT * FROM Doctors WHERE DoctorID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $doctor = $result->fetch_assoc();
        $stmt->close();

        // Redirect if doctor not found
        if (!$doctor) {
            header("Location: doctors.php?error=DoctorNotFound");
            exit();
        }

        // Handle POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = htmlspecialchars(trim($_POST['name']));
            $specialization = htmlspecialchars(trim($_POST['specialization']));
            $phone_number = htmlspecialchars(trim($_POST['phone_number']));

            if (!empty($name) && !empty($specialization) && !empty($phone_number)) {
                try {
                    // Update doctor details
                    $sql = "UPDATE Doctors SET Name = ?, Specialization = ?, PhoneNumber = ? WHERE DoctorID = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssi", $name, $specialization, $phone_number, $doctor_id);
                    $stmt->execute();
                    $stmt->close();

                    // Redirect to the doctor list with a success message
                    // header("Location: doctors.php?success=DoctorUpdated");
                    echo "<p style='color: green;'>Doctor info updated successfully!</p>";
                    echo "<a href='doctors.php'>Back to Doctor List</a>";
                    exit();
                } catch (mysqli_sql_exception $e) {
                    // Handle duplicate entry error for PhoneNumber
                    if (strpos($e->getMessage(), "Duplicate entry") !== false) {
                        $error = "The phone number <strong>$phone_number</strong> is already in use. Please use a unique phone number.";
                    } else {
                        $error = "An unexpected error occurred. Please try again later.";
                    }
                }
            } else {
                $error = "All fields are required.";
            }
        }
    } catch (mysqli_sql_exception $e) {
        // Handle SQL errors gracefully
        header("Location: doctors.php?error=DatabaseError");
        exit();
    }
} else {
    // Redirect if ID is not valid
    header("Location: doctors.php?error=InvalidID");
    exit();
}
?>

<div class="bg-white p-6 shadow rounded">
    <h2 class="text-xl font-bold mb-4">Edit Doctor Information</h2>

    <?php if (isset($error)) {
        echo "<p class='text-red-500'>$error</p>";
    } ?>

    <form method="POST" action="edit_doctor.php?id=<?php echo $doctor_id; ?>">
        <div class="mb-4">
            <label for="name" class="block text-gray-700">Name:</label>
            <input
                type="text"
                id="name"
                name="name"
                value="<?php echo htmlspecialchars($doctor['Name']); ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded"
                required>
        </div>
        <div class="mb-4">
            <label for="specialization" class="block text-gray-700">Specialization:</label>
            <input
                type="text"
                id="specialization"
                name="specialization"
                value="<?php echo htmlspecialchars($doctor['Specialization']); ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded"
                required>
        </div>
        <div class="mb-4">
            <label for="phone_number" class="block text-gray-700">Phone Number:</label>
            <input
                type="text"
                id="phone_number"
                name="phone_number"
                value="<?php echo htmlspecialchars($doctor['PhoneNumber']); ?>"
                class="w-full px-4 py-2 border border-gray-300 rounded"
                required>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Changes</button>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>