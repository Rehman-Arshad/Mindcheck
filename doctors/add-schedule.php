<?php
require_once("../connection.php");
session_start();

if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'd') {
    header("location: ../login.php");
    exit();
}

// Get doctor info
$useremail = $_SESSION["user"];
$userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$userid = $userfetch["docid"];
$username = $userfetch["docname"];

$success_message = "";
$error_message = "";

// Process form submission
if (isset($_POST['add_schedule'])) {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $nop = $_POST['nop'];
    
    // Validate inputs
    if (empty($title) || empty($date) || empty($time) || empty($nop)) {
        $error_message = "All fields are required";
    } else {
        // Check if the slot already exists
        $check_query = "SELECT COUNT(*) as count FROM schedule WHERE docid = ? AND scheduledate = ? AND scheduletime = ?";
        $stmt = $database->prepare($check_query);
        $stmt->bind_param("iss", $userid, $date, $time);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row['count'] > 0) {
            $error_message = "You already have a schedule at this date and time";
        } else {
            // Insert new schedule
            $insert_query = "INSERT INTO schedule (docid, title, scheduledate, scheduletime, nop) VALUES (?, ?, ?, ?, ?)";
            $stmt = $database->prepare($insert_query);
            $stmt->bind_param("isssi", $userid, $title, $date, $time, $nop);
            
            if ($stmt->execute()) {
                $success_message = "Schedule added successfully";
                // Clear form data after successful submission
                $title = $date = $time = $nop = "";
            } else {
                $error_message = "Error adding schedule: " . $database->error;
            }
        }
    }
}

// Get current schedule for the next 30 days
$today = date('Y-m-d');
$month_later = date('Y-m-d', strtotime('+30 days'));
$schedule_query = "SELECT * FROM schedule WHERE docid = ? AND scheduledate BETWEEN ? AND ? ORDER BY scheduledate ASC, scheduletime ASC";
$stmt = $database->prepare($schedule_query);
$stmt->bind_param("iss", $userid, $today, $month_later);
$stmt->execute();
$schedule_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Schedule - MindCheck</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1a73e8;
            --secondary: #2c4964;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --light: #f8f9fa;
            --dark: #343a40;
        }
        
        /* Override the main-content class from header.php */
        .container.main-content {
            margin-top: 0 !important; /* Override the margin from header.php */
            padding: 2rem;
        }
        
        .card {
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.25rem 1.5rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }
        
        .form-grid .col-full {
            grid-column: 1 / -1;
        }
        
        .schedule-list {
            margin-top: 2rem;
        }
        
        .schedule-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .schedule-date {
            width: 120px;
            font-weight: 500;
        }
        
        .schedule-time {
            width: 100px;
            color: var(--primary);
        }
        
        .schedule-title {
            flex-grow: 1;
        }
        
        .schedule-slots {
            width: 80px;
            text-align: center;
            background: rgba(25,119,204,0.1);
            color: var(--primary);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }
        
        .schedule-actions {
            width: 100px;
            text-align: right;
        }
        
        .btn-action {
            border: none;
            background: none;
            color: var(--primary);
            cursor: pointer;
            padding: 0.25rem;
            transition: all 0.3s ease;
        }
        
        .btn-action:hover {
            color: var(--danger);
        }
        
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .schedule-item {
                flex-wrap: wrap;
            }
            
            .schedule-date, .schedule-time {
                width: 50%;
                margin-bottom: 0.5rem;
            }
            
            .schedule-title {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>
    
    <div class="container main-content" style="margin-top: 80px;">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h2 class="h4 mb-0">Add New Schedule</h2>
                        <a href="schedule.php" class="btn btn-outline-primary btn-sm">
                            <i class='bx bx-calendar'></i> View All Schedules
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success">
                                <i class='bx bx-check-circle'></i>
                                <?php echo $success_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger">
                                <i class='bx bx-error-circle'></i>
                                <?php echo $error_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="form-grid">
                                <div class="mb-3">
                                    <label class="form-label">Session Title</label>
                                    <select name="title" class="form-select" required>
                                        <option value="" disabled selected>Select session type</option>
                                        <option value="Morning Session">Morning Session</option>
                                        <option value="Afternoon Session">Afternoon Session</option>
                                        <option value="Evening Session">Evening Session</option>
                                        <option value="Emergency Consultation">Emergency Consultation</option>
                                        <option value="Follow-up Session">Follow-up Session</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Date</label>
                                    <input type="date" name="date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Time</label>
                                    <input type="time" name="time" class="form-control" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Number of Patients</label>
                                    <input type="number" name="nop" class="form-control" min="1" max="10" value="1" required>
                                    <small class="text-muted">Maximum number of patients that can book this slot</small>
                                </div>
                                
                                <div class="col-full">
                                    <button type="submit" name="add_schedule" class="btn btn-primary">
                                        <i class='bx bx-plus'></i> Add Schedule
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h2 class="h4 mb-0">Your Upcoming Schedule</h2>
                    </div>
                    <div class="card-body">
                        <?php if ($schedule_result->num_rows > 0): ?>
                            <div class="schedule-list">
                                <?php while ($row = $schedule_result->fetch_assoc()): ?>
                                    <div class="schedule-item">
                                        <div class="schedule-date">
                                            <?php echo date('M d, Y', strtotime($row['scheduledate'])); ?>
                                        </div>
                                        <div class="schedule-time">
                                            <?php echo date('h:i A', strtotime($row['scheduletime'])); ?>
                                        </div>
                                        <div class="schedule-title">
                                            <?php echo htmlspecialchars($row['title']); ?>
                                        </div>
                                        <div class="schedule-slots">
                                            <?php echo $row['nop']; ?> slots
                                        </div>
                                        <div class="schedule-actions">
                                            <a href="delete-session.php?id=<?php echo $row['scheduleid']; ?>" class="btn-action" onclick="return confirm('Are you sure you want to delete this schedule?')">
                                                <i class='bx bx-trash'></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class='bx bx-info-circle'></i>
                                You don't have any upcoming schedules. Add your first schedule above.
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
