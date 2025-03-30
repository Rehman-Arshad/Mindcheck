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

// Search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_query = '';
if ($search) {
    $search_query = " AND (p.pname LIKE ? OR p.pemail LIKE ?)";
}

// Base query
$query = "
    SELECT 
        p.*,
        MAX(s.scheduledate) as last_visit,
        COUNT(DISTINCT a.appoid) as total_visits,
        (
            SELECT status 
            FROM appointment a2 
            INNER JOIN schedule s2 ON a2.scheduleid = s2.scheduleid
            WHERE a2.pid = p.pid AND s2.docid = ?
            ORDER BY s2.scheduledate DESC 
            LIMIT 1
        ) as latest_status
    FROM patient p
    LEFT JOIN appointment a ON p.pid = a.pid
    LEFT JOIN schedule s ON a.scheduleid = s.scheduleid AND s.docid = ?
    WHERE 1=1 $search_query
    GROUP BY p.pid
    ORDER BY last_visit DESC, p.pname ASC
";

$params = [$docid, $docid];
$param_types = "ii";

if ($search) {
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= "ss";
}

$stmt = $database->prepare($query);
if (!$stmt) {
    die("Error preparing query: " . $database->error);
}

$stmt->bind_param($param_types, ...$params);
$stmt->execute();
$patients = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patients - MindCheck</title>
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

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 1.5rem;
        }
        
        .search-input {
            flex: 1;
            padding: 0.5rem 1rem;
            border: 1px solid #e9ecef;
            border-radius: 5px;
            font-size: 0.9rem;
            color: var(--dark);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(25,119,204,0.25);
        }
        
        .patient-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .patient-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .patient-card:hover {
            transform: translateY(-5px);
        }
        
        .patient-header {
            padding: 1.25rem;
            background: linear-gradient(135deg, var(--primary), #3291e6);
            color: white;
        }
        
        .patient-header h3 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 500;
        }
        
        .visit-badge {
            background: rgba(255,255,255,0.2);
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .patient-body {
            padding: 1.25rem;
        }
        
        .patient-info {
            margin-bottom: 1.25rem;
        }
        
        .patient-info p {
            margin: 0.5rem 0;
            color: var(--secondary);
            font-size: 0.9rem;
        }
        
        .patient-actions {
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
            .search-form {
                flex-direction: column;
            }
            .patient-grid {
                grid-template-columns: 1fr;
            }
            .patient-actions {
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
                <h2>Patients</h2>
                <a href="add-patient.php" class="btn-action btn-primary">
                    <i class='bx bx-user-plus'></i> Add New Patient
                </a>
            </div>
            <div class="card-body">
                <form method="get" class="search-form">
                    <input type="text" name="search" class="search-input" 
                           placeholder="Search by name or email" 
                           value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn-action btn-primary">
                        <i class='bx bx-search'></i> Search
                    </button>
                </form>

                <div class="patient-grid">
                    <?php if ($patients && $patients->num_rows > 0): ?>
                        <?php while($row = $patients->fetch_assoc()): ?>
                            <div class="patient-card">
                                <div class="patient-header">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h3><?php echo htmlspecialchars($row['pname']); ?></h3>
                                        <span class="visit-badge">
                                            <i class='bx bx-time'></i>
                                            <?php echo (int)$row['total_visits']; ?> visits
                                        </span>
                                    </div>
                                    <?php if ($row['latest_status']): ?>
                                        <span class="status-badge status-<?php echo strtolower($row['latest_status']); ?>">
                                            <i class='bx bx-<?php 
                                                echo $row['latest_status'] === 'confirmed' ? 'check-circle' : 
                                                    ($row['latest_status'] === 'cancelled' ? 'x-circle' : 'time-five');
                                            ?>'></i>
                                            <?php echo ucfirst($row['latest_status']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="patient-body">
                                    <div class="patient-info">
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($row['pemail']); ?></p>
                                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['ptel']); ?></p>
                                        <?php if ($row['last_visit']): ?>
                                            <p><strong>Last Visit:</strong> <?php echo date('F j, Y', strtotime($row['last_visit'])); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="patient-actions">
                                        <a href="patient-details.php?id=<?php echo $row['pid']; ?>" class="btn-action btn-primary">
                                            <i class='bx bx-user'></i> View Details
                                        </a>
                                        <a href="schedule.php?pid=<?php echo $row['pid']; ?>" class="btn-action btn-secondary">
                                            <i class='bx bx-calendar-plus'></i> Schedule
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class='bx bx-user-x'></i>
                            <h3>No patients found</h3>
                            <p><?php echo $search ? 'No patients match your search criteria.' : 'You haven\'t added any patients yet.'; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>