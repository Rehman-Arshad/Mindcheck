<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
    header("location: ../login.php");
    exit;
}

include("../connection.php");

// Get patient ID
$email = $_SESSION["user"];
$result = $database->query("SELECT pid FROM patient WHERE pemail='$email'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $pid = $row['pid'];
} else {
    die("Error: Patient not found");
}

// Get all appointments for this patient
$query = "SELECT a.*, s.scheduledate, s.scheduletime, s.title, d.docname, d.specialties
          FROM appointment a
          JOIN schedule s ON a.scheduleid = s.scheduleid
          JOIN doctor d ON s.docid = d.docid
          WHERE a.pid = ?
          ORDER BY s.scheduledate DESC, s.scheduletime DESC";

$stmt = $database->prepare($query);
$stmt->bind_param("i", $pid);
$stmt->execute();
$result = $stmt->get_result();

// Get success message if any
$success = isset($_GET['success']) ? $_GET['success'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - MindCheck</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .schedule-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 20px;
        }
        .appointments-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .appointment-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        .appointment-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .appointment-header {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .appointment-date {
            font-size: 1.2em;
            color: #2c4964;
            font-weight: 600;
            margin: 0;
        }
        .appointment-time {
            color: #666;
            margin: 5px 0 0;
        }
        .appointment-body {
            padding: 15px;
        }
        .doctor-info {
            margin-bottom: 15px;
        }
        .doctor-name {
            font-weight: 500;
            color: #2c4964;
            margin: 0;
        }
        .doctor-specialty {
            color: #666;
            font-size: 0.9em;
            margin: 5px 0;
        }
        .appointment-status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 0.9em;
            font-weight: 500;
        }
        .status-pending {
            background: #fff8e1;
            color: #ffa000;
        }
        .status-confirmed {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .status-completed {
            background: #e3f2fd;
            color: #1565c0;
        }
        .status-cancelled {
            background: #ffebee;
            color: #c62828;
        }
        .no-appointments {
            text-align: center;
            padding: 40px 20px;
            background: #f8f9fa;
            border-radius: 10px;
            color: #666;
        }
        .no-appointments i {
            font-size: 3em;
            color: #1977cc;
            margin-bottom: 15px;
        }
        .success-message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .success-message i {
            font-size: 1.5em;
        }
    </style>
</head>
<body>
    <?php include("../header.php"); ?>

    <div class="schedule-container">
        <h1>My Appointments</h1>

        <?php if ($success == '1'): ?>
        <div class="success-message">
            <i class='bx bx-check-circle'></i>
            <div>
                <strong>Appointment Booked Successfully!</strong>
                <p>Your appointment has been scheduled. The doctor will review and confirm it shortly.</p>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
        <div class="appointments-grid">
            <?php while($appointment = $result->fetch_assoc()): 
                $date = date('F j, Y', strtotime($appointment['scheduledate']));
                $time = date('h:i A', strtotime($appointment['scheduletime']));
                
                $status_class = 'status-' . $appointment['status'];
                $status_text = ucfirst($appointment['status']);
            ?>
            <div class="appointment-card">
                <div class="appointment-header">
                    <h2 class="appointment-date"><?php echo $date; ?></h2>
                    <p class="appointment-time"><?php echo $time; ?></p>
                </div>
                <div class="appointment-body">
                    <div class="doctor-info">
                        <h3 class="doctor-name">Dr. <?php echo $appointment['docname']; ?></h3>
                        <p class="doctor-specialty"><?php echo $appointment['specialties']; ?></p>
                        <p><?php echo $appointment['title']; ?></p>
                    </div>
                    <span class="appointment-status <?php echo $status_class; ?>">
                        <?php echo $status_text; ?>
                    </span>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="no-appointments">
            <i class='bx bx-calendar-x'></i>
            <h2>No Appointments Yet</h2>
            <p>You haven't scheduled any appointments yet.</p>
            <a href="doctors.php" class="btn-primary">Find a Doctor</a>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
