<?php
include("../connection.php");
include("admin_header.php");

// Get patients with their last visit date
$query = "SELECT 
    p.pid,
    p.pname,
    p.ptel,
    p.pemail,
    COUNT(DISTINCT a.appoid) as appointment_count,
    MAX(s.scheduledate) as last_visit
FROM patient p
LEFT JOIN appointment a ON p.pid = a.pid
LEFT JOIN schedule s ON a.scheduleid = s.scheduleid
GROUP BY p.pid
ORDER BY p.pname";

$result = $database->query($query);
?>

<div class="container-fluid p-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Manage Patients</h5>
            <div class="d-flex gap-2">
                <div class="search-box">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search patients...">
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPatientModal">
                    <i class='bx bx-plus'></i> Add Patient
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Appointments</th>
                            <th>Last Visit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result && $result->num_rows > 0): ?>
                            <?php while($patient = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="patient-avatar">
                                                <i class='bx bxs-user-circle fs-4'></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium"><?php echo htmlspecialchars($patient['pname']); ?></div>
                                                <div class="text-muted small">ID: <?php echo $patient['pid']; ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div><i class='bx bx-phone'></i> <?php echo htmlspecialchars($patient['ptel']); ?></div>
                                    </td>
                                    <td>
                                        <div><i class='bx bx-envelope'></i> <?php echo htmlspecialchars($patient['pemail']); ?></div>
                                    </td>
                                    <td>
                                        <?php if($patient['appointment_count'] > 0): ?>
                                            <a href="appointments.php?patient=<?php echo $patient['pid']; ?>" 
                                               class="badge bg-info text-decoration-none">
                                                <?php echo $patient['appointment_count']; ?> appointments
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No appointments</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($patient['last_visit']): ?>
                                            <span class="text-muted">
                                                <?php echo date('M d, Y', strtotime($patient['last_visit'])); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">No visits yet</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="editPatient(<?php echo htmlspecialchars(json_encode($patient)); ?>)">
                                                <i class='bx bx-edit'></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deletePatient(<?php echo $patient['pid']; ?>)">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                            <a href="assessment.php?patient=<?php echo $patient['pid']; ?>" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class='bx bx-clipboard'></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class='bx bx-user-x fs-1 text-muted'></i>
                                    <p class="mt-2 mb-0">No patients found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Patient Modal -->
<div class="modal fade" id="addPatientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addPatientForm">
                    <div class="mb-3">
                        <label class="form-label">Patient Name</label>
                        <input type="text" class="form-control" name="pname" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" name="ptel" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="pemail" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="ppassword" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="savePatient()">Save Patient</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Patient Modal -->
<div class="modal fade" id="editPatientModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editPatientForm">
                    <input type="hidden" id="editPid" name="pid">
                    <div class="mb-3">
                        <label class="form-label">Patient Name</label>
                        <input type="text" class="form-control" id="editPname" name="pname" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" id="editPtel" name="ptel" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="editPemail" name="pemail" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updatePatient()">Update Patient</button>
            </div>
        </div>
    </div>
</div>

<script>
function savePatient() {
    const form = document.getElementById('addPatientForm');
    const formData = new FormData(form);

    fetch('add_patient.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error adding patient');
        }
    });
}

function editPatient(patient) {
    document.getElementById('editPid').value = patient.pid;
    document.getElementById('editPname').value = patient.pname;
    document.getElementById('editPtel').value = patient.ptel;
    document.getElementById('editPemail').value = patient.pemail;
    new bootstrap.Modal(document.getElementById('editPatientModal')).show();
}

function updatePatient() {
    const form = document.getElementById('editPatientForm');
    const formData = new FormData(form);

    fetch('update_patient.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error updating patient');
        }
    });
}

function deletePatient(id) {
    if (confirm('Are you sure you want to delete this patient?')) {
        fetch('delete_patient.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error deleting patient');
            }
        });
    }
}

// Search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const tableRows = document.querySelectorAll('tbody tr');

    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>