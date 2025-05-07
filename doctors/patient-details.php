<?php
session_start();

// Check if the user is logged in as a doctor
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'd') {
    header("location: ../login.php");
    exit;
}

include("../connection.php");

// Get doctor info
$useremail = $_SESSION["user"];
$userrow = $database->query("SELECT * FROM doctor WHERE docemail='$useremail'");
$userfetch = $userrow->fetch_assoc();
$docid = $userfetch["docid"];

// Get patient ID from URL
$patient_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($patient_id <= 0) {
    die("Invalid patient ID");
}

// Get patient information
$patient_query = "SELECT * FROM patient WHERE pid = ?";
$stmt = $database->prepare($patient_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient_result = $stmt->get_result();

if ($patient_result->num_rows == 0) {
    die("Patient not found");
}

$patient = $patient_result->fetch_assoc();

// Get patient's appointments with this doctor
$appointments_query = "SELECT a.*, s.scheduledate, s.scheduletime, s.title 
                     FROM appointment a 
                     JOIN schedule s ON a.scheduleid = s.scheduleid 
                     WHERE a.pid = ? AND s.docid = ? 
                     ORDER BY s.scheduledate DESC, s.scheduletime DESC";

$stmt = $database->prepare($appointments_query);
$stmt->bind_param("ii", $patient_id, $docid);
$stmt->execute();
$appointments_result = $stmt->get_result();

// Get patient's assessments
$assessments_query = "SELECT a.*, 
                    (SELECT COALESCE(SUM(score), 0) FROM assessment_scores WHERE assessment_id = a.id) as total_score
                    FROM assessments a 
                    WHERE a.patient_id = ? 
                    ORDER BY a.created_at DESC";
$stmt = $database->prepare($assessments_query);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$assessments_result = $stmt->get_result();

// Include necessary CSS before the header
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Details - MindCheck</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
<?php
include("header.php");
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-user-circle text-primary me-2"></i> Patient Details</h2>
                <a href="patient.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-2"></i> Back to Patients</a>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-circle mx-auto mb-3">
                            <span class="avatar-initials"><?php echo substr($patient['pname'], 0, 1); ?></span>
                        </div>
                        <h3 class="mb-0"><?php echo htmlspecialchars($patient['pname']); ?></h3>
                        <p class="text-muted">Patient ID: <?php echo $patient['pid']; ?></p>
                    </div>
                    
                    <div class="patient-info">
                        <div class="info-item">
                            <i class="fas fa-envelope text-primary"></i>
                            <span><?php echo htmlspecialchars($patient['pemail']); ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-phone text-primary"></i>
                            <span><?php echo htmlspecialchars($patient['ptel']); ?></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-id-card text-primary"></i>
                            <span>NIC: <?php echo htmlspecialchars($patient['pnic']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <ul class="nav nav-tabs mb-4" id="patientTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button" role="tab">Appointments</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="assessments-tab" data-bs-toggle="tab" data-bs-target="#assessments" type="button" role="tab">Assessments</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="notes-tab" data-bs-toggle="tab" data-bs-target="#notes" type="button" role="tab">Medical Notes</button>
                </li>
            </ul>
            
            <div class="tab-content" id="patientTabsContent">
                <!-- Appointments Tab -->
                <div class="tab-pane fade show active" id="appointments" role="tabpanel">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Appointment History</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($appointments_result->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Session</th>
                                                <th>Status</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($appointment = $appointments_result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo date('M d, Y', strtotime($appointment['scheduledate'])); ?></td>
                                                    <td><?php echo date('h:i A', strtotime($appointment['scheduletime'])); ?></td>
                                                    <td><?php echo htmlspecialchars($appointment['title']); ?></td>
                                                    <td>
                                                        <?php if ($appointment['status'] == 'pending'): ?>
                                                            <span class="badge bg-warning">Pending</span>
                                                        <?php elseif ($appointment['status'] == 'confirmed'): ?>
                                                            <span class="badge bg-success">Confirmed</span>
                                                        <?php elseif ($appointment['status'] == 'cancelled'): ?>
                                                            <span class="badge bg-danger">Cancelled</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($appointment['status'] == 'pending'): ?>
                                                            <a href="confirm-appointment.php?id=<?php echo $appointment['appoid']; ?>" class="btn btn-sm btn-success">Confirm</a>
                                                        <?php endif; ?>
                                                        <?php if ($appointment['status'] != 'cancelled'): ?>
                                                            <a href="cancel-appointment.php?id=<?php echo $appointment['appoid']; ?>" class="btn btn-sm btn-danger">Cancel</a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times text-muted mb-3" style="font-size: 3rem;"></i>
                                    <p class="mb-0">No appointments found for this patient.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Assessments Tab -->
                <div class="tab-pane fade" id="assessments" role="tabpanel">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Assessment History</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($assessments_result->num_rows > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Child Name</th>
                                                <th>Gender</th>
                                                <th>Score</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($assessment = $assessments_result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo date('M d, Y', strtotime($assessment['created_at'])); ?></td>
                                                    <td><?php echo htmlspecialchars($assessment['child_name']); ?></td>
                                                    <td><?php echo ucfirst($assessment['gender']); ?></td>
                                                    <td>
                                                        <div class="progress" style="height: 10px;">
                                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo min(100, $assessment['total_score']); ?>%" aria-valuenow="<?php echo $assessment['total_score']; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                        <small class="mt-1 d-block"><?php echo number_format($assessment['total_score'], 1); ?> points</small>
                                                    </td>
                                                    <td>
                                                        <a href="view-assessment.php?id=<?php echo $assessment['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-clipboard-list text-muted mb-3" style="font-size: 3rem;"></i>
                                    <p class="mb-0">No assessments found for this patient.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Medical Notes Tab -->
                <div class="tab-pane fade" id="notes" role="tabpanel">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Medical Notes</h5>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                                <i class="fas fa-plus me-1"></i> Add Note
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-4">
                                <i class="fas fa-notes-medical text-muted mb-3" style="font-size: 3rem;"></i>
                                <p class="mb-0">No medical notes found for this patient.</p>
                                <p class="text-muted">Click "Add Note" to create the first note.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Medical Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="add-note.php" method="post">
                <div class="modal-body">
                    <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                    <div class="mb-3">
                        <label for="note_title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="note_title" name="note_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="note_content" class="form-label">Content</label>
                        <textarea class="form-control" id="note_content" name="note_content" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Note</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Main content positioning to account for fixed navbar */
    .container.mt-4 {
        margin-top: 100px !important;
    }
    
    .avatar-circle {
        width: 100px;
        height: 100px;
        background-color: #0A76D8;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .avatar-initials {
        color: white;
        font-size: 40px;
        font-weight: bold;
        text-transform: uppercase;
    }
    
    .patient-info {
        margin-top: 20px;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .info-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    .info-item i {
        font-size: 18px;
        width: 30px;
    }
    
    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
    }
    
    .nav-tabs .nav-link.active {
        color: #0A76D8;
        border-bottom: 2px solid #0A76D8;
    }
</style>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Footer content can be added here if needed -->
