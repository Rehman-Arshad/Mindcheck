<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'd') {
    header("location: ../login.php");
    exit();
}

include("../connection.php");

// Get doctor ID
$email = $_SESSION["user"];
$result = $database->query("SELECT docid FROM doctor WHERE docemail='$email'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $docid = $row['docid'];
} else {
    die("Error: Doctor not found");
}

// Get filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : 'upcoming';

// Base query
$query = "
    SELECT 
        a.appoid,
        a.status,
        s.scheduledate,
        s.scheduletime,
        s.title,
        p.pname,
        p.pemail,
        p.pid
    FROM appointment a
    JOIN schedule s ON a.scheduleid = s.scheduleid
    JOIN patient p ON a.pid = p.pid
    WHERE s.docid = ?
";

$param_types = "i";
$params = [$docid];

// Add filters
if ($status_filter) {
    $query .= " AND a.status = ?";
    $params[] = $status_filter;
    $param_types .= "s";
}

if ($date_filter === 'today') {
    $query .= " AND s.scheduledate = CURDATE()";
} elseif ($date_filter === 'upcoming') {
    $query .= " AND s.scheduledate >= CURDATE()";
} elseif ($date_filter === 'past') {
    $query .= " AND s.scheduledate < CURDATE()";
} elseif ($date_filter) {
    $query .= " AND s.scheduledate = ?";
    $params[] = $date_filter;
    $param_types .= "s";
}

$query .= " ORDER BY s.scheduledate ASC, s.scheduletime ASC";

$stmt = $database->prepare($query);
if (!$stmt) {
    die("Error preparing query: " . $database->error);
}

if (!empty($params)) {
    $stmt->bind_param($param_types, ...$params);
}
$stmt->execute();
$appointments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - MindCheck</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/main.css">
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
            background-color: var(--light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }

        .card-header {
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            padding: 1.5rem;
            border-radius: 15px 15px 0 0 !important;
        }

        .card-header h2 {
            margin: 0;
            color: var(--secondary);
            font-size: 1.5rem;
        }

        .card-body {
            padding: 1.5rem;
        }

        .filter-options {
            display: flex;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .appointment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }
        
        .appointment-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .appointment-card:hover {
            transform: translateY(-5px);
        }
        
        .appointment-header {
            padding: 1.25rem;
            background: linear-gradient(135deg, var(--primary), #3291e6);
            color: white;
        }
        
        .appointment-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 500;
        }
        
        .appointment-header p {
            margin: 0.5rem 0 0;
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .appointment-body {
            padding: 1.25rem;
        }
        
        .appointment-info {
            margin-bottom: 1.25rem;
        }
        
        .appointment-info p {
            margin: 0.5rem 0;
            color: var(--secondary);
            font-size: 0.9rem;
        }
        
        .appointment-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: #1565b0;
            color: white;
        }

        .btn-secondary {
            background: var(--light);
            color: var(--primary);
            border: 1px solid var(--primary);
        }

        .btn-secondary:hover {
            background: #e9ecef;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .status-pending {
            background: rgba(255,193,7,0.1);
            color: var(--warning);
        }

        .status-confirmed {
            background: rgba(40,167,69,0.1);
            color: var(--success);
        }

        .status-cancelled {
            background: rgba(220,53,69,0.1);
            color: var(--danger);
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            grid-column: 1 / -1;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 1rem;
        }
        
        .empty-state h3 {
            color: var(--secondary);
            margin-bottom: 0.5rem;
            font-size: 1.25rem;
        }
        
        .empty-state p {
            color: #6c757d;
            margin: 0;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .filter-options {
                flex-direction: column;
            }
            .appointment-grid {
                grid-template-columns: 1fr;
            }
            .appointment-actions {
                flex-direction: column;
            }
            .btn-action {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <?php include("header.php"); ?>

    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2>Appointments</h2>
                <a href="schedule.php" class="btn-action btn-primary">
                    <i class='bx bx-plus'></i> New Appointment
                </a>
            </div>
            <div class="card-body">
                <div class="filter-options">
                    <a href="?date=today" class="btn-action <?php echo $date_filter === 'today' ? 'btn-primary' : 'btn-secondary'; ?>">
                        <i class='bx bx-calendar'></i> Today
                    </a>
                    <a href="?date=upcoming" class="btn-action <?php echo $date_filter === 'upcoming' ? 'btn-primary' : 'btn-secondary'; ?>">
                        <i class='bx bx-calendar-check'></i> Upcoming
                    </a>
                    <a href="?date=past" class="btn-action <?php echo $date_filter === 'past' ? 'btn-primary' : 'btn-secondary'; ?>">
                        <i class='bx bx-calendar-x'></i> Past
                    </a>
                </div>

                <div class="appointment-grid">
                    <?php if ($appointments && $appointments->num_rows > 0): ?>
                        <?php while($row = $appointments->fetch_assoc()): ?>
                            <div class="appointment-card">
                                <div class="appointment-header">
                                    <h3><?php echo date('F j, Y', strtotime($row['scheduledate'])); ?></h3>
                                    <p><?php echo date('g:i A', strtotime($row['scheduletime'])); ?> - <?php echo $row['title']; ?></p>
                                </div>
                                <div class="appointment-body">
                                    <div class="appointment-info">
                                        <p><strong>Patient:</strong> <?php echo htmlspecialchars($row['pname']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($row['pemail']); ?></p>
                                        <p>
                                            <strong>Status:</strong> 
                                            <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                                <i class='bx bx-<?php 
                                                    echo $row['status'] === 'confirmed' ? 'check-circle' : 
                                                        ($row['status'] === 'cancelled' ? 'x-circle' : 'time-five');
                                                ?>'></i>
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </p>
                                    </div>
                                    <div class="appointment-actions">
                                        <?php if ($row['status'] === 'pending'): ?>
                                            <button onclick="updateStatus(<?php echo $row['appoid']; ?>, 'confirmed')" class="btn-action btn-primary">
                                                <i class='bx bx-check'></i> Confirm
                                            </button>
                                            <button onclick="updateStatus(<?php echo $row['appoid']; ?>, 'cancelled')" class="btn-action btn-secondary">
                                                <i class='bx bx-x'></i> Cancel
                                            </button>
                                        <?php endif; ?>
                                        <a href="patient-details.php?id=<?php echo $row['pid']; ?>" class="btn-action btn-secondary">
                                            <i class='bx bx-user'></i> View Patient
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class='bx bx-calendar-x'></i>
                            <h3>No appointments found</h3>
                            <p>There are no appointments matching your filters.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateStatus(appointmentId, status) {
            if (confirm('Are you sure you want to ' + status + ' this appointment?')) {
                fetch('update-appointment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${appointmentId}&status=${status}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to update appointment status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the appointment');
                });
            }
        }
    </script>
</body>
</html>