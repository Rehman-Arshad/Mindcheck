<?php
include("connection.php");

// Display detailed error information
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// First, check if there are already available slots in the future
$check_slots = $database->query("SELECT COUNT(*) as count FROM schedule WHERE scheduledate >= CURDATE()");
$row = $check_slots->fetch_assoc();

if ($row['count'] > 0) {
    echo "<div style='background-color: #d1ecf1; color: #0c5460; padding: 15px; margin-bottom: 20px; border-radius: 5px;'>
            There are already {$row['count']} schedule slots in the system for future dates.
          </div>";
    
    // Check if there are any available slots (not booked)
    $check_available = $database->query("SELECT COUNT(*) as count FROM schedule s 
                                        LEFT JOIN appointment a ON s.scheduleid = a.scheduleid 
                                        WHERE s.scheduledate >= CURDATE() AND a.scheduleid IS NULL");
    $available = $check_available->fetch_assoc();
    
    if ($available['count'] == 0) {
        echo "<div style='background-color: #fff3cd; color: #856404; padding: 15px; margin-bottom: 20px; border-radius: 5px;'>
                All existing slots are booked. Adding new available slots...
              </div>";
    } else {
        echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px;'>
                There are {$available['count']} available slots that are not yet booked.
              </div>";
        
        // Let's clear any existing appointments to make all slots available for testing
        if (isset($_GET['reset']) && $_GET['reset'] == 'true') {
            $database->query("DELETE FROM appointment");
            echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px;'>
                    All appointments have been cleared. All slots are now available.
                  </div>";
        } else {
            echo "<p>If you want to clear all existing appointments and make all slots available, <a href='add_available_slots.php?reset=true'>click here</a>.</p>";
        }
        
        echo "<br><a href='patient/doctors.php' class='btn' style='background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Go to Doctors Page</a>";
        exit;
    }
}

// Get all doctors
$doctors = $database->query("SELECT docid FROM doctor");

// Current date
$current_date = date('Y-m-d');

// Generate slots for the next 14 days
$success_count = 0;
$error_count = 0;

while ($doctor = $doctors->fetch_assoc()) {
    $docid = $doctor['docid'];
    
    // Generate slots for each doctor
    for ($day = 1; $day <= 14; $day++) {
        // Skip weekends (6 = Saturday, 0 = Sunday)
        $date = date('Y-m-d', strtotime("+$day days"));
        $dayOfWeek = date('w', strtotime($date));
        
        if ($dayOfWeek == 0 || $dayOfWeek == 6) {
            continue; // Skip weekends
        }
        
        // Morning slot
        $morning_time = "09:00:00";
        $morning_query = "INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop) 
                         VALUES ($docid, 'Morning Session', '$date', '$morning_time', 1)";
        
        if ($database->query($morning_query)) {
            $success_count++;
        } else {
            $error_count++;
        }
        
        // Afternoon slot
        $afternoon_time = "14:00:00";
        $afternoon_query = "INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop) 
                          VALUES ($docid, 'Afternoon Session', '$date', '$afternoon_time', 1)";
        
        if ($database->query($afternoon_query)) {
            $success_count++;
        } else {
            $error_count++;
        }
    }
}

echo "<div style='background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px;'>
        Successfully added $success_count available slots for all doctors.
      </div>";

if ($error_count > 0) {
    echo "<div style='background-color: #fff3cd; color: #856404; padding: 15px; margin-bottom: 20px; border-radius: 5px;'>
            $error_count slots could not be added (possibly due to duplicates).
          </div>";
}

echo "<br><a href='patient/doctors.php' class='btn' style='background-color: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Go to Doctors Page</a>";
?>
