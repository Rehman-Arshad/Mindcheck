<?php
include("../connection.php");
include("admin_header.php");

// Get all assessment questions
$query = "SELECT * FROM assessment_questions ORDER BY id ASC";
$result = $database->query($query);

?>

<div class="container-fluid p-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Assessment Questions</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                <i class='bx bx-plus'></i> Add Question
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category</th>
                            <th>Question</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result && $result->num_rows > 0): ?>
                            <?php while($question = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $question['id']; ?></td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?php echo ucfirst(str_replace('_', ' ', $question['category'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($question['question']); ?></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    onclick="editQuestion(<?php echo $question['id']; ?>)">
                                                <i class='bx bx-edit'></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteQuestion(<?php echo $question['id']; ?>)">
                                                <i class='bx bx-trash'></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <i class='bx bx-info-circle fs-1 text-muted'></i>
                                    <p class="mt-2 mb-0">No assessment questions found</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div class="modal fade" id="addQuestionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addQuestionForm">
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category" required>
                            <option value="social_interaction">Social Interaction</option>
                            <option value="communication">Communication</option>
                            <option value="behavior_patterns">Behavior Patterns</option>
                            <option value="emotional_regulation">Emotional Regulation</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Question</label>
                        <textarea class="form-control" name="question" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveQuestion()">Save Question</button>
            </div>
        </div>
    </div>
</div>

<script>
function saveQuestion() {
    const form = document.getElementById('addQuestionForm');
    const formData = new FormData(form);

    fetch('add_question.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error adding question');
        }
    });
}

function editQuestion(id) {
    // Implement edit functionality
}

function deleteQuestion(id) {
    if (confirm('Are you sure you want to delete this question?')) {
        fetch('delete_question.php?id=' + id)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting question');
            }
        });
    }
}
</script>
