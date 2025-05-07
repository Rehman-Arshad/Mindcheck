<?php
require_once("connection.php");

echo "<h2>Checking Appointment Data</h2>";

// Check if there are any appointments in the system
$query = "SELECT COUNT(*) as count FROM appointment";
$result = $database->query($query);
$row = $result->fetch_assoc();
echo "<p>Total appointments in system: {$row['count']}</p>";

// Check schedule data
$query = "SELECT COUNT(*) as count FROM schedule";
$result = $database->query($query);
$row = $result->fetch_assoc();
echo "<p>Total schedule slots in system: {$row['count']}</p>";

// Check appointment data with schedule and doctor info
echo "<h3>Appointment Details:</h3>";
$query = "SELECT 
    a.appoid, a.pid, a.scheduleid, a.status,
    s.docid, s.scheduledate, s.scheduletime,
    d.docname, p.pname
    FROM appointment a 
    JOIN schedule s ON a.scheduleid = s.scheduleid
    JOIN doctor d ON s.docid = d.docid
    JOIN patient p ON a.pid = p.pid
    ORDER BY s.scheduledate DESC, s.scheduletime ASC";
$result = $database->query($query);

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Appointment ID</th><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['appoid']}</td>";
        echo "<td>{$row['pname']} (ID: {$row['pid']})</td>";
        echo "<td>{$row['docname']} (ID: {$row['docid']})</td>";
        echo "<td>{$row['scheduledate']}</td>";
        echo "<td>{$row['scheduletime']}</td>";
        echo "<td>{$row['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No appointments found with doctor and patient details.</p>";
}

// Check today's appointments for a specific doctor (e.g., docid = 1)
echo "<h3>Today's Appointments for Doctor ID 1:</h3>";
$today = date('Y-m-d');
$docid = 1;
$query = "SELECT 
    a.*, p.pname, s.scheduledate, s.scheduletime
    FROM appointment a 
    JOIN schedule s ON a.scheduleid = s.scheduleid
    JOIN patient p ON a.pid = p.pid
    WHERE s.docid = $docid AND s.scheduledate = '$today'";
$result = $database->query($query);

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Appointment ID</th><th>Patient</th><th>Time</th><th>Status</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['appoid']}</td>";
        echo "<td>{$row['pname']}</td>";
        echo "<td>{$row['scheduletime']}</td>";
        echo "<td>{$row['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No appointments found for today for Doctor ID 1.</p>";
}

// Check if the appointment status values are correct
echo "<h3>Appointment Status Values:</h3>";
$query = "SELECT DISTINCT status FROM appointment";
$result = $database->query($query);

if ($result->num_rows > 0) {
    echo "<ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>{$row['status']}</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No status values found.</p>";
}

// Check if the appointment table has a numeric status column instead of string
echo "<h3>Checking Appointment Status Column Type:</h3>";
$query = "SHOW COLUMNS FROM appointment LIKE 'status'";
$result = $database->query($query);
$row = $result->fetch_assoc();
echo "<p>Status column type: {$row['Type']}</p>";

// Check if there are any appointments with status = 1 (which might be 'confirmed' in string format)
$query = "SELECT COUNT(*) as count FROM appointment WHERE status = 1";
$result = $database->query($query);
$row = $result->fetch_assoc();
echo "<p>Appointments with status = 1: {$row['count']}</p>";

$query = "SELECT COUNT(*) as count FROM appointment WHERE status = 'confirmed'";
$result = $database->query($query);
$row = $result->fetch_assoc();
echo "<p>Appointments with status = 'confirmed': {$row['count']}</p>";
?>
