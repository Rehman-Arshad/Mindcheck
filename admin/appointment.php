<?php
include("../connection.php");
include("admin_header.php");

// Get all appointments with related information
$query = "SELECT 
    a.*,
    p.pname as patient_name,
    p.ptel as patient_contact,
    d.docname as doctor_name,
    d.specialties as doctor_specialty,
    s.scheduledate,
    s.scheduletime
FROM appointment a
JOIN schedule s ON a.scheduleid = s.scheduleid
JOIN patient p ON a.pid = p.pid
JOIN doctor d ON s.docid = d.docid
ORDER BY s.scheduledate DESC, s.scheduletime DESC";

try {
    $result = $database->query($query);
} catch (Exception $e) {
    error_log("Error fetching appointments: " . $e->getMessage());
    die("An error occurred while fetching the data.");
}
?>

<div class="container-fluid p-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">All Appointments</h5>
            <div class="d-flex gap-2">
                <div class="search-box">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search appointments...">
                </div>
                <a href="schedule.php" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class='bx bx-plus'></i>
                    <span>New Appointment</span>
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Schedule</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result && $result->num_rows > 0): ?>
                            <?php while($appointment = $result->fetch_assoc()): ?>
                                <?php 
                                    $appointmentDate = new DateTime($appointment['scheduledate'] . ' ' . $appointment['scheduletime']);
                                    $now = new DateTime();
                                    
                                    if ($appointmentDate > $now) {
                                        $status = 'upcoming';
                                        $statusBadge = 'bg-primary';
                                        $statusText = 'Upcoming';
                                    } elseif (date('Y-m-d', $appointmentDate->getTimestamp()) === date('Y-m-d')) {
                                        $status = 'today';
                                        $statusBadge = 'bg-warning';
                                        $statusText = 'Today';
                                    } else {
                                        $status = 'past';
                                        $statusBadge = 'bg-secondary';
                                        $statusText = 'Past';
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="patient-avatar">
                                                <i class='bx bxs-user'></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium"><?php echo htmlspecialchars($appointment['patient_name']); ?></div>
                                                <div class="text-muted small">
                                                    <i class='bx bx-phone'></i>
                                                    <?php echo htmlspecialchars($appointment['patient_contact']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-medium"><?php echo htmlspecialchars($appointment['doctor_name']); ?></div>
                                            <div class="text-muted small">
                                                <i class='bx bx-briefcase'></i>
                                                <?php echo htmlspecialchars($appointment['doctor_specialty']); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-medium">
                                                <i class='bx bx-calendar'></i>
                                                <?php echo date('M j, Y', strtotime($appointment['scheduledate'])); ?>
                                            </div>
                                            <div class="text-muted small">
                                                <i class='bx bx-time'></i>
                                                <?php echo date('g:i A', strtotime($appointment['scheduletime'])); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $statusBadge; ?>">
                                            <?php echo $statusText; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if($status === 'upcoming'): ?>
                                                <a href="edit-appointment.php?id=<?php echo $appointment['appoid']; ?>" 
                                                   class="btn btn-sm btn-outline-secondary btn-icon">
                                                    <i class='bx bx-edit'></i>
                                                </a>
                                                <a href="delete-appointment.php?id=<?php echo $appointment['appoid']; ?>" 
                                                   class="btn btn-sm btn-outline-danger btn-icon"
                                                   onclick="return confirm('Are you sure you want to cancel this appointment?');">
                                                    <i class='bx bx-x'></i>
                                                </a>
                                            <?php endif; ?>
                                            <a href="view-appointment.php?id=<?php echo $appointment['appoid']; ?>" 
                                               class="btn btn-sm btn-outline-primary btn-icon">
                                                <i class='bx bx-show'></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class='bx bx-calendar-x fs-1 text-muted'></i>
                                    <p class="mt-2 mb-0">No appointments found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableRows = document.querySelectorAll('tbody tr');

    searchInput.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();

        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
});
</script>