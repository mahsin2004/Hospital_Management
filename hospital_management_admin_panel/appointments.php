<?php
$pageTitle = "Appointments";
ob_start(); // Start output buffering
include 'db.php'; // Include database connection

// Fetch all doctors and patients for the select options
$doctors_sql = "SELECT DoctorID, Name FROM Doctors";
$doctors_result = $conn->query($doctors_sql);

$patients_sql = "SELECT PatientID, Name FROM Patients";
$patients_result = $conn->query($patients_sql);

// Handle form submission for adding an appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $status = $_POST['status'];

    // Insert appointment into the database
    $sql = "INSERT INTO Appointments (DoctorID, PatientID, AppointmentDate, AppointmentTime, Status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisss", $doctor_id, $patient_id, $appointment_date, $appointment_time, $status);
    $stmt->execute();
    $stmt->close();
    $message = "Appointment added successfully!";
}

// Fetch all appointments from the database
$sql = "SELECT a.AppointmentID, a.AppointmentDate, a.AppointmentTime, a.Status, 
               d.Name AS doctor_name, p.Name AS patient_name
        FROM Appointments a
        JOIN Doctors d ON a.DoctorID = d.DoctorID
        JOIN Patients p ON a.PatientID = p.PatientID
        ORDER BY a.AppointmentDate, a.AppointmentTime";
$result = $conn->query($sql);
?>

<div class="bg-white p-6 shadow rounded">
    <h2 class="text-xl font-bold mb-4">Appointments</h2>

    <!-- Form to add new appointment -->
    <div class="bg-white p-6 shadow rounded mb-6">
        <form method="POST" action="appointments.php" class="">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700">Patient:</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded" name="patient_id" required>
                        <option value="">Select Patient</option>
                        <?php while ($row = $patients_result->fetch_assoc()) { ?>
                            <option value="<?php echo $row['PatientID']; ?>"><?php echo $row['Name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700">Doctor:</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded" name="doctor_id" required>
                        <option value="">Select Doctor</option>
                        <?php while ($row = $doctors_result->fetch_assoc()) { ?>
                            <option value="<?php echo $row['DoctorID']; ?>"><?php echo $row['Name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700">Date:</label>
                    <input class="w-full px-4 py-2 border border-gray-300 rounded" type="date" name="appointment_date" required>
                </div>
                <div>
                    <label class="block text-gray-700">Time:</label>
                    <input class="w-full px-4 py-2 border border-gray-300 rounded" type="time" name="appointment_time" required>
                </div>
                <div class="col-span-2">
                    <label class="block text-gray-700">Status:</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded" name="status" required>
                        <option value="Scheduled">Scheduled</option>
                        <option value="Completed">Completed</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-4">Add Appointment</button>
        </form>
        <?php if (isset($message)) {
            echo "<p class='text-green-500 mt-4'>$message</p>";
        } ?>
    </div>

    <!-- Table to display appointments -->
    <table class="w-full border-collapse border border-gray-200">
        <thead>
            <tr class="bg-gray-100">
                <th class="border border-gray-200 p-2">ID</th>
                <th class="border border-gray-200 p-2">Patient Name</th>
                <th class="border border-gray-200 p-2">Doctor</th>
                <th class="border border-gray-200 p-2">Date</th>
                <th class="border border-gray-200 p-2">Time</th>
                <th class="border border-gray-200 p-2">Status</th>
                <th class="border border-gray-200 p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td class="border border-gray-200 p-2"><?php echo $row['AppointmentID']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $row['patient_name']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $row['doctor_name']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $row['AppointmentDate']; ?></td>
                        <td class="border border-gray-200 p-2"><?php echo $row['AppointmentTime']; ?></td>
                        <td class="border border-gray-200 p-2 text-<?php echo strtolower($row['Status']) == 'scheduled' ? 'blue' : (strtolower($row['Status']) == 'completed' ? 'green' : 'red'); ?>-500">
                            <?php echo $row['Status']; ?>
                        </td>
                        <td class="border border-gray-200 p-2 text-center">
                            <a href="edit_appointment.php?id=<?php echo $row['AppointmentID']; ?>" class="text-blue-500 hover:underline">Edit</a>
                            <a href="delete_appointment.php?id=<?php echo $row['AppointmentID']; ?>" class="text-red-500 hover:underline ml-2">Delete</a>
                        </td>
                    </tr>
                <?php }
            } else { ?>
                <tr>
                    <td colspan="7" class="border border-gray-200 p-2 text-center">No appointments found.</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean(); // Capture the output and assign it to $content
include 'layout.php'; // Include the layout file
?>
