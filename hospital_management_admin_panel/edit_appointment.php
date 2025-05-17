<?php
$pageTitle = "Edit Appointment";
ob_start(); // Start output buffering
include 'db.php'; // Include database connection

// Fetch doctor and patient lists for dropdowns
$doctor_sql = "SELECT DoctorID, name FROM Doctors";  // Replace 'name' with the correct column name
$doctor_result = $conn->query($doctor_sql);

$patient_sql = "SELECT PatientID, Name FROM Patients";
$patient_result = $conn->query($patient_sql);

// Fetch appointment data if 'id' is passed
if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];

    // Fetch the appointment details from the database
    $sql = "SELECT * FROM appointments WHERE AppointmentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();
    $stmt->close();

    // Check if the appointment exists
    if (!$appointment) {
        echo "<p class='text-red-500'>Appointment not found.</p>";
        exit;
    }
}

// Handle form submission for updating an appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $status = $_POST['status'];

    // Update the appointment in the database
    $sql = "UPDATE appointments SET PatientID = ?, DoctorID = ?, AppointmentDate = ?, AppointmentTime = ?, Status = ? WHERE AppointmentID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssi", $patient_id, $doctor_id, $date, $time, $status, $appointment_id);
    if ($stmt->execute()) {
        echo "<p style='color: green;'>Appointment info updated successfully!</p>";
        echo "<a href='appointments.php'>Back to Appointment List</a>";
        exit();
    } else {
        $message = "Error updating appointment.";
    }
    $stmt->close();
}
?>

<div class="bg-white p-6 shadow rounded">
    <h2 class="text-xl font-bold mb-4">Edit Appointment</h2>

    <!-- Form to edit the appointment -->
    <div class="bg-white p-6 shadow rounded mb-6">
        <form method="POST" action="edit_appointment.php?id=<?php echo $appointment_id; ?>" class="">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700">Patient:</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded" name="patient_id" required>
                        <option value="">Select Patient</option>
                        <?php while ($row = $patient_result->fetch_assoc()) { ?>
                            <option value="<?php echo $row['PatientID']; ?>" <?php echo ($appointment['PatientID'] == $row['PatientID']) ? 'selected' : ''; ?>>
                                <?php echo $row['Name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700">Doctor:</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded" name="doctor_id" required>
                        <option value="">Select Doctor</option>
                        <?php while ($row = $doctor_result->fetch_assoc()) { ?>
                            <option value="<?php echo $row['DoctorID']; ?>" <?php echo ($appointment['DoctorID'] == $row['DoctorID']) ? 'selected' : ''; ?>>
                                <?php echo $row['name']; ?> <!-- Replace 'name' with the correct column name -->
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700">Date:</label>
                    <input class="w-full px-4 py-2 border border-gray-300 rounded" type="date" name="date" value="<?php echo $appointment['AppointmentDate']; ?>" required>
                </div>
                <div>
                    <label class="block text-gray-700">Time:</label>
                    <input class="w-full px-4 py-2 border border-gray-300 rounded" type="time" name="time" value="<?php echo $appointment['AppointmentTime']; ?>" required>
                </div>
                <div class="col-span-2">
                    <label class="block text-gray-700">Status:</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded" name="status" required>
                        <option value="Scheduled" <?php echo ($appointment['Status'] === 'Scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                        <option value="Completed" <?php echo ($appointment['Status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="Cancelled" <?php echo ($appointment['Status'] === 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Update Appointment</button>
        </form>
        <?php if (isset($message)) {
            echo "<p class='text-green-500 mt-4'>$message</p>";
        } ?>
    </div>
</div>

<?php
$content = ob_get_clean(); // Capture the output and assign it to $content
include 'layout.php'; // Include the layout file
?>