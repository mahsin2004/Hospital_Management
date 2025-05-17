<?php
// Include the database connection file
include 'db.php';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and collect form data
    $patientName = htmlspecialchars($_POST['patient_name']);
    $number = htmlspecialchars($_POST['number']);
    $doctorId = htmlspecialchars($_POST['doctor']); // doctor id from form
    $appointmentDate = htmlspecialchars($_POST['appointment_date']);
    $appointmentTime = htmlspecialchars($_POST['appointment_time']);

    // Assume you have a function to retrieve the patient ID based on the patient's name or number
    // This is a simplified example. You may need to create this logic or take the patient ID from the session or form
    $patientId = getPatientId($patientName, $number); // Custom function to get PatientID

    if (!$patientId) {
        die("Patient not found.");
    }

    // Prepare SQL query to insert appointment into the database
    $sql = "INSERT INTO Appointments (DoctorID, PatientID, AppointmentDate, AppointmentTime, Status)
            VALUES (?, ?, ?, ?, ?)";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind parameters
        $status = 'Scheduled'; // Default status
        $stmt->bind_param("iisss", $doctorId, $patientId, $appointmentDate, $appointmentTime, $status);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Appointment booked successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
}

// Custom function to retrieve Patient ID
function getPatientId($patientName, $number) {
    global $conn;

    // SQL query to retrieve patient ID based on the name and number
    $sql = "SELECT PatientID FROM Patients WHERE Name = ? AND Number = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $patientName, $number);
        $stmt->execute();
        $stmt->bind_result($patientId);
        if ($stmt->fetch()) {
            return $patientId;
        }
        $stmt->close();
    }

    return false;
}
?>
