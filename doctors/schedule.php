<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'd') {
    header("location: ../login.php");
    exit();
}

include("../connection.php");
include("includes/functions.php");

// Get doctor info
$useremail = $_SESSION["user"];
$userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["docid"];
$username = $userfetch["docname"];

// Get schedule data
$query = "
    SELECT 
        s.*,
        COUNT(a.appoid) as booked_appointments,
        GROUP_CONCAT(p.pname SEPARATOR ', ') as patient_names
    FROM schedule s
    LEFT JOIN appointment a ON s.scheduleid = a.scheduleid
    LEFT JOIN patient p ON a.pid = p.pid
    WHERE s.docid = ?
    GROUP BY s.scheduleid
    ORDER BY s.scheduledate ASC, s.scheduletime ASC
";

$stmt = $database->prepare($query);
$stmt->bind_param("i", $userid);
$stmt->execute();
$schedules = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule - MindCheck</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1977cc;
            --secondary: #2c4964;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        .main-content {
            margin-top: 10px;
            padding: 2rem;
        }
        .page-header {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        .schedule-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        .schedule-card:hover {
            transform: translateY(-5px);
        }
        .schedule-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }
        .schedule-date {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            font-weight: 600;
        }
        .schedule-time {
            background: var(--light);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.9rem;
            color: var(--secondary);
        }
        .schedule-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin: 1rem 0;
            color: var(--secondary);
        }
        .schedule-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin: 1rem 0;
        }
        .stat-item {
            text-align: center;
            padding: 0.75rem;
            background: var(--light);
            border-radius: 8px;
        }
        .stat-value {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
            margin: 0;
        }
        .stat-label {
            font-size: 0.75rem;
            color: #666;
            margin: 0;
        }
        .patient-list {
            margin: 1rem 0;
            padding: 1rem;
            background: var(--light);
            border-radius: 8px;
        }
        .patient-list h4 {
            font-size: 0.875rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        .patient-list p {
            margin: 0;
            font-size: 0.9rem;
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .btn-action {
            flex: 1;
            padding: 0.5rem;
            border-radius: 8px;
            font-size: 0.875rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
        .btn-danger-soft {
            background: rgba(220,53,69,0.1);
            color: var(--danger);
        }
        .btn-danger-soft:hover {
            background: var(--danger);
            color: white;
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }
        .empty-state p {
            color: #6c757d;
            margin: 0;
        }
        .add-schedule-form {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        @media (max-width: 768px) {
            .schedule-grid {
                grid-template-columns: 1fr;
            }
            .action-buttons {
                flex-direction: column;
            }
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>

        <div class="container">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0">Schedule Management</h1>
                        <p class="text-muted mb-0">Manage your session schedules and appointments</p>
                    </div>
                    <a href="add-schedule.php" class="btn btn-primary">
                        <i class='bx bx-plus'></i> Add New Session
                    </a>
                </div>
            </div>



            <?php if ($schedules && $schedules->num_rows > 0): ?>
                <div class="schedule-grid">
                    <?php while($row = $schedules->fetch_assoc()): ?>
                        <div class="schedule-card">
                            <div class="schedule-header">
                                <div class="schedule-date">
                                    <i class='bx bx-calendar'></i>
                                    <?php echo date('d M Y', strtotime($row['scheduledate'])); ?>
                                </div>
                                <div class="schedule-time">
                                    <i class='bx bx-time'></i>
                                    <?php echo date('h:i A', strtotime($row['scheduletime'])); ?>
                                </div>
                            </div>

                            <h3 class="schedule-title"><?php echo htmlspecialchars($row['title']); ?></h3>

                            <div class="schedule-stats">
                                <div class="stat-item">
                                    <p class="stat-value"><?php echo (int)$row['booked_appointments']; ?></p>
                                    <p class="stat-label">Booked</p>
                                </div>
                                <div class="stat-item">
                                    <p class="stat-value"><?php echo (int)$row['nop'] - (int)$row['booked_appointments']; ?></p>
                                    <p class="stat-label">Available</p>
                                </div>
                            </div>

                            <?php if ($row['patient_names']): ?>
                                <div class="patient-list">
                                    <h4>Booked Patients</h4>
                                    <p><?php echo htmlspecialchars($row['patient_names']); ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="action-buttons">
                                <a href="edit-schedule.php?id=<?php echo $row['scheduleid']; ?>" class="btn-action btn-primary-soft">
                                    <i class='bx bx-edit'></i> Edit
                                </a>
                                <a href="#" onclick="if(confirm('Are you sure you want to delete this session?')) 
                                                   window.location.href='delete-session.php?id=<?php echo $row['scheduleid']; ?>'" 
                                   class="btn-action btn-danger-soft">
                                    <i class='bx bx-trash'></i> Delete
                                </a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class='bx bx-calendar-x'></i>
                    <p>No sessions scheduled yet</p>
                </div>
            <?php endif; ?>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>