<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
    header("location: ../login.php");
    exit;
}

include("../connection.php");

// Get patient ID from session
$useremail = $_SESSION["user"];
$userrow = $database->query("SELECT * FROM patient WHERE pemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$pid = $userfetch["pid"];

// Get doctor ID from URL if provided
$doctor_id = isset($_GET['doctor']) ? $_GET['doctor'] : null;

// Get all available schedules
$schedule_query = "SELECT s.*, d.docname, d.specialties 
                  FROM schedule s 
                  JOIN doctor d ON s.docid = d.docid 
                  WHERE s.scheduledate >= CURDATE()";

if ($doctor_id) {
    $schedule_query .= " AND s.docid = $doctor_id";
}

$schedule_query .= " ORDER BY s.scheduledate ASC, s.scheduletime ASC";
$result = $database->query($schedule_query);

// Handle appointment booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['schedule_id'])) {
    $schedule_id = $_POST['schedule_id'];
    
    // Check if slot is still available
    $check_query = "SELECT * FROM appointment WHERE scheduleid = $schedule_id";
    $check_result = $database->query($check_query);
    
    if ($check_result->num_rows < 1) {
        // Create new appointment
        $apponum = 1;
        $date = date('Y-m-d');
        
        $insert_query = "INSERT INTO appointment (pid, apponum, scheduleid, appodate) 
                        VALUES ($pid, $apponum, $schedule_id, '$date')";
        
        if ($database->query($insert_query)) {
            $success = "Appointment booked successfully!";
            // Refresh the schedules list
            $result = $database->query($schedule_query);
        } else {
            $error = "Error booking appointment. Please try again.";
        }
    } else {
        $error = "Sorry, this slot is no longer available.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Appointment - MindCheck</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .schedule-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .page-title {
            background: linear-gradient(45deg, #1977cc, #3291e6);
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 40px;
            color: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .page-title h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .schedule-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        .schedule-card:hover {
            transform: translateY(-5px);
        }
        .schedule-header {
            background: #1977cc;
            color: white;
            padding: 15px;
            font-size: 1.2em;
        }
        .schedule-body {
            padding: 20px;
        }
        .schedule-info {
            margin-bottom: 20px;
        }
        .schedule-info p {
            margin: 10px 0;
            color: #444;
        }
        .schedule-info i {
            color: #1977cc;
            margin-right: 10px;
        }
        .book-btn {
            background: #1977cc;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 1em;
            transition: all 0.3s ease;
        }
        .book-btn:hover {
            background: #166ab5;
        }
        .book-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
        }
        .no-schedules {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .no-schedules i {
            font-size: 3em;
            color: #1977cc;
            margin-bottom: 20px;
        }
        @media (max-width: 768px) {
            .schedule-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include("../header.php"); ?>

    <div class="schedule-container">
        <div class="page-title">
            <h1>Schedule an Appointment</h1>
            <p>Choose from available time slots to book your appointment</p>
        </div>

        <?php if(isset($success)): ?>
            <div class="alert alert-success">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-error">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if($result && $result->num_rows > 0): ?>
            <div class="schedule-grid">
                <?php while($schedule = $result->fetch_assoc()): ?>
                    <div class="schedule-card">
                        <div class="schedule-header">
                            Dr. <?php echo htmlspecialchars($schedule['docname']); ?>
                        </div>
                        <div class="schedule-body">
                            <div class="schedule-info">
                                <p><i class='bx bx-calendar'></i> Date: <?php echo date('F j, Y', strtotime($schedule['scheduledate'])); ?></p>
                                <p><i class='bx bx-time'></i> Time: <?php echo date('g:i A', strtotime($schedule['scheduletime'])); ?></p>
                                <p><i class='bx bx-briefcase-alt-2'></i> Specialization: <?php echo htmlspecialchars($schedule['specialties']); ?></p>
                            </div>
                            <form method="POST" action="">
                                <input type="hidden" name="schedule_id" value="<?php echo $schedule['scheduleid']; ?>">
                                <button type="submit" class="book-btn" <?php echo isset($schedule['booked']) ? 'disabled' : ''; ?>>
                                    <?php echo isset($schedule['booked']) ? 'Booked' : 'Book Appointment'; ?>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-schedules">
                <i class='bx bx-calendar-x'></i>
                <h2>No available schedules</h2>
                <p>There are currently no available appointment slots. Please check back later.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
