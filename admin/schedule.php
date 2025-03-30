<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedule - MindCheck</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php
    include("../connection.php");
    include("admin_header.php");

    // Get all schedules with doctor info
    $query = "SELECT 
        s.*,
        d.docname,
        d.specialties,
        COUNT(a.appoid) as booked_slots
    FROM schedule s
    JOIN doctor d ON s.docid = d.docid
    LEFT JOIN appointment a ON s.scheduleid = a.scheduleid
    WHERE s.scheduledate >= CURDATE()
    GROUP BY s.scheduleid
    ORDER BY s.scheduledate ASC, s.scheduletime ASC";

    $result = $database->query($query);

    // Get all doctors for the add schedule form
    $doctors_query = "SELECT docid, docname, specialties FROM doctor ORDER BY docname";
    $doctors_result = $database->query($doctors_query);
    ?>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Manage Schedules</h5>
            <div class="d-flex gap-2">
                <div class="search-box">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search schedules...">
                </div>
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                    <i class='bx bx-plus'></i>
                    <span>Add Schedule</span>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Slots</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result && $result->num_rows > 0): ?>
                            <?php while($schedule = $result->fetch_assoc()): ?>
                                <?php
                                    $scheduleDate = new DateTime($schedule['scheduledate'] . ' ' . $schedule['scheduletime']);
                                    $now = new DateTime();
                                    
                                    if ($scheduleDate > $now) {
                                        $status = 'upcoming';
                                        $statusBadge = 'bg-primary';
                                        $statusText = 'Upcoming';
                                    } elseif (date('Y-m-d', $scheduleDate->getTimestamp()) === date('Y-m-d')) {
                                        $status = 'today';
                                        $statusBadge = 'bg-warning';
                                        $statusText = 'Today';
                                    } else {
                                        $status = 'past';
                                        $statusBadge = 'bg-secondary';
                                        $statusText = 'Past';
                                    }

                                    $availableSlots = $schedule['nop'] - $schedule['booked_slots'];
                                    $slotsBadge = $availableSlots > 0 ? 'bg-success' : 'bg-danger';
                                ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="doctor-avatar">
                                                <i class='bx bxs-user-rectangle'></i>
                                            </div>
                                            <div>
                                                <div class="fw-medium"><?php echo htmlspecialchars($schedule['docname']); ?></div>
                                                <div class="text-muted small"><?php echo htmlspecialchars($schedule['specialties']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class='bx bx-calendar text-muted'></i>
                                            <?php echo date('M j, Y', strtotime($schedule['scheduledate'])); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class='bx bx-time text-muted'></i>
                                            <?php echo date('g:i A', strtotime($schedule['scheduletime'])); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $slotsBadge; ?>">
                                            <?php echo $availableSlots; ?> / <?php echo $schedule['nop']; ?> slots
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $statusBadge; ?>">
                                            <?php echo $statusText; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-secondary btn-icon"
                                                    onclick="editSchedule(<?php echo htmlspecialchars(json_encode($schedule)); ?>)">
                                                <i class='bx bx-edit'></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger btn-icon"
                                                    onclick="deleteSchedule(<?php echo $schedule['scheduleid']; ?>)">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class='bx bx-calendar-x fs-1 text-muted'></i>
                                    <p class="mt-2 mb-0">No schedules found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Schedule Modal -->
    <div class="modal fade" id="addScheduleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addScheduleForm">
                        <div class="mb-3">
                            <label for="docid" class="form-label">Doctor</label>
                            <select class="form-select" id="docid" name="docid" required>
                                <option value="">Select Doctor</option>
                                <?php while($doctor = $doctors_result->fetch_assoc()): ?>
                                    <option value="<?php echo $doctor['docid']; ?>">
                                        <?php echo htmlspecialchars($doctor['docname']); ?> 
                                        (<?php echo htmlspecialchars($doctor['specialties']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="scheduledate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="scheduledate" name="scheduledate" 
                                   min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="scheduletime" class="form-label">Time</label>
                            <input type="time" class="form-control" id="scheduletime" name="scheduletime" required>
                        </div>
                        <div class="mb-3">
                            <label for="nop" class="form-label">Number of Slots</label>
                            <input type="number" class="form-control" id="nop" name="nop" min="1" max="20" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveSchedule()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Schedule Modal -->
    <div class="modal fade" id="editScheduleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editScheduleForm">
                        <input type="hidden" id="editScheduleId" name="scheduleid">
                        <div class="mb-3">
                            <label for="editDocid" class="form-label">Doctor</label>
                            <select class="form-select" id="editDocid" name="docid" required>
                                <option value="">Select Doctor</option>
                                <?php 
                                $doctors_result->data_seek(0);
                                while($doctor = $doctors_result->fetch_assoc()): 
                                ?>
                                    <option value="<?php echo $doctor['docid']; ?>">
                                        <?php echo htmlspecialchars($doctor['docname']); ?> 
                                        (<?php echo htmlspecialchars($doctor['specialties']); ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editScheduledate" class="form-label">Date</label>
                            <input type="date" class="form-control" id="editScheduledate" name="scheduledate" required>
                        </div>
                        <div class="mb-3">
                            <label for="editScheduletime" class="form-label">Time</label>
                            <input type="time" class="form-control" id="editScheduletime" name="scheduletime" required>
                        </div>
                        <div class="mb-3">
                            <label for="editNop" class="form-label">Number of Slots</label>
                            <input type="number" class="form-control" id="editNop" name="nop" min="1" max="20" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateSchedule()">Update</button>
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

    function editSchedule(schedule) {
        document.getElementById('editScheduleId').value = schedule.scheduleid;
        document.getElementById('editDocid').value = schedule.docid;
        document.getElementById('editScheduledate').value = schedule.scheduledate;
        document.getElementById('editScheduletime').value = schedule.scheduletime;
        document.getElementById('editNop').value = schedule.nop;
        new bootstrap.Modal(document.getElementById('editScheduleModal')).show();
    }

    function deleteSchedule(id) {
        if (confirm('Are you sure you want to delete this schedule?')) {
            fetch('delete_schedule.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'scheduleid=' + id
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error deleting schedule');
                }
            });
        }
    }

    function saveSchedule() {
        const form = document.getElementById('addScheduleForm');
        const formData = new FormData(form);

        fetch('add_schedule.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error adding schedule');
            }
        });
    }

    function updateSchedule() {
        const form = document.getElementById('editScheduleForm');
        const formData = new FormData(form);

        fetch('update_schedule.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error updating schedule');
            }
        });
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>