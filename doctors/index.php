<?php
require_once("../connection.php");
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'd') {
    header("location: ../login.php");
    exit();
}

// Initialize variables with default values
$username = '';
$userid = 0;
$total_patients = 0;
$pending_appointments = 0;
$today_appointments = null;
$upcoming_schedule = null;
$error_messages = [];
$recent_patients = null;
$monthly_sessions = 0;

try {
    // Get doctor info
    $useremail = $_SESSION["user"];
    $userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
    if (!$userrow) {
        throw new Exception("Error fetching doctor information");
    }
    $userfetch = $userrow->fetch_assoc();
    $userid = $userfetch["docid"];
    $username = $userfetch["docname"];

    // Get today's appointments with patient details
    $today = date('Y-m-d');
    $today_appointments_query = "
        SELECT 
            a.*,
            p.pname,
            p.pemail,
            (SELECT COUNT(*) FROM appointment a2 JOIN schedule s2 ON a2.scheduleid = s2.scheduleid WHERE a2.pid = p.pid AND s2.docid = ?) as visit_count
        FROM appointment a 
        INNER JOIN patient p ON a.pid = p.pid 
        INNER JOIN schedule s ON a.scheduleid = s.scheduleid
        WHERE s.docid = ? 
        AND s.scheduledate = ? 
        ORDER BY s.scheduletime ASC
    ";
    $stmt = $database->prepare($today_appointments_query);
    if (!$stmt) {
        throw new Exception("Error preparing appointments query");
    }
    $stmt->bind_param("iis", $userid, $userid, $today);
    $stmt->execute();
    $today_appointments = $stmt->get_result();

    // Get recent patients with their last visit
    $recent_patients_query = "
        SELECT 
            p.*,
            MAX(s.scheduledate) as last_visit,
            COUNT(DISTINCT a.appoid) as total_visits
        FROM patient p
        INNER JOIN appointment a ON p.pid = a.pid
        INNER JOIN schedule s ON a.scheduleid = s.scheduleid
        WHERE s.docid = ?
        GROUP BY p.pid
        ORDER BY last_visit DESC
        LIMIT 5
    ";
    $stmt = $database->prepare($recent_patients_query);
    if (!$stmt) {
        throw new Exception("Error preparing recent patients query");
    }
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $recent_patients = $stmt->get_result();

    // Get upcoming schedule for next 7 days
    $next_week = date('Y-m-d', strtotime('+7 days'));
    $upcoming_schedule_query = "
        SELECT 
            s.*,
            COUNT(a.appoid) as booked_count,
            GROUP_CONCAT(p.pname SEPARATOR ', ') as patient_names
        FROM schedule s 
        LEFT JOIN appointment a ON s.scheduleid = a.scheduleid 
        LEFT JOIN patient p ON a.pid = p.pid
        WHERE s.docid = ? 
        AND s.scheduledate BETWEEN ? AND ?
        GROUP BY s.scheduleid 
        ORDER BY s.scheduledate ASC, s.scheduletime ASC
    ";
    $stmt = $database->prepare($upcoming_schedule_query);
    if (!$stmt) {
        throw new Exception("Error preparing schedule query");
    }
    $stmt->bind_param("iss", $userid, $today, $next_week);
    $stmt->execute();
    $upcoming_schedule = $stmt->get_result();

    // Get total active patients
    $total_patients_query = "
        SELECT COUNT(DISTINCT p.pid) as count 
        FROM patient p 
        INNER JOIN appointment a ON p.pid = a.pid 
        INNER JOIN schedule s ON a.scheduleid = s.scheduleid
        WHERE s.docid = ?
    ";
    $stmt = $database->prepare($total_patients_query);
    if (!$stmt) {
        throw new Exception("Error preparing patient count query");
    }
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $total_patients = $result->fetch_assoc()['count'];

    // Get monthly statistics
    $month_start = date('Y-m-01');
    $month_end = date('Y-m-t');
    $monthly_stats_query = "
        SELECT COUNT(*) as count
        FROM appointment a
        JOIN schedule s ON a.scheduleid = s.scheduleid
        WHERE s.docid = ? 
        AND s.scheduledate BETWEEN ? AND ?
        AND (a.status = 'confirmed' OR a.status = 1)
    ";
    $stmt = $database->prepare($monthly_stats_query);
    if (!$stmt) {
        throw new Exception("Error preparing monthly stats query");
    }
    $stmt->bind_param("iss", $userid, $month_start, $month_end);
    $stmt->execute();
    $result = $stmt->get_result();
    $monthly_sessions = $result->fetch_assoc()['count'];

    // Get pending appointments
    $pending_query = "
        SELECT COUNT(*) as count 
        FROM appointment a
        JOIN schedule s ON a.scheduleid = s.scheduleid
        WHERE s.docid = ? AND (a.status = 'pending' OR a.status = 0)
    ";
    $stmt = $database->prepare($pending_query);
    if (!$stmt) {
        throw new Exception("Error preparing pending appointments query");
    }
    $stmt->bind_param("i", $userid);
    $stmt->execute();
    $result = $stmt->get_result();
    $pending_appointments = $result->fetch_assoc()['count'];

} catch (Exception $e) {
    $error_messages[] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - MindCheck</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1977cc;
            --secondary: #2c4964;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        body { 
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .dashboard-header {
            background: var(--primary);
            padding: 1rem;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .main-content {
            margin-top: 30px;
            padding: 2rem;
        }
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary), #3291e6);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        .welcome-banner::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 100%;
            background: url('../img/medical-pattern.png') no-repeat center right;
            opacity: 0.1;
        }
        .welcome-banner h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .welcome-banner p {
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-card .icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 1rem;
        }
        .stat-card .icon.blue {
            background: rgba(25,119,204,0.1);
            color: var(--primary);
        }
        .stat-card .icon.green {
            background: rgba(40,167,69,0.1);
            color: var(--success);
        }
        .stat-card .icon.yellow {
            background: rgba(255,193,7,0.1);
            color: var(--warning);
        }
        .stat-card h3 {
            font-size: 0.875rem;
            color: var(--secondary);
            margin: 0;
        }
        .stat-card .value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin: 0.5rem 0;
        }
        .stat-card .trend {
            font-size: 0.875rem;
            color: var(--success);
        }
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
        }
        .card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: none;
            margin-bottom: 1.5rem;
        }
        .card-header {
            background: none;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.5rem;
        }
        .card-header h2 {
            font-size: 1.25rem;
            color: var(--secondary);
            margin: 0;
        }
        .card-body {
            padding: 1.5rem;
        }
        .appointment-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            background: var(--light);
            transition: all 0.3s ease;
        }
        .appointment-item:hover {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .appointment-item .time {
            background: var(--primary);
            color: white;
            padding: 0.5rem;
            border-radius: 8px;
            min-width: 90px;
            text-align: center;
            margin-right: 1rem;
        }
        .appointment-item .info {
            flex: 1;
        }
        .appointment-item .info h3 {
            font-size: 1rem;
            margin: 0 0 0.25rem 0;
            color: var(--secondary);
        }
        .appointment-item .info p {
            font-size: 0.875rem;
            color: #666;
            margin: 0;
        }
        .appointment-item .status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-confirmed {
            background: rgba(40,167,69,0.1);
            color: var(--success);
        }
        .status-pending {
            background: rgba(255,193,7,0.1);
            color: var(--warning);
        }
        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        .btn-primary-soft {
            background: rgba(25,119,204,0.1);
            color: var(--primary);
        }
        .btn-primary-soft:hover {
            background: var(--primary);
            color: white;
        }
        .patient-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .patient-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        .patient-item:last-child {
            border-bottom: none;
        }
        .patient-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--light);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: var(--primary);
        }
        .patient-info {
            flex: 1;
        }
        .patient-info h4 {
            font-size: 0.875rem;
            margin: 0 0 0.25rem 0;
            color: var(--secondary);
        }
        .patient-info p {
            font-size: 0.75rem;
            color: #666;
            margin: 0;
        }
        .visit-count {
            background: rgba(25,119,204,0.1);
            color: var(--primary);
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.75rem;
        }
        @media (max-width: 992px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>

    <div class="container-fluid">
            <?php if (!empty($error_messages)): ?>
                <?php foreach ($error_messages as $message): ?>
                    <div class="alert alert-danger">
                        <i class='bx bx-error-circle'></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="welcome-banner">
                <h1>Welcome back, Dr. <?php echo htmlspecialchars($username); ?>!</h1>
                <p>Here's your practice overview for today, <?php echo date('F d, Y'); ?></p>
                <div class="d-flex gap-3">
                    <a href="appointment.php" class="btn btn-light">
                        <i class='bx bx-calendar-plus'></i> New Appointment
                    </a>
                    <a href="schedule.php" class="btn btn-outline-light">
                        <i class='bx bx-time'></i> Manage Schedule
                    </a>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon blue">
                        <i class='bx bx-group'></i>
                    </div>
                    <h3>Total Patients</h3>
                    <div class="value"><?php echo $total_patients; ?></div>
                    <div class="trend">
                        <i class='bx bx-user-plus'></i> Active Patients
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon green">
                        <i class='bx bx-calendar-check'></i>
                    </div>
                    <h3>Today's Appointments</h3>
                    <div class="value"><?php echo $today_appointments ? $today_appointments->num_rows : 0; ?></div>
                    <div class="trend">
                        <i class='bx bx-calendar'></i> <?php echo date('d M Y'); ?>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon yellow">
                        <i class='bx bx-time-five'></i>
                    </div>
                    <h3>Monthly Sessions</h3>
                    <div class="value"><?php echo $monthly_sessions; ?></div>
                    <div class="trend">
                        <i class='bx bx-calendar'></i> This Month
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon blue">
                        <i class='bx bx-note'></i>
                    </div>
                    <h3>Pending Appointments</h3>
                    <div class="value"><?php echo $pending_appointments; ?></div>
                    <div class="trend">
                        <i class='bx bx-time'></i> Awaiting Confirmation
                    </div>
                </div>
            </div>

            <div class="content-grid">
                <div class="appointments-section">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h2>Today's Schedule</h2>
                            <a href="appointments.php" class="btn-action btn-primary-soft">
                                <i class='bx bx-calendar'></i> View All
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if ($today_appointments && $today_appointments->num_rows > 0): ?>
                                <?php while($row = $today_appointments->fetch_assoc()): ?>
                                    <div class="appointment-item">
                                        <div class="time">
                                            <?php echo htmlspecialchars(date('h:i A', strtotime($row['appotime']))); ?>
                                        </div>
                                        <div class="info">
                                            <h3><?php echo htmlspecialchars($row['pname']); ?></h3>
                                            <p>
                                                <i class='bx bx-envelope'></i> <?php echo htmlspecialchars($row['pemail']); ?>
                                                <?php if(isset($row['visit_count'])): ?>
                                                    <span class="ms-2">
                                                        <i class='bx bx-history'></i> Visit #<?php echo (int)$row['visit_count']; ?>
                                                    </span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <span class="status <?php echo $row['status'] == 1 ? 'status-confirmed' : 'status-pending'; ?>">
                                            <?php echo $row['status'] == 1 ? 'Confirmed' : 'Pending'; ?>
                                        </span>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class='bx bx-calendar-x' style="font-size: 3rem; color: #dee2e6;"></i>
                                    <p class="mt-2 text-muted">No appointments scheduled for today</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h2>Upcoming Schedule</h2>
                            <a href="schedule.php" class="btn-action btn-primary-soft">
                                <i class='bx bx-time'></i> Manage
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if ($upcoming_schedule && $upcoming_schedule->num_rows > 0): ?>
                                <?php while($row = $upcoming_schedule->fetch_assoc()): ?>
                                    <div class="appointment-item">
                                        <div class="time">
                                            <?php echo htmlspecialchars(date('d M', strtotime($row['scheduledate']))); ?>
                                            <div class="small"><?php echo htmlspecialchars(date('h:i A', strtotime($row['scheduletime']))); ?></div>
                                        </div>
                                        <div class="info">
                                            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                                            <p>
                                                <i class='bx bx-user'></i> 
                                                <?php echo (int)$row['booked_count']; ?>/<?php echo (int)$row['nop']; ?> Slots
                                                <?php if($row['patient_names']): ?>
                                                    <br><small class="text-muted"><?php echo htmlspecialchars($row['patient_names']); ?></small>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <a href="view_schedule.php?id=<?php echo (int)$row['scheduleid']; ?>" class="btn-action btn-primary-soft">
                                            <i class='bx bx-show'></i>
                                        </a>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class='bx bx-time' style="font-size: 3rem; color: #dee2e6;"></i>
                                    <p class="mt-2 text-muted">No upcoming sessions scheduled</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="side-section">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h2>Recent Patients</h2>
                            <a href="patient.php" class="btn-action btn-primary-soft">
                                <i class='bx bx-group'></i> All Patients
                            </a>
                        </div>
                        <div class="card-body">
                            <?php if ($recent_patients && $recent_patients->num_rows > 0): ?>
                                <ul class="patient-list">
                                    <?php while($row = $recent_patients->fetch_assoc()): ?>
                                        <li class="patient-item">
                                            <div class="patient-avatar">
                                                <i class='bx bx-user'></i>
                                            </div>
                                            <div class="patient-info">
                                                <h4><?php echo htmlspecialchars($row['pname']); ?></h4>
                                                <p>Last visit: <?php echo date('d M Y', strtotime($row['last_visit'])); ?></p>
                                            </div>
                                            <span class="visit-count">
                                                <?php echo (int)$row['total_visits']; ?> visits
                                            </span>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class='bx bx-user-x' style="font-size: 3rem; color: #dee2e6;"></i>
                                    <p class="mt-2 text-muted">No recent patients</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>