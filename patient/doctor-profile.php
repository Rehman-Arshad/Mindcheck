<?php
session_start();
if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
    header("location: ../login.php");
    exit;
}

include("../connection.php");

// Get doctor ID from URL
$doctor_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($doctor_id <= 0) {
    die("Invalid doctor ID");
}

// Get doctor information
$query = "SELECT d.*, s.name as specialty_name 
          FROM doctor d 
          LEFT JOIN specialties s ON d.specialties = s.id 
          WHERE d.docid = ?";

$stmt = $database->prepare($query);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Doctor not found");
}

$doctor = $result->fetch_assoc();

// Get doctor's available slots
$slots_query = "SELECT s.*, 
                (SELECT COUNT(*) FROM appointment a WHERE a.scheduleid = s.scheduleid) as booked
                FROM schedule s 
                WHERE s.docid = ? AND s.scheduledate >= CURDATE()
                ORDER BY s.scheduledate ASC, s.scheduletime ASC";

$slots_stmt = $database->prepare($slots_query);
$slots_stmt->bind_param("i", $doctor_id);
$slots_stmt->execute();
$slots_result = $slots_stmt->get_result();

// Count available slots
$available_slots = 0;
while ($slot = $slots_result->fetch_assoc()) {
    if ($slot['booked'] < $slot['nop']) {
        $available_slots++;
    }
}

// Reset result pointer
$slots_result->data_seek(0);

include("patient_header.php");
?>

