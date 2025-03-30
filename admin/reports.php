<?php 
if (!function_exists('base_url')) {
    function base_url($atRoot=FALSE, $atCore=FALSE, $parse=FALSE){
        if (isset($_SERVER['HTTP_HOST'])) {
            $http = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
            $hostname = $_SERVER['HTTP_HOST'];
            $dir =  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

            $core = preg_split('@/@', str_replace($_SERVER['DOCUMENT_ROOT'], '', realpath(dirname(__FILE__))), -1, PREG_SPLIT_NO_EMPTY);
            $core = $core[0];

            $tmplt = $atRoot ? ($atCore ? "%s://%s/%s/" : "%s://%s/") : ($atCore ? "%s://%s/%s/" : "%s://%s%s");
            $end = $atRoot ? ($atCore ? $core : $hostname) : ($atCore ? $core : $dir);
            $base_url = sprintf( $tmplt, $http, $hostname, $end );
        }
        else $base_url = 'http://localhost/';

        if ($parse) {
            $base_url = parse_url($base_url);
            if (isset($base_url['path'])) if ($base_url['path'] == '/') $base_url['path'] = '';
        }

        return $base_url;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>patient</title>
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
    <link href="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/../css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/../css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/../css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/../css/highcharts.css" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .stats-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .progress {
            height: 8px;
            border-radius: 4px;
        }

        .category-item {
            padding: 0.75rem;
            border-radius: 8px;
            transition: background-color 0.2s;
        }

        .category-item:hover {
            background-color: rgba(0,0,0,0.02);
        }
    </style>
</head>
<body>
    <?php
    include("../connection.php");
    include("admin_header.php");

    // Get statistics for reports
    $stats = array();

    // Total appointments
    $query = "SELECT COUNT(*) as total FROM appointment";
    $result = $database->query($query);
    $stats['total_appointments'] = $result->fetch_assoc()['total'];

    // Total patients
    $query = "SELECT COUNT(*) as total FROM patient";
    $result = $database->query($query);
    $stats['total_patients'] = $result->fetch_assoc()['total'];

    // Total doctors
    $query = "SELECT COUNT(*) as total FROM doctor";
    $result = $database->query($query);
    $stats['total_doctors'] = $result->fetch_assoc()['total'];

    // Appointments by status
    $query = "SELECT 
        SUM(CASE WHEN s.scheduledate > CURDATE() THEN 1 ELSE 0 END) as upcoming,
        SUM(CASE WHEN s.scheduledate = CURDATE() THEN 1 ELSE 0 END) as today,
        SUM(CASE WHEN s.scheduledate < CURDATE() THEN 1 ELSE 0 END) as past
    FROM appointment a
    JOIN schedule s ON a.scheduleid = s.scheduleid";
    $result = $database->query($query);
    $appointment_stats = $result->fetch_assoc();

    // Get reports data
    $query = "SELECT 
        COUNT(DISTINCT p.pid) as total_patients,
        COUNT(DISTINCT d.docid) as total_doctors,
        COUNT(DISTINCT a.appoid) as total_appointments,
        COUNT(DISTINCT s.scheduleid) as total_schedules
    FROM patient p
    LEFT JOIN appointment a ON p.pid = a.pid
    LEFT JOIN schedule s ON a.scheduleid = s.scheduleid
    LEFT JOIN doctor d ON s.docid = d.docid";

    $result = $database->query($query);
    $stats = $result->fetch_assoc();
    ?>

    <!-- Overview Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-primary">
                            <i class='bx bx-calendar-check'></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-muted">Total Appointments</h6>
                            <h3 class="mb-0"><?php echo $stats['total_appointments']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-success">
                            <i class='bx bxs-user'></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-muted">Total Patients</h6>
                            <h3 class="mb-0"><?php echo $stats['total_patients']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-info">
                            <i class='bx bxs-user-rectangle'></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-muted">Total Doctors</h6>
                            <h3 class="mb-0"><?php echo $stats['total_doctors']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon bg-warning">
                            <i class='bx bx-time'></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="mb-0 text-muted">Today's Appointments</h6>
                            <h3 class="mb-0"><?php echo $appointment_stats['today']; ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Reports -->
    <div class="row g-3">
        <!-- Appointment Status -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Appointment Status</h5>
                </div>
                <div class="card-body">
                    <div class="appointment-stats">
                        <div class="stat-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Upcoming</span>
                                <span class="badge bg-primary"><?php echo $appointment_stats['upcoming']; ?></span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-primary" style="width: <?php echo ($appointment_stats['upcoming'] / $stats['total_appointments']) * 100; ?>%"></div>
                            </div>
                        </div>
                        <div class="stat-item mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Today</span>
                                <span class="badge bg-warning"><?php echo $appointment_stats['today']; ?></span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-warning" style="width: <?php echo ($appointment_stats['today'] / $stats['total_appointments']) * 100; ?>%"></div>
                            </div>
                        </div>
                        <div class="stat-item mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Past</span>
                                <span class="badge bg-secondary"><?php echo $appointment_stats['past']; ?></span>
                            </div>
                            <div class="progress">
                                <div class="progress-bar bg-secondary" style="width: <?php echo ($appointment_stats['past'] / $stats['total_appointments']) * 100; ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assessment Summary -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Assessment Categories</h5>
                </div>
                <div class="card-body">
                    <div class="assessment-categories">
                        <div class="category-item">
                            <div class="d-flex align-items-center mb-3">
                                <div class="category-icon bg-primary-light text-primary">
                                    <i class='bx bx-group'></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0">Social Interaction</h6>
                                    <small class="text-muted">Interpersonal skills assessment</small>
                                </div>
                            </div>
                        </div>
                        <div class="category-item">
                            <div class="d-flex align-items-center mb-3">
                                <div class="category-icon bg-success-light text-success">
                                    <i class='bx bx-conversation'></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0">Communication</h6>
                                    <small class="text-muted">Verbal and non-verbal skills</small>
                                </div>
                            </div>
                        </div>
                        <div class="category-item">
                            <div class="d-flex align-items-center mb-3">
                                <div class="category-icon bg-warning-light text-warning">
                                    <i class='bx bx-shape-circle'></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0">Behavior Patterns</h6>
                                    <small class="text-muted">Behavioral analysis</small>
                                </div>
                            </div>
                        </div>
                        <div class="category-item">
                            <div class="d-flex align-items-center">
                                <div class="category-icon bg-info-light text-info">
                                    <i class='bx bx-brain'></i>
                                </div>
                                <div class="ms-3">
                                    <h6 class="mb-0">Emotional Regulation</h6>
                                    <small class="text-muted">Emotional control assessment</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid p-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">System Statistics</h5>
                    </div>
                    <div class="card-body">
                        Total Patients: <?php echo $stats['total_patients']; ?><br>
                        Total Doctors: <?php echo $stats['total_doctors']; ?><br>
                        Total Appointments: <?php echo $stats['total_appointments']; ?><br>
                        Total Schedules: <?php echo $stats['total_schedules']; ?><br>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url(TRUE); ?>Mindcheck/script/jquery.min.js"></script>
    <script src="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/js/dataTables.bootstrap5.min.js"></script>
    <script src="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/js/dataTables.buttons.min.js"></script>
    <script src="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/js/jszip.min.js"></script>
    <script src="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/js/pdfmake.min.js"></script>
    <script src="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/js/vfs_fonts.js"></script>
    <script src="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/js/buttons.html5.min.js"></script>
    <script src="<?php echo base_url(TRUE); ?>Mindcheck/patient_assets/vendor/bootstrap/js/highcharts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>