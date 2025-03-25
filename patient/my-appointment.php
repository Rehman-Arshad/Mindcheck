<?php

// Database connection code
include("../connection.php");

// Check if form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $date = $_POST['date'];
    $department = $_POST['department'];
    $doctor = $_POST['doctor'];
    $message = $_POST['message'];

    // Prepare SQL statement to insert data into appointment table
    $sql = "INSERT INTO myappointment (name, email, phone, appointment_date, department, doctor, message) VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Prepare and bind parameters
    $stmt = $database->prepare($sql);
    $stmt->bind_param("sssssss", $name, $email, $phone, $date, $department, $doctor, $message);

    // Execute the statement
    if($stmt->execute()) {
        // Appointment successfully added
        echo "Your appointment request has been sent successfully. Thank you!";
    } else {
        // Error occurred while adding appointment
        echo "Error: " . $sql . "<br>" . $database->error;
    }

    // Close statement and connection
    $stmt->close();
    $database->close();
}

?>