<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
    .profile-container {
        max-width: 1000px;
        margin: 40px auto;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }
    
    .profile-header {
        background: linear-gradient(135deg, #0A76D8, #006dd3);
        color: white;
        padding: 30px;
        position: relative;
    }
    
    .doctor-name {
        font-size: 2.2em;
        margin-bottom: 5px;
    }
    
    .doctor-specialty {
        font-size: 1.2em;
        opacity: 0.9;
        margin-bottom: 20px;
    }
    
    .profile-stats {
        display: flex;
        gap: 30px;
    }
    
    .stat-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .stat-item i {
        font-size: 1.5em;
    }
    
    .profile-body {
        padding: 30px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }
    
    @media (max-width: 768px) {
        .profile-body {
            grid-template-columns: 1fr;
        }
    }
    
    .info-section {
        background: #f9f9f9;
        border-radius: 10px;
        padding: 25px;
    }
    
    .section-title {
        font-size: 1.3em;
        color: #2c4964;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-title i {
        color: #0A76D8;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        color: #555;
    }
    
    .info-item i {
        font-size: 1.2em;
        color: #1977cc;
        margin-right: 15px;
        min-width: 24px;
    }
    
    .info-item span {
        font-size: 1em;
    }
    
    .schedule-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #eee;
    }
    
    .schedule-item:last-child {
        border-bottom: none;
    }
    
    .schedule-date {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .schedule-date i {
        color: #0A76D8;
    }
    
    .schedule-status {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85em;
        font-weight: 500;
    }
    
    .status-available {
        background: #e8f5e9;
        color: #2e7d32;
    }
    
    .status-booked {
        background: #ffebee;
        color: #c62828;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
    }
    
    .btn {
        flex: 1;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        font-size: 1em;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .btn-primary {
        background: #0A76D8;
        color: white;
    }
    
    .btn-primary:hover {
        background: #006dd3;
    }
    
    .btn-secondary {
        background: #f8f9fa;
        color: #2c4964;
        border: 1px solid #dee2e6;
    }
    
    .btn-secondary:hover {
        background: #e9ecef;
    }
    
    .no-slots {
        text-align: center;
        padding: 30px 0;
        color: #777;
    }
    
    .no-slots i {
        font-size: 3em;
        color: #ccc;
        margin-bottom: 15px;
    }
</style>

<section class="breadcrumbs">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h2>Doctor Profile</h2>
            <ol>
                <li><a href="index.php">Home</a></li>
                <li><a href="doctors.php">Doctors</a></li>
                <li>Profile</li>
            </ol>
        </div>
    </div>
</section>

<div class="container">
    <div class="profile-container">
        <div class="profile-header">
            <h1 class="doctor-name">Dr. <?php echo htmlspecialchars($doctor['docname']); ?></h1>
            <p class="doctor-specialty"><?php echo htmlspecialchars($doctor['specialty_name']); ?></p>
            
            <div class="profile-stats">
                <div class="stat-item">
                    <i class='bx bx-medal'></i>
                    <span><?php echo $doctor['docexp']; ?> years experience</span>
                </div>
                <div class="stat-item">
                    <i class='bx bx-calendar-check'></i>
                    <span><?php echo $available_slots; ?> available slots</span>
                </div>
                <div class="stat-item">
                    <i class='bx bx-dollar-circle'></i>
                    <span>$<?php echo number_format(isset($doctor['consultation_fee']) ? $doctor['consultation_fee'] : 50.00, 2); ?> per session</span>
                </div>
            </div>
        </div>
        
        <div class="profile-body">
            <div class="info-section">
                <h3 class="section-title"><i class='bx bx-user'></i> Contact Information</h3>
                
                <div class="info-item">
                    <i class='bx bx-envelope'></i>
                    <span><?php echo htmlspecialchars($doctor['docemail']); ?></span>
                </div>
                
                <div class="info-item">
                    <i class='bx bx-phone'></i>
                    <span><?php echo htmlspecialchars($doctor['doctel']); ?></span>
                </div>
                
                <div class="info-item">
                    <i class='bx bx-building'></i>
                    <span>MindCheck Mental Health Clinic</span>
                </div>
                
                <div class="info-item">
                    <i class='bx bx-time'></i>
                    <span>Available on weekdays, 9:00 AM - 5:00 PM</span>
                </div>
                
                <div class="action-buttons">
                    <?php if ($available_slots > 0): ?>
                    <a href="book-appointment.php?docid=<?php echo $doctor['docid']; ?>" class="btn btn-primary">
                        <i class='bx bx-calendar-plus'></i> Book Appointment
                    </a>
                    <?php else: ?>
                    <a href="contact.php" class="btn btn-primary">
                        <i class='bx bx-bell'></i> Notify Me
                    </a>
                    <?php endif; ?>
                    
                    <a href="doctors.php" class="btn btn-secondary">
                        <i class='bx bx-arrow-back'></i> Back to Doctors
                    </a>
                </div>
            </div>
            
            <div class="info-section">
                <h3 class="section-title"><i class='bx bx-calendar'></i> Upcoming Schedule</h3>
                
                <?php if ($slots_result->num_rows > 0): ?>
                    <?php 
                    $count = 0;
                    while ($slot = $slots_result->fetch_assoc()): 
                        $is_available = $slot['booked'] < $slot['nop'];
                        // Only show first 5 slots
                        if ($count++ >= 5) break;
                    ?>
                    <div class="schedule-item">
                        <div class="schedule-date">
                            <i class='bx bx-calendar'></i>
                            <div>
                                <div><strong><?php echo date('l, F j, Y', strtotime($slot['scheduledate'])); ?></strong></div>
                                <div><?php echo date('h:i A', strtotime($slot['scheduletime'])); ?></div>
                            </div>
                        </div>
                        
                        <?php if ($is_available): ?>
                        <span class="schedule-status status-available">Available</span>
                        <?php else: ?>
                        <span class="schedule-status status-booked">Booked</span>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                    
                    <?php if ($slots_result->num_rows > 5): ?>
                    <div style="text-align: center; margin-top: 15px;">
                        <a href="book-appointment.php?docid=<?php echo $doctor['docid']; ?>" style="color: #0A76D8; text-decoration: none;">
                            View all available slots
                        </a>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                <div class="no-slots">
                    <i class='bx bx-calendar-x'></i>
                    <p>No upcoming schedule available</p>
                    <p>Please check back later or contact us for more information</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include("patient_footer.php"); ?>
