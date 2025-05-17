<?php
$pageTitle = "Dashboard";
ob_start(); // Start output buffering

include 'db.php'; // Include database connection

// Fetch total patients count
$total_patients_sql = "SELECT COUNT(*) as total FROM Patients";
$total_patients_result = $conn->query($total_patients_sql);
$total_patients = $total_patients_result->fetch_assoc()['total'];

// Fetch total doctors count
$total_doctors_sql = "SELECT COUNT(*) as total FROM Doctors";
$total_doctors_result = $conn->query($total_doctors_sql);
$total_doctors = $total_doctors_result->fetch_assoc()['total'];

// Fetch total appointments count
$total_appointments_sql = "SELECT COUNT(*) as total FROM Appointments";
$total_appointments_result = $conn->query($total_appointments_sql);
$total_appointments = $total_appointments_result->fetch_assoc()['total'];

// Fetch total lab tests count
$total_lab_tests_sql = "SELECT COUNT(*) as total FROM LabTests";
$total_lab_tests_result = $conn->query($total_lab_tests_sql);
$total_lab_tests = $total_lab_tests_result->fetch_assoc()['total'];

// Fetch total lab reports count
$total_lab_reports_sql = "SELECT COUNT(*) as total FROM LabTestReports";
$total_lab_reports_result = $conn->query($total_lab_reports_sql);
$total_lab_reports = $total_lab_reports_result->fetch_assoc()['total'];

// Fetch today's appointments count
$today_appointments_sql = "SELECT COUNT(*) as total_today FROM Appointments WHERE DATE(AppointmentDate) = CURDATE()";
$today_appointments_result = $conn->query($today_appointments_sql);
$today_appointments = $today_appointments_result->fetch_assoc()['total_today'];

// Fetch today's appointments list with Patient and Doctor names
$today_appointments_list_sql = "
    SELECT appointments.AppointmentID, appointments.AppointmentDate, appointments.Status, 
           patients.Name AS PatientName, doctors.Name AS DoctorName
    FROM Appointments appointments
    LEFT JOIN Patients patients ON appointments.PatientID = patients.PatientID
    LEFT JOIN Doctors doctors ON appointments.DoctorID = doctors.DoctorID
    WHERE DATE(appointments.AppointmentDate) = CURDATE()
";

$today_appointments_list_result = $conn->query($today_appointments_list_sql);
?>

<div class="grid grid-cols-3 gap-6">
    <!-- Total Patients Section -->
    <div class="p-6 bg-white shadow rounded">
        <h2 class="text-lg font-semibold">Total Patients</h2>
        <p class="text-2xl font-bold text-indigo-500"><?php echo $total_patients; ?></p>
    </div>

    <!-- Total Doctors Section -->
    <div class="p-6 bg-white shadow rounded">
        <h2 class="text-lg font-semibold">Total Doctors</h2>
        <p class="text-2xl font-bold text-indigo-500"><?php echo $total_doctors; ?></p>
    </div>

    <!-- Total Appointments Section -->
    <div class="p-6 bg-white shadow rounded">
        <h2 class="text-lg font-semibold">Total Appointments</h2>
        <p class="text-2xl font-bold text-indigo-500"><?php echo $total_appointments; ?></p>
    </div>

    <!-- Total Lab Tests Section -->
    <div class="p-6 bg-white shadow rounded">
        <h2 class="text-lg font-semibold">Total Lab Tests</h2>
        <p class="text-2xl font-bold text-indigo-500"><?php echo $total_lab_tests; ?></p>
    </div>

    <!-- Total Lab Reports Section -->
    <div class="p-6 bg-white shadow rounded">
        <h2 class="text-lg font-semibold">Total Lab Reports</h2>
        <p class="text-2xl font-bold text-indigo-500"><?php echo $total_lab_reports; ?></p>
    </div>
</div>

<!-- Today's Appointments Section -->
<div class="p-6 bg-white shadow rounded mt-6">
    <h2 class="text-lg font-semibold">Today's Appointments</h2>
    <p class="text-2xl font-bold text-indigo-500"><?php echo $today_appointments; ?> Appointment(s)</p>

    <!-- Display Today's Appointments List -->
    <?php if ($today_appointments_list_result->num_rows > 0): ?>
        <table class="min-w-full bg-white border border-gray-300 mt-4">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 text-left border-b border-r">Patient</th>
                    <th class="py-2 px-4 text-left border-b border-r">Doctor</th>
                    <th class="py-2 px-4 text-left border-b border-r">Date</th>
                    <th class="py-2 px-4 text-left border-b">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($appointment = $today_appointments_list_result->fetch_assoc()): ?>
                    <tr>
                        <td class="py-2 px-4 border-b border-r"><?php echo $appointment['PatientName']; ?></td>
                        <td class="py-2 px-4 border-b border-r"><?php echo $appointment['DoctorName']; ?></td>
                        <td class="py-2 px-4 border-b border-r"><?php echo date('F j, Y, g:i a', strtotime($appointment['AppointmentDate'])); ?></td>
                        <td class="py-2 px-4 border-b"><?php echo $appointment['Status']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No appointments scheduled for today.</p>
    <?php endif; ?>



</div>

<?php
$content = ob_get_clean(); // Capture the output and assign it to $content
include 'layout.php'; // Include the layout file
?>