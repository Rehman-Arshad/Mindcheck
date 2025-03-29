<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user"]) || $_SESSION["usertype"] != 'p') {
    header("location: ../login.php");
    exit;
}

include("../connection.php");

$pid = $_SESSION["pid"];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $child_name = $_POST["child_name"];
    $age = $_POST["age"];
    $gender = $_POST["gender"];
    
    // Create assessment record
    $stmt = $database->prepare("INSERT INTO assessments (patient_id, child_name, age, gender) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $pid, $child_name, $age, $gender);
    $stmt->execute();
    $assessment_id = $stmt->insert_id;
    
    $total_score = 0;
    $question_count = 0;
    
    // Process responses
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'question_') === 0) {
            $question_id = substr($key, 9);
            $score = intval($value);
            $total_score += $score;
            $question_count++;
            
            // Save response
            $stmt = $database->prepare("INSERT INTO assessment_responses (assessment_id, question_id, score) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $assessment_id, $question_id, $score);
            $stmt->execute();
        }
    }
    
    // Calculate and update total score
    if ($question_count > 0) {
        $avg_score = ($total_score / $question_count) * 100;
        $stmt = $database->prepare("UPDATE assessments SET total_score = ? WHERE id = ?");
        $stmt->bind_param("di", $avg_score, $assessment_id);
        $stmt->execute();
    }
    
    header("location: assessment_result.php?id=" . $assessment_id);
    exit;
}

// Get assessment questions
$questions = $database->query("SELECT * FROM assessment_questions ORDER BY category, id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Development Assessment - MindCheck</title>
    <link rel="stylesheet" href="../css/animations.css">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        .assessment-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .assessment-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .assessment-header h1 {
            color: #2c4964;
            margin-bottom: 10px;
        }
        .assessment-header p {
            color: #666;
        }
        .assessment-form {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .form-section {
            margin-bottom: 30px;
        }
        .form-section h2 {
            color: #2c4964;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e3f2fd;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c4964;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .question-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
        }
        .question-text {
            color: #2c4964;
            margin-bottom: 15px;
            font-weight: 500;
        }
        .options {
            display: flex;
            gap: 15px;
        }
        .option-label {
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }
        .submit-btn {
            background: #1977cc;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: block;
            margin: 30px auto 0;
        }
        .submit-btn:hover {
            background: #1565c0;
        }
        .category-header {
            color: #1977cc;
            margin: 30px 0 15px;
            font-size: 1.2em;
        }
        @media (max-width: 768px) {
            .options {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <?php include("../header.php"); ?>

    <div class="assessment-container">
        <div class="assessment-header">
            <h1>Child Development Assessment</h1>
            <p>Please answer the following questions about your child's behavior and development</p>
        </div>

        <form method="POST" class="assessment-form">
            <div class="form-section">
                <h2>Child Information</h2>
                <div class="form-group">
                    <label for="child_name">Child's Name</label>
                    <input type="text" id="child_name" name="child_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="age">Age (in years)</label>
                    <input type="number" id="age" name="age" min="2" max="18" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>

            <div class="form-section">
                <h2>Assessment Questions</h2>
                <?php 
                $current_category = '';
                while($question = $questions->fetch_assoc()): 
                    if ($current_category != $question['category']):
                        $current_category = $question['category'];
                        echo "<h3 class='category-header'>" . ucwords(str_replace('_', ' ', $question['category'])) . "</h3>";
                    endif;
                ?>
                    <div class="question-item">
                        <div class="question-text"><?php echo htmlspecialchars($question['question']); ?></div>
                        <div class="options">
                            <label class="option-label">
                                <input type="radio" name="question_<?php echo $question['id']; ?>" value="1" required>
                                Never
                            </label>
                            <label class="option-label">
                                <input type="radio" name="question_<?php echo $question['id']; ?>" value="2" required>
                                Rarely
                            </label>
                            <label class="option-label">
                                <input type="radio" name="question_<?php echo $question['id']; ?>" value="3" required>
                                Sometimes
                            </label>
                            <label class="option-label">
                                <input type="radio" name="question_<?php echo $question['id']; ?>" value="4" required>
                                Often
                            </label>
                            <label class="option-label">
                                <input type="radio" name="question_<?php echo $question['id']; ?>" value="5" required>
                                Always
                            </label>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <button type="submit" class="submit-btn">Submit Assessment</button>
        </form>
    </div>
</body>
</html>
