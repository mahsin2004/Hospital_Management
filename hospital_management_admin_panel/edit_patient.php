<?php
$pageTitle = "Edit Patient Info";
ob_start(); // Start output buffering
include 'db.php';

// Enable mysqli exceptions for better error handling
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];

    // Fetch patient details
    $sql = "SELECT * FROM Patients WHERE PatientID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $patient = $result->fetch_assoc();
    $stmt->close();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $age = $_POST['age'];
        $phone = $_POST['phone'];

        try {
            // Update patient details
            $sql = "UPDATE Patients SET Name = ?, Age = ?, PhoneNumber = ? WHERE PatientID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sisi", $name, $age, $phone, $patient_id);
            $stmt->execute();
            $stmt->close();

            echo "<p style='color: green;'>Patient info updated successfully!</p>";
            echo "<a href='patients.php'>Back to Patient List</a>";
            exit();
        } catch (mysqli_sql_exception $e) {
            // Handle duplicate entry error
            if (strpos($e->getMessage(), "Duplicate entry") !== false) {
                echo "<p style='color: red;'>The phone number <strong>$phone</strong> is already in use by another patient. Please use a different phone number.</p>";
            } else {
                // Handle other SQL errors
                echo "<p style='color: red;'>An unexpected error occurred: " . $e->getMessage() . "</p>";
            }
            // echo "<a href='edit_patient.php?id=$patient_id'>Go Back</a>";
        }
    }
} else {
    // Redirect to patient list if ID is not set
    header("Location: patients.php");
    exit();
}
?>



<!-- Form to edit patient -->
<div class="bg-white p-6 shadow rounded mb-4">
    <h2 class="text-xl font-bold mb-4">Edit Patient Info</h2>
    <form method="POST" action="edit_patient.php?id=<?php echo $patient['PatientID']; ?>">
        <div class="mb-4">
            <label class="block text-gray-700">Name:</label>
            <input class="w-full px-4 py-2 border border-gray-300 rounded" type="text" name="name" value="<?php echo $patient['Name']; ?>" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Age:</label>
            <input class="w-full px-4 py-2 border border-gray-300 rounded" type="number" name="age" value="<?php echo $patient['Age']; ?>" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700">Phone Number:</label>
            <input class="w-full px-4 py-2 border border-gray-300 rounded" type="text" name="phone" value="<?php echo $patient['PhoneNumber']; ?>" required>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Save Changes</button>
    </form>
</div>

<?php
$content = ob_get_clean(); // Capture the output and assign it to $content
include 'layout.php'; // Include the layout file
?>
