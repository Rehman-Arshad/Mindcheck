<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctors - MindCheck</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    include("../connection.php");
    include("admin_header.php");

    // Get all doctors with their specialties and schedule count
    $query = "SELECT 
        d.docid,
        d.docname,
        s.name as specialty_name,
        d.doctel,
        d.docemail,
        COUNT(sch.scheduleid) as total_schedules
    FROM doctor d 
    LEFT JOIN specialties s ON d.specialties = s.id
    LEFT JOIN schedule sch ON d.docid = sch.docid 
    GROUP BY d.docid 
    ORDER BY d.docname";

    $result = $database->query($query);

    // Get all specialties for the dropdown
    $specialties_query = "SELECT id, name FROM specialties ORDER BY name";
    $specialties_result = $database->query($specialties_query);
    $specialties = [];
    if ($specialties_result) {
        while ($row = $specialties_result->fetch_assoc()) {
            $specialties[] = $row;
        }
    }
    ?>

    <div class="container-fluid p-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Manage Doctors</h5>
                <div class="d-flex gap-2">
                    <div class="search-box">
                        <input type="text" id="searchInput" class="form-control" placeholder="Search doctors...">
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDoctorModal">
                        <i class='bx bx-plus'></i> Add Doctor
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Doctor</th>
                                <th>Specialty</th>
                                <th>Contact</th>
                                <th>Schedules</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($result && $result->num_rows > 0): ?>
                                <?php while($doctor = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="doctor-avatar">
                                                    <i class='bx bxs-user-circle fs-4'></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium"><?php echo htmlspecialchars($doctor['docname']); ?></div>
                                                    <div class="text-muted small">ID: <?php echo $doctor['docid']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                <?php echo htmlspecialchars($doctor['specialty_name']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <div><i class='bx bx-phone'></i> <?php echo htmlspecialchars($doctor['doctel']); ?></div>
                                                <?php if(!empty($doctor['docemail'])): ?>
                                                    <div class="text-muted small">
                                                        <i class='bx bx-envelope'></i> <?php echo htmlspecialchars($doctor['docemail']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if($doctor['total_schedules'] > 0): ?>
                                                <a href="schedule.php?doctor=<?php echo $doctor['docid']; ?>" 
                                                   class="badge bg-info text-decoration-none">
                                                    <?php echo $doctor['total_schedules']; ?> schedules
                                                </a>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">No schedules</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="editDoctor(<?php echo htmlspecialchars(json_encode($doctor)); ?>)">
                                                    <i class='bx bx-edit'></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteDoctor(<?php echo $doctor['docid']; ?>)">
                                                    <i class='bx bx-trash'></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <i class='bx bx-user-x fs-1 text-muted'></i>
                                        <p class="mt-2 mb-0">No doctors found</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Doctor Modal -->
    <div class="modal fade" id="addDoctorModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Doctor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addDoctorForm">
                        <div class="mb-3">
                            <label class="form-label">Doctor Name</label>
                            <input type="text" class="form-control" name="docname" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Specialty</label>
                            <select class="form-select" name="specialties" required>
                                <option value="">Select Specialty</option>
                                <?php foreach($specialties as $specialty): ?>
                                    <option value="<?php echo $specialty['id']; ?>">
                                        <?php echo htmlspecialchars($specialty['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" name="doctel" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="docemail" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="docpassword" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveDoctor()">Save Doctor</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function saveDoctor() {
        const form = document.getElementById('addDoctorForm');
        const formData = new FormData(form);

        fetch('add_doctor.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error adding doctor');
            }
        });
    }

    function editDoctor(doctor) {
        // Implement edit functionality
        console.log('Edit doctor:', doctor);
    }

    function deleteDoctor(id) {
        if (confirm('Are you sure you want to delete this doctor?')) {
            fetch('delete_doctor.php?id=' + id)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error deleting doctor');
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

    <?php
    $database->close();
    ?>
</body>
</html>