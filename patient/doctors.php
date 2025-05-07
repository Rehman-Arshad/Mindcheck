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

// Get specialty filter if any
$specialty_filter = isset($_GET['specialty']) ? $_GET['specialty'] : '';

// Get all doctors with their available slots
$query = "SELECT d.*, s.name as specialty_name,
          (SELECT COUNT(*) FROM schedule s3 
           LEFT JOIN appointment a2 ON s3.scheduleid = a2.scheduleid
           WHERE s3.docid = d.docid 
           AND s3.scheduledate >= CURDATE() 
           AND s3.scheduledate <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
           AND a2.scheduleid IS NULL) as available_slots,
          (SELECT MIN(s4.scheduledate) FROM schedule s4 
           LEFT JOIN appointment a3 ON s4.scheduleid = a3.scheduleid
           WHERE s4.docid = d.docid 
           AND s4.scheduledate >= CURDATE() 
           AND a3.scheduleid IS NULL) as next_available
          FROM doctor d 
          LEFT JOIN specialties s ON d.specialties = s.id";
          
// Add WHERE clause for specialty filter before GROUP BY
if ($specialty_filter) {
    $query .= " WHERE d.specialties IN (SELECT id FROM specialties WHERE name LIKE ?)";
}

// Add GROUP BY and ORDER BY after any WHERE clauses
$query .= " GROUP BY d.docid ORDER BY d.docname";

$stmt = $database->prepare($query);
if ($specialty_filter) {
    $specialty_param = "%$specialty_filter%";
    $stmt->bind_param("s", $specialty_param);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Doctors - MindCheck</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/main.css">
    <style>
        .doctors-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        .doctor-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }
        .doctor-header {
            padding: 20px;
            background: linear-gradient(45deg, #1977cc, #3291e6);
            color: white;
        }
        .doctor-name {
            font-size: 1.4em;
            margin: 0;
            font-weight: 600;
        }
        .doctor-specialty {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 0.95em;
        }
        .doctor-body {
            padding: 20px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
            color: #555;
        }
        .info-item i {
            font-size: 1.2em;
            color: #1977cc;
            margin-right: 10px;
            min-width: 24px;
        }
        .info-item span {
            font-size: 0.95em;
        }
        .availability-section {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .available {
            background: #e8f5e9;
            color: #2e7d32;
        }
        .unavailable {
            background: #ffebee;
            color: #c62828;
        }
        .next-available {
            font-size: 0.9em;
            margin-top: 5px;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 0.95em;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn i {
            font-size: 1.2em;
        }
        .btn-primary {
            background: #1977cc;
            color: white;
        }
        .btn-primary:hover {
            background: #1565c0;
        }
        .btn-secondary {
            background: #f8f9fa;
            color: #1977cc;
            border: 1px solid #1977cc;
        }
        .btn-secondary:hover {
            background: #e9ecef;
        }
        .filters {
            margin-bottom: 30px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <?php include("../header.php"); ?>

    <div class="doctors-container">
        <h1>Our Mental Health Specialists</h1>
        <p>Book an appointment with our experienced mental health professionals</p>

        <?php if ($specialty_filter): ?>
        <div class="filters">
            <a href="doctors.php" class="filter-btn">All Specialists</a>
            <span class="filter-btn active"><?php echo htmlspecialchars($specialty_filter); ?></span>
        </div>
        <?php endif; ?>

        <?php if ($result && $result->num_rows > 0): ?>
        <div class="doctors-grid">
            <?php while($doctor = $result->fetch_assoc()): ?>
            <div class="doctor-card">
                <div class="doctor-header">
                    <h2 class="doctor-name">Dr. <?php echo $doctor['docname']; ?></h2>
                    <p class="doctor-specialty"><?php echo $doctor['specialty_name']; ?></p>
                </div>
                <div class="doctor-body">
                    <div class="info-section">
                        <div class="info-item">
                            <i class='bx bx-medal'></i>
                            <span><?php echo $doctor['docexp']; ?> years of experience</span>
                        </div>
                        <div class="info-item">
                            <i class='bx bx-dollar-circle'></i>
                            <span><strong>$<?php echo number_format(isset($doctor['consultation_fee']) ? $doctor['consultation_fee'] : 50.00, 2); ?></strong> per session</span>
                        </div>
                        <div class="info-item">
                            <i class='bx bx-phone'></i>
                            <span><?php echo $doctor['doctel']; ?></span>
                        </div>
                        <div class="info-item">
                            <i class='bx bx-envelope'></i>
                            <span><?php echo $doctor['docemail']; ?></span>
                        </div>
                    </div>

                    <?php if ($doctor['available_slots'] > 0): ?>
                    <div class="availability-section available">
                        <i class='bx bx-calendar-check'></i>
                        <strong><?php echo $doctor['available_slots']; ?> available slots</strong>
                        <div class="next-available">Next available: <?php echo date('F j', strtotime($doctor['next_available'])); ?></div>
                    </div>
                    <?php else: ?>
                    <div class="availability-section unavailable">
                        <i class='bx bx-calendar-x'></i>
                        <strong>No available slots</strong>
                        <?php if ($doctor['next_available']): ?>
                        <div class="next-available">Next available after: <?php echo date('F j', strtotime($doctor['next_available'])); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <div class="action-buttons">
                        <?php if ($doctor['available_slots'] > 0): ?>
                        <a href="book-appointment.php?docid=<?php echo $doctor['docid']; ?>" class="btn btn-primary">
                            <i class='bx bx-calendar-plus'></i>
                            Book Now
                        </a>
                        <?php else: ?>
                        <a href="contact.php" class="btn btn-primary">
                            <i class='bx bx-bell'></i>
                            Notify Me
                        </a>
                        <?php endif; ?>
                        <a href="doctor-profile.php?id=<?php echo $doctor['docid']; ?>" class="btn btn-secondary">
                            <i class='bx bx-user'></i>
                            Profile
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="no-results">
            <i class='bx bx-search-alt'></i>
            <h2>No doctors found</h2>
            <?php if ($specialty_filter): ?>
            <p>No doctors available for <?php echo htmlspecialchars($specialty_filter); ?> at the moment.</p>
            <a href="doctors.php" class="btn-book">View All Doctors</a>
            <?php else: ?>
            <p>Please try again later or contact us for assistance.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>