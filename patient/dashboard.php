<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
    header("location: ../login.php");
    exit;
}

include("../connection.php");

$userid = $_SESSION["user"];
$usertype = $_SESSION["usertype"];

// Get patient info
$userrow = $database->query("SELECT * FROM patient WHERE pemail='$userid'");
$userfetch = $userrow->fetch_assoc();
$pid = $userfetch["pid"];

// Get upcoming appointments
$upcoming_query = "SELECT a.*, d.docname, d.specialties, s.scheduledate, s.scheduletime 
                  FROM appointment a 
                  JOIN schedule s ON a.scheduleid = s.scheduleid 
                  JOIN doctor d ON s.docid = d.docid 
                  WHERE a.pid = ? AND s.scheduledate >= CURDATE() 
                  ORDER BY s.scheduledate ASC, s.scheduletime ASC LIMIT 5";
$stmt = $database->prepare($upcoming_query);
$stmt->bind_param("i", $pid);
$stmt->execute();
$upcoming = $stmt->get_result();

// Get total doctors
$total_doctors = $database->query("SELECT COUNT(*) as count FROM doctor")->fetch_assoc()['count'];

// Get recent assessments
$recent_assessments = $database->query("SELECT * FROM assessments WHERE patient_id = $pid ORDER BY created_at DESC LIMIT 3");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - MindCheck</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .welcome-section {
            background: linear-gradient(45deg, #1977cc, #3291e6);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }
        .stat-card i {
            font-size: 2em;
            color: #1977cc;
            margin-bottom: 10px;
        }
        .stat-card h3 {
            color: #2c4964;
            margin-bottom: 5px;
        }
        .stat-card p {
            color: #666;
            margin: 0;
        }
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .action-btn {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            color: #2c4964;
            transition: transform 0.3s ease;
            border: none;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        .action-btn:hover {
            transform: translateY(-5px);
            background: white;
        }
        .action-btn i {
            font-size: 2em;
            color: #1977cc;
        }
        .section-title {
            color: #2c4964;
            margin: 30px 0 20px;
        }
        .appointments-list {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .appointment-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .appointment-item:last-child {
            border-bottom: none;
        }
        .appointment-info h4 {
            color: #2c4964;
            margin: 0 0 5px;
        }
        .appointment-info p {
            color: #666;
            margin: 0;
        }
        .appointment-status {
            padding: 5px 15px;
            border-radius: 20px;
            background: #e3f2fd;
            color: #1977cc;
        }
        .assessment-btn {
            background: white
            color: #1977cc;
        }
        .assessment-btn:hover {
            background: white;
            color: #1977cc;
        }
        .recent-assessments {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .assessment-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .assessment-item:last-child {
            border-bottom: none;
        }
        .assessment-item h4 {
            color: #2c4964;
            margin: 0 0 5px;
        }
        .assessment-item p {
            color: #666;
            margin: 0;
        }
        .assessment-score {
            font-weight: bold;
            color: #1977cc;
        }
    </style>
</head>
<body>
    <?php include("../header.php"); ?>

    <div class="dashboard-container">
        <div class="welcome-section">
            <h1>Welcome, <?php echo $userfetch["pname"] ?>!</h1>
            <p>Here's an overview of your mental health journey.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <i class='bx bx-calendar-check'></i>
                <h3><?php echo $upcoming->num_rows ?></h3>
                <p>Upcoming Appointments</p>
            </div>
            <div class="stat-card">
                <i class='bx bx-user-plus'></i>
                <h3><?php echo $total_doctors ?></h3>
                <p>Available Specialists</p>
            </div>
            <div class="stat-card">
                <i class='bx bx-clipboard'></i>
                <h3><?php echo $recent_assessments ? $recent_assessments->num_rows : '0' ?></h3>
                <p>Recent Assessments</p>
            </div>
        </div>

        <div class="quick-actions">
            <a href="schedule.php" class="action-btn">
                <i class='bx bx-calendar-plus'></i>
                <span>Schedule Appointment</span>
            </a>
            <a href="doctors.php" class="action-btn">
                <i class='bx bx-user'></i>
                <span>Find Specialists</span>
            </a>
            <a href="assessment.php" class="action-btn assessment-btn">
                <i class='bx bx-clipboard'></i>
                <span>Take Assessment</span>
            </a>
        </div>

        <h2 class="section-title">Upcoming Appointments</h2>
        <div class="appointments-list">
            <?php if($upcoming->num_rows > 0): ?>
                <?php while($row = $upcoming->fetch_assoc()): ?>
                    <div class="appointment-item">
                        <div class="appointment-info">
                            <h4>Dr. <?php echo $row["docname"] ?></h4>
                            <p><?php echo $row["specialties"] ?></p>
                            <p><?php echo date('F j, Y', strtotime($row["scheduledate"])) ?> at <?php echo date('g:i A', strtotime($row["scheduletime"])) ?></p>
                        </div>
                        <span class="appointment-status">Upcoming</span>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No upcoming appointments</p>
            <?php endif; ?>
        </div>

        <h2 class="section-title">Recent Assessments</h2>
        <div class="recent-assessments">
            <?php if($recent_assessments && $recent_assessments->num_rows > 0): ?>
                <?php while($assessment = $recent_assessments->fetch_assoc()): ?>
                    <div class="assessment-item">
                        <h4><?php echo htmlspecialchars($assessment["child_name"]) ?></h4>
                        <p>Date: <?php echo date('F j, Y', strtotime($assessment["created_at"])) ?></p>
                        <p>Score: <span class="assessment-score"><?php echo $assessment["total_score"] ?></span></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No assessments taken yet. <a href="assessment.php">Take your first assessment</a></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>