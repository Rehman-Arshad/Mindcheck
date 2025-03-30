<?php
include("../connection.php");
include("admin_header.php");

// Get total patients
$patients_query = "SELECT COUNT(*) as total FROM patient";
$patients_result = $database->query($patients_query);
$total_patients = $patients_result->fetch_assoc()['total'];

// Get total doctors
$doctors_query = "SELECT COUNT(*) as total FROM doctor";
$doctors_result = $database->query($doctors_query);
$total_doctors = $doctors_result->fetch_assoc()['total'];

// Get today's appointments
$today_appointments_query = "SELECT COUNT(*) as total FROM appointment a 
                           JOIN schedule s ON a.scheduleid = s.scheduleid 
                           WHERE s.scheduledate = CURDATE()";
$today_appointments_result = $database->query($today_appointments_query);
$today_appointments = $today_appointments_result->fetch_assoc()['total'];

// Get pending assessments
$pending_assessments_query = "SELECT COUNT(*) as total FROM assessment WHERE status = 'pending'";
$pending_assessments_result = $database->query($pending_assessments_query);
$pending_assessments = $pending_assessments_result->fetch_assoc()['total'];

// Get recent appointments
$recent_appointments_query = "SELECT 
    a.appoid,
    p.pname as patient_name,
    d.docname as doctor_name,
    s.scheduledate,
    s.scheduletime,
    a.status
FROM appointment a
JOIN schedule s ON a.scheduleid = s.scheduleid
JOIN patient p ON a.pid = p.pid
JOIN doctor d ON s.docid = d.docid
ORDER BY s.scheduledate DESC, s.scheduletime DESC
LIMIT 5";
$recent_appointments_result = $database->query($recent_appointments_query);

// Get upcoming schedules
$upcoming_schedules_query = "SELECT 
    s.scheduleid,
    d.docname,
    s.scheduledate,
    s.scheduletime,
    s.nop,
    COUNT(a.appoid) as booked
FROM schedule s
JOIN doctor d ON s.docid = d.docid
LEFT JOIN appointment a ON s.scheduleid = a.scheduleid
WHERE s.scheduledate >= CURDATE()
GROUP BY s.scheduleid
ORDER BY s.scheduledate ASC, s.scheduletime ASC
LIMIT 5";
$upcoming_schedules_result = $database->query($upcoming_schedules_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MindCheck</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-primary">
                                <i class='bx bxs-user'></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="card-subtitle mb-1">Total Patients</h6>
                                <h3 class="card-title mb-0"><?php echo $total_patients; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-2">
                        <a href="patients.php" class="btn btn-link btn-sm text-primary w-100">View All Patients</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-success">
                                <i class='bx bxs-user-rectangle'></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="card-subtitle mb-1">Total Doctors</h6>
                                <h3 class="card-title mb-0"><?php echo $total_doctors; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-2">
                        <a href="doctors.php" class="btn btn-link btn-sm text-success w-100">View All Doctors</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-warning">
                                <i class='bx bxs-calendar-check'></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="card-subtitle mb-1">Today's Appointments</h6>
                                <h3 class="card-title mb-0"><?php echo $today_appointments; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-2">
                        <a href="appointment.php" class="btn btn-link btn-sm text-warning w-100">View All Appointments</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-info">
                                <i class='bx bx-clipboard'></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="card-subtitle mb-1">Pending Assessments</h6>
                                <h3 class="card-title mb-0"><?php echo $pending_assessments; ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer p-2">
                        <a href="assessment.php" class="btn btn-link btn-sm text-info w-100">View All Assessments</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <a href="patients.php" class="quick-action-card">
                                    <div class="quick-action-icon bg-primary-light text-primary">
                                        <i class='bx bx-user-plus'></i>
                                    </div>
                                    <span>Add New Patient</span>
                                </a>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <a href="doctors.php" class="quick-action-card">
                                    <div class="quick-action-icon bg-success-light text-success">
                                        <i class='bx bx-user-voice'></i>
                                    </div>
                                    <span>Add New Doctor</span>
                                </a>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <a href="schedule.php" class="quick-action-card">
                                    <div class="quick-action-icon bg-warning-light text-warning">
                                        <i class='bx bx-calendar-plus'></i>
                                    </div>
                                    <span>Create Schedule</span>
                                </a>
                            </div>
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <a href="assessment.php" class="quick-action-card">
                                    <div class="quick-action-icon bg-info-light text-info">
                                        <i class='bx bx-list-plus'></i>
                                    </div>
                                    <span>New Assessment</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row g-4">
            <!-- Recent Appointments -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Recent Appointments</h5>
                        <a href="appointment.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Doctor</th>
                                        <th>Date & Time</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($recent_appointments_result && $recent_appointments_result->num_rows > 0): ?>
                                        <?php while($appointment = $recent_appointments_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($appointment['patient_name']); ?></td>
                                                <td><?php echo htmlspecialchars($appointment['doctor_name']); ?></td>
                                                <td>
                                                    <?php 
                                                        echo date('M j, Y', strtotime($appointment['scheduledate'])) . '<br>';
                                                        echo '<small class="text-muted">' . 
                                                             date('g:i A', strtotime($appointment['scheduletime'])) . 
                                                             '</small>';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        $status_class = match($appointment['status']) {
                                                            'completed' => 'bg-success',
                                                            'cancelled' => 'bg-danger',
                                                            default => 'bg-warning'
                                                        };
                                                    ?>
                                                    <span class="badge <?php echo $status_class; ?>">
                                                        <?php echo ucfirst($appointment['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4">No recent appointments</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Schedules -->
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Upcoming Schedules</h5>
                        <a href="schedule.php" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Doctor</th>
                                        <th>Date & Time</th>
                                        <th>Slots</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($upcoming_schedules_result && $upcoming_schedules_result->num_rows > 0): ?>
                                        <?php while($schedule = $upcoming_schedules_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($schedule['docname']); ?></td>
                                                <td>
                                                    <?php 
                                                        echo date('M j, Y', strtotime($schedule['scheduledate'])) . '<br>';
                                                        echo '<small class="text-muted">' . 
                                                             date('g:i A', strtotime($schedule['scheduletime'])) . 
                                                             '</small>';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                        $available = $schedule['nop'] - $schedule['booked'];
                                                        $slot_class = $available > 0 ? 'bg-success' : 'bg-danger';
                                                    ?>
                                                    <span class="badge <?php echo $slot_class; ?>">
                                                        <?php echo $available; ?> / <?php echo $schedule['nop']; ?> slots
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" class="text-center py-4">No upcoming schedules</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Stats Card Styles */
    .stat-card {
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }

    /* Quick Action Styles */
    .quick-action-card {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        border-radius: 0.5rem;
        background: white;
        border: 1px solid rgba(0,0,0,0.05);
        text-decoration: none;
        color: var(--text-color);
        transition: all 0.2s;
    }

    .quick-action-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        color: var(--text-color);
    }

    .quick-action-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    /* Background Colors */
    .bg-primary-light {
        background-color: rgba(25,119,204,0.1);
    }

    .bg-success-light {
        background-color: rgba(40,167,69,0.1);
    }

    .bg-warning-light {
        background-color: rgba(255,193,7,0.1);
    }

    .bg-info-light {
        background-color: rgba(23,162,184,0.1);
    }

    /* Table Styles */
    .table td {
        white-space: nowrap;
        vertical-align: middle;
    }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Close the database connection
$database->close();
?>